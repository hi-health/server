<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HiHealthDate;

    public $table = 'messages';

    protected $fillable = [
        'doctors_id',
        'members_id',
        'member_requests_id',
        'message',
        'source',
        'visible',
        'member_readed_at',
        'doctor_readed_at',
        'started_at',
    ];
    
    protected $dates = [
        'member_readed_at',
        'doctor_readed_at',
        'started_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    const EXPIRED_START_MINUTES = 10;
    const EXPIRED_LEAVE_MINUTES = 40;

    public function member()
    {
        return $this->hasOne(User::class, 'id', 'members_id');
    }

    public function doctor()
    {
        return $this->hasOne(User::class, 'id', 'doctors_id');
    }
    
    public function memberRequest()
    {
        return $this->hasOne(MemberRequest::class, 'id', 'member_request_id');
    }

}
