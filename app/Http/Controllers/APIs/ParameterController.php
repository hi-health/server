<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Parameter;
use App\Clinic;

class ParameterController extends Controller
{
    public function getCities()
    {
        $cities = Parameter
            ::where('type', 'city')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->key,
                    'name' => $item->value['name'],
                    'districts' => $item->value['districts'],
                ];
            });

        return response()->json($cities);
    }
	public function getClinic()
	{
		$clinics = Clinic::get();
		
		$clinic = [];
		foreach($clinics as $v){
			if(!array_key_exists($v->location, $clinic))
				$clinic[$v->location] = [];
			
			$clinic[$v->location][] = $v;
		}
		
		return view('shops', ["clinic" => $clinic]);
	}
}
