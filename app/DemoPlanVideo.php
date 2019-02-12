<?php

namespace App;

use App\Traits\HiHealthDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoPlanVideo extends Model
{
    use HiHealthDate,
        SoftDeletes;

    public $table = 'demo_plan_videos';

    protected $fillable = [
        'demo_plans_id',
        'video',
        'thumbnail',
        'description',
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

    // public function score()
    // {
    //     return $this->hasMany(ServicePlanDaily::class, 'demo_plan_videos_id', 'id');
    // }

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
