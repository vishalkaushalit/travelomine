<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle an admin login request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Attempt to login
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user has admin or manager role (access to admin panel)
            if ($user->isAdmin() || $user->isManager()) {
                // Regenerate session to prevent session fixation
                $request->session()->regenerate();
                
                // Update last login timestamp
                $user->update(['last_login' => now()]);
                
                // Redirect to admin dashboard
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back, ' . $user->name . '!');
            }
            
            // If user is not admin/manager, logout and show error
            Auth::logout();
            return redirect()->back()
                ->withErrors([
                    'email' => 'You do not have permission to access the admin panel.',
                ])
                ->withInput($request->except('password'));
        }

        // If login attempt fails
        return redirect()->back()
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->withInput($request->except('password'));
    }

    /**
     * Log the user out of the application
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')
            ->with('success', 'You have been successfully logged out.');
    }
}