<?php

namespace App\Console\Commands;

use App\Events\DoctorOfflineEvent;
use App\Traits\SlackNotify;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;
class NotifyDoctorOffline extends Command
{
    use SlackNotify;

    protected $signature = 'doctor:notify_offline';

    protected $description = 'Notify doctor has been offline';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        return ;
        $doctors = User
            ::with('deviceToken')
            ->where('login_type', 2)
            ->where('online', true)
            ->where('online_at', '<', Carbon::now()->subHour(1))
            ->get();
        $updated = User
            ::whereIn('id', $doctors->pluck('id'))
            ->update([
                'online' => false
            ]);
        event(
            new DoctorOfflineEvent($doctors)
        );
    }
}
