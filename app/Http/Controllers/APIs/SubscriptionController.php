<?php

namespace App\Http\Controllers\APIs;

use App\Subscription; 
use App\Events\MemberServiceCompletedEvent;
use App\Http\Controllers\Controller;
use App\Traits\SettingUtility;
use App\Traits\SlackNotify;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;


class SubscriptionController extends Controller
{
    use SlackNotify, SettingUtility;

    public function getSubscriptionByUserID(Request $request, $user_id){
        $subscription = Subscription
            ::where('users_id', $user_id)
            ->first();

        if (!$subscription) {
            return response()->json(null,404);
        } 
        
        return response()->json($subscription);
    }

    public function updateOrCreateSubscription(Request $request, $user_id){
        $this->validate($request, [
            'services_id' => ['required','integer','min:1'],
            'service_plans_id' => ['required','integer','min:1'],
            'due_date' => ['nullable','date','date_format:Y-m-d']
        ]);
        $services_id = $request->input('services_id');
        $service_plans_id = $request->input('service_plans_id');
        $due_date = $request->input('due_date');

        
        $subscription = Subscription
            ::updateOrCreate([
                'users_id' => $user_id,
                'services_id' => $services_id,
                'service_plans_id' => $service_plans_id,
                'due_date' => $due_date,
            ]);
        return response()->json($subscription);
    }

    public function deleteSubscription(Request $request){
        $this->validate($request, [
            'users_id' => ['required', 'integer', 'min:1'],
            'services_id' => ['required', 'integer', 'min:1'],
            'service_plans_id' => ['required', 'integer', 'min:1']
        ]);
        $user_id = $request->input('users_id');
        $services_id = $request->input('services_id');
        $service_plans_id = $request->input('service_plans_id');

        $subscription = Subscription
            ::where('users_id', $user_id)
            ->where('services_id', $services_id)
            ->where('service_plans_id', $service_plans_id);

        if (!$subscription) {
            return response()->json(null,404);
        } 
        
        $subscription->delete();

        return response()->json($subscription);
    }
}
