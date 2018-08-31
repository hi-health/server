<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ServicePlanDailyCollection extends Collection
{
    public function average($callback = null)
    {
        $average = 0;
        if ($this->count() > 0) {
            $average = $this->sum('score') / $this->count();
        }

        return $average;
    }
}
