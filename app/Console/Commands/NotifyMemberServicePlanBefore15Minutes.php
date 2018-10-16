<?php

namespace App\Console\Commands;

use App\Events\MemberServicePlanBefore15MinutesEvent;
use App\Service;
use App\ServicePlan;
use App\Traits\SlackNotify;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class NotifyMemberServicePlanBefore15Minutes extends Command
{
    use SlackNotify;

    protected $signature = 'member:notify_service_plan {before_minutes}';

    protected $description = 'Notify member plan will start';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $before_minutes = $this->argument('before_minutes');
        Log::alert('into NotifyMemberServicePlanBefore15Minutes command');
        $before_time = Carbon::now()->addMinutes($before_minutes)->format('H:i');
//        $before_time = '08:00';
        $service_plans = ServicePlan
            ::where('started_at', $before_time)
            ->get();
        $services = Service
            ::whereIn('id', $service_plans->pluck('services_id'))
            ->get();
        $users = User
            ::with('deviceToken')
            ->whereIn('id', $services->pluck('members_id'))
            ->get();

        event(
            new MemberServicePlanBefore15MinutesEvent($users, $before_minutes)
        );
    }
}
