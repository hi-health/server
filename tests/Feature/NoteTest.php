<?php

namespace Tests\Feature;

use Faker\Factory as Faker;

class NoteTest extends ApiTest
{
    protected $faker = null;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }

    public function testSaveNoteSuccess()
    {
        $response = $this->post('/api/notes', [
            'doctors_id' => $this->doctor_id,
            'members_id' => $this->member_id,
            'note' => $this->faker->realText(50),
        ]);
        $response->assertStatus(200);
    }

    public function testSaveNoteFailure()
    {
        $response = $this->post('/api/notes');
        $response->assertStatus(422);
    }

    public function testGetNoteSuccess()
    {
        $response = $this->get('/api/notes/doctors/'.$this->doctor_id.'/members/'.$this->member_id.'/latest');
        $response->assertStatus(200);
    }

    public function testGetNoteFailure()
    {
        $response = $this->get('/api/notes/doctors/-1/members/-1/latest');
        $response->assertStatus(200);
    }
}
