<?php

namespace App\Events;

use App\Service;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberServiceCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $service = null;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
