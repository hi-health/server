<?php

use App\Member;
use App\MemberRequest;

class TestMemberRequestTableSeeder extends Seeder
{
    public function run()
    {
        $this->truncate();
        $requests = Member::where('login_type', 1)
            ->get()
            ->map(function($member) {
                $request = factory(MemberRequest::class)
                    ->make();
                $member->requests()->save($request);
                return $request;
            });
    }

    protected function truncate()
    {
        MemberRequest::truncate();
    }
}
