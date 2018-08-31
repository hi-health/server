<?php

namespace App\Listeners;

use App\Doctor;
use App\Events\MemberCreateRequestEvent;
use App\Traits\AWSSNS;
use App\Traits\SlackNotify;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMemberCreateRequestNotification implements ShouldQueue
{
    use AWSSNS, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(MemberCreateRequestEvent $event)
    {
        $member = $event->member;
        $member_request = $event->member_request;
        // aleiku 2017.09.29 需求接單改成不看treatment_type，只通知同個區域的醫生
        // $doctors_id = Doctor
        //     ::where('treatment_type', $member_request->treatment_type)
        //     ->get()
        //     ->pluck('users_id');
        $doctors = User
            ::with('doctor')
            ->whereHas('doctor')
            // ->whereIn('id', $doctors_id)
            ->where('city_id', $member->city_id)
            ->get();
        $devices_arn = $doctors->pluck('deviceToken.device_arn')
            ->filter(function ($arn) {
                return $arn !== null;
            });
        $message = '您有新的需求接單客戶，您可以馬上進入接單需求頁面查看';
        $data = [
            'event_type' => 'member_create_request',
            'data' => [
                'member' => $member,
                'member_request' => $member_request,
            ],
        ];
//        if (request()->getClientIp() !== '127.0.0.1') {
            $this->pushMultipleToSNS($devices_arn, $message, $data);
            $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
            $this->slackNotify('Receiver: '.json_encode($devices_arn));
//        }
    }
}
