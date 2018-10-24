<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Log;
use Carbon\Carbon;
class User extends Authenticatable
{
    use City,
        SoftDeletes;

    public $table = 'users';

    protected $fillable = [
        'name',
        'account',
        'password',
        'email',
        'login_type',
        'facebook_id',
        'facebook_token',
        'avatar',
        'male',
        'birthday',
        'city_id',
        'district_id',
        'mrs',
        'treatment_type',
        'treatment_kind',
        'onset_date',
        'onset_part',
        'online',
        'status',
        'online_at'
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'city',
        'district',
    ];

    protected $dates = [
        'online_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function service()
    {
        return $this->hasOne(Service::class, 'members_id', 'id')
            ->where('payment_status', 3)
            ->orderBy('id','DESC');
    }
    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'users_id', 'id');
    }
    public function manager()
    {
        return $this->hasOne(Manager::class, 'users_id', 'id');
    }
    
    public function deviceToken()
    {
        return $this->hasOne(UserDeviceToken::class, 'users_id', 'id')
            ->orderBy('created_at', 'DESC');
    }
    
    public function firstMessage()
    {
        return $this->hasOne(Message::class, 'doctors_id', 'id')
            ->where('source', 'doctor')
            ->whereNotNull('started_at')
            ->orderBy('started_at', 'DESC');
    }

    public function scopeWithMember($query, $id)
    {
        $query->where('id', $id)
            ->where('login_type', 1)
            ->where('status', 1);
    }

    public function scopeWithMembers($query, $id)
    {
        $query->whereIn('id', $id)
            ->where('login_type', 1)
            ->where('status', 1);
    }

    public function scopeWithDoctor($query, $id)
    {
        $query->with('doctor')
            ->where('id', $id)
            ->where('login_type', 2)
            ->where('status', 1);
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function getGenderAttribute()
    {
        Log::info($this->attributes['male']);
        return $this->attributes['male'] === 1 ? '男' : '女';
    }
    
    public function getDeviceArnAttribute()
    {
        $device_arn = null;
        if ($this->deviceToken) {
            $device_arn = $this->deviceToken->device_arn;
        }
        return $device_arn;
    }

    public function getTreatmentTypeAttribute()
    {
        if ($this->doctor) {
            return $this->doctor->treatment_type;
        }

        return null;
    }

    public function getTreatmentKindAttribute()
    {
        return 1; //@todo
    }

    public function getOnsetDateAttribute()
    {
        if ($this->attributes['onset_date'])
            return new Carbon($this->attributes['onset_date']);
        return null;
        // return '';// date('Y-m-d'); //@todo
    }

    public function getStatusTextAttribute()
    {
        $user_status = config('define.user_status');

        return array_get($user_status, $this->attributes['status']);
    }

    public function isMember()
    {
        return $this->login_type === '1';
    }

    public function isDoctor()
    {
        return $this->login_type === '2';
    }
}
