<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        
        // Redirect unauthenticated users
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('agent/*')) {
                return route('agent.login');
            }
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('support/*')) {
                return route('support.login');
            }
            if ($request->is('charge/*')) {
                return route('charge.login');
            }
            if ($request->is('mis/*')) {
                return route('mis.login');
            }
            if ($request->is('mis-manager/*')) {
                return route('mis-manager.login');
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();