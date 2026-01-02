<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            if ($user->role === 'super_admin') {
                return redirect()->intended('/admin/dashboard')
                    ->with('success', 'Welcome back, ' . $user->name . '! You have successfully logged in.');
            }

            return redirect()->intended('/organization/dashboard')
                ->with('success', 'Welcome back, ' . $user->name . '! You have successfully logged in.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $userName = Auth::user()->name;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Goodbye, ' . $userName . '! You have been logged out successfully.');
    }
}
