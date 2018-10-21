<?php

namespace App\Http\Controllers\APIs;

use Hash;
use App\User;
use App\PointProduce;
use App\PointConsume;
use Exception;
// use Illuminate\Support\MessageBag;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PointController extends Controller
{
    public function index($users_id)
    {
        $PointProduce = PointProduce::where('users_id', $users_id)
                            ->sum('point');

        $PointConsume = PointConsume::where('users_id', $users_id)
                            ->sum('point');

        $RemainedPoint = $PointConsume + $PointProduce;

        return view('points.detail', compact('users_id','RemainedPoint'));

    }

    public function showListProduce($users_id)
    {
        $PointProduce = PointProduce::where('users_id', $users_id)
                            ->sum('point');

        $PointConsume = PointConsume::where('users_id', $users_id)
                            ->sum('point');

        $RemainedPoint = $PointConsume + $PointProduce;

        $PointProduce_FromDaily = PointProduce::where('users_id', $users_id)
                                    ->whereNull('pointconsume_id')->orderByDESC('created_at')->get();

        $PointProduce_FromUser = PointProduce::where('users_id', $users_id)
                                    ->whereNull('service_plan_daily_id')->orderByDESC('created_at')->get();

        return view('points.list_point_produce', compact('users_id', 'RemainedPoint', 'PointProduce_FromDaily','PointProduce_FromUser'));
    }

    public function showListConsume($users_id)
    {
        $PointProduce = PointProduce::where('users_id', $users_id)
                        ->sum('point');

        $PointConsume = PointConsume::where('users_id', $users_id)
                        ->sum('point');

        $RemainedPoint = $PointConsume + $PointProduce;

        $PointConsume = PointConsume::where('users_id', $users_id)
                        ->orderByDESC('created_at')->get();

        // $PointProduce = PointConsume::where('users_id', $users_id)->transaction()

        return view('points.list_point_consume', compact('users_id', 'RemainedPoint', 'PointConsume'));
    }

    public function showListAllTransaction($users_id)
    {
        $PointProduce = PointProduce::where('users_id', $users_id)
                        ->sum('point');

        $PointConsume = PointConsume::where('users_id', $users_id)
                        ->sum('point');

        $RemainedPoint = $PointConsume + $PointProduce;

        $PointProduce = PointProduce::where('users_id', $users_id)
                        ->get();

        $PointConsume = PointConsume::where('users_id', $users_id)
                        ->get();

        $collection = collect([$PointProduce,$PointConsume]);

        $Transaction = $collection
                        ->collapse()
                        ->sortByDESC('created_at')
                        ->values()
                        ->all();

        // $PointConsume_id = PointConsume::where('users_id')->transaction()->where('pointconsume_id')->get();

        return view('points.list_all_transaction', compact('users_id', 'Transaction', 'RemainedPoint'));
    }

    public function showTransfer($users_id)
    {
        $PointProduce = PointProduce::where('users_id', $users_id)
                            ->sum('point');

        $PointConsume = PointConsume::where('users_id', $users_id)
                            ->sum('point');

        $RemainedPoint = $PointConsume + $PointProduce;

        return view('points.point_transfer', compact('users_id','RemainedPoint'));
    }

    public function PointTransfer(Request $request, $users_id)
    {
        \Log::INFO("sucess");
        $this->validate($request, [
            'receiver_account' => 'required', 
            'transferred_point' => 'required|integer',
            'password' => ['required', 'string'],
        ]);
        $user = User::where('id', $users_id)
                ->where('status', 1)
                ->first();
        if (!$user) {
            return response()->json('wrong account', 401);
        }

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // 密碼是對的不做事等09行回覆
        } else {
            $user = null;
            return response()->json('wrong password', 401);
        }
        if($user){
            $receiver = User::where('account',$request->receiver_account)
                            ->first();
            if($receiver)
            {
                $receiver_id = $receiver->id;
            }else{
                return response()->json('wrong receiver', 400);
            }

            try {
                DB::beginTransaction();

                $PointConsume = new PointConsume();
                $PointConsume->users_id = $users_id;
                $PointConsume->point = -$request->transferred_point;
                $PointConsume->save();

                $PointProduce = new PointProduce();
                $PointProduce->users_id = $receiver_id;
                $PointProduce->point = $request->transferred_point;
                $PointProduce->pointconsume_id = $PointConsume->id;
                $PointProduce->save();

                $PointProduce = PointProduce::where('users_id', $users_id)->sum('point');
                $PointConsume = PointConsume::where('users_id', $users_id)->sum('point');
                $RemainedPoint = $PointConsume + $PointProduce;
                if($RemainedPoint<0)
                {
                    // throw new Exception("Error Processing Request", 1);
                    return back(); 
                }

                DB::commit();

            } catch (QueryException $exception) {
                DB::rollback();
                // $errors = new Illuminate\Support\MessageBag();
                // return back()->withErrors(['點數不足']);
                return response()->json(null, 500);
            }
        }

        return view('points.point_transfer', compact('RemainedPoint','users_id'));
    }

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

    public function getRemainedPoint($users_id)
    {
        $PointProduce = PointProduce::where('users_id', $users_id)->sum('point');

        $PointConsume = PointConsume::where('users_id', $users_id)->sum('point');

        $RemainedPoint = $PointConsume + $PointProduce;

        return response()->json($RemainedPoint);
    }

    public function getHistoryByUsersId($users_id)
    {
        $PointProduce = PointProduce::where('users_id', $users_id)->get();

        $PointConsume = PointConsume::where('users_id', $users_id)->get();

        $collection = collect([$PointProduce,$PointConsume]);

        $sorted = $collection
            ->collapse()
            ->sortByDESC('created_at')
            ->values()
            ->all();
        return response()->json($sorted);
    }

    public function getAllPoint()
    {
        $all_point = PointProduce::whereNotNull('service_plan_daily_id')
                        ->sum('point');                       
        return response()->json($all_point);
    }
}
