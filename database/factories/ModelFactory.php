<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password = '100200300';

    return [
        'name' => $faker->name,
        'account' => $faker->numerify('09########'),
        'password' => $password,
        'login_type' => $faker->numberBetween(1, 2),
        'facebook_id' => $faker->numerify('########'),
        'facebook_token' => $faker->sha256(),
//        'avatar' => str_replace('public/', '', $faker->image('public/tests/avatar', 360, 270, 'cats')),
        'male' => $faker->numberBetween(0, 1),
        'birthday' => $faker->date(),
        'city_id' => 1,
        'district_id' => $faker->numberBetween(100, 110),
        'mrs' => $faker->numberBetween(1, 50),
        'online' => true,//$faker->boolean(),
        'status' => true,//$faker->boolean(),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Doctor::class, function (Faker\Generator $faker) {
    return [
        'number' => $faker->numerify('D#####'),
        'treatment_type' => $faker->numberBetween(1, 2),
        'title' => $faker->name,
        'experience_year' => $faker->numberBetween(1, 15),
        'experience' => [
            $faker->name,
            $faker->name,
        ],
        'specialty' => [
            $faker->name,
            $faker->name,
        ],
        'education' => [
            $faker->name,
            $faker->name,
        ],
        'license' => [
            $faker->name,
            $faker->name,
        ],
        'education_bonus' => $faker->numberBetween(1000, 10000),
        'longitude' => '121.'.$faker->numberBetween(1000001, 9999999),
        'latitude' => '25.'.$faker->numberBetween(1000001, 9999999),
    ];
});

$factory->define(App\Service::class, function (Faker\Generator $faker) {
    return [
        'treatment_type' => $faker->numberBetween(1, 2),
        'charge_amount' => $faker->numberBetween(1000, 2000),
        'payment_method' => $faker->numberBetween(1, 2),
        'payment_status' => $faker->numberBetween(0, 2),
    ];
});

$factory->define(App\MemberRequest::class, function (Faker\Generator $faker) {
    return [
        'treatment_type' => $faker->numberBetween(1, 2),
        'treatment_kind' => $faker->numberBetween(1, 4),
        'onset_date' => $faker->date('2017-m-d'),
        'onset_part' => $faker->numberBetween(1, 5),
    ];
});
