<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function form()
    {
        return auth()->check() ? redirect()->route('home') : view('auth.login');
    }

    public function login(Request $r)
    {
        $c = $r->validate(['username' => 'required|string', 'password' => 'required|string']);
        if (Auth::attempt($c, $r->boolean('ingat'))) {
            $r->session()->regenerate();
            $r->session()->forget('token_ok');
            return redirect()->route('home');
        }
        return back()->withErrors(['username' => 'Username atau kata sandi salah.'])->onlyInput('username');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function home()
    {
        $u = auth()->user();
        return redirect()->route(match (true) {
            $u->hasRole('siswa') => 'siswa.token',
            $u->hasRole('pengawas') => 'admin.monitor',
            default => 'admin.dashboard',
        });
    }
}
