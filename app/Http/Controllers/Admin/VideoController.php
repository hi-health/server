<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service;
use App\ServicePlan;
use App\User;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function showListPage(Request $request)
    {
        $per_page = $request->get('per_page', 20);
        $service_plans_group = ServicePlan
            ::with('service')
            ->selectRaw('services_id')
            ->groupBy('services_id')
            ->paginate($per_page);
        $service_plans = ServicePlan
            ::with('videos')
            ->whereIn('services_id', $service_plans_group->pluck('services_id'))
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('admin.videos.list', [
            'service_plans_group' => $service_plans_group,
            'service_plans' => $service_plans,
        ]);
    }
    public function showDetailPage($service_id)
    {
        $service = Service
            ::where('id', $service_id)
            ->first();
        if (!$service) {
            return redirect()
                ->route('admin-videos-list');
        }
        $service_plans = ServicePlan
            ::with('videos')
            ->where('services_id', $service_id)
            ->get();
        if (!$service_plans) {
            return redirect()
                ->route('admin-videos-list');
        }

        return view('admin.videos.detail', [
            'service' => $service,
            'service_plans' => $service_plans
        ]);
    }
}
