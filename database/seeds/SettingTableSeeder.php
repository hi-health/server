<?php

use App\Setting;

class SettingTableSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            'membership_fee' => [
                'name' => '會員費',
                'type' => 'number',
                'value' => '1200',
                'rules' => ['required', 'integer'],
                'placeholder' => '請輸入會員費',
                'weight' => 1,
            ],
            'income_percentage' => [
                'name' => '本月治療收入百分比',
                'type' => 'number',
                'value' => '0.7',
                'rules' => ['required', 'numeric'],
                'placeholder' => '請輸入百分比，例如70%，請輸入0.7',
                'weight' => 2,
            ],
            'service_email' => [
                'name' => '客服信箱',
                'type' => 'email',
                'value' => 'service@hi-health.com.tw',
                'rules' => ['required', 'email'],
                'placeholder' => '請輸入客服信箱',
                'weight' => 3,
            ],
            'treatment_days_1' => [
                'name' => '高級服務的治療天數',
                'type' => 'number',
                'value' => '30',
                'rules' => ['required', 'integer'],
                'placeholder' => '請輸入高級服務的治療天數',
                'weight' => 4,
            ],
            'treatment_days_2' => [
                'name' => '一般服務的治療天數',
                'type' => 'number',
                'value' => '30',
                'rules' => ['required', 'integer'],
                'placeholder' => '請輸入一般服務的治療天數',
                'weight' => 5,
            ],
            'treatment_time_1' => [
                'name' => '高級服務的治療時間,單位為分鐘',
                'type' => 'number',
                'value' => '45',
                'rules' => ['required', 'integer'],
                'placeholder' => '請輸入一般服務的治療時間',
                'weight' => 6,
            ],
            'treatment_time_2' => [
                'name' => '一般服務的治療時間,單位為分鐘',
                'type' => 'number',
                'value' => '30',
                'rules' => ['required', 'integer'],
                'placeholder' => '請輸入高級服務的治療時間',
                'weight' => 7,
            ],
            'message_expire_time' => [
                'name' => '醫師最晚回覆時間,單位為分鐘',
                'type' => 'number',
                'value' => 12 * 60,
                'rules' => ['required', 'integer'],
                'placeholder' => '請輸入醫師最晚回覆時間',
                'weight' => 8,
            ]
            
        ];
        Setting
            ::where('type', 'setting')
            ->delete();
        collect($settings)->each(function ($value, $name) {
            Setting::create([
                'type' => 'setting',
                'key' => $name,
                'value' => $value,
            ]);
        });
        $banners = [
            [
                'image' => '/banners/img_banner_01.png',
                'redirect_url' => 'http://www.google.com',
            ],
            [
                'image' => '/banners/img_banner_02.png',
                'redirect_url' => 'http://www.google.com',
            ],
            [
                'image' => '/banners/img_banner_03.png',
                'redirect_url' => 'http://www.google.com',
            ],
            [
                'image' => '/banners/img_banner_04.png',
                'redirect_url' => 'http://www.google.com',
            ],
            [
                'image' => '/banners/img_banner_05.png',
                'redirect_url' => 'http://www.google.com',
            ],
        ];
        Setting
            ::where('type', 'banner')
            ->delete();
        collect($banners)->each(function ($value, $index) {
            Setting::create([
                'type' => 'banner',
                'key' => $index + 1,
                'value' => $value,
            ]);
        });
    }
}
