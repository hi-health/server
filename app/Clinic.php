<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Log;
use Carbon\Carbon;

class Clinic extends Model
{
    public $table = 'clinic';

    protected $fillable = [
        'name',
        'web',
        'phone',
        'email',
        'location',
        'address',
        'contract',
    ];
}
