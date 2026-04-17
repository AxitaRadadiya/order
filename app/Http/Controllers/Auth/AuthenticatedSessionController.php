<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Generate a new unique session token for this login
        $newToken = Str::random(60);

        // Save it to the user record — this invalidates any other active session
        $user->forceFill(['session_token' => $newToken])->save();

        // Store the token in the current session so middleware can verify it
        $request->session()->put('session_token', $newToken);

        // Set allowed modules in session based on role / customer type.
        // Admins get full access (no restriction). Retailer customer type gets items and orders only.
        try {
            if ($user->hasRole('admin')) {
                $request->session()->forget('allowed_modules');
            } elseif ($user->customerType && strcasecmp($user->customerType->name, 'retailer') === 0) {
                $request->session()->put('allowed_modules', ['items', 'orders']);
            } else {
                $request->session()->forget('allowed_modules');
            }
        } catch (\Throwable $e) {
            // Don't let session-setting failures block login; fall back to no restrictions.
            $request->session()->forget('allowed_modules');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Clear session token from DB on logout so no device remains "active"
        if ($user) {
            $user->forceFill(['session_token' => null])->save();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}