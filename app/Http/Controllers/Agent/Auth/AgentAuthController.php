<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgentAuthController extends Controller
{
    public function showLogin()
    {
        return view('agent.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        // Only allow role=agent
        $credentials['role'] = 'agent';

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('agent.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not an agent account.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('agent.login');
    }
}
