<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DoctorSendMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $doctor = null;

    public $member = null;

    public function __construct(User $doctor, User $member)
    {
        $this->doctor = $doctor;
        $this->member = $member;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
