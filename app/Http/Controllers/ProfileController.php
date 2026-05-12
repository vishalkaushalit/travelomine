<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $views = [
            'admin' => 'admin.profile.index',
            'manager' => 'manager.profile.index',
            'agent' => 'agent.profile.index',
            'charge' => 'charge.profile.index',
            'support' => 'support.profile.index',
            'mis' => 'mis.profile.index',
            'mis-manager' => 'mis-manager.profile.index',
            'changes' => 'changes.profile.index',
        ];

        if (isset($views[$user->role])) {
            return view($views[$user->role], compact('user'));
        }

        return redirect()->route('public.home');
    }
}