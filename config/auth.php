<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        
        'agent' => [
            'driver' => 'session',
            'provider' => 'agents',
        ],
        
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
        
        'support' => [
            'driver' => 'session',
            'provider' => 'supports',
        ],
        
        'charge' => [
            'driver' => 'session',
            'provider' => 'charges',
        ],
        
        'mis' => [
            'driver' => 'session',
            'provider' => 'mis',
        ],
        
        'mis-manager' => [
            'driver' => 'session',
            'provider' => 'mis_managers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        
        'agents' => [
            'driver' => 'eloquent',
            'model' => App\Models\Agent::class,
        ],
        
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
        
        'supports' => [
            'driver' => 'eloquent',
            'model' => App\Models\Support::class,
        ],
        
        'charges' => [
            'driver' => 'eloquent',
            'model' => App\Models\Charge::class,
        ],
        
        'mis' => [
            'driver' => 'eloquent',
            'model' => App\Models\Mis::class,
        ],
        
        'mis_managers' => [
            'driver' => 'eloquent',
            'model' => App\Models\MisManager::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        
        'agents' => [
            'provider' => 'agents',
            'table' => 'agent_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];