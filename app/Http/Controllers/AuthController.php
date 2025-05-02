<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        $userData = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $userData['password'] = bcrypt($userData['password']);
        $user = User::create($userData);
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function login_email(Request $request): RedirectResponse
    {
        $credetentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credetentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    public function login_username(Request $request): RedirectResponse
    {
        $credetentials = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credetentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
