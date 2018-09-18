<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ServiceController extends Controller
{
    public function showListPage(Request $request)
    {
        $per_page = $request->get('per_page', 20);
        $services = Service
            ::with('doctor', 'member')
            ->whereNotNull('members_id')
            ->whereNotNull('doctors_id')
            ->orderBy('order_number', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate($per_page);

        return view('admin.services.list', [
            'services' => $services,
        ]);
    }
    
    public function showListByThisMonthPage(Request $request)
    {
        
        $per_page = $request->get('per_page', 20);
        $services = Service
            ::with('doctor', 'member')
            ->whereNotNull('members_id')
            ->whereNotNull('doctors_id')
            ->where('paid_at', '>=', Carbon::now()->startOfMonth())
            ->where('paid_at', '<=', Carbon::now()->endOfMonth())
            ->where('payment_status',3)
            ->orderBy('paid_at', 'DESC')
            ->paginate($per_page);

        return view('admin.services.list_by_thismonth', [
            'services' => $services,
        ]);
    }

    public function showListByDoctorPage(Request $request, $doctor_id)
    {
        $per_page = $request->get('per_page', 20);
        $doctor = User
            ::where('id', $doctor_id)
            ->where('login_type', 2)
            ->first();
        if (!$doctor) {
            return redirect()
                ->route('admin-services-list');
        }
        $services = Service
            ::with('doctor', 'member')
            ->where('doctors_id', $doctor_id)
            ->orderBy('updated_at', 'ASC')
            ->paginate($per_page);

        return view('admin.services.list_by_doctor', [
            'doctor' => $doctor,
            'services' => $services,
        ]);
    }
    
    public function showListByMemberPage(Request $request, $member_id)
    {
        $per_page = $request->get('per_page', 20);
        $member = User
            ::where('id', $member_id)
            ->where('login_type', 1)
            ->first();
        if (!$member) {
            return redirect()
                ->route('admin-services-list');
        }
        $services = Service
            ::with('doctor', 'member')
            ->where('members_id', $member_id)
            ->orderBy('updated_at', 'ASC')
            ->paginate($per_page);

        return view('admin.services.list_by_member', [
            'member' => $member,
            'services' => $services,
        ]);
    }
    
    public function showDetailPage($service_id)
    {
        $service = Service
            ::with('doctor', 'member')
            ->where('id', $service_id)
            ->first();
        if (!$service) {
            return redirect()
                ->route('admin-services-list');
        }
        return view('admin.services.detail', [
            'service' => $service
        ]);
    }
}
