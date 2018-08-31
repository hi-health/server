<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberSendMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $member = null;

    public $doctor = null;

    public function __construct(User $member, User $doctor)
    {
        $this->member = $member;
        $this->doctor = $doctor;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
