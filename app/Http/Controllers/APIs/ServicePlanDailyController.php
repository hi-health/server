<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Mail\ServicePlanExportedById;
use App\Service;
use App\ServicePlan;
use App\ServicePlanVideo;
use App\ServicePlanDaily;
use App\PointProduce;
use App\Traits\SlackNotify;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ServicePlanDailyController extends Controller
{
    use SlackNotify;

    public function getAllDate(Request $request, $service_id)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $result = $service->daily
            ->groupBy('scored_at')
            ->map(function ($daily, $scored_at) {
                $plans = $daily->unique('plan')
                    ->pluck('plan');

                return [
                    'date' => $scored_at,
                    'plans' => $plans->sortBy('started_at')
                        ->map(function ($plan) use ($daily) {
                            return [
                                'id' => $plan->id,
                                'started_at' => $plan->started_at,
                                'stopped_at' => $plan->stopped_at,
                                'videos' => $plan->videos
                                    ->sortBy('weight')
                                    ->map(function ($video) use ($daily) {
                                        return [
                                            'id' => $video->id,
                                            'video_url' => $video->video_url,
                                            'thumbnail_url' => $video->thumbnail_url,
                                            'description' => $video->description,
                                            'score' => max(0, $daily->where('service_plan_videos_id', $video->id)
                                                ->pluck('score')
                                                ->first()
                                            ),
                                        ];
                                    })->values(),
                            ];
                        })->values(),
                ];
            })->values();

        return response()->json($result);
    }

    public function getByDate(Request $request, $service_id, $date)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $daily = $service->daily()
            ->where('scored_at', $date)
            ->get();
        if ($daily->isEmpty()) {
            $result = $service->plans
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'started_at' => $plan->started_at,
                        'stopped_at' => $plan->stopped_at,
                        'videos' => $plan->videos
                            ->sortBy('weight')
                            ->map(function ($video) {
                                return [
                                    'id' => $video->id,
                                    'video_url' => $video->video_url,
                                    'thumbnail_url' => $video->thumbnail_url,
                                    'score' => null,
                                ];
                            })->values(),
                    ];
                });
        } else {
            $result = $daily->unique('plan')
                ->pluck('plan')
                ->sortBy('started_at')
                ->map(function ($plan) use ($daily) {
                    return [
                        'id' => $plan->id,
                        'started_at' => $plan->started_at,
                        'stopped_at' => $plan->stopped_at,
                        'videos' => $plan->videos
                            ->sortBy('weight')
                            ->map(function ($video) use ($daily) {
                                return [
                                    'id' => $video->id,
                                    'video_url' => $video->video_url,
                                    'thumbnail_url' => $video->thumbnail_url,
                                    'score' => $daily->where('service_plan_videos_id', $video->id)->pluck('score')->first(),
                                ];
                            })->values(),
                    ];
                })->values();
        }

        return response()->json($result);
    }
    public function updateOrCreate(Request $request, $service_id, $plan_id)
    {
        $this->validate($request, [
            'date' => ['required', 'date_format:Y-m-d'],
            'video' => ['required'],
            'video.id' => ['required','integer'],
            'video.test_data.start_at' => ['required','date_format:Y-m-d H:i:s'],
            'video.test_data.stop_at' => ['required','date_format:Y-m-d H:i:s'],
            'video.test_data.repeat_time' => ['required','integer'],
            'video.test_data.data' => ['required','array'],
        ]);
        $date = $request->input('date');
        $video = $request->input('video');

        $service = Service::where('id', $service_id)->first();
        if (!$service) {
            error_log('NOOOOO service');
            return response()->json(null, 404);
        }
        $service_plan = ServicePlan::where('id', $plan_id)->where('services_id', $service_id)->first();
        if (!$service_plan) {
            error_log('NOOOOO service plan');
            return response()->json(null, 404);
        }
        $service_plan_video = ServicePlanVideo::where('id', $video['id'])->where('service_plans_id', $plan_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json(null, 404);
        }

        $score = $this->claculateScore($plan_id,$video['test_data']);
        $service_plan_daily = ServicePlanDaily::updateOrCreate(
                                                [
                                                    'services_id' => $service->id,
                                                    'service_plans_id' => $plan_id,
                                                    'service_plan_videos_id' => $video['id'],
                                                    'scored_at' => $date,
                                                ], 
                                                [
                                                    'score' => $score,
                                                    'movement_test_data' => json_encode($video['test_data'])
                                                ]);
        // $point = $this->claculatePoint($score);
        // $PointProduce = PointProduce::updateOrCreate(
        //     [
        //         'service_plan_daily_id' => $service_plan_daily_id,
        //         'point' => $point,
        //         'users_id' => $
        //     ]
        // )


        return response($score,200);
    }
    /*
    public function updateOrCreate(Request $request, $service_id)
    {
        $this->validate($request, [
            'date' => ['required', 'date_format:Y-m-d'],
            'plans' => ['required', 'array', 'min:1'],
            'plans.*.id' => ['required'],
            'plans.*.videos' => ['required', 'array', 'min:1', 'max:5'],
            'plans.*.videos.*.id' => ['required'],
            'plans.*.videos.*.test_data.start_at' => ['required','date_format:Y-m-d H:i:s'],
            'plans.*.videos.*.test_data.stop_at' => ['required','date_format:Y-m-d H:i:s'],
            'plans.*.videos.*.test_data.repeat_time' => ['required','integer'],
            'plans.*.videos.*.test_data.data' => ['required','array'],
        ]);
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $date = $request->input('date');
        $plans = $request->input('plans');
        $service_plan_daily = collect($plans)->map(function ($plan) use ($service, $date) {
            return collect($plan['videos'])->map(function ($video) use ($service, $plan, $date) {
                $score = $this->claculateScore($plan['id'],$video['test_data']);
                return ServicePlanDaily::updateOrCreate([
                    'services_id' => $service->id,
                    'service_plans_id' => $plan['id'],
                    'service_plan_videos_id' => $video['id'],
                    'scored_at' => $date,
                ], [
                    'score' => $score,
                    'movement_test_data' => json_encode($video['test_data'])
                ]);
            });
        });

        return response()->json($service_plan_daily);
    }
    */

    public function export(Request $request, $service_id)
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
        ]);
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        if ($service->daily->isEmpty()) {
            return response()->json(null, 404);
        }
        $email = $request->input('email');
        try {
            Mail::to($email)->send(
                new ServicePlanExportedById($service)
            );
            $this->slackNotify('自我評分表輸出信件已寄出給:'.$email);
            $result = true;
        } catch (Exception $exception) {
            $result = false;
//            dump($exception->getMessage());
        }

        return response()->json($result);
    }

    public function claculateScore($servicePlan_id, $test_data)
    {
        return 94;
    }

    public function claculatePoint($score)
    {
        return round($score/20);
    }
}
