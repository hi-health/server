<?php

namespace Tests\Feature;

use Faker\Factory as Faker;

class Pay2GoTest extends ApiTest
{
    protected $faker = null;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }
}
