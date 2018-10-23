<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

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
            Log::alert('~~~~~');
            Log::alert(Auth::user());
            if (Auth::check()){
                return redirect() ->route('dashboard');
            }
            return redirect()->route('admin-login')->withErrors([
                                                        'account' => 'Auth::check() false',
                                                    ]);
        }
        elseif (Auth::guard('manager')->attempt([
            'account' => $account,
            'password' => $password,
            'login_type' => -1,
            'status' => true,
        ])) {
            Log::alert('~~~~~');
            Log::alert(Auth::guard('manager')->user());
            if (Auth::guard('manager')->check()){
                return redirect() ->route('admin-doctors-add-form');
            }
            return redirect()->route('admin-login')->withErrors([
                                                        'account' => 'Auth::check() false',
                                                    ]);
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
