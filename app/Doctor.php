<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
/**
 * Reference URL
 * https://developers.google.com/maps/articles/phpsqlsearch_v3?csw=1.
 *
 * 25.0910446,121.6669354
 *
 * SELECT
 id,
 ( 6371 * acos( cos( radians(25.0910446) ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(121.6669354) ) + sin( radians(25.0910446) ) * sin( radians( `latitude` ) ) ) ) AS distance
 */
class Doctor extends Model
{
    use City,
        HiHealthDate,
        SoftDeletes;

    public $table = 'doctors';

    protected $fillable = [
        'number',
        'title',
        'treatment_type',
        'experience_year',
        'experience',
        'specialty',
        'education',
        'license',
        'education_bonus',
        'longitude',
        'latitude',
        'due_at',
    ];

    protected $hidden = [
        'users_id',
    ];

    protected $casts = [
        'experience' => 'json',
        'specialty' => 'json',
        'education' => 'json',
        'license' => 'json',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'due_at'
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'doctors_id', 'users_id');
    }

    public function requests()
    {
        return $this->hasMany(MemberRequest::class, 'doctors_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'users_id')
            ->with('doctor');
    }

    public function scopeJoinUser($query, $id, $key = 'id')
    {
        $query->selectRaw($this->getUserFields().', doctors.*')
            ->join('users', 'users.id', 'doctors.users_id')
            ->where('doctors.'.($key === 'id' ? 'id' : 'number'), $id);
    }

    public function scopeJoinUsers($query)
    {
        $query->selectRaw($this->getUserFields().', doctors.*')
            ->join('users', 'users.id', 'doctors.users_id');
    }

    public function getIsValidAttribute($value)
    {
        if (!$this -> due_at) return false;

        $due_date = $this -> due_at -> copy() -> endOfDay();
        if ($due_date -> gt(Carbon::now()))
        {
            return true;
        }

        return false;
    }

    public function setExperienceAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['experience'] = json_encode(
                explode(',', $value)
            );
        }
    }

    public function setSpecialtyAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['specialty'] = json_encode(
                explode(',', $value)
            );
        }
    }

    public function setEducationAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['education'] = json_encode(
                explode(',', $value)
            );
        }
    }

    public function setLicenseAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['license'] = json_encode(
                explode(',', $value)
            );
        }
    }

    protected function getUserFields()
    {
        return collect([
            'avatar',
            'male',
            'birthday',
            'city_id',
            'district_id',
            'online',
        ])->map(function ($item) {
            return 'users.'.$item;
        })->implode(',');
    }
}
