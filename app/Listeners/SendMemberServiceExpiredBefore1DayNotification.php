<?php

namespace App\Listeners;

use App\Events\MemberServiceExpiredBefore1DayEvent;
use App\Traits\AWSSNS;
use App\Traits\SlackNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMemberServiceExpiredBefore1DayNotification implements ShouldQueue
{
    use AWSSNS, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(MemberServiceExpiredBefore1DayEvent $event)
    {
        $message = '您的服務即將於一天內過期';
        $event->members->each(function ($member) use ($message) {
            if (empty($member->deviceToken)) {
                return;
            }
            $data = [
                'event_type' => 'service_expire_at_one_day',
                'data' => $member,
            ];
            $arn = $member->deviceToken->device_arn;
            if (request()->getClientIp() !== '127.0.0.1') {
                $this->pushToSNS($arn, $message, $data);
                $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
                $this->slackNotify('Receiver: '.$arn);
            }
        });
    }
}
