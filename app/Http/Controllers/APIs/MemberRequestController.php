<?php

namespace App\Http\Controllers\APIs;

use App\Events\MemberCreateRequestEvent;
use App\Http\Controllers\Controller;
use App\Member;
use App\MemberRequest;
use App\MemberRequestDoctor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberRequestController extends Controller
{
    public function getById(Request $request, $request_id)
    {
        $member_request = MemberRequest
            ::with('member', 'doctor', 'messages')
            ->where('id', $request_id)
            ->first();
        if ($member_request) {
            $doctor_id = $request->get('doctor_id');
            if ($doctor_id) {
                MemberRequestDoctor::create([
                    'member_requests_id' => $member_request->id,
                    'doctors_id' => $doctor_id,
                ]);
            }

            return response()->json($member_request);
        }

        return response()->json(null, 404);
    }

    public function getCollectionByMember(Request $request, $member_id)
    {
        $member = Member
            ::where('id', $member_id)
            ->first();
        if (!$member) {
            return response()->json(null, 404);
        }
        $member_requests = MemberRequest
            ::where('members_id', $member_id)
            ->get();

        return response()->json($member_requests);
    }

    public function getPagination(Request $request)
    {
        $per_page = $request->get('per_page');
        $treatment_type = (int) $request->get('treatment_type');
        $city_id = (int) $request->get('city_id');
        $district_id = (int) $request->get('district_id');
        $km = 6371;
//        $longitude = (float) $request->get('longitude');
//        $latitude = (float) $request->get('latitude');
        $distance = $request->get('distance', 10);
        $member_request_model = MemberRequest
            ::with('member')
            ->orderBy('updated_at', 'DESC')
            ->groupBy('members_id', 'treatment_type');
        if ($treatment_type) {
            $member_request_model->where('treatment_type', $treatment_type);
        }
//        if (!empty($longitude) and !empty($latitude)) {
//            $member_request_model
//                ->selectRaw('*, ('.$km.' * acos(cos(radians('.$latitude.')) * cos(radians(latitude)) * cos(radians(longitude) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin(radians(latitude)))) AS distance')
//                ->having('distance', '<', max(0, $distance));
//        }

        if ($city_id) {
            $member_request_model->Where('city_id', $city_id);
        }

        if ($district_id) {
            $member_request_model->Where('district_id', $district_id);
        }

        $member_requests = $member_request_model->paginate($per_page);
        $doctor_id = $request->get('doctor_id');
        if ($doctor_id) {
            $member_requests_id = MemberRequestDoctor
                ::where('doctors_id', $doctor_id)
                ->whereIn('member_requests_id', $member_requests->pluck('id'))
                ->get()
                ->pluck('member_requests_id');
            $member_requests
                ->whereNotIn('id', $member_requests_id)
                ->each(function ($member_request) use ($doctor_id) {
                    MemberRequestDoctor::create([
                        'member_requests_id' => $member_request->id,
                        'doctors_id' => $doctor_id,
                    ]);
                });
        }

        return response()->json($member_requests);
    }

    public function add(Request $request, $member_id)
    {
        $this->validate($request, [
            'treatment_type' => ['between:1,2'],
            'treatment_kind' => ['between:1,4'],
            'onset_date' => ['date_format:Y-m-d'],
            'onset_part' => ['between:1,5'],
//            'longitude' => [], //, 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
//            'latitude' => [], // 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'city_id' => [],
            'district_id' => [],
        ]);
        $member = Member
            ::where('id', $member_id)
            ->first();
        if (!$member) {
            return response()->json(null, 404);
        }
        $data = $request->input();
        $data['members_id']  = $member_id;
        $data['created_at']  = Carbon::now();
        $data['updated_at']  = Carbon::now();
        $data['city_id']     = $member -> city_id;
        $data['district_id'] = $member -> district_id;
        $member_request = MemberRequest::updateOrCreate([
            'members_id'  => $member_id,
        ], $data);
        MemberRequestDoctor
            ::where('member_requests_id', $member_request->id)
            ->delete();
        event(
            new MemberCreateRequestEvent($member, $member_request)
        );

        return response()->json($member_request);
    }
}
