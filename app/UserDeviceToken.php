<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
    public $table = 'user_device_token';

    protected $fillable = [
        'device_arn',
        'device_token',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
