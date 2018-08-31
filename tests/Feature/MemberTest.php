<?php

namespace Tests\Feature;

use Exception;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MemberTest extends ApiTest
{

    public function testGetMemberByIdSuccess()
    {
        $response = $this->get('/api/members/'.$this->member_id);
        $response->assertStatus(200);
    }

    public function testGetMemberByIdFailure()
    {
        $response = $this->get('/api/members/-1');
        $response->assertStatus(404);
    }

    public function testGetMemberSummarySuccess()
    {
        $response = $this->get('/api/members/'.$this->member_id.'/summary');
        $response->assertStatus(200);
    }

    public function testGetMemberSummaryFailure()
    {
        $response = $this->get('/api/members/-1/summary');
        $response->assertStatus(404);
    }
    public function testGetMemberRequestsSuccess()
    {
        $response = $this->get('/api/members/'.$this->member_id.'/requests');
        $response->assertStatus(200);
        $response = $this->get('/api/requests/members/'.$this->member_id);
        $response->assertStatus(200);
    }
    public function testGetMemberRequestsFailure()
    {
        $response = $this->get('/api/members/-1/requests');
        $response->assertStatus(404);
        $response = $this->get('/api/requests/members/-1');
        $response->assertStatus(404);
    }
}
