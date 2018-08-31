<?php

namespace App;

use Illuminate\Support\Facades\Cache;

trait City
{
    private $cache_key = 'city-{id}';

    public function city()
    {
        return $this->hasOne(Parameter::class, 'key', 'city_id')
            ->where('type', 'city');
    }

    public function getCityAttribute()
    {
        $city = $this->getCityFromCache();

        if (!$city) {
            $city = $this->city()
                ->first();
            if ($city) {
                $this->addCityToCache($city);
            }
        }
        if ($city) {
            return $city->name;
        }

        return null;
    }

    public function getDistrictAttribute()
    {
        $city = $this->getCityFromCache();
        if (!$city) {
            $city = $this->city()
                ->first();
        }
        if ($city) {
            $district = collect($city->districts)
                ->where('id', $this->district_id)
                ->first();
            if ($district) {
                return $district['name'];
            }
        }

        return null;
    }

    protected function getCityFromCache()
    {
        return Cache::get(
            $this->getCacheCityKey($this->attributes['city_id'])
        );
    }

    protected function getCacheCityKey($city_id)
    {
        return strtr($this->cache_key, [
            '{id}' => $city_id,
        ]);
    }

    protected function addCityToCache(Parameter $city)
    {
        return Cache::add(
            $this->getCacheCityKey($city->key), $city, 600
        );
    }
}
