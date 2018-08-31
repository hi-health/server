<?php

namespace Tests\Feature;

use Faker\Factory as Faker;

class MessageTest extends ApiTest
{
    protected $faker = null;

    protected $member_request_id = 1;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }

    public function testSendMessageToMemberSuccess()
    {
        $response = $this->post('/api/messages', [
            'doctors_id' => $this->doctor_id,
            'members_id' => $this->member_id,
            'source' => 'doctor',
            'message' => $this->faker->realText(50),
        ]);
        $response->assertStatus(200);
    }

    public function testSendMessageToDoctorSuccess()
    {
        $response = $this->post('/api/messages', [
            'doctors_id' => $this->doctor_id,
            'members_id' => $this->member_id,
            'source' => 'member',
            'message' => $this->faker->realText(50),
        ]);
        $response->assertStatus(200);
    }

    public function testSendMessageToSystemSuccess()
    {
        $response = $this->post('/api/messages', [
            'doctors_id' => $this->doctor_id,
            'members_id' => $this->member_id,
            'source' => 'member',
            'message' => $this->faker->realText(50),
            'visible' => 0,
        ]);
        $response->assertStatus(200);
    }

    public function testSendMessageWithMemberRequestSuccess()
    {
        $response = $this->post('/api/messages', [
            'doctors_id' => $this->doctor_id,
            'members_id' => $this->member_id,
            'member_requests_id' => $this->member_request_id,
            'source' => 'member',
            'message' => $this->faker->realText(50),
        ]);
        $response->assertStatus(200);
    }

    public function testSaveMessageFailure()
    {
        $response = $this->post('/api/messages');
        $response->assertStatus(422);
    }

    public function testGetMessageChunkSuccess()
    {
        $response = $this->get('/api/messages/doctors/'.$this->doctor_id.'/members/'.$this->member_id, [
            'per_page' => 3,
        ]);
        $response->assertStatus(200);
        $chunk = $response->json();
        $response = $this->get('/api/messages/doctors/'.$this->doctor_id.'/members/'.$this->member_id, [
            'first_id' => $chunk['first_id'],
            'per_page' => 3,
        ]);
        $response->assertStatus(200);
        $chunk = $response->json();
        $response = $this->get('/api/messages/doctors/'.$this->doctor_id.'/members/'.$this->member_id, [
            'latest_id' => $chunk['latest_id'],
            'per_page' => 3,
        ]);
        $response->assertStatus(200);
        $response = $this->get('/api/messages/doctors/'.$this->doctor_id.'/members/'.$this->member_id, [
            'member_request_id' => $this->member_request_id,
            'per_page' => 3,
        ]);
        $response->assertStatus(200);
    }

    public function testGetMessageChunkFailure()
    {
        $response = $this->get('/api/messages/doctors/-1/members/-1', []);
        $response->assertStatus(404);
    }
}
