<?php

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class ServiceCollection extends Collection
{
    public function chargeAmount()
    {
        return $this->sum('charge_amount');
    }
}
