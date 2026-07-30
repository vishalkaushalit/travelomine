<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure PHP uses the application timezone so date/time helpers use it
        date_default_timezone_set(config('app.timezone'));

        // View composer for notifications bell
        \Illuminate\Support\Facades\View::composer('components.notifications-bell', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $unreadCount = $user->unreadNotifications()->count();
                $notifications = $user->unreadNotifications()->take(5)->get();
            } else {
                $unreadCount = 0;
                $notifications = collect();
            }

            $view->with(compact('unreadCount', 'notifications'));
        });
    }
}
