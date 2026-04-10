<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SingleDeviceSession
{

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user            = Auth::user();
            $sessionToken    = $request->session()->get('session_token');
            $dbToken         = $user->session_token;

            // Token mismatch = another device has logged in
            if (empty($sessionToken) || empty($dbToken) || $sessionToken !== $dbToken) {
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('status', 'You have been logged out because your account was signed in on another device.');
            }
        }

        return $next($request);
    }
}