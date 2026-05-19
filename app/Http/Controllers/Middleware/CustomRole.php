<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated with any guard
        $guards = ['agent', 'admin', 'support', 'charge', 'mis', 'mis-manager', 'web'];
        $isAuthenticated = false;
        $userRole = null;
        
        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                $isAuthenticated = true;
                $userRole = $guard;
                break;
            }
        }
        
        if (!$isAuthenticated) {
            // Redirect to appropriate login page based on request path
            if ($request->is('agent/*')) {
                return redirect()->route('agent.login');
            }
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login');
            }
            if ($request->is('support/*')) {
                return redirect()->route('support.login');
            }
            if ($request->is('charge/*')) {
                return redirect()->route('charge.login');
            }
            if ($request->is('mis/*')) {
                return redirect()->route('mis.login');
            }
            if ($request->is('mis-manager/*')) {
                return redirect()->route('mis-manager.login');
            }

            // if ($request->is('changes/*')) {
            //     return redirect()->route('changes.login');
            // }
            
            abort(403, 'You must be logged in to access this page.');
        }

        // Check if user has any of the allowed roles
        $allowedRoles = is_array($roles) ? $roles : explode('|', $roles[0]);
        
        if (!in_array($userRole, $allowedRoles)) {
            $roleList = implode(', ', $allowedRoles);
            abort(403, "Access denied. Allowed roles: {$roleList}");
        }
        
        return $next($request);
    }
}