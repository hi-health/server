<?php

namespace App\Listeners;

use App\Events\MemberSendMessageEvent;
use App\Traits\AWSSNS;
use App\Traits\MemberUtility;
use App\Traits\SlackNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMemberSendMessageNotification implements ShouldQueue
{
    use AWSSNS, MemberUtility, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(MemberSendMessageEvent $event)
    {
        $member = $event->member;
        $doctor = $event->doctor;
        $is_vip_member = $this->isVIPMember($member->id, $doctor->id);
        if ($is_vip_member) {
            $event_type = 'vip_member_send_message_to_doctor';
            $message = '您有新的諮詢訊息';
        } else {
            $event_type = 'member_send_message_to_doctor';
            $message = '您有新的聯繫訊息';
        }
        $data = [
            'event_type' => $event_type,
            'data' => $member,
        ];
//        if (request()->getClientIp() !== '127.0.0.1') {
            if ($this->pushToSNS($doctor->device_arn, $message, $data)) {
                $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
                $this->slackNotify('Receiver: '.$doctor->device_arn);
            }
//        }
    }
}
