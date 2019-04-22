<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MemberRequest;
use App\Service;
use App\ServicePlan;
use App\User;
use App\PointProduce;
use App\Message;

class DashboardController extends Controller
{
    public function showDashboardPage()
    {
        $doctors = User::where('login_type', 2)
            ->get();

        $members = User::where('login_type', 1)
            ->get();

        $member_requests = MemberRequest::get();

        $services = Service::where('payment_status', 3)
            ->orderBy('paid_at', 'DESC')
            ->get();

        $service_plans = ServicePlan::with('videos')
            ->orderBy('created_at', 'DESC')
            ->get();

        $sum_points = PointProduce::whereNotNull('service_plan_daily_id')
            ->sum('point');

        $service_member = Service::whereNotNull('members_id')->select('members_id')->get()->pluck('members_id');
        $no_service_member = User::where('login_type', 1)->whereNotIn('id',$service_member)->get();
        $message = Message::with('member', 'doctor')->whereIn('members_id',$no_Deservice_member)->where('visible', true)->orderBy('updated_at', 'DESC')->get();
        return view('admin.dashboard', [
            'doctors' => $doctors,
            'members' => $members,
            'member_requests' => $member_requests,
            'services' => $services,
            'service_plans' => $service_plans,
            'sum_points' => $sum_points,
            'no_service_member' =>$no_service_member,
            'service_member' =>$service_member,
            'message'=>$message

        ]);
    }
}
