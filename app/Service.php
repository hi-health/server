<?php

namespace App;

use App\Collections\ServiceCollection;
use App\Traits\SettingUtility;
use App\Traits\HiHealthDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes,
        SettingUtility,
        HiHealthDate;

    public $table = 'services';

    protected $fillable = [
        'doctors_id',
        'members_id',
        'treatment_type',
        'charge_amount',
        'payment_method',
        'payment_status',
        'opened_at',
        'started_at',
        'stopped_at',
    ];

    protected $appends = [
        'service_minutes',
        'leave_days',
    ];

    protected $dates = [
        'opened_at',
        'paid_at',
        'started_at',
        'stopped_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function newCollection(array $models = [])
    {
        return new ServiceCollection($models);
    }

    public function member()
    {
        return $this->hasOne(User::class, 'id', 'members_id')
            ->where('login_type', 1);
    }

    public function doctor()
    {
        return $this->hasOne(User::class, 'id', 'doctors_id')
            ->where('login_type', 2)
            ->with('doctor');
    }

    public function memberMessage()
    {
        return $this->hasOne(Message::class, 'members_id', 'members_id')
            ->orderBy('created_at', 'DESC');
    }
    
    public function doctorMessage()
    {
        return $this->hasOne(Message::class, 'doctors_id', 'doctors_id')
            ->orderBy('created_at', 'DESC');
    }

    public function paymentHistory()
    {
        return $this->hasMany(PaymentHistory::class, 'services_id', 'id');
    }

    public function plans()
    {
        return $this->hasMany(ServicePlan::class, 'services_id', 'id')
            ->with('videos')
            ->orderBy('started_at', 'ASC');
//            ->orderBy('weight', 'ASC');
    }

    public function daily()
    {
        return $this->hasMany(ServicePlanDaily::class, 'services_id', 'id')
            ->with('plan', 'video')
            ->orderBy('scored_at', 'ASC');
    }

    public function canPay()
    {
        return !empty($this->members_id);
    }

    public function isPaid()
    {
        return $this->payment_status === '1';
    }

    public function getTreatmentTypeTextAttribute()
    {
        $treatment_types = config('define.treatment_types');

        return array_get($treatment_types, $this->attributes['treatment_type']);
    }

    public function getPaymentMethodTextAttribute()
    {
        $payment_methods = config('define.payment_methods');

        return array_get($payment_methods, $this->attributes['payment_method']);
    }

    public function getInvoiceStatusTextAttribute()
    {
        if ($this->payment_status != 1)
            return '---';

        if (strlen($this->invoice) == 0)
            return '未開發票';
        if (strlen($this->invoice) != 0)
            return '已開發票';

        return '---';
    }

    public function getPaymentStatusTextAttribute()
    {
        $payment_status = config('define.payment_status');

        return array_get($payment_status, $this->attributes['payment_status']);
    }

    public function getServiceMinutesAttribute()
    {
        if (!$this->started_at) {
            return 0;
        }
        $diffed_at = $this->stopped_at ? $this->stopped_at : Carbon::now();

        return max(0, $this->started_at->diffInMinutes($diffed_at));
    }

    public function getLeaveDaysAttribute()
    {
        /*
        if (!$this->stopped_at) {
            return 0;
        }
        */
        $sub_days = $this->treatment_type === '1' ? $this->getSetting('treatment_days_1') : $this->getSetting('treatment_days_2');
        $now = new Carbon();
        $leave_time = $now->subDays($sub_days);
        // $leave_days = $leave_time->diffInDays($this->paid_at, false);
        return $leave_time->diffInDays($this->paid_at, false);
    }

    public function getTreatmentTimeAttribute()
    {
        return $this->treatment_type === '1' ? $this->getSetting('treatment_time_1', 30) : $this->getSetting('treatment_time_2', 45);
    }

    public function getCurrentTreatmentTimeAttribute()
    {
        return $this->started_at->diffInMinutes(Carbon::now(), false);
//        return $this->started_at->diffInSeconds(Carbon::now(), false);
    }

    public function generateOrderNumber()
    {
        $prefix = config('app.env') === 'production' ? 'S' : 'TS2';
        $date = Carbon::now()->format('ymd');
        $service = self::where('order_number', 'LIKE', strtr('{prefix}{date}%', [
                '{prefix}' => $prefix,
                '{date}' => $date,
            ]))->orderBy('order_number', 'DESC')
            ->first();
        $increment = 1;
        if ($service) {
            $increment = substr($service->order_number, -5) + 1;
        }
        $this->attributes['order_number'] = strtr('{prefix}{date}{increment}', [
            '{prefix}' => $prefix,
            '{date}' => $date,
            '{increment}' => sprintf('%05d', $increment),
        ]);
    }
}
