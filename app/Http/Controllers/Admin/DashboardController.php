<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\MemberRequest;
use App\Service;
use App\ServicePlan;
use App\User;

class DashboardController extends Controller
{
    public function showDashboardPage()
    {
        $doctors = User
            ::where('login_type', 2)
            ->get();
        $members = User
            ::where('login_type', 1)
            ->get();
        $member_requests = MemberRequest
            ::get();
        $services = Service
            ::where('payment_status', 3)
            ->orderBy('paid_at', 'DESC')
            ->get();
        $service_plans = ServicePlan
            ::with('videos')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('admin.dashboard', [
            'doctors' => $doctors,
            'members' => $members,
            'member_requests' => $member_requests,
            'services' => $services,
            'service_plans' => $service_plans,
        ]);
    }
}
