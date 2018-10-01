<?php

namespace App\Http\Controllers\APIs;

use App\Doctor;
use App\Http\Controllers\Controller;
use App\Member;
use App\MemberRequest;
use App\MemberRequestDoctor;
use App\Message;
use App\Service;
use App\Setting;
use App\Traits\MemberUtility;
use App\Traits\SettingUtility;
use App\User;
use Carbon\Carbon;
use DB,Log;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use MemberUtility,
        SettingUtility;

    public function getById(Request $request, $doctor_id)
    {
        $user = User
            ::where('id', $doctor_id)
            ->first();
        if (!$user or !$user->doctor) {
            return response()->json(null, 404);
        }

        return response()->json($user);
    }

    public function getByNumber(Request $request, $number)
    {
        $doctor = Doctor
            ::with('user')
            ->where('number', $number)
            ->first();
        if (!$doctor or !$doctor->user) {
            return response()->json(null, 404);
        }

        return response()->json($doctor->user);
    }

    public function getSummary(Request $request, $doctor_id)
    {
        $user = User
            ::where('id', $doctor_id)
            ->first();
        if (!$user or !$user->doctor) {
            return response()->json(null, 404);
        }
        $not_read_member_requests = max(0, MemberRequest::count() - MemberRequestDoctor::where('doctors_id', $doctor_id)->count());
        $not_open_services = Service
            ::where('doctors_id', $doctor_id)
            ->whereNull('opened_at')
            ->count();
        $not_read_members_id = Message
            ::where('doctors_id', $doctor_id)
            ->whereNull('doctor_readed_at')
            ->where('visible', true)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy('members_id');
        $not_read_members_status = $not_read_members_id->map(function ($item, $member_id) use ($doctor_id) {
            return [
                'is_vip' => $this->isVIPMember($member_id, $doctor_id),
            ];
        });
        $not_read_members_vip = $not_read_members_status->where('is_vip', true)->count();
        $not_read_members_normal = $not_read_members_status->where('is_vip', false)->count();
        $not_read_messages = $not_read_members_id->count();
        $banners = Setting
            ::where('type', 'banner')
            ->get();

        return response()->json([
            'banners' => $banners->map(function ($banner) {
                return [
                    'image_url' => $banner->getValue('image'),
                    'redirect_url' => $banner->getValue('redirect_url'),
                ];
            }),
            'badge_count' => [
                'not_read_member_requests' => $not_read_member_requests,
                'not_read_messages' => [
                    'total' => $not_read_messages,
                    'vip' => $not_read_members_vip,
                    'normal' => $not_read_members_normal,
                ],
                'not_open_services' => $not_open_services,
            ],
            'online_status' => $user->online,
            'service_email' => $this->getSetting('service_email'),
        ]);
    }

    public function getServiceMemberCollection(Request $request, $doctor_id)
    {
        $doctor = User
            ::withDoctor($doctor_id)
            ->first();
        if (!$doctor) {
            return response()->json(null, 404);
        }
        $is_paid = $request->get('is_paid', null);
        // 先取得該醫生有服務記錄的病人編號
        $members_id = Service
            ::where('doctors_id', $doctor_id)
            ->whereNotNull('members_id')
            ->get()
            ->unique('members_id')
            ->pluck('members_id');
        // 從病人編號開始查詢服務記錄
        $service_model = Service
            ::with(['member'])
            ->where('doctors_id', $doctor_id)
            ->whereIn('members_id', $members_id);
        if ($is_paid === '1') { // 只取得已付款
            $service_model->where('payment_status', 3)->orderBy('paid_at', 'DESC');
        } elseif ($is_paid === '0') { // 只取得未付款
            $service_model->where('payment_status', 0)->orderBy('created_at', 'DESC');
        } else { // 取得已付款及未付款的
            $service_model->whereIn('payment_status', [0, 1, 3])->orderBy('created_at', 'DESC');
        }
        $services = $service_model->get();

        Log::info($services->pluck('id'));
        if ($is_paid === '1') {
            $services = $services->filter(function ($service) {
                // 過濾超過時間的服務，依據治療方式決定30天或45天
                return $service->leave_days > 0;
            });
            $services = $services->unique('members_id');
        }
        $expire_seconds = intval($this->getSetting('message_expire_time', 720)) * 60;
        //$services = $services->unique('members_id');
        $services = $services->each(function ($service) use ($expire_seconds) {
            // 計算回應剩餘時間
            $member_message = Message::where('doctors_id', $service->doctors_id)
                ->where('members_id', $service->members_id)
                ->where('source', 'member')
                ->orderBy('created_at', 'DESC')
                ->first();
            $doctor_message = Message::where('doctors_id', $service->doctors_id)
                ->where('members_id', $service->members_id)
                ->where('source', 'doctor')
                ->orderBy('created_at', 'DESC')
                ->first();
            if ($member_message) {
                if ($doctor_message && Carbon::now()->diffInSeconds($doctor_message->created_at) < $expire_seconds) {
                    $service->reply_expire = null;
                } else {
                    $service->reply_expire = max(0, $expire_seconds - Carbon::now()->diffInSeconds($member_message->created_at));
                }
            } else {
                $service->reply_expire = null;
            }

            if($service->payment_status == 0 && $service->payment_method == 1){
                $service->service_status = 1;
                $service->service_name = '等待付款';
            } else if($service->payment_status == 3 && $service->leave_days > 0){
                $service->service_status = 2;
                $service->service_name = '服務中';
            } else if($service->payment_status == 3 && $service->leave_days <= 0){
                $service->service_status = 3;
                $service->service_name = '已過期';
            } else{
                $service->service_status = 0;
                $service->service_name = '';
            }
            
            unset($service->message);
        })->values();
        if ($is_paid === '0') {
            // 排除已付款的病人
            $paid_members_id = Service
                ::where('doctors_id', $doctor_id)
                ->whereIn('members_id', $members_id)
                ->where('payment_status', 3)
                ->get()
                ->unique('members_id')
                ->pluck('members_id');
            $services = $services->filter(function ($service) use ($paid_members_id) {
                return !in_array($service->members_id, $paid_members_id->toArray(), true);
            })->values();
        }

        $services = $services->filter(function ($service) {
            return $service->service_status != 0;
        })->values();
        
        $services->sortBy('service_status');

        $updated = Service
            ::where('doctors_id', $doctor_id)
            ->whereNull('opened_at')
            ->update([
                'opened_at' => Carbon::now(),
            ]);

        return response()->json($services);
    }

    public function getMembersPaginationWithMemberRequestByDoctor(Request $request, $doctor_id)
    {
        $per_page = $request->get('per_page', 20);
        $doctor = User
            ::withDoctor($doctor_id)
            ->first();
        if (!$doctor) {
            return response()->json(null, 404);
        }
        $services = Service
            ::where('doctors_id', $doctor_id)
            ->where('payment_status', 3)
            //->where('paid_at', '>', Carbon::now()->subMonth())
            ->get();
        $messages = Message
            ::where('doctors_id', $doctor_id)
            ->whereNotIn('members_id', $services->unique('members_id')->pluck('members_id'))
            ->where('visible', true)
            ->get();
        $members = Member
            ::with(['latestRequest'])
            ->whereIn('id', $messages->unique('members_id')->pluck('members_id'))
            ->where('login_type', 1)
            ->paginate($per_page);

        return response()->json($members);
    }

    public function getCollectionWithNear(Request $request)
    {
        Log::info($request->input());
        $this->validate($request, [
            'members_id' => ['required', 'exists:users,id'],
            // 'longitude' => ['required'], // 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            // 'latitude'  => ['required'], // 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'distance'  => ['numeric', 'min:1'],
        ]);

        $km = 6371;
        $longitude  = $request->input('longitude');
        $latitude   = $request->input('latitude');
        $distance   = $request->input('distance', 999);
        $members_id = $request->input('members_id');

        $member = User::where('id', $members_id)->first();

        // aleiku 2017.08.30 改成用使用者的服務區域去找。

        $doctors = User
            ::with(['firstMessage' => function ($query) use ($members_id) {
                $query->where('members_id', $members_id);
            }])
            ->where('city_id', $member->city_id)
            // aleiku 2017.09.29 初期只用城市，取消用區域搜尋。
            // ->where('district_id', $member->district_id)
            ->where('login_type', '2')
            ->where('online', true)
            ->orderBy('online_at', 'DESC')
            ->get()
            ->map(function ($user) use ($members_id) {
                $user->doctor = Doctor::where('users_id', $user->id)->first();
                // user === doctor
                $is_vip = $this->isVIPMember($members_id, $user->id);
                if (!$is_vip) {
                    if ($user->firstMessage) {
                        $user->diff_minutes = $user->firstMessage->started_at->diffInMinutes();
                        $user->chat_allowed = ($user->diff_minutes < Message::EXPIRED_START_MINUTES) || ($user->diff_minutes > Message::EXPIRED_LEAVE_MINUTES);
                    } else {
                        $user->diff_miunutes = 0;
                        $user->chat_allowed = true;
                    }
                } else {
                    $user->diff_miunutes = 0;
                    $user->chat_allowed = true;
                }
                $user->is_vip = $is_vip;
                unset($user->firstMessage);

                return $user;
            });
        // BEGIN aleiku 2017.08.30 之前用gps來找的備份

        // $collection = Doctor
        //     ::selectRaw('*, ('.$km.' * acos(cos(radians('.$latitude.')) * cos(radians(latitude)) * cos(radians(longitude) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin(radians(latitude)))) AS distance')
        //     ->having('distance', '<', max(0, $distance))
        //     ->get();

        // $collection2 = Doctor
        //     ::whereNull('latitude')
        //     ->whereNull('longitude')
        //     ->get()
        //     ->each(function ($item) use ($collection) {
        //         $collection->push($item);
        //     });

        // $doctors = User
        //     ::with(['firstMessage' => function ($query) use ($members_id) {
        //         $query->where('members_id', $members_id);
        //     }])
        //     ->whereIn('id', $collection->pluck('users_id'))
        //     ->where('login_type', '2')
        //     ->where('online', true)
        //     ->orderBy('online_at', 'DESC')
        //     ->get()
        //     ->map(function ($user) use ($collection, $members_id) {
        //         $user->doctor = $collection->where('users_id', $user->id)->first();
        //         // user === doctor
        //         $is_vip = $this->isVIPMember($members_id, $user->id);
        //         if (!$is_vip) {
        //             if ($user->firstMessage) {
        //                 $user->diff_minutes = $user->firstMessage->started_at->diffInMinutes();
        //                 $user->chat_allowed = ($user->diff_minutes < Message::EXPIRED_START_MINUTES) || ($user->diff_minutes > Message::EXPIRED_LEAVE_MINUTES);
        //             } else {
        //                 $user->diff_miunutes = 0;
        //                 $user->chat_allowed = true;
        //             }
        //         } else {
        //             $user->diff_miunutes = 0;
        //             $user->chat_allowed = true;
        //         }
        //         $user->is_vip = $is_vip;
        //         unset($user->firstMessage);

        //         return $user;
        //     });
        // END   aleiku 2017.08.30 之前用gps來找的備份
        return response()->json($doctors);
    }

    public function getCollectionBySearch(Request $request)
    {
        $this->validate($request, [
            'members_id' => ['required', 'exists:users,id'],
            'keyword' => ['required'],
            'city_id' => [],
            // 'treatment_type' => ['in:1,2'],
            // aleiku 不帶在地圖搜尋才查得到醫生
            'treatment_type' => [],
        ]);
        $keyword = $request->input('keyword');
        $city_id = $request->input('city_id');
        $treatment_type = $request->input('treatment_type', null);
        $members_id = $request->input('members_id');
        $user_model = User
            ::with([
                'doctor',
                'firstMessage' => function ($query) use ($members_id) {
                    $query->where('members_id', $members_id);
                },
            ])
            ->where('login_type', 2)
            ->where('online', true)
            ->where('name', 'LIKE', '%'.$keyword.'%');
        if ($city_id) {
            $user_model->where('city_id', $city_id);
        }
        $doctors = $user_model->get();
//        if ($treatment_type) {
//            $doctors = $doctors->filter(function ($doctor) use ($treatment_type) {
////                return $doctor->treatment_type === intval($treatment_type);
//            })->values();
//        }
        $doctors->each(function ($user) use ($members_id) {
            // user === doctor
            $is_vip = $this->isVIPMember($members_id, $user->id);
            if (!$is_vip) {
                if ($user->firstMessage) {
                    $user->diff_minutes = $user->firstMessage->started_at->diffInMinutes();
                    $user->chat_allowed = ($user->diff_minutes < Message::EXPIRED_START_MINUTES) || ($user->diff_minutes > Message::EXPIRED_LEAVE_MINUTES);
                } else {
                    $user->diff_miunutes = 0;
                    $user->chat_allowed = true;
                }
            } else {
                $user->diff_miunutes = 0;
                $user->chat_allowed = true;
            }
            $user->is_vip = $is_vip;
            unset($user->firstMessage);
        });

        return response()->json($doctors);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'account' => ['required', 'string', 'unique:users,account'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'name' => ['required', 'string'],
            'male' => ['required', 'in:0,1'],
            'birthday' => ['required'],
            'avatar' => ['nullable', 'image', 'max:10240'],
            'city_id' => ['required'],
            'district_id' => ['required'],
            'avatar' => ['nullable', 'mimes:jpeg,bmp,png,gif', 'max:1024'],
            'status' => ['in:0,1'],
            // 'online' => ['in:0,1'],
            'title' => ['required', 'string'],
            // 'number' => ['string'],
            // 'treatment_type' => ['in:1,2'],
            'treatment_type' => [],
            'experience_year' => ['numeric'],
            'experience' => ['nullable', 'string'],
            'specialty' => ['nullable', 'string'],
            'education' => ['nullable', 'string'],
            'license' => ['nullable', 'string'],
            // 'education_bonus' => ['numeric', 'min:0'],
            // 'longitude' => [], //, 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            // 'latitude' => [], // 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
        $user = new User($request->input());
        $user->login_type = 2;
        $doctor = new Doctor($request->input());
        $user->save();
        $user->doctor()->save($doctor);
        $avatar_file = $request->file('avatar');
        if ($avatar_file) {
            $folder_name = 'doctors';
            $path = public_path($folder_name);
            $name = 'avatar_'.$user->id.'.'.$avatar_file->getClientOriginalExtension();
            $avatar_file->move($path, $name);
            $user->avatar = '/'.$folder_name.'/'.$name;
            $user->save();
        }

        return response()->json($user, 200);
    }

    public function update(Request $request, $doctor_id)
    {
        $this->validate($request, [
            'name' => ['required', 'string'],
            'male' => ['required', 'in:0,1'],
            'birthday' => ['required'],
            'due_at' => ['nullable'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'city_id' => ['required'],
            'district_id' => ['required'],
            'status' => ['in:0,1'],
//            'online' => ['in:0,1'],
            'title' => ['required', 'string'],
//            'number' => ['string'],
            // 'treatment_type' => ['in:1,2'],
            'experience_year' => ['numeric'],
            'experience' => ['nullable', 'string'],
            'specialty' => ['nullable', 'string'],
            'education' => ['nullable', 'string'],
            'license' => ['nullable', 'string'],
            'education_bonus' => ['numeric', 'min:0'],
//            'longitude' => [], //, 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
//            'latitude' => [], // 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
        $user = User
            ::with('doctor')
            ->where('id', $doctor_id)
            ->first();
        if (!$user) {
            return response()->json(null, 404);
        }
        $user->update($request->input());
        $user->doctor->update($request->input());
        $avatar_file = $request->file('avatar');
        if ($avatar_file) {
            $folder_name = 'doctors';
            $path = public_path($folder_name);
            $name = 'avatar_'.$user->id.'.'.$avatar_file->getClientOriginalExtension();
            $avatar_file->move($path, $name);
            $user->avatar = '/'.$folder_name.'/'.$name;
            $user->save();
        }

        return response()->json([
            'updated' => true,
        ]);
    }
}

/*----- start sql log ------*/
DB::listen(
    function ($sql) {
        // $sql is an object with the properties:
        //  sql: The query
        //  bindings: the sql query variables
        //  time: The execution time for the query
        //  connectionName: The name of the connection

        // To save the executed queries to file:
        // Process the sql and the bindings:
        foreach ($sql->bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
                if (is_string($binding)) {
                    $sql->bindings[$i] = "'$binding'";
                }
            }
        }

        // Insert bindings into query
        $query = str_replace(['%', '?'], ['%%', '%s'], $sql->sql);

        $query = vsprintf($query, $sql->bindings);

        // Save the query to file
        $logFile = fopen(
            storage_path('logs'.DIRECTORY_SEPARATOR.date('Y-m-d').'_query.log'),
            'a+'
        );
        fwrite($logFile, date('Y-m-d H:i:s').': '.$query.PHP_EOL);
        fclose($logFile);
    }
);
