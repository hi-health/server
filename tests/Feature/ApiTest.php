<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiTest extends TestCase
{
    protected $member_id = 4;
    protected $doctor_id = 3;
    public function testApi()
    {
        return $this->assertTrue(true);
    }
    public function get($uri, array $data = [], array $headers = [])
    {
        if (!empty($data)) {
            $uri .= '?'.http_build_query($data);
        }
        return parent::get($uri, $this->getHeaders($headers));
    }
    public function post($uri, array $data = [], array $headers = [])
    {
        return parent::post($uri, $data, $this->getHeaders($headers));
    }
    public function put($uri, array $data = [], array $headers = [])
    {
        return parent::put($uri, $data, $this->getHeaders($headers));
    }
    public function delete($uri, array $data = array(), array $headers = array()) {
        return parent::delete($uri, $data, $this->getHeaders($headers));
    }
    public function call($method, $uri, $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null) {
        return parent::call($method, $uri, $parameters, $cookies, $files, $this->getHeaders($server), $content);
    }
    protected function getHeaders(array $headers = [])
    {
        return array_merge([
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], $headers);
    }
}
