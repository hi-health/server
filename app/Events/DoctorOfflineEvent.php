<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DoctorOfflineEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $doctors = null;

    public function __construct(Collection $doctors)
    {
        $this->doctors = $doctors;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
