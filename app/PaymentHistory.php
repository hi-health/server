<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HiHealthDate;

    public $table = 'payment_histories';

    protected $fillable = [
        'services_id',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
