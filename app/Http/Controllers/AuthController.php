<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('nama_user', 'password');

        if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return back()->with('loginSuccess', true);
    }

    return back()->with('loginError', 'Username atau password salah.');


    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
