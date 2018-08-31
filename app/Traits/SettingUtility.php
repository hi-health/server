<?php

namespace App\Traits;

use App\Setting;

trait SettingUtility
{
    protected function getSetting($name, $default = null)
    {
        $setting = Setting
            ::where('type', 'setting')
            ->where('key', $name)
            ->first();
        if ($setting) {
            return $setting->getValue('value');
        }

        return $default;
    }
}
