<?php

use App\Doctor;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TestDoctorTableSeeder extends Seeder
{
    public function run()
    {
        $this->truncate();
        $doctors = User
            ::where('login_type', 2)
            ->get()
            ->map(function ($user) {
                try {
                    DB::beginTransaction();
                    $doctor = factory(Doctor::class)
                        ->make();
                    $user->doctor()->save($doctor);
                    DB::commit();
                } catch (QueryException $exception) {
                    DB::rollback();
                }

                return $doctor;
            });
        $this->info('Created '.$doctors->count().' Doctors');
    }

    protected function truncate()
    {
        Doctor::truncate();
    }
}
