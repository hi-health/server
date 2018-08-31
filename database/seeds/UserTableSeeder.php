<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        $default_users = [
            [
                'name' => 'Administrator',
                'account' => 'admin',
                'login_type' => 0,
                'password' => '100200300',
                'male' => 1,
                'birthday' => date('Y-m-d'),
                'city_id' => 1,
                'district_id' => 100,
                'mrs' => 0,
                'online' => true,
                'status' => true,
            ]
        ];
        foreach($default_users as $user) {
            User::updateOrCreate([
                'account' => $user['account'],
            ], $user);
        }
    }
}
