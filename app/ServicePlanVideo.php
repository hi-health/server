<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePlanVideo extends Model
{
    use HiHealthDate,
        SoftDeletes;

    public $table = 'service_plan_videos';

    protected $fillable = [
        'service_plans_id',
        'video',
        'thumbnail',
        'description',
        'weight',
        'movement_template_data',
        'repeat_time',
        'session'
    ];

    protected $appends = [
        'video_url',
        'thumbnail_url'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function score()
    {
        return $this->hasMany(ServicePlanDaily::class, 'service_plan_videos_id', 'id');
    }

    public function getVideoUrlAttribute()
    {
        if ($this->video) {
            return url($this->video);
        }

        return null;
    }
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return url($this->thumbnail);
        }

        return null;
    }
}
