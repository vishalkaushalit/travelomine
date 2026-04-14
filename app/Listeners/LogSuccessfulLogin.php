<?php

// app/Listeners/LogSuccessfulLogin.php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\ActivityLogger;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if (($event->user->role ?? null) !== 'admin') {
            ActivityLogger::log(
                'user',
                'login',
                $event->user->name . ' logged in',
                'User',
                $event->user->id
            );
        }
    }
}
