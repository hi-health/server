<?php

namespace Tests\Feature;

use App\Service;
use Faker\Factory as Faker;

class ServiceTest extends ApiTest
{
    protected $faker = null;

    protected $service_id = 1;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }

    public function testGetServiceByIdSuccess()
    {
        $response = $this->get('/api/services/'.$this->service_id);
        $response->assertStatus(200);
    }

    public function testGetServiceByIdFailure()
    {
        $response = $this->get('/api/services/-1');
        $response->assertStatus(404);
    }

    public function testGetServiceInvoiceByIdSuccess()
    {
        $response = $this->get('/api/services/'.$this->service_id.'/invoice');
        $response->assertStatus(200);
    }

    public function testGetServiceInvoiceByIdFailure()
    {
        $response = $this->get('/api/services/-1/invoice');
        $response->assertStatus(404);
    }

    public function testCreateServiceSuccess()
    {
        $response = $this->post('/api/services', [
            'doctors_id' => $this->doctor_id,
            'charge_amount' => $this->faker->numberBetween(1000, 2000),
            'treatment_type' => $this->faker->numberBetween(1, 2),
        ]);
        $response->assertStatus(200);
    }

    public function testCreateServiceFailure()
    {
        $response = $this->post('/api/services', []);
        $response->assertStatus(422);
    }

    public function testSetServicePaymentSuccess()
    {
        $service = Service
            ::orderBy('created_at', 'DESC')
            ->take(1)
            ->first();
        $response = $this->post('/api/services/'.$service->id.'/payment', [
            'members_id' => $this->member_id,
            'payment_method' => $this->faker->numberBetween(1, 2),
        ]);
        $response->assertStatus(200);
    }

    public function testSetServicePaymentFailure()
    {
        $response = $this->post('/api/services/-1/payment', [
            'members_id' => $this->member_id,
            'payment_method' => $this->faker->numberBetween(1, 2),
        ]);
        $response->assertStatus(404);
    }

    public function testSetServicePaymentValidationFailure()
    {
        $service = Service
            ::orderBy('created_at', 'DESC')
            ->take(1)
            ->first();
        $response = $this->post('/api/services/'.$service->id.'/payment', []);
        $response->assertStatus(422);
    }

    public function testSetServiceTreatmentSuccess()
    {
        $service = Service
            ::orderBy('created_at', 'DESC')
            ->take(1)
            ->first();
        $response = $this->post('/api/services/'.$service->id.'/treatment', [
            'treatment_type' => $this->faker->numberBetween(1, 2),
        ]);
        $response->assertStatus(200);
    }

    public function testSetServiceTreatmentFailure()
    {
        $response = $this->post('/api/services/-1/treatment', [
            'treatment_type' => $this->faker->numberBetween(1, 2),
        ]);
        $response->assertStatus(404);
    }

    public function testSetServiceTreatmentValidationFailure()
    {
        $service = Service
            ::orderBy('created_at', 'DESC')
            ->take(1)
            ->first();
        $response = $this->post('/api/services/'.$service->id.'/treatment', []);
        $response->assertStatus(422);
    }

    public function testStartServiceSuccess()
    {
        $service = Service
            ::orderBy('created_at', 'DESC')
            ->take(1)
            ->first();
        $response = $this->post('/api/services/'.$service->id.'/start');
        $response->assertStatus(200);
    }

    public function testStartServiceFailure()
    {
        $response = $this->post('/api/services/-1/start');
        $response->assertStatus(404);
    }

    public function testStopServiceSuccess()
    {
        $service = Service
            ::orderBy('created_at', 'DESC')
            ->take(1)
            ->first();
        $response = $this->post('/api/services/'.$service->id.'/stop');
        $response->assertStatus(200);
    }

    public function testStopServiceFailure()
    {
        $response = $this->post('/api/services/-1/stop');
        $response->assertStatus(404);
    }

    public function testGetServiceHistoryByDoctorSuccess()
    {
        $response = $this->get('/api/services/histories/doctors/'.$this->doctor_id, [
            'per_page' => 20,
        ]);
        $response->assertStatus(200);
    }

    public function testGetServiceHistoryByDoctorFailure()
    {
        $response = $this->get('/api/services/histories/doctors/-1');
        $response->assertStatus(404);
    }
    
    public function testGetServiceHistoryByMemberSuccess()
    {
        $response = $this->get('/api/services/histories/members/'.$this->member_id, [
            'per_page' => 20,
        ]);
        $response->assertStatus(200);
    }

    public function testGetServiceHistoryByMemberFailure()
    {
        $response = $this->get('/api/services/histories/members/-1');
        $response->assertStatus(404);
    }

    public function testExportServiceSuccess()
    {
        $response = $this->post('/api/services/'.$this->service_id.'/export', [
            'email' => 'tirme0812@gmail.com',
        ]);
        $response->assertStatus(200);
    }

    public function testExportServiceFailure()
    {
        $response = $this->post('/api/services/-1/export', [
            'email' => 'tirme0812@gmail.com',
        ]);
        $response->assertStatus(404);
    }
    
    public function testExportServicesByDoctorSuccess()
    {
        $response = $this->post('/api/services/export/doctors/'.$this->doctor_id, [
            'email' => 'tirme0812@gmail.com',
        ]);
        $response->assertStatus(200);
    }

    public function testExportServicesByDoctorFailure()
    {
        $response = $this->post('/api/services/export/doctors/-1', [
            'email' => 'tirme0812@gmail.com',
        ]);
        $response->assertStatus(404);
    }

    public function testExportServiceValidationFailure()
    {
        $response = $this->post('/api/services/'.$this->service_id.'/export');
        $response->assertStatus(422);
    }
}
