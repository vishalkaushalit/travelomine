<?php

// app/Listeners/LogSuccessfulLogout.php
namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Services\ActivityLogger;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        if ($event->user && ($event->user->role ?? null) !== 'admin') {
            ActivityLogger::log(
                'user',
                'logout',
                $event->user->name . ' logged out',
                'User',
                $event->user->id
            );
        }
    }
}
