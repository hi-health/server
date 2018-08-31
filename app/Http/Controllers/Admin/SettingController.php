<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Setting;

class SettingController extends Controller
{
    public function showSettingsForm()
    {
        $settings = Setting
            ::where('type', 'setting')
            ->get();
        $banners = Setting
            ::where('type', 'banner')
            ->orderBy('key', 'ASC')
            ->get();

        return view('admin.settings.form', [
            'settings' => $settings,
            'banners' => $banners,
        ]);
    }
}
