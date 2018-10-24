<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MemberRequest;
use App\Service;
use App\ServicePlan;
use App\User;
use App\PointProduce;

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

        return view('admin.dashboard', [
            'doctors' => $doctors,
            'members' => $members,
            'member_requests' => $member_requests,
            'services' => $services,
            'service_plans' => $service_plans,
            'sum_points' => $sum_points,
        ]);
    }
}
