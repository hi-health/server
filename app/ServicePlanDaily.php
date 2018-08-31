<?php

namespace App;

use App\Collections\ServicePlanDailyCollection;
use App\Traits\HiHealthDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePlanDaily extends Model
{
    use HiHealthDate,
        SoftDeletes;

    public $table = 'service_plan_daily';

    protected $fillable = [
        'services_id',
        'service_plans_id',
        'service_plan_videos_id',
        'movement_test_data',
        'score',
        'scored_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function newCollection(array $models = [])
    {
        return new ServicePlanDailyCollection($models);
    }

    public function plan()
    {
        return $this->hasOne(ServicePlan::class, 'id', 'service_plans_id')
            ->with('videos');
    }

    public function video()
    {
        return $this->hasOne(ServicePlanVideo::class, 'id', 'service_plan_videos_id');
    }
}
