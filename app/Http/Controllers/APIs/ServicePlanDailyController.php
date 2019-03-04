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
use App\Traits\MemberUtility;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Log;
use App\AI\RepeatMultiDirectionAI;
use App\AI\RepeatMultiDirectionAIv1;
use App\AI\RepeatMultiDirectionAIv3;
use App\AI\RepeatMultiDirectionAIv3_1;

class ServicePlanDailyController extends Controller
{
    use MemberUtility,
        SlackNotify;

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

                                        $ai_score = json_decode($daily->where('service_plan_videos_id', $video->id)
                                                ->pluck('score')
                                                ->first(), true);

                                        $reason = json_decode($daily->where('service_plan_videos_id', $video->id)
                                                ->pluck('reason')
                                                ->first(), true); 

                                        $ai_score = is_array($ai_score) ? $ai_score : []; 

                                        return [
                                            'id' => $video->id,
                                            'video_url' => $video->video_url,
                                            'thumbnail_url' => $video->thumbnail_url,
                                            'description' => $video->description,
                                            'score' => max(0, $daily->where('service_plan_videos_id', $video->id)
                                                ->pluck('score')
                                                ->first()
                                            ),
                                            'ai_score'     =>  $ai_score,
                                            'ai_score_avg' =>  count($ai_score) > 0 ? round(array_sum($ai_score)/count($ai_score)) : 0,
                                            'reason' => $reason
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
                                $ai_score = json_decode($daily->where('service_plan_videos_id', $video->id) ->pluck('score')->first(), true);
                                $reason = json_decode($daily->where('service_plan_videos_id', $video->id)->pluck('reason')->first(), true);

                                $ai_score = is_array($ai_score) ? $ai_score : []; 

                                return [
                                    'id' => $video->id,
                                    'video_url' => $video->video_url,
                                    'thumbnail_url' => $video->thumbnail_url,
                                    'score' => $daily->where('service_plan_videos_id', $video->id)->pluck('score')->first(),
                                    'ai_score'     =>  $ai_score,
                                    'ai_score_avg' =>  count($ai_score) > 0 ? array_sum($ai_score)/count($ai_score) : 0,
                                    'reason' => $reason
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
            'video.test_data.data' => ['required','array'],
        ]);
        $date = $request->input('date');
        $video = $request->input('video');

        $service = Service::where('id', $service_id)->first();
        if (!$service) {
            error_log('NOOOOO service');
            return response()->json('1', 404);
        }
        if(!$this->isVIPMember($service->members_id, $service->doctors_id))  {
            return response()->json('invalid member', 404);
        }
        $service_plan = ServicePlan::where('id', $plan_id)->where('services_id', $service_id)->first();
        if (!$service_plan) {
            error_log('NOOOOO service plan');
            return response()->json('2', 404);
        }
        $service_plan_video = ServicePlanVideo::where('id', $video['id'])->where('service_plans_id', $plan_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json('3', 404);
        }
       //try{
            $service_plan_daily = ServicePlanDaily::updateOrCreate(
                                                    [
                                                        'services_id' => $service->id,
                                                        'service_plans_id' => $plan_id,
                                                        'service_plan_videos_id' => $video['id'],
                                                        'scored_at' => $date,
                                                    ], 
                                                    [
                                                        'movement_test_data' => json_encode($video['test_data'])
                                                    ]);
            $tmp = $this->calculateScore($plan_id,$video['id'],$video['test_data']);
            $score = $tmp['score'];
            $reason = $tmp['reason'];
            $service_plan_daily = ServicePlanDaily::updateOrCreate(
                                                    [
                                                        'services_id' => $service->id,
                                                        'service_plans_id' => $plan_id,
                                                        'service_plan_videos_id' => $video['id'],
                                                        'scored_at' => $date,
                                                    ], 
                                                    [
                                                        'score' => json_encode($score),
                                                        'reason' => json_encode($reason),
                                                        'movement_test_data' => json_encode($video['test_data'])
                                                    ]);

