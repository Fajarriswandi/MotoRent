<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;   // Tambahkan ini
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm(Request $request)
    {
        if ($request->has('redirect')) {
            session(['url.intended' => $request->redirect]);
        }

        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        // 🔹 Tambahkan Log untuk debug
        Log::info('User berhasil login', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'role'    => $user->role
        ]);

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