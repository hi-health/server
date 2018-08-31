<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        'App\Events\DoctorOnlineEvent' => [
            'App\Listeners\SendDoctorOnlineNotification',
        ],
        'App\Events\DoctorOfflineEvent' => [
            'App\Listeners\SendDoctorOfflineNotification',
        ],
        'App\Events\MemberCreateRequestEvent' => [
            'App\Listeners\SendMemberCreateRequestNotification',
        ],
        'App\Events\DoctorSendMessageEvent' => [
            'App\Listeners\SendDoctorSendMessageNotification',
        ],
        'App\Events\MemberSendMessageEvent' => [
            'App\Listeners\SendMemberSendMessageNotification',
        ],
        'App\Events\MemberServiceCompletedEvent' => [
            'App\Listeners\SendMemberServiceCompletedNotification',
        ],
        'App\Events\MemberServiceExpiredBefore1DayEvent' => [
            'App\Listeners\SendMemberServiceExpiredBefore1DayNotification',
        ],
        'App\Events\MemberServicePlanBefore15MinutesEvent' => [
            'App\Listeners\SendMemberServicePlanBefore15MinutesNotification',
        ],
        
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
