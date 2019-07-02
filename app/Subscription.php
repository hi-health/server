<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes,
        HiHealthDate;

    public $table = 'subscription';

    protected $fillable = [
        'service',
        'plan',
    ];

    protected $date = [
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
