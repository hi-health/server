<?php

namespace App\Http\Controllers\APIs;

use App\Events\DoctorOnlineEvent;
use App\Http\Controllers\Controller;
use App\Services\Facades\MitakeSmexpress;
use App\Traits\AWSSNS;
use App\Traits\SlackNotify;
use App\User;
use App\UserDeviceToken;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Log;
class UserController extends Controller
{
    use AWSSNS,
        SlackNotify;

    public function register(Request $request)
    {
        Log::info('register',$request -> input());
        $this->validate($request, [
            'account' => ['required', 'string', 'unique:users,account'],
            'password' => ['required', 'string', 'min:6'],
            'login_type' => ['required', 'in:1,2'],
            'name' => ['required', 'string'],
            'male' => ['required', 'in:0,1'],
            'birthday' => ['required', 'date_format:Y-m-d'],
            'email' => ['required', 'email'],
            'city_id' => ['required'],
            'district_id' => ['required'],
            'avatar' => ['image', 'max:1024'],
            'status' => ['in:0,1'],
        ]);
        $avatar_file = $request->file('avatar');
        $user = User::create($request->input());
        if ($avatar_file) {
            $folder_name = 'members';
            $path = public_path($folder_name);
            $name = 'avatar_'.$user->id.'.'.$avatar_file->getClientOriginalExtension();
            $avatar_file->move($path, $name);
            $user->avatar = '/'.$folder_name.'/'.$name;
            $user->save();
        }

        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        Log::info($request->input());
        $this->validate($request, [
            'login_type' => ['required', 'in:1,2'],
            'account' => ['required_without:facebook_id', 'string'],
            'password' => ['required_without:facebook_id', 'string'],
            'facebook_id' => ['required_without:account', 'string'],
            'treatment_type' => ['between:1,2'],
            'treatment_kind' => ['between:1,4'],
            'onset_date' => ['date_format:Y-m-d'],
            'onset_part' => ['between:1,5'],
        ]);
        if ($request->has('account')) {
            $user = User
                ::where('account', $request->input('account'))
                ->where('login_type', $request->input('login_type'))
                ->where('status', 1)
                ->first();

            if (!$user) {
                //400：查無此帳號，可以註冊
                return response()->json(null, 400);
            }

            if ($user && Hash::check($request->input('password'), $user->password)) {
                // 密碼是對的不做事等09行回覆
            } else {
                $user = null;
                return response()->json(null, 401);
            }
        } elseif ($request->has('facebook_id')) {
            $user = User
                ::where('facebook_id', $request->input('facebook_id'))
                ->where('login_type', $request->input('login_type'))
                ->where('status', 1)
                ->first();
        }

        if ($user) {
            $user->treatment_type = $request->input('treatment_type', null);
            $user->treatment_kind = $request->input('treatment_kind', null);
            $user->onset_date = $request->input('onset_date', null);
            $user->onset_part = $request->input('onset_part', null);
            $user->online = true;
            $user->save();
            $user->access_token = Hash::make(serialize($user));

            return response()->json($user);
        }

        //400：查無此帳號，可以註冊
        return response()->json([
            'from_facebook' => $request->has('facebook_id'),
        ], 400);

        // TODO 402: 註冊第二次的問題
        // return response()->json([
        //     'from_facebook' => $request->has('facebook_id'),
        // ], 402);
    }

    public function sendSmsCode(Request $request)
    {
        $this->validate($request, [
            'phone' => ['required', 'unique:users,account'],
        ]);
        $phone = $request->input('phone');
        $code = rand(100000, 999999);
        $message = '您的驗證碼為：'.$code;
        $sended = MitakeSmexpress::send($phone, $phone, $message);
        if ($sended) {
            return response()->json([
                'code' => $code,
            ]);
        }

        return response()->json(null, 500);
    }

    public function update(Request $request, $user_id)
    {
        $this->validate($request, [
            'name' => ['required', 'string'],
            'birthday' => ['required', 'date_format:Y-m-d'],
            'email' => ['required', 'email'],
            'city_id' => ['required'],
            'district_id' => ['required'],
            //'avatar' => ['image', 'max:1024'],
            'status' => ['in:0,1'],
        ]);
        $avatar_file = $request->file('avatar');
        $user = User
            ::where('id', $user_id)
            ->first();
        if (!$user) {
            return response()->json(null, 404);
        }
        $result = $user->update($request->only([
            'name',
            'birthday',
            'email',
            'city_id',
            'district_id',
            'status',
        ]));
        if ($avatar_file) {
            $folder_name = 'members';
            $path = public_path($folder_name);
            $name = 'avatar_'.$user->id.'.'.$avatar_file->getClientOriginalExtension();
            $avatar_file->move($path, $name);
            $user->avatar = '/'.$folder_name.'/'.$name;
            $user->save();
        }

        return response()->json($user);
    }

