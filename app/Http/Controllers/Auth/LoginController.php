<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{


    use AuthenticatesUsers;


    // protected $redirectTo = '/home';
    // protected function redirectTo()
    // {
    //     if (auth()->user()->role === 'admin') {
    //         return '/admin/dashboard';
    //     }

    //     if (auth()->user()->role === 'customer') {
    //         return '/customer/dashboard';
    //     }

    //     return '/';
    // }

    public function showLoginForm(Request $request)
    {
        if ($request->has('redirect')) {
            session(['url.intended' => $request->redirect]);
        }

        return view('auth.login');
    }




    protected function authenticated(Request $request, $user)
    {
        if (session()->has('url.intended')) {
            return redirect()->intended();
        }

        // Fallback: redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'customer') {
            return redirect('/customer/dashboard');
        }

        return redirect('/');
    }








    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
