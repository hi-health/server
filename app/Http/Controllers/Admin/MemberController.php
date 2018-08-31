<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function showListPage(Request $request)
    {
        $per_page = $request->get('per_page', 20);
        $users = User
            ::where('login_type', 1)
            ->orderBy('id', 'DESC')
            ->paginate($per_page);

        return view('admin.members.list', [
            'users' => $users,
        ]);
    }

    public function showDetailPage($member_id)
    {
        $member = User
            ::where('id', $member_id)
            ->where('login_type', 1)
            ->first();
        if (!$member) {
            return redirect()
                ->route('admin-members-list');
        }

        return view('admin.members.detail', [
            'member' => $member,
        ]);
    }
}
