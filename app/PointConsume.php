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

    public function transaction()
    {
        return $this->hasOne(PointProduce::class, 'pointconsume_id', 'id')->with('user');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'users_id');
    }
}
