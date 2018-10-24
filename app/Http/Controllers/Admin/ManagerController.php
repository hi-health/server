<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Parameter;
use App\User;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function showListPage(Request $request)
    {
        $per_page = $request->get('per_page', 20);
        $users = User
            ::with('manager')
            ->where('login_type', 3)
            ->orderBy('id', 'DESC')
            ->paginate($per_page);

        return view('admin.managers.list', [
            'users' => $users,
        ]);
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

        return view('admin.managers.add', [
            'cities' => $cities,
        ]);
    }

    public function showEditForm($manager_id)
    {
        $user = User
            ::with('manager')
            ->where('id', $manager_id)
            ->where('login_type', 3)
            ->first();
        if (!$user) {
            return redirect()
                ->route('admin-managers-list');
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

        return view('admin.managers.edit', [
            'user' => $user,
            'cities' => $cities,
        ]);
    }
}
