<?php

namespace App\Services\Facades;

use Illuminate\Support\Facades\Facade;

class Pay2GoMPG extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pay2go_mpg';
    }
}
