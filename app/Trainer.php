<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Trainer extends Model
{
    use HiHealthDate,
        SoftDeletes;

    public $table = 'trainers';

    protected $hidden = [
        'users_id',
    ];
    
    protected $fillable = [
        'name',
        'title',
        'experience',
        'paragraph',
        'due_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'due_at'
    ];
}
