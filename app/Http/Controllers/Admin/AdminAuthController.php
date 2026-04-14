<?php
// app/Http/Controllers/Admin/AdminAuthController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login'); // Make sure you have this view file
    }

    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to login
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            // Check if the authenticated user is admin or manager
            $user = Auth::user();
            
            if ($user->isAdmin() || $user->isManager()) {
                // Regenerate session to prevent session fixation
                $request->session()->regenerate();
                
                // Update last login timestamp
                $user->update(['last_login' => now()]);
                
                // Redirect to intended page or admin dashboard
                return redirect()->intended('/admin/dashboard'); // Adjust this path as needed
            }
            
            // If user is not admin/manager, logout and show error
            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have permission to access the admin panel.',
            ])->onlyInput('email');
        }

        // If login attempt fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}