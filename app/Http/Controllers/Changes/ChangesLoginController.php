<?php
namespace App\Http\Controllers\Change;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangesLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('change.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if (auth()->user()->role === 'change') {
                // Ensure the authenticated user has the Spatie role required by role:change middleware.
                auth()->user()->syncRoles(['change']);
                return redirect()->intended(route('change.dashboard'));
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Unauthorized access.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    /**
     * Log the change out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/change/login')->with('status', 'You have been logged out safely.');
    }
}
