<?php

namespace App\Http\Controllers\APIs;

use App\Events\DoctorSendMessageEvent;
use App\Events\MemberSendMessageEvent;
use App\Http\Controllers\Controller;
use App\Message;
use App\Traits\MemberUtility;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class MessageController extends Controller
{
    use MemberUtility;

    public function getChunk(Request $request, $doctor_id, $member_id)
    {
        $member = User
            ::withMember($member_id)
            ->first();
        Log::alert('1');
        if (!$member) {
            return response()->json(null, 404);
        }
        $doctor = User
            ::withDoctor($doctor_id)
            ->first();
        Log::alert('2');
        if (!$doctor) {
            return response()->json(null, 404);
        }
        Log::alert('3');
        $first_id = $request->get('first_id', null);
        $latest_id = $request->get('latest_id', null);
        $member_request_id = $request->get('member_request_id', null);
        $per_page = $request->get('per_page', 20);
        $is_vip = $this->isVIPMember($member_id, $doctor_id);
        $message_model = Message
            ::with('member', 'doctor')
            ->where('doctors_id', $doctor_id)
            ->where('members_id', $member_id)
            ->where('visible', true);
//        if (!$is_vip) {
//            $message_model->where('created_at', '>', Carbon::now()->subMinute(5));
//        }
        if ($first_id > 0) {
            $message_model->where('id', '<', $first_id);
        }
        if ($latest_id > 0) {
            $message_model->where('id', '>', $latest_id);
        }
        if ($member_request_id > 0) {
            $message_model->where('member_requests_id', $member_request_id);
        }
        $pagination = $message_model->orderBy('id', 'DESC')
            ->paginate($per_page);
        if ($pagination->isEmpty()) {
            $chunk = [
                'first_id' => 0,
                'latest_id' => 0,
                'count' => $pagination->total(),
                'data' => [],
            ];
        } else {
            $chunk = [
                'first_id' => $pagination->last()->id,
                'latest_id' => $pagination->first()->id,
                'total' => $pagination->total(),
                'data' => $pagination->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'source' => $item->source,
                        'member_id' => $item->members_id,
                        'member' => [
                            'name' => $item->member->name,
                            'avatar' => $item->member->avatar,
                        ],
                        'doctor_id' => $item->doctors_id,
                        'doctor' => [
                            'name' => $item->doctor->name,
                            'avatar' => $item->doctor->avatar,
                        ],
                        'message' => $item->message,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                        'doctor_readed_at' => $item->doctor_readed_at ? $item->doctor_readed_at->format('Y-m-d H:i:s') : null,
                        'member_readed_at' => $item->member_readed_at ? $item->member_readed_at->format('Y-m-d H:i:s') : null,
                    ];
                })->sort()->values(),
            ];
        }
        if (!$is_vip) {
            $first_message = Message
                ::where('members_id', $member_id)
                ->where('doctors_id', $doctor_id)
                ->where('source', 'doctor')
                ->whereNotNull('started_at')
                ->orderBy('started_at', 'DESC')
                ->first();
            if ($first_message) {
                $chunk['expired_at'] = $first_message->started_at->addMinutes(12 * 60);
                $diff_minutes = $first_message->started_at->diffInMinutes();
                $chunk['diff_minutes'] = $diff_minutes;
                $chunk['chat_allowed'] = ($diff_minutes < Message::EXPIRED_START_MINUTES) || ($diff_minutes > Message::EXPIRED_LEAVE_MINUTES);
            } else {
                $chunk['diff_minutes'] = 0;
                $chunk['chat_allowed'] = true;
            }
        } else {
            $chunk['diff_minutes'] = 0;
            $chunk['chat_allowed'] = true;
        }
        $source = $request->get('source');
        if ($source === 'doctor') {
            $updated = Message
                ::where('doctors_id', $doctor_id)
                ->where('members_id', $member_id)
                ->whereNull('doctor_readed_at')
                ->update([
                    'doctor_readed_at' => Carbon::now(),
                ]);
        } else if ($source === 'member') {
            $updated = Message
                ::where('members_id', $member_id)
                ->where('doctors_id', $doctor_id)
                ->whereNull('member_readed_at')
                ->update([
                    'member_readed_at' => Carbon::now(),
                ]);
        }
        $chunk['is_vip'] = $is_vip;

        return response()->json($chunk);
    }

    public function send(Request $request)
    {
        Log::alert('@@@@@@@@@@@@@@@@');
        $this->validate($request, [
            'source' => ['required', 'in:doctor,member'],
            'doctors_id' => ['required', 'exists:doctors,users_id'],
            'members_id' => ['required', 'exists:users,id'],
            'member_request_id' => ['nullable', 'exists:member_requests,id'],
            'message' => ['required', 'string'],
            'visible' => ['in:0,1'],
        ]);
        Log::info($request -> input());
        $doctor_id = $request->input('doctors_id');
        $member_id = $request->input('members_id');
        $source = $request->input('source');
        $field_name = $source.'_readed_at';
        $visible = $request->input('visible', true);
        $has_message = Message
            ::where('members_id', $member_id)
            ->where('doctors_id', $doctor_id)
            ->where('source', 'doctor')
            ->whereNotNull('started_at')
            ->where('started_at', '>=', Carbon::now()->subMinute(30))
            ->exists();
        $message = new Message($request->input());
        if (!$has_message) {
            $message->started_at = Carbon::now();
        }
        $message->$field_name = Carbon::now();
        $message->save();
        $latest_messages = Message
            ::with('member', 'doctor')
            ->where('doctors_id', $doctor_id)
            ->where('members_id', $member_id)
            ->whereNull($field_name)
            ->where('visible', true)
            ->orderBy('id', 'ASC')
            ->get();
        $updated = Message
            ::whereIn('id', $latest_messages->pluck('id'))
            ->update([
                $field_name => Carbon::now(),
            ]);
        if ($latest_messages->isEmpty()) {
            $message->latest_messages = [
                'first_id' => 0,
                'latest_id' => 0,
                'total' => 0,
                'data' => [],
            ];
        } else {
            $message->latest_messages = [
                'first_id' => $latest_messages->first()->id,
                'latest_id' => $latest_messages->last()->id,
                'total' => $latest_messages->count(),
                'data' => $latest_messages->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'member_id' => $item->members_id,
                        'member' => [
                            'name' => $item->member->name,
                            'avatar' => $item->member->avatar,
                        ],
                        'doctor_id' => $item->doctors_id,
                        'doctor' => [
                            'name' => $item->doctor->name,
                            'avatar' => $item->doctor->avatar,
                        ],
                        'message' => $item->message,
                        'doctor_readed_at' => $item->doctor_readed_at ? $item->doctor_readed_at->format('Y-m-d H:i:s') : null,
                        'member_readed_at' => $item->member_readed_at ? $item->member_readed_at->format('Y-m-d H:i:s') : null,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    ];
                })->sort()->values(),
            ];
        }
        // 9/6 要求移除這項檢查
//        if ($visible) {
            $doctor = User
                ::withDoctor($doctor_id)
                ->first();
            $member = User
                ::withMember($member_id)
                ->first();
            if ($source === 'doctor') {
                event(
                    new DoctorSendMessageEvent($doctor, $member)
                );
            } else if ($source === 'member') {
                event(
                    new MemberSendMessageEvent($member, $doctor)
                );
            }
//        }
        Log::alert('5');
        return response()->json($message);
    }
}
