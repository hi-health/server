<?php

use App\User;

class TestUserTableSeeder extends Seeder
{
    public function run()
    {
        $this->truncate();
        $quantity = 100;
        factory(User::class, $quantity)->make()->each(function($user, $index) {
            if ($index === 0) {
                $user->account = 'admin';
                $user->name = 'Admin';
                $user->login_type = 0;
                $user->status = 1;
            } else if ($index === 1) {
                $user->login_type = 1;
                $user->status = 1;
            } else if ($index === 2) {
                $user->login_type = 2;
                $user->status = 1;
            }
            $user->save();
        });
        $this->info('Created '.$quantity.' Users');
    }

    protected function truncate()
    {
        User::truncate();
    }
}
