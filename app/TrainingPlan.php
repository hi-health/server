<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingPlan extends Model
{
    use SoftDeletes;

    public $table = 'training_plans';

    protected $fillable = [
        'trainer_id',
        'name',
        'paragraph',
        'cover_img',
        'price'
    ];
}
