<?php
namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('mis.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if (auth()->user()->role === 'mis') {
                // Ensure the authenticated user has the Spatie role required by role:mis middleware.
                auth()->user()->syncRoles(['mis']);
                return redirect()->intended(route('mis.dashboard'));
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Unauthorized access.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    /**
     * Log the mis out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/mis/login')->with('status', 'You have been logged out safely.');
    }
}
