<?php

namespace Tests\Feature;

use Exception;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserTest extends ApiTest
{
    protected $faker = null;

    protected $user_id = 3;

    protected $login_type = 1;

    protected $account = '0980645979';

    protected $password = '100200300';

    protected $facebook_id = 'F1234567890';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Faker::create();
    }

    public function testChangeUserOnlineSuccess()
    {
        $response = $this->post('/api/users/'.$this->user_id.'/online');
        $response->assertStatus(200);
    }

    public function testChangeUserOnlineFailure()
    {
        $user_id = -1;
        $response = $this->post('/api/users/-1/online');
        $response->assertStatus(404);
    }

    public function testChangeUserOfflineSuccess()
    {
        $response = $this->post('/api/users/'.$this->user_id.'/offline');
        $response->assertStatus(200);
    }

    public function testChangeUserOfflineFailure()
    {
        $response = $this->post('/api/users/-1/offline');
        $response->assertStatus(404);
    }

//    public function testGetSendSmsCodeSuccess()
//    {
//        $response = $this->post('/api/users/sms/code', [
//            'phone' => $this->account,
//        ]);
//        $response->assertStatus(200);
//    }

    public function testGetSendSmsCodeFailure()
    {
        $response = $this->post('/api/users/sms/code', []);
        $response->assertStatus(422);
    }

    public function testSignUpUserSuccess()
    {
        try {
            Storage::disk('local')->copy('avatar_test.png', 'avatar.png');
        } catch (Exception $e) {
        }
        $avatar_path = storage_path('app/avatar.png');
        $avatar = new UploadedFile($avatar_path, 'avatar.png', filesize($avatar_path), 'image/png', null, true);
        $response = $this->call('post', '/api/users/register', [
            'name' => $this->faker->name,
            'account' => $this->account,
            'password' => $this->password,
            'login_type' => $this->login_type,
            'facebook_id' => $this->facebook_id,
            'facebook_token' => $this->faker->sha256(),
            'male' => $this->faker->numberBetween(0, 1),
            'birthday' => $this->faker->date(),
            'city_id' => 1,
            'district_id' => 100,
            'status' => true,
        ], [], [
            'avatar' => $avatar,
        ]);
        $response->assertStatus(200);
    }

    public function testSignUpUserValidation()
    {
        $response = $this->call('post', '/api/users/register', []);
        $response->assertStatus(422);
    }

    public function testSignInUserSuccess()
    {
        $response = $this->post('/api/users/login', [
            'login_type' => $this->login_type,
            'account' => $this->account,
            'password' => $this->password,
            'treatment_type' => 1,
            'treatment_kind' => 2,
            'onset_date' => date('Y-m-d'),
            'onset_part' => 3,
        ]);
        $response->assertStatus(200);
    }

    public function testSignInUserUseFacebookSuccess()
    {
        $response = $this->post('/api/users/login', [
            'login_type' => $this->login_type,
            'facebook_id' => $this->facebook_id,
            'facebook_token' => '190b67d76d046191f2e9c52c87b867625f602889259ebfb44ddb47bb720402a8',
        ]);
        $response->assertStatus(200);
    }

    public function testSignInUserUseFacebookIncorrectFailure()
    {
        $response = $this->post('/api/users/login', [
            'login_type' => $this->login_type,
            'facebook_id' => '190b67d76d046',
            'facebook_token' => '190b67d76d046191f2e9c52c87b867625f602889259ebfb44ddb47bb720402a8',
        ]);
        $response->assertStatus(401);
    }

    public function testSignInUserIncorrectPasswordFailure()
    {
        $response = $this->post('/api/users/login', [
            'login_type' => $this->login_type,
            'account' => $this->account,
            'password' => '999999',
        ]);
        $response->assertStatus(401);
    }

    public function testAddDeviceTokenWithAPNSuccess()
    {
        $response = $this->post('/api/users/'.$this->user_id.'/device_token', [
            'arn' => 'member-apn',
            'device_token' => md5(time()),
        ]);
        $response->assertStatus(200);
    }

    public function testAddDeviceTokenWithGCMSuccess()
    {
        $response = $this->post('/api/users/'.$this->user_id.'/device_token', [
            'arn' => 'member-gcm',
            'device_token' => md5(time()),
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateUserSuccess()
    {
        try {
            Storage::disk('local')->copy('avatar_test.png', 'avatar.png');
            Storage::disk('local')->copy('avatar_test.jpg', 'avatar.jpg');
        } catch (Exception $e) {
        }
        $avatar_path = storage_path('app/avatar.png');
//        $avatar_path = storage_path('app/avatar.jpg');
        $avatar = new UploadedFile($avatar_path, 'avatar.png', filesize($avatar_path), 'image/png', null, true);
//        $avatar = new UploadedFile($avatar_path, 'avatar.jpg', filesize($avatar_path), 'image/jpeg', null, true);
        $response = $this->call('put', '/api/users/'.$this->user_id, [
            'name' => $this->faker->name,
            'password' => $this->password,
            'birthday' => $this->faker->date(),
            'city_id' => 1,
            'district_id' => 100,
            'status' => true,
        ], [], [
            'avatar' => $avatar,
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateUserVaildationFailure()
    {
        $response = $this->call('put', '/api/users/'.$this->user_id, []);
        $response->assertStatus(422);
    }

    public function testUpdateWithoutUserFailure()
    {
        $response = $this->call('put', '/api/users/-1', [
            'name' => $this->faker->name,
            'password' => $this->password,
            'birthday' => $this->faker->date(),
            'city_id' => 1,
            'district_id' => 100,
            'status' => true,
        ]);
        $response->assertStatus(404);
    }

    public function testRemoveUserSuccess()
    {
        $response = $this->delete('/api/users/account/'.$this->account);
        $response->assertStatus(200);
    }

    public function testRemoveUserFailure()
    {
        $response = $this->delete('/api/users/account/not_exists_account');
        $response->assertStatus(400);
    }
}
