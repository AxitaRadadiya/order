<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthActivity
{
    /**
     * Handle successful login.
     */
    public function handleLogin(Login $event): void
    {
        // Auth::user() may not be set yet at this point,
        // so we use the event's user directly.
        \App\Models\ActivityLog::create([
            'user_id'     => $event->user->id,
            'user_name'   => $event->user->name,
            'action'      => 'login',
            'description' => "User [{$event->user->name}] logged in.",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    /**
     * Handle logout.
     */
    public function handleLogout(Logout $event): void
    {
        if (! $event->user) return;

        \App\Models\ActivityLog::create([
            'user_id'     => $event->user->id,
            'user_name'   => $event->user->name,
            'action'      => 'logout',
            'description' => "User [{$event->user->name}] logged out.",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}