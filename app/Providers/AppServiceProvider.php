<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;               // ✅ added
use Illuminate\Auth\Events\Logout;              // ✅ added
use Illuminate\Support\Facades\Event;           // ✅ added
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
        // ✅ Log Login / Logout events automatically
        Event::listen(Login::class,  [\App\Listeners\LogAuthActivity::class, 'handleLogin']);
        Event::listen(Logout::class, [\App\Listeners\LogAuthActivity::class, 'handleLogout']);
    }
}