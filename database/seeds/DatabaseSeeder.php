<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->call(ParameterTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        if (App::environment() === 'production') {
            $this->call(UserTableSeeder::class);
        } else {
            $this->call(TestUserTableSeeder::class);
            $this->call(TestServiceTableSeeder::class);
            $this->call(TestDoctorTableSeeder::class);
            $this->call(TestMemberRequestTableSeeder::class);
        }
    }
}
