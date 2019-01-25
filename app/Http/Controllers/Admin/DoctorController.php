<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use App\Parameter;
use App\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function showListPage(Request $request)
    {
        if(Auth::user()->role == 1){
            $per_page = $request->get('per_page', 20);
            $users = User
                ::with('doctor')
                ->where('login_type', 2)
                ->orderBy('id', 'DESC')
                ->paginate($per_page);

            return view('admin.doctors.list', [
                'users' => $users,
            ]);
        }
        elseif(Auth::user()->manager->doctor){
            $tmp = [];
            foreach (Auth::user()->manager->doctor as $value) {
                $tmp[] = $value->user->id;
            }
            
            $per_page = $request->get('per_page', 20);
            $users = User::with('doctor')
                ->where('login_type', 2)
                ->whereIn('id', $tmp)
                ->orderBy('id', 'DESC')
                ->paginate($per_page);

            return view('admin.doctors.list', [
                'users' => $users,
            ]);
        }
    }

    public function showAddForm()
    {
        $cities = Parameter
            ::where('type', 'city')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->key,
                    'name' => $item->value['name'],
                    'districts' => $item->value['districts'],
                ];
            });

        return view('admin.doctors.add', [
            'cities' => $cities,
        ]);
    }

    public function showEditForm($doctor_id)
    {
        $user = User::with('doctor')
            ->where('id', $doctor_id)
            ->where('login_type', 2)
            ->first();
        if (!$user) {
            return redirect()
                ->route('admin-doctors-list');
        }
        $cities = Parameter::where('type', 'city')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->key,
                    'name' => $item->value['name'],
                    'districts' => $item->value['districts'],
                ];
            });

        return view('admin.doctors.edit', [
            'user' => $user,
            'cities' => $cities,
        ]);
    }
}
