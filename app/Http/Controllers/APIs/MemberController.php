<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\MemberRequest;
use App\Setting;
use App\Service;
use App\PointProduce;
use App\PointConsume;
use App\User;
use Illuminate\Http\Request;
use Log;
use App\Traits\AWSSNS;
class MemberController extends Controller
{
    use AWSSNS;
    public function getById(Request $request, $member_id)
    {
        $member = User
            ::withMember($member_id)
            ->first();
        if (!$member) {
            return response()->json(null, 404);
        }
        $service = Service
            :: with('doctor')
            -> where('members_id', $member_id)
            -> where('payment_status', 3)
            -> orderBy('paid_at', 'DESC')
            -> first();
        if ($service) {
            $member->last_service_id = $service->id;
        }

        return response()->json($member);
    }

    public function getSummary(Request $request, $member_id)
    {
        $user = User
            ::withMember($member_id)
            ->first();
        if (!$user) {
            return response()->json(null, 404);
        }
        
        $services = Service
            :: with('doctor')
            -> where('members_id', $member_id)
            -> where('payment_status', 3)
            -> orderBy('paid_at', 'DESC')
            -> get();
        $member_doctors = $services->unique('doctors_id')
            ->pluck('doctor');
        $last_service_id = !$services->isEmpty() ? $services->first()->id : null;
        $banners = Setting
            ::where('type', 'banner')
            ->get();

        $PointProduce = PointProduce::where('users_id', $member_id)->sum('point');
        $PointConsume = PointConsume::where('users_id', $member_id)->sum('point');
        $RemainedPoint = $PointConsume + $PointProduce;

        return response()->json([
            'need_rating'=> false,
            'need_rating_message' => '您目前有0份服務尚未評分',
            'rating_url'=>url("/point/{$member_id}"),
            'points_url'=>url("/point/{$member_id}"),
            'banners' => $banners->map(function($banner) {
                return [
                    'image_url' => $banner->getValue('image'),
                    'redirect_url' => $banner->getValue('redirect_url'),
                ];
            }),
            'doctors' => $member_doctors,
            'last_service_id' => $last_service_id,
            'online_status' => $user->online,
            'points' => $RemainedPoint
        ]);
    }

    public function getRequestCollection(Request $request, $member_id)
    {
        $member = User
            ::withMember($member_id)
            ->first();
        if (!$member) {
            return response()->json(null, 404);
        }
        $requests = MemberRequest
            ::where('members_id', $member_id)
            ->get();
        return response()->json($requests);
    }
}
