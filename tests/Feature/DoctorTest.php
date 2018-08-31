<?php

namespace Tests\Feature;

use Faker\Factory as Faker;

class DoctorTest extends ApiTest
{
    protected $faker;

    protected $account = null;

    protected $password = '100200300';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }

    public function testGetDoctorByIdSuccess()
    {
        $response = $this->get('/api/doctors/'.$this->doctor_id);
        $response->assertStatus(200);
    }

    public function testGetDoctorByIdFailure()
    {
        $response = $this->get('/api/doctors/-1');
        $response->assertStatus(404);
    }

    public function testGetDoctorByNumberSuccess()
    {
        $doctor = $this->get('/api/doctors/'.$this->doctor_id)->json();
        $response = $this->get('/api/doctors/number/'.$doctor['doctor']['number']);
        $response->assertStatus(200);
    }

    public function testGetDoctorByNumberFailure()
    {
        $response = $this->get('/api/doctors/-1');
        $response->assertStatus(404);
    }

    public function testGetDoctorSummarySuccess()
    {
        $response = $this->get('/api/doctors/'.$this->doctor_id.'/summary');
        $response->assertStatus(200);
    }

    public function testGetDoctorSummaryFailure()
    {
        $response = $this->get('/api/doctors/-1/summary');
        $response->assertStatus(404);
    }

    public function testGetMembersSuccess()
    {
        $response = $this->get('/api/doctors/'.$this->doctor_id.'/services/members');
        $response->assertStatus(200);
    }

    public function testGetMembersFailure()
    {
        $response = $this->get('/api/doctors/-1/services/members');
        $response->assertStatus(404);
    }

    public function testGetNearByCollectionSuccess()
    {
        $response = $this->post('/api/doctors/nearby', [
            'longitude' => 121.5613108,
            'latitude' => 25.032782,
            'distance' => 100,
            'members_id' => 2,
        ]);
        $response->assertStatus(200);
    }

    public function testGetNearByCollectionFailure()
    {
        $response = $this->post('/api/doctors/nearby', [
            'distance' => 0,
        ]);
        $response->assertStatus(422);
    }

    public function testSearchByCollectionSuccess()
    {
        $response = $this->post('/api/doctors/search', [
            'keyword' => 'Mr',
            'city_id' => 1,
            'treatment_type' => 2,
            'members_id' => 2,
        ]);
//        $response->dump();
        $response->assertStatus(200);
    }

    public function testSearchCollectionFailure()
    {
        $response = $this->post('/api/doctors/search', []);
        $response->assertStatus(422);
    }

    public function testCreateDoctorSuccess()
    {
        $response = $this->post('/api/doctors', [
            'account' => $this->faker->numerify('09########'),
            'password' => $this->password,
            'password_confirmation' => $this->password,
            'name' => $this->faker->name,
            'male' => $this->faker->numberBetween(0, 1),
            'birthday' => $this->faker->date(),
            'city_id' => 1,
            'district_id' => 100,
            'status' => true,
            'number' => $this->faker->numerify('D#####'),
            'treatment_type' => $this->faker->numberBetween(1, 2),
            'title' => $this->faker->name,
            'experience_year' => $this->faker->numberBetween(1, 15),
            'experience' => implode(',', [
                $this->faker->name,
                $this->faker->name,
            ]),
            'specialty' => implode(',', [
                $this->faker->name,
                $this->faker->name,
            ]),
            'education' => implode(',', [
                $this->faker->name,
                $this->faker->name,
            ]),
            'license' => implode(',', [
                $this->faker->name,
                $this->faker->name,
            ]),
            'education_bonus' => $this->faker->numberBetween(1000, 10000),
            'longitude' => '121.'.$this->faker->numberBetween(1000001, 9999999),
            'latitude' => '25.'.$this->faker->numberBetween(1000001, 9999999),
        ]);
        $response->assertStatus(200);
    }
}

//$response->assertJsonStructure([
//    '*' => [
//        'id',
//        'name',
//        'title',
//        'city_id',
//        'city',
//        'experience_year',
//        'experience',
//        'specialty',
//        'education',
//        'license',
//        'education_bonus',
//        'longitude',
//        'latitude',
//        'distance',
//        'created_at',
//        'updated_at',
//        'deleted_at',
//    ],
//]);
