<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function loginAuth(Request $request)
    {
        $this->validate($request, [
            'account' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        $account = $request->input('account');
        $password = $request->input('password');
        if (Auth::guard('admin')->attempt([
            'account' => $account,
            'password' => $password,
            'login_type' => 0,
            'status' => true,
        ])) {
            error_log('This is some useful information.');
            error_log(Auth::user() -> role );
            error_log(Auth::user());
            if (Auth::user() && Auth::user() -> role == 2){
                return redirect() ->route('admin-doctors-list');
            }
            return redirect()
                ->route('dashboard');
        }

        return redirect()->route('admin-login')
            ->withErrors([
                'account' => trans('auth.failed'),
            ]);
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()
            ->route('admin-login');
    }
}
