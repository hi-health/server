<?php

namespace Tests\Feature;

use Faker\Factory as Faker;

class MemberRequestTest extends ApiTest
{
    protected $faker = null;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }

    public function testAddRequestSuccess()
    {
        $response = $this->post('/api/requests/members/'.$this->member_id, [
            'treatment_type' => $this->faker->numberBetween(1, 2),
            'treatment_kind' => $this->faker->numberBetween(1, 4),
            'onset_date' => date('Y-').$this->faker->date('m-d'),
            'onset_part' => $this->faker->numberBetween(1, 5),
            'city_id' => 1,
            'district_id' => 100,
//            'longitude' => '121.'.$this->faker->numberBetween(1000001, 9999999),
//            'latitude' => '25.'.$this->faker->numberBetween(1000001, 9999999),
        ]);
        $response->assertStatus(200);
    }

    public function testAddRequestWithoutMemberFailure()
    {
        $response = $this->post('/api/requests/members/-1', [
            'treatment_type' => $this->faker->numberBetween(1, 2),
            'treatment_kind' => $this->faker->numberBetween(1, 4),
            'onset_date' => date('Y-').$this->faker->date('m-d'),
            'onset_part' => $this->faker->numberBetween(1, 5),
            'city_id' => 1,
            'district_id' => 100,
        ]);
        $response->assertStatus(404);
    }

    public function testAddRequestValidationFailure()
    {
        $response = $this->post('/api/requests/members/'.$this->member_id, [
            'treatment_type' => 0,
            'treatment_kind' => 0,
            'onset_date' => 0,
            'onset_part' => 0,
            'city_id' => 0,
            'district_id' => 0,
        ]);
        $response->assertStatus(422);
    }

    public function testGetRequestsSuccess()
    {
        $response = $this->get('/api/requests');
        $response->assertStatus(200);
    }

    public function testGetRequestsWithCitySuccess()
    {
        $response = $this->get('/api/requests', [
            'treatment_type' => 1,
            'city_id' => 1,
            'district_id' => 100,
        ]);
        $response->assertStatus(200);
    }

    public function testGetRequestsMemberByDoctorSuccess()
    {
        $response = $this->get('/api/doctors/'.$this->doctor_id.'/requests/members');
        $response->assertStatus(200);
    }

    public function testGetRequestsMemberByDoctorFailure()
    {
        $doctor_id = -1;
        $response = $this->get('/api/doctors/'.$doctor_id.'/requests/members');
        $response->assertStatus(404);
    }

    public function testGetRequestsByMemberSuccess()
    {
        $response = $this->get('/api/requests/members/'.$this->member_id);
        $response->assertStatus(200);
    }

    public function testGetRequestsByMemberFailure()
    {
        $response = $this->get('/api/requests/members/-1');
        $response->assertStatus(404);
    }
}
