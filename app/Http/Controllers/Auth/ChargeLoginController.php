<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChargeLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.charge-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if (auth()->user()->role === 'charge') {
                return redirect()->intended(route('charge.dashboard'));
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Unauthorized access.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    /**
     * Log the charge out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/charge/login')->with('status', 'You have been logged out safely.');
    }
}
