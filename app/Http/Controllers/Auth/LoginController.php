<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $adminPassword = env('ADMIN_PASSWORD', 'password123');

        if ($request->email === $adminEmail && $request->password === $adminPassword) {
            session(['admin_authenticated' => true]);
            session(['admin_email' => $request->email]);
            
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        session()->forget(['admin_authenticated', 'admin_email']);
        
        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }
}
