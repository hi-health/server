<?php

namespace App\Listeners;

use App\Events\DoctorOnlineEvent;
use App\MemberRequest;
use App\Traits\AWSSNS;
use App\Traits\SlackNotify;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDoctorOnlineNotification implements ShouldQueue
{
    use AWSSNS, SlackNotify, InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(DoctorOnlineEvent $event)
    {
        $doctor = $event->doctor;
        $member_requests = MemberRequest
            ::with(['member' => function ($query) {
                $query->with('deviceToken');
            }])
            ->where('treatment_type', $doctor->treatment_type)
            ->get();
        $devices_arn = $member_requests
            ->unique('members_id')
            ->pluck('member.deviceToken.device_arn')
            ->filter(function ($arn) {
                return $arn !== null;
            });
        $message = '符合您需求的諮詢師已上線，您可以馬上查看諮詢師頁面';
        $data = [
            'event_type' => 'doctor_online',
            'data' => $doctor,
        ];
//        if (request()->getClientIp() !== '127.0.0.1') {
            $this->pushMultipleToSNS($devices_arn, $message, $data);
            $this->slackNotify('Push to AWS SNS, Message: '.$message.' extra_data: '.json_encode($data));
            $this->slackNotify('Receiver: '.json_encode($devices_arn));
//        }
    }
}
