<?php

namespace App\Listeners;

use App\Events\MemberServiceCompletedEvent;
use App\Traits\AWSSNS;
use App\Traits\SlackNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMemberServiceCompletedNotification implements ShouldQueue
{
    use AWSSNS, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(MemberServiceCompletedEvent $event)
    {
        $message = '服務完成';
        $service = $event->service;
        $member = $service->member;
        $device_token = $service->doctor->deviceToken;
        if (empty($device_token)) {
            return;
        }
        $data = [
            'event_type' => 'service_completed',
            'data' => $service,
        ];
        $arn = $device_token->device_arn;
//        if (request()->getClientIp() !== '127.0.0.1') {
            $this->pushToSNS($arn, $message, $data);
            $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
            $this->slackNotify('Receiver: '.$arn);
//        }
    }
}
