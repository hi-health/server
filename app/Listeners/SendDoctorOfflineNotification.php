<?php

namespace App\Listeners;

use App\Events\DoctorOfflineEvent;
use App\Traits\AWSSNS;
use App\Traits\SlackNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDoctorOfflineNotification implements ShouldQueue
{
    use AWSSNS, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(DoctorOfflineEvent $event)
    {
        $message = '上線超過一小時，已將您設為下線';
        $event->doctors->each(function ($doctor) use ($message) {
            if (empty($doctor->deviceToken)) {
                return;
            }
            $data = [
                'event_type' => 'doctor_offline',
                'data' => $doctor,
            ];
            $arn = $doctor->deviceToken->device_arn;
            if (request()->getClientIp() !== '127.0.0.1') {
                $this->pushToSNS($arn, $message, $data);
                $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
                $this->slackNotify('Receiver: '.$arn);
            }
        });
    }
}
