<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\NotifyDoctorOffline::class,
        \App\Console\Commands\NotifyMemberServiceExpiredBefore1Day::class,
        \App\Console\Commands\NotifyMemberServicePlanBefore15Minutes::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('member:notify_service_expire')
            ->dailyAt('10:00');
        $schedule->command('member:notify_service_plan 15')
            ->everyMinute();
        $schedule->command('doctor:notify_offline')
            ->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
