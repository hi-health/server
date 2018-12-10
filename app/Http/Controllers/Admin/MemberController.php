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
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage('page') ?: 1;
        $startIndex = ($page - 1) * $per_page;


        $users = User
            ::with('pointproduce','pointconsume')
            ->where('login_type', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function($item,$key){
                $item->pointproduce = $item->pointproduce->sum('point');
                $item->pointconsume = $item->pointconsume->sum('point');
                return $item;
            });
        //$users_perPage = array_slice($users, $startIndex, $per_page);

        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
                $users->forPage($page,$per_page), 
                count($users), 
                $per_page,
                $page
            );
        $pagination->setPath(url()->current());


        return view('admin.members.list', [
            'users' => $pagination,
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
