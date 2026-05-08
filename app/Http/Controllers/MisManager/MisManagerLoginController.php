<?php
namespace App\Http\Controllers\MisManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MisManagerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('mis-manager.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if (auth()->user()->hasRole('mis-manager')) {
                return redirect()->intended(route('mis-manager.dashboard'));
            }
            
            Auth::logout();
            return back()->withErrors(['email' => 'Unauthorized access.']);
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    /**
     * Log the mis manager out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/mis-manager/login')->with('status', 'You have been logged out safely.');
    }
}
