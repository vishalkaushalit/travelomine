<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if any guard is authenticated
        if (!Auth::check() && !Auth::guard('agent')->check() && !Auth::guard('admin')->check()) {
            if ($request->is('agent/*')) {
                return redirect()->route('agent.login');
            }
            return redirect('/');
        }

        // Determine the role based on which guard is authenticated
        $userRole = null;
        if (Auth::guard('agent')->check()) {
            $userRole = 'agent';
        } elseif (Auth::guard('admin')->check()) {
            $userRole = 'admin';
        } elseif (Auth::guard('support')->check()) {
            $userRole = 'support';
        } elseif (Auth::guard('charge')->check()) {
            $userRole = 'charge';
        } elseif (Auth::guard('mis')->check()) {
            $userRole = 'mis';
        } elseif (Auth::guard('mis-manager')->check()) {
            $userRole = 'mis-manager';
        } elseif (Auth::check()) {
            $userRole = Auth::user()->role ?? 'user';
        }

        if (!$userRole || !in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}