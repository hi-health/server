<?php

namespace App\Listeners;

use App\Events\DoctorSendMessageEvent;
use App\Traits\AWSSNS;
use App\Traits\MemberUtility;
use App\Traits\SlackNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;
class SendDoctorSendMessageNotification implements ShouldQueue
{
    use AWSSNS, MemberUtility, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(DoctorSendMessageEvent $event)
    {
        $doctor = $event->doctor;
        $member = $event->member;
        $is_vip_member = $this->isVIPMember($member->id, $doctor->id);
        if ($is_vip_member) {
            $event_type = 'doctor_send_message_to_vip_member';
            $message = '您有新的諮詢訊息';
        } else {
            $event_type = 'doctor_send_message_to_member';
            // 2017 09 15
            $message  = "您的需求有人回應了，請點選本通知直接連結，或進入您好健康APP";
            $message .= " --> 取得健康服務 --> 諮詢師媒合";
            
            if ($doctor -> city)
            {
                $message .= " --> 聯繫 " . $doctor -> city;
            }
            if ($doctor -> district)
            {
                $message .= $doctor -> district;
            }
            $message .= "的 " . $doctor -> name . " 諮詢師";
        }
        $data = [
            'event_type' => $event_type,
            'data' => $doctor,
        ];
//        if (request()->getClientIp() !== '127.0.0.1') {
            if ($this->pushToSNS($member->device_arn, $message, $data)) {
                $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
                $this->slackNotify('Receiver: '.$member->device_arn);
            }
//        }
    }
}
