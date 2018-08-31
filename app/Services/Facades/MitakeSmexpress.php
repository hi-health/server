<?php

namespace App\Services\Facades;

use Illuminate\Support\Facades\Facade;

class MitakeSmexpress extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mitake_smexpress';
    }
}
