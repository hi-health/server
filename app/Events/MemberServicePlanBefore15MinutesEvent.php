<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberServicePlanBefore15MinutesEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $members = null;
    public $before_minutes = 0;

    public function __construct(Collection $members, $before_minutes)
    {
        $this->members = $members;
        $this->before_minutes = $before_minutes;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