            $point = $this->calculatePoint($service_id, $plan_id, $service_plan_daily->id , $score);
            $users_id = Service::where('id', $service_id)->first()->member->id;
            $PointProduce = PointProduce::updateOrCreate(
                [
                    'service_plan_daily_id' => $service_plan_daily->id,
                    'point' => $point,
                    'users_id' => $users_id,
                ]);

            //return response($score,200);
            $arr = [];
            foreach ($score as $key => $value) {
                if($value<0){
                    $score[$key] = 0;
                }
            }
            return response()->json(["ai_score"=>$score,"reason"=>$reason]);
        //}
        //catch(Exception $exception){
        //    return response()->json($exception, 500);
        //}
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
                $score = $this->calculateScore($plan['id'],$video['test_data']);
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
            return $exception;
//            dump($exception->getMessage());
        }

        return response()->json($result);
    }

    public function calculateScore($servicePlan_id,$servicePlanVideo_id, $test_data)
    {   
        
        $service_plan_video = ServicePlanVideo::where('id', $servicePlanVideo_id)->where('service_plans_id', $servicePlan_id)->first();
        $template = json_decode($service_plan_video->movement_template_data);
        $test = $test_data['data'];
        $param = [
            'session' => $service_plan_video->session,
            'repeat_time' => $service_plan_video->repeat_time,
            'major_threshold' => 1.5,
            'error_threshold' => 0.4,
            'point_threshold' => 5
        ];

        Log::debug('AI optimization: '.strval($servicePlanVideo_id));
        $ai = new RepeatMultiDirectionAIv1($template, $test, $param);
        $tmp = $ai->calScore();
        return $tmp;
    }

    public function calculatePoint($service_id, $plan_id, $daily_id, $score)
    {
        $Service_charge = Service::where('id',$service_id)->first()->charge_amount;

        if(Service::where('id',$service_id)->first()->treatment_type=1) {
            $Service_day = 15;
        } else {
            $Service_day = 30;
        }  

        $perday_point_given = $Service_charge/$Service_day *0.15;

        $total_daily_count = 0;
        $Service_Plan = ServicePlan::where('services_id',$service_id)->get();
        foreach ($Service_Plan as $service_plan) {
            $Service_Plan_Video = ServicePlanVideo::where('service_plans_id', $service_plan->id)->count();
            $total_daily_count += $Service_Plan_Video;
        }
        $Service_Plan_Daily = ServicePlanDaily::where('id', $daily_id)->first();
        $session = $Service_Plan_Daily->video->session;
        if($total_daily_count != 0){
            $per_daily_point_most = $perday_point_given/$total_daily_count;
        } else {
            return 'false';
        }
        $per_daily_session_finished = count($score);

        $per_daily_point_get = $per_daily_session_finished/$session * $per_daily_point_most;

        return $per_daily_point_get;
    }

    public function AIDevelop(Request $request, $video_id, $daily_id){
        $service_plan_video = ServicePlanVideo::where('id', $video_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json('3', 404);
        }

        $service_plan_daily = ServicePlanDaily::where('id', $daily_id)->first();
        if (!$service_plan_daily) {
            error_log('NOOOOO service plan video');
            return response()->json('3', 404);
        }


        $template = json_decode($service_plan_video->movement_template_data);
        $test = json_decode($service_plan_daily->movement_test_data)->data;
        $param = [
            'session' => $service_plan_video->session,
            'repeat_time' => $service_plan_video->repeat_time,
            'major_threshold' => 1.5,
            'error_threshold' => 0.4,
            'point_threshold' => 5
        ];

        $ai = new RepeatMultiDirectionAIv1($template, $test, $param);
        $feature = $ai->printFeature();

        return response()->json($feature);

    }

    
    // ---------------------------------- 新測試版AI ---------------------------------- //

    public function newTest_updateOrCreate(Request $request, $service_id, $plan_id)
    {
        $this->validate($request, [
            'date' => ['required', 'date_format:Y-m-d'],
            'video' => ['required'],
            'video.id' => ['required','integer'],
            'video.e.start_at' => ['required','date_format:Y-m-d H:i:s'],
            'video.test_data.stop_at' => ['required','date_format:Y-m-d H:i:s'],
            'video.test_data.data' => ['required','array'],
        ]);
        $date = $request->input('date');
        $video = $request->input('video');

        $service = Service::where('id', $service_id)->first();
        if (!$service) {
            error_log('NOOOOO service');
            return response()->json('1', 404);
        }
        if(!$this->isVIPMember($service->members_id, $service->doctors_id))  {
            return response()->json('invalid member', 404);
        }
        $service_plan = ServicePlan::where('id', $plan_id)->where('services_id', $service_id)->first();
        if (!$service_plan) {
            error_log('NOOOOO service plan');
            return response()->json('2', 404);
        }
        $service_plan_video = ServicePlanVideo::where('id', $video['id'])->where('service_plans_id', $plan_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json('3', 404);
        }
       //try{
            $service_plan_daily = ServicePlanDaily::updateOrCreate(
                                                    [
                                                        'services_id' => $service->id,
                                                        'service_plans_id' => $plan_id,
                                                        'service_plan_videos_id' => $video['id'],
                                                        'scored_at' => $date,
                                                    ], 
                                                    [
                                                        'movement_test_data' => json_encode($video['test_data'])
                                                    ]);
            $tmp = $this->newTest_calculateScore($plan_id,$video['id'],$video['test_data']);
            $score = $tmp['score'];
            $reason = $tmp['reason'];
            $service_plan_daily = ServicePlanDaily::updateOrCreate(
                                                    [
                                                        'services_id' => $service->id,
                                                        'service_plans_id' => $plan_id,
                                                        'service_plan_videos_id' => $video['id'],
                                                        'scored_at' => $date,
                                                    ], 
                                                    [
                                                        'score' => json_encode($score),
                                                        'reason' => json_encode($reason),
                                                        'movement_test_data' => json_encode($video['test_data'])
                                                    ]);

            $point = $this->calculatePoint($service_id, $plan_id, $service_plan_daily->id , $score);
            $users_id = Service::where('id', $service_id)->first()->member->id;
            $PointProduce = PointProduce::updateOrCreate(
                [
                    'service_plan_daily_id' => $service_plan_daily->id,
                    'point' => $point,
                    'users_id' => $users_id,
                ]);

            //return response($score,200);
            $arr = [];
            foreach ($score as $key => $value) {
                if($value<0){
                    $score[$key] = 0;
                }
            }
            return response()->json(["ai_score"=>$score,"reason"=>$reason]);
        //}
        //catch(Exception $exception){
        //    return response()->json($exception, 500);
        //}
    }

    public function newTest_calculateScore($servicePlan_id,$servicePlanVideo_id, $test_data)
    {   
        
        $service_plan_video = ServicePlanVideo::where('id', $servicePlanVideo_id)->where('service_plans_id', $servicePlan_id)->first();
        $template = json_decode($service_plan_video->movement_template_data);
        $test = $test_data['data'];
        $param = [
            'session' => $service_plan_video->session,
            'repeat_time' => $service_plan_video->repeat_time,
            'major_threshold' => 1.5,
            'error_threshold' => 0.4,
            'point_threshold' => 5
        ];

        Log::debug('AI optimization: '.strval($servicePlanVideo_id));
        $ai = new RepeatMultiDirectionAI($template, $test, $param);
        $tmp = $ai->calScore();
        return $tmp;
    }

    public function newTest_AIDevelop(Request $request, $video_id, $daily_id)
    {
        $service_plan_video = ServicePlanVideo::where('id', $video_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json('3', 404);
        }

        $service_plan_daily = ServicePlanDaily::where('id', $daily_id)->first();
        if (!$service_plan_daily) {
            error_log('NOOOOO service plan video');
            return response()->json('3', 404);
        }


        $template = json_decode($service_plan_video->movement_template_data);
        $test = json_decode($service_plan_daily->movement_test_data)->data;
        $param = [
            'session' => $service_plan_video->session,
            'repeat_time' => $service_plan_video->repeat_time,
            'major_threshold' => 1.5,
            'error_threshold' => 0.4,
            'point_threshold' => 5
        ];

        $ai = new RepeatMultiDirectionAI($template, $test, $param);
        $feature = $ai->printFeature();

        return response()->json($feature);
    }
}
