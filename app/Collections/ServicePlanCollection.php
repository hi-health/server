<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ServicePlanCollection extends Collection
{
    public function videosCount()
    {
        return $this->sum(function($item) {
            return $item->videos->count();
        });
    }
}
