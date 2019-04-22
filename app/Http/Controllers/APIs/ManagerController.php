<?php

namespace App\Http\Controllers\APIs;

use App\Manager;
use App\Http\Controllers\Controller;
use App\Member;
use App\MemberRequest;
use App\MemberRequestDoctor;
use App\Message;
use App\Service;
use App\Setting;
use App\PointProduce;
use App\PointConsume;
use App\Traits\MemberUtility;
use App\Traits\SettingUtility;
use App\User;
use Carbon\Carbon;
use DB,Log;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    use MemberUtility,
        SettingUtility;

    public function create(Request $request)
    {
        $this->validate($request, [
            'account' => ['required', 'string', 'unique:users,account'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'name' => ['required', 'string'],
            'male' => ['required', 'in:0,1'],
            'birthday' => ['required'],
            'avatar' => ['image','mimes:jpeg,bmp,png,gif', 'max:5120'],
            'city_id' => ['required'],
            'district_id' => ['required'],
            'status' => ['in:0,1'],
            // 'online' => ['in:0,1'],
            // 'number' => ['string'],
            // 'treatment_type' => ['in:1,2'],
            'treatment_type' => [],
            'bank_account' => ['required', 'string'],
            'phone' => ['required', 'string'],
            // 'education_bonus' => ['numeric', 'min:0'],
            // 'longitude' => [], //, 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            // 'latitude' => [], // 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
        $user = new User($request->input());
        $user->login_type = 3;
        $manager = new Manager($request->input());
        $user->save();
        $user->manager()->save($manager);
        $avatar_file = $request->file('avatar');
        if ($avatar_file) {
            $folder_name = 'managers';
            $path = public_path($folder_name);
            $name = 'avatar_'.$user->id.'.'.$avatar_file->getClientOriginalExtension();
            $avatar_file->move($path, $name);
            $user->avatar = '/'.$folder_name.'/'.$name;
            $user->save();
        }

        return response()->json($user, 200);
    }

    public function update(Request $request, $manager_id)
    {
        $this->validate($request, [
            'name' => ['required', 'string'],
            'male' => ['required', 'in:0,1'],
            'birthday' => ['required'],
            'avatar' => ['nullable', 'image', 'max:10240'],
            'city_id' => ['required'],
            'district_id' => ['required'],
            'status' => ['in:0,1'],
//            'online' => ['in:0,1'],
            'treatment_type' => [],
            'bank_account' => ['required', 'string'],
            'phone' => ['required', 'string'],
//            'longitude' => [], //, 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
//            'latitude' => [], // 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        ]);
        $user = User::with('manager')
            ->where('id', $manager_id)
            ->first();
        if (!$user) {
            return response()->json(null, 404);
        }
        $user->update($request->input());
        $user->manager->update($request->input());
        $avatar_file = $request->file('avatar');
        if ($avatar_file) {
            $folder_name = 'managers';
            $path = public_path($folder_name);
            $name = 'avatar_'.$user->id.'.'.$avatar_file->getClientOriginalExtension();
            $avatar_file->move($path, $name);
            $user->avatar = '/'.$folder_name.'/'.$name;
            $user->save();
        }

        return response()->json([
            'updated' => true,
        ]);
    }
}
