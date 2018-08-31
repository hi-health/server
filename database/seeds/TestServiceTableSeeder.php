<?php

use App\Doctor;
use App\Member;
use App\Service;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TestServiceTableSeeder extends Seeder
{
    public function run()
    {
        $this->truncate();
        $members = Member
            ::where('login_type', 1)
            ->get();
        $doctors = User
            ::with('doctor')
            ->where('login_type', 2)
            ->get()
            ->map(function ($user) use($members) {
                $doctor = $user->doctor;
                if ($doctor) {
                    try {
                        $member = $members->random(1)->first();
                        DB::beginTransaction();
                        $service = factory(Service::class)
                            ->make();
                        $service->generateOrderNumber();
                        $doctor->services()->save($service);
                        $member->services()->save($service);
                        DB::commit();
                    } catch (QueryException $exception) {
                        DB::rollback();
                    }
                }

                return $doctor;
            });
    }
    protected function truncate()
    {
        Service::truncate();
    }
}
