<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;

class MemberRequestDoctor extends Model
{
    use HiHealthDate;

    public $table = 'member_request_doctors';

    protected $fillable = [
        'member_requests_id',
        'doctors_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
