<?php

namespace App\Http\Controllers\APIs;

use App\Subscription; 
use App\Events\MemberServiceCompletedEvent;
use App\Http\Controllers\Controller;
use App\Traits\SettingUtility;
use App\Traits\SlackNotify;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;

class ServiceController extends Controller
{
    use SlackNotify, SettingUtility;

    public function getServiceAndPlanByID(Request $request, $user_id){
        $serviceAndPlan = Subscription
        ::with('service','plan')
        ->where('id', $user_id);

        if (!$subscription) {
            return response()->json(null,404);
        } 

        return response()->json($serviceAndPlan);
    }


}