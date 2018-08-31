<?php

namespace App\Events;

use App\MemberRequest;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberCreateRequestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $member = null;

    public $member_request = null;

    public function __construct(User $member, MemberRequest $member_request)
    {
        $this->member = $member;
        $this->member_request = $member_request;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
