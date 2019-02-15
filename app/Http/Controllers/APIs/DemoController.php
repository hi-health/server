<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Log;
use App\DemoPlan;
use App\DemoPlanVideo;
use App\AI\RepeatMultiDirectionAI;
// use App\AI\RepeatMultiDirectionAIv3;
// use App\AI\RepeatMultiDirectionAIv3_1;

class DemoController extends Controller
{
    public function getAllDemoPlan()
    {
        $demo_plan = DemoPlan::get()
            ->map(function ($item, $key) {
                $tmp1 = $item->videos->map(function ($item1, $key1) {
                    
                    if($item1->movement_template_data == null){
                        $item1['activation_flag'] = -1 ;
                    }
                    else{
                        $item1['activation_flag'] = 1 ;
                    }
                    
                    $item1->movement_template_data = null;
                    return $item1;
                });
                return $item;
            });

        if (!$demo_plan) {
            return response()->json(null, 404);
        }
        return response()->json($demo_plan);
    }

    public function updateOrCreate(Request $request, $demo_plan_id)
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

        $demo_plan = DemoPlan::where('id', $demo_plan_id)->first();
        if (!$demo_plan) {
            error_log('NOOOOO service plan');
            return response()->json('2', 404);
        }
        $demo_plan_video = DemoPlanVideo::where('id', $video['id'])->where('demo_plans_id', $demo_plan_id)->first();
        if (!$demo_plan_video) {
            error_log('NOOOOO demo plan video');
            return response()->json('3', 404);
        }

            $tmp = $this->calculateScore($demo_plan_id,$video['id'],$video['test_data']);
            $score = $tmp['score'];
            $reason = $tmp['reason'];

            foreach ($score as $key => $value) {
                if($value<0){
                    $score[$key] = 0;
                }
            }

            return response()->json(["ai_score"=>$score,"reason"=>$reason]);
    }

    public function calculateScore($DemoPlan_id,$DemoPlanVideo_id, $Demo_test_data)
    {   
        
        $Demo_plan_video = DemoPlanVideo::where('id', $DemoPlanVideo_id)->where('demo_plans_id', $DemoPlan_id)->first();
        $template = json_decode($Demo_plan_video->movement_template_data);
        $test = $Demo_test_data['data'];
        $param = [
            'session' => $Demo_plan_video->session,
            'repeat_time' => $Demo_plan_video->repeat_time,
            'major_threshold' => 1.5,
            'error_threshold' => 0.4,
            'point_threshold' => 5
        ];

        Log::debug('AI optimization: '.strval($DemoPlanVideo_id));
        $ai = new RepeatMultiDirectionAI($template, $test, $param);
        $tmp = $ai->calScore();
        return $tmp;
    }

    public function addMovementTemplateData(Request $request, $demo_plan_video_id)
    {
        $this->validate($request, [
            'movement_template_data' => ['required', 'array'],
        ]);

        $movement_template_data = json_encode($request->movement_template_data);

        $demo_video = DemoPlanVideo::where('id', $demo_plan_video_id)->first();

        $demo_video->movement_template_data = $movement_template_data;
        $demo_video->save();
        
        return response('uploaded successfully', 200)->header('Content-Type', 'text/plain');
    }
}
