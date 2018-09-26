<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Service;
use App\ServicePlan;
use App\ServicePlanVideo;
use App\Traits\SlackNotify;
use Exception;
use FFMpeg;
use Illuminate\Http\Request;
use Log;

class ServicePlanController extends Controller
{
    use SlackNotify;

    public function getAll(Request $request, $service_id)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();\Log::info($service_id);
        if (!$service) {
            return response()->json(null, 404);
        }

        return response()->json($service->plans);
    }

    public function getActivationFlag(Request $request, $service_id, $plan_id, $video_id)
    {
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
        $service_plan_video = ServicePlanVideo::where('id', $video_id)->where('service_plans_id', $plan_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json(null, 404);
        }

        return response($service_plan_video->activation_flag, 200);
    }

    public function updateActivationFlag(Request $request, $service_id, $plan_id, $video_id)
    {
        $this->validate($request, [
            'activation_flag' => ['required', 'integer', 'between:-1,1'],
        ]);

        $new_flag = json_encode($request->activation_flag);

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
        $service_plan_video = ServicePlanVideo::where('id', $video_id)->where('service_plans_id', $plan_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json(null, 404);
        }
        $service_plan_video->activation_flag = $new_flag;
        $service_plan_video->save();

        return response($service_plan_video->activation_flag, 200);
    }

    public function updateOrCreateTemplate(Request $request, $service_id, $plan_id, $video_id)
    {
        $this->validate($request, [
            'movement_template_data' => ['required', 'array'],
        ]);

        $movement_template_data = json_encode($request->movement_template_data);

        $service = Service::where('id', $service_id)->first();
        if (!$service) {
            error_log('NOOOOO service');
            return response()->json(null, 404);
        }
        $service = Service::where('id', $service_id)->where('payment_status',3)->where('leave_days','>',0)->first();
        if (!$service) {
            error_log('NOOOOO VIP service');
            return response()->json(null, 404);
        }
        $service_plan = ServicePlan::where('id', $plan_id)->where('services_id', $service_id)->first();
        if (!$service_plan) {
            error_log('NOOOOO service plan');
            return response()->json(null, 404);
        }
        $service_plan_video = ServicePlanVideo::where('id', $video_id)->where('service_plans_id', $plan_id)->first();
        if (!$service_plan_video) {
            error_log('NOOOOO service plan video');
            return response()->json(null, 404);
        }

        if($service_plan_video->activation_flag != -1){
            if (isset($service_plan_video->movement_template_data)){
                $service_plan_video->movement_template_data = $movement_template_data;
                $service_plan_video->activation_flag = 1;
                $service_plan_video->save();
            } else {
                $service_plan_video->movement_template_data = $movement_template_data;
                $service_plan_video->activation_flag = 1;
                $service_plan_video->save();
            }
        } else {
            return response()->json(null, 404);
        }


        return response('uploaded successfully', 200)->header('Content-Type', 'text/plain');
    }
    
    public function updateOrCreate(Request $request, $service_id)
    {
        $this->validate($request, [
            'plans' => ['required', 'array', 'min:1'],
            'plans.*.id' => ['nullable', 'integer'],
            'plans.*.started_at' => ['required', 'date_format:H:i'],
            'plans.*.stopped_at' => ['required', 'date_format:H:i'],
            'plans.*.weight' => ['nullable', 'integer'],
            'plans.*.videos' => ['required', 'array', 'min:1', 'max:5'],
            'plans.*.videos.*.id' => ['nullable', 'integer'],
            'plans.*.videos.*.file' => ['nullable', 'mimetypes:video/avi,video/mpeg,video/mp4,video/quicktime', 'max: '.(30 * 1024)],
            'plans.*.videos.*.weight' => ['nullable', 'integer'],
            'plans.*.videos.*.description' => ['nullable', 'string'],
            'plans.*.videos.*.repeat_time'=> ['required', 'integer'],
            'plans.*.videos.*.session'    => ['required', 'integer'],
            'plans.*.videos.*.video_path'=> ['nullable', 'string']
        ]);
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $plans = $request->input('plans');
        $plans_file = $request->file('plans');
        $service_plans = collect($plans)->map(function ($item) use ($service, &$plans_file) {
            if (isset($item['id'])) {
                $service_plan = ServicePlan
                    ::where('id', $item['id'])
                    ->first();
                if ($service_plan) {
                    $service_plan->update([
                        'services_id' => $service->id,
                        'started_at' => $item['started_at'],
                        'stopped_at' => $item['stopped_at'],
                        'weight' => array_get($item, 'weight', 0),
                    ]);
                }
            } else {
                $service_plan = ServicePlan::create([
                    'services_id' => $service->id,
                    'started_at' => $item['started_at'],
                    'stopped_at' => $item['stopped_at'],
                    'weight' => array_get($item, 'weight', 0),
                ]);
            }
            if (empty($plans_file)) {
                $plan_video_files = [];
            } else {
                $plan_video_files = array_get(array_shift($plans_file), 'videos');
            }
            $service_plan->videos = collect($item['videos'])->map(function ($video, $index) use ($service, $service_plan, &$plan_video_files) {
                if (isset($plan_video_files[$index])) {
                    $video_file = array_get($plan_video_files[$index], 'file');
                } else {
                    $video_file = null;
                }
                
                $data = [
                    'service_plans_id' => $service_plan->id,
                    'description' => $video['description'],
                    'weight' => array_get($video, 'weight', 0),
                    'session' => $video['session'],
                    'repeat_time' => $video['repeat_time'],
                ];
                
                if ( array_key_exists('activation_flag', $video) )
                    $data['activation_flag'] =  $video['activation_flag'];
                Log::alert($data);
                if ($video_file) {
                    try {
                        $folder_name = strtr('services/{service_id}', [
                            '{service_id}' => $service->order_number,
                        ]);
                        $path = public_path($folder_name);
                        $file_name = md5($service->order_number.'_video_'.$service_plan->id.'_'.uniqid());
                        $new_file = strtr('{name}.{extension}', [
                            '{name}' => $file_name,
                            '{extension}' => $video_file->getClientOriginalExtension(),
                        ]);

                        if ($video_file->move($path, $new_file)) {
                            $data['video'] = strtr('{folder_name}/{file_name}', [
                                '{folder_name}' => $folder_name,
                                '{file_name}' => $new_file,
                            ]);

                            $thumbnail_name = $folder_name.'/'.$file_name.'.jpg';
                            
                            $generated = FFMpeg 
                                ::fromDisk('video')
                                ->open($data['video'])
                                ->getFrameFromSeconds(1)
                                ->export()
                                ->toDisk('thumbnails')
                                ->save($thumbnail_name);
                            
                            if ($generated) {
                                $data['thumbnail'] = $thumbnail_name;
                            }
                        }
                    } catch (Exception $exception) {

                       var_dump($exception->getMessage());
                       die();
                    }
                } elseif (array_key_exists('video_path', $video)){
                    $data['video'] = $video['video_path'];
                    $data['thumbnail'] = explode('.', $video['video_path'], -1)[0].'.jpg';

                }
                if (isset($video['id'])) {
                    $service_plan_video = ServicePlanVideo
                        ::where('service_plans_id', $service_plan->id)
                        ->where('id', $video['id'])
                        ->first();
                    if ($service_plan_video) {
                        $service_plan_video->update($data);
                    }
                } else {
                    $service_plan_video = ServicePlanVideo::create($data);
                }

                return $service_plan_video;
            })->filter(function ($item) {
                return $item !== null;
            });

            return $service_plan;
        });

        return response()->json($service_plans);
    }

    public function delete(Request $request, $service_id)
    {
        $this->validate($request, [
            'plans' => ['required', 'array', 'min:1'],
            'plans.*.id' => ['required', 'integer'],
        ]);
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $plans = $request->input('plans');
        $service_plans_delete = collect($plans)->map(function ($plan) {
            $service_plan = ServicePlan
                ::where('id', $plan['id'])
                ->first();
            if ($service_plan) {
                $deleted = $service_plan->delete();
                $service_plan->videos()->delete();
                $service_plan->daily()->delete();
                if ($deleted) {
                    return [
                        'id' => $plan['id'],
                    ];
                }
            }
        })->filter(function ($item) {
            return $item !== null;
        });

        return response()->json([
            'deleted' => $service_plans_delete,
        ]);
    }

    public function deleteVideos(Request $request, $service_id, $service_plan_id)
    {
        $this->validate($request, [
            'videos' => ['required', 'array', 'min:1'],
            'videos.*.id' => ['required', 'integer'],
        ]);
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return response()->json(null, 404);
        }
        $videos = $request->input('videos');
        $service_plan_videos_delete = collect($videos)->map(function ($video) {
            $service_plan_video = ServicePlanVideo
                ::where('id', $video['id'])
                ->first();
            if ($service_plan_video) {
                $deleted = $service_plan_video->delete();
                if ($deleted) {
                    return [
                        'id' => $video['id'],
                    ];
                }
            }
        })->filter(function ($item) {
            return $item !== null;
        });

        return response()->json([
            'deleted' => $service_plan_videos_delete,
        ]);
    }
}
