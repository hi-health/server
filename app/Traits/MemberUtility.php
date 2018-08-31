<?php

namespace App\Traits;

use App\Service;

trait MemberUtility
{
    protected function isVIPMember($member_id, $doctor_id)
    {
        // 2017-10-03 aleiku 多加一個判斷，必須要是最後一筆成立的服務
        $service = Service
            ::where('members_id', $member_id)
            ->where('payment_status', 1)
            ->orderBy('id','DESC')
            ->first();

        if ($service  
            && $service->doctors_id == $doctor_id
            && $service->leave_days > 0
           )            
            return true;

        return false;

        // return !Service
        //     ::where('members_id', $member_id)
        //     ->where('doctors_id', $doctor_id)
        //     ->where('payment_status', 1)
        //     ->get()
        //     ->filter(function ($service) {
        //         return $service->leave_days > 0;
        //     })->isEmpty();
    }
}
