<?php

namespace App\Console\Commands;

use App\Events\MemberServiceExpiredBefore1DayEvent;
use App\Service;
use App\Traits\SlackNotify;
use App\User;
use Illuminate\Console\Command;

class NotifyMemberServiceExpiredBefore1Day extends Command
{
    use SlackNotify;

    protected $signature = 'member:notify_service_expire';

    protected $description = 'Notify member\'s service has expired before 1 day';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $members = User
            ::where('login_type', 1)
            ->where('status', 1)
            ->get();
        $members_id = Service
            ::whereIn('members_id', $members->pluck('id'))
            ->where('payment_status', 3)
            ->get()
            ->groupBy('members_id')
            ->filter(function ($services) {
                return $services->max('leave_days') === 1;
            })->keys();
        $notify_members = User
            ::with('deviceToken')
            ->whereIn('id', $members_id)
            ->get();
        event(
            new MemberServiceExpiredBefore1DayEvent($notify_members)
        );
    }
}
