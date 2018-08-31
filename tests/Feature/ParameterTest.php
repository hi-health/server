<?php

namespace Tests\Feature;

class ParameterTest extends ApiTest
{
    public function testGetCitiesSuccess()
    {
        $response = $this->get('/api/parameters/cities');
        $response->assertStatus(200);
    }
}
