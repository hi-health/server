<?php

namespace App\Http\Controllers\APIs;

use App\PointProduce;
use App\PointConsume;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PointController extends Controller
{
	public function CreateDailyPoint($daily_result)
    {
        try {
            DB::beginTransaction();
            $PointProduce = new PointProduce();
            $PointProduce->users_id = $daily_result->users_id;
            $PointProduce->point = $daily_result->point;
            $PointProduce->service_plan_daily_id = $daily_result->service_plan_daily_id;
            $PointProduce->save();
            DB::commit();
        } catch (QueryException $exception) {
            DB::rollback();

            return $exception;
        }
        return 'sucess';
    }

	public function CreateTransactionPoint(Request $request)
    {
        $this->validate($request, [
    		'users_id_from' => ['required', 'integer'],
            'users_id_to' => ['required', 'integer'],
    		'point' => ['required', 'integer'],
        ]);
		try {
            DB::beginTransaction();

            $PointConsume = new PointConsume();
            $PointConsume->users_id = $request->users_id_from;
            $PointConsume->point = -$request->point;
            $PointConsume->save();

            $PointProduce = new PointProduce();
            $PointProduce->users_id = $request->users_id_to;
            $PointProduce->point = $request->point;
            $PointProduce->pointconsume_id = $PointConsume->id;
            $PointProduce->save();

            if($this->getRemainedPoint($request->users_id_from)<0)
            {
                throw new Exception("Error Processing Request", 1);
            }

            DB::commit();

        } catch (QueryException $exception) {
            DB::rollback();

            return response()->json(null, 500);
        }

        return response()->json([$PointProduce, $PointConsume]);    		
    }

    public function getRemainedPoint($users_id)
    {
        $PointProduce = PointProduce
            ::where('users_id', $users_id)
            ->sum('point');

        $PointConsume = PointConsume
            ::where('users_id', $users_id)
            ->sum('point');

        $RemainedPoint = $PointConsume + $PointProduce;

        return response()->json($RemainedPoint);
    }

    public function getHistoryByUsersId($users_id)
    {
        $PointProduce = PointProduce
            ::where('users_id', $users_id)
            ->get();

        $PointConsume = PointConsume
            ::where('users_id', $users_id)
            ->get();

        $collection = collect([$PointProduce,$PointConsume]);

        $sorted = $collection
                    ->collapse()
                    ->sortByDESC('created_at')
                    ->values()
                    ->all();

                return response()->json($sorted);
    }
}
