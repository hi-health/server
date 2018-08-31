<?php

use App\Parameter;
use Illuminate\Support\Facades\Storage;

class ParameterTableSeeder extends Seeder
{
    public function run()
    {
        try {
            Parameter
                ::where('type', 'city')
                ->delete();
            $taiwan_cities = Storage
                ::disk('local')
                ->get('taiwan_cities.json');
            collect(json_decode($taiwan_cities))->each(function($item, $index) {
                Parameter::create([
                    'type' => 'city',
                    'key' => $item->id,
                    'value' => [
                        'name' => $item->name,
                        'districts' => $item->districts
                    ],
                ]);
            });
        } catch(Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
