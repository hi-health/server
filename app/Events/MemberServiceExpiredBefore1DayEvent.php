<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberServiceExpiredBefore1DayEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $members = null;

    public function __construct(Collection $members)
    {
        $this->members = $members;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
