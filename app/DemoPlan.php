<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoPlan extends Model
{
    use SoftDeletes;

    public $table = 'demo_plans';

    protected $fillable = [
        'started_at',
        'stopped_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function videos()
    {
        return $this->hasMany(DemoPlanVideo::class, 'demo_plans_id', 'id');
            //->orderBy('weight', 'ASC');
    }

    // public function daily()
    // {
    //     return $this->hasMany(ServicePlanDaily::class, 'demo_plans_id', 'id');
    // }
}
