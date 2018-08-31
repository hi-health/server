<?php

namespace App;

use App\Collections\ServicePlanCollection;
use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePlan extends Model
{
    use SoftDeletes;

    public $table = 'service_plans';

    protected $fillable = [
        'services_id',
        'started_at',
        'stopped_at',
        'weight',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function newCollection(array $models = [])
    {
        return new ServicePlanCollection($models);
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'services_id');
    }

    public function videos()
    {
        return $this->hasMany(ServicePlanVideo::class, 'service_plans_id', 'id')
            ->with('score')
            ->orderBy('weight', 'ASC');
    }

    public function daily()
    {
        return $this->hasMany(ServicePlanDaily::class, 'service_plans_id', 'id');
    }
}
