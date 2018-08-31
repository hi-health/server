<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DoctorOnlineEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $doctor = null;

    public function __construct(User $doctor)
    {
        $this->doctor = $doctor;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
