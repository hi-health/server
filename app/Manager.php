<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manager extends Model
{
    public $table = 'managers';

    protected $fillable = [
        'bank_account',
        'phone',
    ];

    protected $hidden = [
        'users_id',
    ];

	protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    //
}
