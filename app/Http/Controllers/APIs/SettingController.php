<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getAll()
    {
        $settings = Setting
            ::where('type', 'setting')
            ->get()
            ->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => $setting->getValue('value'),
                ];
            });

        return response()->json($settings);
    }

    public function save(Request $request)
    {
        $inputs = $request->input();
        $settings = Setting
            ::where('type', 'setting')
            ->whereIn('key', array_keys($inputs))
            ->get()
            ->keyBy('key');
        $rules = $settings->map(function ($setting) {
            $rules = $setting->getValue('rules', []);

            return [
                $setting->key => $rules,
            ];
        })->collapse()
        ->toArray();
        $this->validate($request, $rules);
        collect($inputs)->each(function ($value, $key) use ($settings) {
            $setting = $settings->get($key);
            if ($setting) {
                $setting->setValue($value);
                $setting->save();
            }
        });

        return response()->json([
            'updated' => true,
        ]);
    }

    public function saveBanner(Request $request)
    {
        $images = collect($request->file('images', []));
        $redirect_url = collect($request->input('redirect_url', []));
        $banners = Setting
            ::where('type', 'banner')
            ->orderBy('key', 'ASC')
            ->get();
        $banners->each(function ($banner) use ($images, $redirect_url) {
            $value = [
                'image' => $banner->getValue('image'),
                'redirect_url' => $redirect_url->get($banner->key),
            ];
            $image_file = $images->get($banner->key);
            if ($image_file && @is_array(getimagesize($image_file))) {
                $folder_name = 'banners';
                $path = public_path($folder_name);
                $name = 'img_banner_'.sprintf('%02d', $banner->key).'.png';
                $image_file->move($path, $name);
            }
            $banner->value = $value;
            $banner->save();
        });
    }
}
