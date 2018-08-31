<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HiHealthDate;

    public $table = 'notes';

    protected $fillable = [
        'doctors_id',
        'members_id',
        'note',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function member()
    {
        return $this->hasOne(User::class, 'id', 'members_id');
    }

    public function doctor()
    {
        return $this->hasOne(User::class, 'id', 'doctors_id');
    }
}
