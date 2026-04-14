<?php
// app/Http/Middleware/CustomRole.php

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
        if (!auth()->check()) {
            abort(403, 'You must be logged in to access this page.');
        }

        // Check if user has any of the allowed roles
        $allowedRoles = is_array($roles) ? $roles : explode('|', $roles[0]);
        
        if (!in_array(auth()->user()->role, $allowedRoles)) {
            $roleList = implode(', ', $allowedRoles);
            abort(403, "Access denied. Allowed roles: {$roleList}");
        }
        
        return $next($request);
    }
}