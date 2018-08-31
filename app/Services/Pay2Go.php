<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;

abstract class Pay2Go
{
    protected $merchant_id;

    protected $hash_key;

    protected $hash_iv;

    public function __construct()
    {
        $this->merchant_id = config('services.pay2go.merchant_id');
        $this->hash_key = config('services.pay2go.hash_key');
        $this->hash_iv = config('services.pay2go.hash_iv');
    }

    protected function exception($message)
    {
        throw new Exception($message);
    }
}
