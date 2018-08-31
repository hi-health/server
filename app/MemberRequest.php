<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberRequest extends Model
{
    use City,
        HiHealthDate,
        SoftDeletes;

    public $table = 'member_requests';

    protected $fillable = [
        'members_id',
        'treatment_type',
        'treatment_kind',
        'onset_date',
        'onset_part',
        'city_id',
        'district_id',
        'longitude',
        'latitude',
        'created_at',
        'updated_at',
    ];

    protected $visible = [
        'id',
        'members_id',
        'treatment_type',
        'treatment_kind',
        'city_id',
        'district_id',
        'city',
        'district',
        'longitude',
        'latitude',
        'onset_date',
        'onset_part',
        'created_at',
        'updated_at',
        'deleted_at',
        'member',
    ];

    protected $appends = [
        'city',
        'district',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'members_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'requests_id', 'id')
            ->with('member', 'doctor');
    }
}
