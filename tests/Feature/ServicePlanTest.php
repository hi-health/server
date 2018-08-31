<?php

namespace Tests\Feature;

use App\ServicePlan;
use App\ServicePlanDaily;
use App\ServicePlanVideo;
use Carbon\Carbon;
use Exception;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ServicePlanTest extends ApiTest
{
    protected $faker = null;

    protected $service_id = 1;

    protected $quantity = 5;

    protected $start_hour = 7;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }

    public function testCreateServicePlanSuccess()
    {
        ServicePlan::truncate();
        ServicePlanVideo::truncate();
        ServicePlanDaily::truncate();
        $response = $this->post('/api/services/'.$this->service_id.'/plans', [
            'plans' => $this->getTestCreatePlansData($this->quantity),
        ]);
        $response->assertStatus(200);
    }

    public function testCreateServicePlanValidationFailure()
    {
        $response = $this->post('/api/services/'.$this->service_id.'/plans');
        $response->assertStatus(422);
    }

    public function testCreateServicePlanWithoutServiceFailure()
    {
        $response = $this->post('/api/services/-1/plans', [
            'plans' => $this->getTestCreatePlansData($this->quantity),
        ]);
        $response->assertStatus(404);
    }

    public function testUpdateFirstServicePlanSuccess()
    {
        $response = $this->put('/api/services/'.$this->service_id.'/plans', [
            'plans' => [
                $this->getTestUpdatePlanData(1, $this->start_hour + 1, 1),
            ],
        ]);
        $response->assertStatus(200);
    }
    
    public function testDeleteServicePlanVideoSuccess()
    {
        $response = $this->delete('/api/services/'.$this->service_id.'/plans/1/videos', [
            'videos' => [
                $this->getTestDeletePlanVideoData(15)
            ],
        ]);
        $response->assertStatus(200);
    }
    
    public function testDeleteServicePlanSuccess()
    {
        $response = $this->delete('/api/services/'.$this->service_id.'/plans', [
            'plans' => [
                $this->getTestDeletePlanData($this->quantity)
            ],
        ]);
        $response->assertStatus(200);
    }

    public function testGetServicePlanByServiceIdSuccess()
    {
        $response = $this->get('/api/services/'.$this->service_id.'/plans');
        $response->assertStatus(200);
    }

    public function testGetServicePlanWithoutServiceIdFailure()
    {
        $response = $this->get('/api/services/-1/plans');
        $response->assertStatus(404);
    }

    public function testScoringServicePlanDailyByServiceIdSuccess()
    {
        $response = $this->get('/api/services/'.$this->service_id.'/plans');
        $plans = $response->json();
        $score = collect($plans)->map(function ($plan) {
            return [
                'id' => $plan['id'],
                'videos' => collect($plan['videos'])->map(function ($video) {
                    return [
                        'id' => $video['id'],
                        'score' => $this->faker->numberBetween(1, 3),
                    ];
                })->toArray(),
            ];
        })->toArray();
        $response = $this->post('/api/services/'.$this->service_id.'/plans/daily', [
            'date' => Carbon::now()->format('Y-m-d'),
            'plans' => $score,
        ]);
        $response = $this->post('/api/services/'.$this->service_id.'/plans/daily', [
            'date' => Carbon::now()->subDay()->format('Y-m-d'),
            'plans' => $score,
        ]);
        $response->assertStatus(200);
    }

    public function testScoringServicePlanDailyByServiceIdValidationFailure()
    {
        $response = $this->post('/api/services/'.$this->service_id.'/plans/daily', [
            'date' => date('Y-02-31'),
            'plans' => [
                [
                    'id' => 1,
                    'videos' => [
                        [
                            'id' => 1,
                            'score' => 0,
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function testScoringServicePlanDailyWithoutServiceIdFailure()
    {
        $response = $this->post('/api/services/-1/plans/daily', [
            'date' => date('Y-01-01'),
            'plans' => [
                [
                    'id' => 1,
                    'videos' => [
                        [
                            'id' => 1,
                            'score' => 1,
                        ],
                    ],
                ],
            ],
        ]);
        $response->assertStatus(404);
    }

    public function testGetServicePlanDailyByServiceIdSuccess()
    {
        $response = $this->get('/api/services/'.$this->service_id.'/plans/daily');
        $response->assertStatus(200);
    }

    public function testGetServicePlanDailyWithoutServiceIdFailure()
    {
        $response = $this->get('/api/services/-1/plans/daily');
        $response->assertStatus(404);
    }

    public function testGetServicePlanDailyByServiceIdWithDateSuccess()
    {
        $response = $this->get('/api/services/'.$this->service_id.'/plans/daily/'.date('Y-m-d'));
        $response->assertStatus(200);
    }

    public function testGetServicePlanDailyByServiceIdWithIncorrectDateFailure()
    {
        $response = $this->get('/api/services/'.$this->service_id.'/plans/daily/'.date('Y-02-31'));
        $response->assertStatus(200);
    }

    public function testGetServicePlanDailyWithoutServiceIdWithDateFailure()
    {
        $response = $this->get('/api/services/-1/plans/daily/'.date('Y-m-d'));
        $response->assertStatus(404);
    }

    public function testExportServicePlanSuccess()
    {
        $response = $this->post('/api/services/'.$this->service_id.'/plans/export', [
            'email' => 'tirme0812@gmail.com',
        ]);
        $response->assertStatus(200);
    }

    public function testExportServicePlanFailure()
    {
        $response = $this->post('/api/services/-1/plans/export', [
            'email' => 'tirme0812@gmail.com',
        ]);
        $response->assertStatus(404);
    }

    protected function getTestCreatePlansData($quantity = 5)
    {
        $hour = $this->start_hour;
        $data = [];
        for ($i = 1; $i <= $quantity; ++$i) {
            $data[] = $this->getTestCreatePlanData($hour, $i);
            $hour += 3;
        }

        return $data;
    }

    protected function getTestCreatePlanData($hour, $weight = 1)
    {
        return [
            'started_at' => date(
                sprintf('%02d', $hour).':00'
            ),
            'stopped_at' => date(
                sprintf('%02d', ($hour + 1)).':00'
            ),
            'videos' => $this->getTestCreateVideoFiles(
                $weight,
                $this->quantity - $weight + 1
            ),
        ];
    }
    
    protected function getTestUpdatePlanData($id, $hour, $weight = 1)
    {
        return [
            'id' => $id,
            'started_at' => date(
                sprintf('%02d', $hour).':00'
            ),
            'stopped_at' => date(
                sprintf('%02d', ($hour + 1)).':00'
            ),
            'videos' => [
                $this->getTestUpdateVideoFile(9)
            ],
        ];
    }
    
    protected function getTestDeletePlanData($id)
    {
        return [
            'id' => $id,
        ];
    }
    
    protected function getTestDeletePlanVideoData($id)
    {
        return [
            'id' => $id
        ];
    }

    protected function getTestCreateVideoFiles($weight, $quantity = 5, $attach_video = true)
    {
        $files = [];
        for ($i = 1; $i <= $quantity; ++$i) {
            $files[] = $this->getTestCreateVideoFile($weight, $i, $attach_video);
        }

        return $files;
    }

    protected function getTestCreateVideoFile($weight, $index, $attach_video = true)
    {
        if ($attach_video) {
            try {
                $new_file = 'video_'.$weight.'_'.$index.'.mp4';
                $test_file = 'video_test_'.$index.'.mp4';
                $res = Storage::disk('local')->copy($test_file, $new_file);
            } catch (Exception $exception) {
            }
            $video_path = storage_path('app/'.$new_file);

            return [
                'file' => new UploadedFile($video_path, $new_file, filesize($video_path), 'video/mp4', null, true),
                'description' => $this->faker->realText(50),
            ];
        }

        return [
            'description' => $this->faker->realText(50),
        ];
    }
    protected function getTestUpdateVideoFile($id)
    {
        return [
            'id' => $id,
            'description' => 'Update',
        ];
    }
}