    public function changeOnline(Request $request, $user_id)
    {
        $user = User
            ::where('id', $user_id)
            ->first();
        if (!$user) {
            return response()->json(null, 404);
        }
        $user->online = true;
        $user->online_at = Carbon::now();
        $user->save();
        if ($user->login_type === '2') {
            event(new DoctorOnlineEvent($user));
            $longitude = $request->input('longitude');
            $latitude = $request->input('latitude');
            if (!empty($longitude) and !empty($latitude)) {
                $user->doctor->longitude = $longitude;
                $user->doctor->latitude = $latitude;
                $user->doctor->update();
            }

            $city_id = $request->input('city_id');
            $district_id = $request->input('district_id');
            if (!empty($city_id) and !empty($district_id)) {
                $result = $user->update($request->only([
                    'city_id',
                    'district_id'
                ]));
            }
        }

        return response()->json(null);
    }

    public function changeOffline(Request $request, $user_id)
    {
        $user = User
            ::where('id', $user_id)
            ->first();
        if (!$user) {
            return response()->json(null, 404);
        }
        $user->online = false;
        $user->save();

        return response()->json(null);
    }

    public function addToken(Request $request, $user_id)
    {
        $arns = implode(',', array_keys(config('aws.arns'))); // member-gcm,member-apn,doctor-gcm,doctor-apn
        $this->validate($request, [
            'arn' => ['required', 'in:'.$arns],
            'device_token' => ['required', 'string'],
        ]);
        $user = User
            ::where('id', $user_id)
            ->where('status', 1)
            ->first();
        if (!$user) {
            return response()->json(null, 404);
        }
        $arn = $request->input('arn');
        $device_token = $request->input('device_token');
        $app_arn = config('aws.arns.'.$arn);
        try {
            $device_arn = $this->addToSNS($app_arn, $device_token);
            $user_device_token = new UserDeviceToken([
                'device_arn' => $device_arn,
                'device_token' => $device_token,
            ]);
            $user->deviceToken()->save($user_device_token);
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }

        return response()->json($user_device_token);
    }

    public function deleteByAccount(Request $request, $account)
    {   
        $user = User
            ::where('account', $account)
            ->first();
        $deleted = false;
        if ($user) {
            $deleted = $user->forceDelete();
        }
        if ($deleted) {
            return response()->json([
                'deleted' => true,
            ]);
        }
        return response()->json(null, 400);
    }

// 　　↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓重設密碼↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

    public function InputPhoneView()
    {
        return view('password.input_phone');
    }

    public function ResetPassword_SmsCode(Request $request)
    {
        $this->validate($request, [
            'phone' => ['required'],
        ]);
        $phone = $request->input('phone');
        $user = User::where('account', $phone)
                ->first();
        $user_id = $user->id;
        if($user) {
            $code = rand(100000, 999999);
            $message = '您的驗證碼為：'.$code;
            $sended = MitakeSmexpress::send($phone, $phone, $message);
            if ($sended) {
                return view('password.sms_input', compact('phone', 'code', 'user_id'));
            } else {
                return back()->withErrors(['驗證碼發送失敗']);
            }
        } else {
            return back()->withErrors(['尚未註冊']);
        }
    }

    public function ResetPassword_SmsCodeCheck(Request $request, $user_id, $code)
    {
        //檢驗手機驗證碼
        $this->validate($request, [
            'sms_code' => ['required'],
        ]);
        
        $input_code = $request->input('sms_code');
        
        if($input_code == $code){
            return view('password.reset_password', compact('user_id'));
        } else {
            return back()->withErrors(['驗證碼驗證失敗']);
        }
    }

    public function ResetPassword(Request $request, $user_id)
    {
        $this->validate($request, [
            'new_password' => ['required', 'string'],
            'check_new_password' => ['required', 'string'],
        ]);

        $user = User::where('id', $user_id)
                ->first();

        if ($user) {
            $user->password = $request->input('new_password');
            $user->save();
            return response()->json($user->password);
        } else {
            return '此帳號尚未註冊';
        }
    }

    public function ModifyPassword(Request $request, $user_id)
    {
        $this->validate($request, [
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string'],
        ]);

        $user = User::where('id', $user_id)
                ->first();

        if ($user && password_verify($request->input('old_password'), $user->password)) {
            $user->password = $request->input('new_password');
            $user->save();
            return response()->json($user->password);
        } else {
            return 'failed';
        }
    }
}
