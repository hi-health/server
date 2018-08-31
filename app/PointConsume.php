<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PointConsume extends Model
{
    public $table = 'pointconsume';

    protected $fillable = [
        'users_id',
        'point',
        'users_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
