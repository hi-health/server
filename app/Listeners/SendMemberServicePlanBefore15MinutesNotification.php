<?php

namespace App\Listeners;

use App\Events\MemberServicePlanBefore15MinutesEvent;
use App\Traits\AWSSNS;
use App\Traits\SlackNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMemberServicePlanBefore15MinutesNotification implements ShouldQueue
{
    use AWSSNS, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(MemberServicePlanBefore15MinutesEvent $event)
    {
        $message = '您的課程即將於'.$event->before_minutes.'分鐘後開始';
        $event->members->each(function ($member) use ($message) {
            if (empty($member->deviceToken)) {
                return;
            }
            $data = [
                'event_type' => 'service_plan_before_at_15_minutes',
                'data' => $member,
            ];
            $arn = $member->deviceToken->device_arn;
//            if (request()->getClientIp() !== '127.0.0.1') {
                $this->pushToSNS($arn, $message, $data);
                $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
                $this->slackNotify('Receiver: '.$arn);
//            }
        });
    }
}
