<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PointProduce extends Model
{
    public $table = 'pointproduce';

    protected $fillable = [
        'users_id',
        'pointconsume_id',
        'point',
        'service_plan_daily_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
