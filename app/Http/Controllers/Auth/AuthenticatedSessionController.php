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

        // Set allowed modules in session derived from role permissions.
        try {
            $role = $user->role;

            // Super-admin / admin: no restriction
            if ($user->hasRole(['super-admin', 'admin'])) {
                $request->session()->forget('allowed_modules');
            } elseif ($role) {
                $permissionNames = $role->permissions()->pluck('name')->map(fn($n) => strtolower($n))->toArray();

                // If role has a catch-all permission name like `all-modules` or `all`, grant full access
                if (in_array('all-modules', $permissionNames, true) || in_array('all', $permissionNames, true)) {
                    $request->session()->forget('allowed_modules');
                    // short-circuit — full access
                    return redirect()->intended(route('dashboard', absolute: false));
                }

                $modules = [];

                // Fixed permissions for retailer and distributor roles
                if ($user->hasRole(['retailer', 'distributor'])) {
                    $modules = ['catalog', 'orders'];

                    // If the distributor/retailer role also has customer-* permissions,
                    // include the customers module in allowed modules.
                    $rolePerms = $role ? $role->permissions()->pluck('name')->map(fn($n) => strtolower($n))->toArray() : [];
                    if (collect($rolePerms)->contains(fn($p) => str_starts_with($p, 'customer-'))) {
                        $modules[] = 'customers';
                    }
                } else {
                    foreach ($permissionNames as $perm) {
                        if (str_starts_with($perm, 'item-')) {
                            $modules[] = 'items';
                        }

                        if (str_starts_with($perm, 'order-')) {
                            $modules[] = 'orders';
                        }

                        if (str_starts_with($perm, 'catalog-') || $perm === 'catalog') {
                            $modules[] = 'catalog';
                        }

                        if (str_starts_with($perm, 'dashboard-')) {
                            $modules[] = 'dashboard';
                        }

                        if (str_starts_with($perm, 'user-')) {
                            $modules[] = 'users';
                        }

                        if (str_starts_with($perm, 'customer-')) {
                            $modules[] = 'customers';
                        }

                        if (str_starts_with($perm, 'setting-')) {
                            $modules[] = 'settings';
                        }

                        if (str_starts_with($perm, 'report-')) {
                            $modules[] = 'reports';
                        }

                        if (str_starts_with($perm, 'role-') || str_starts_with($perm, 'permission-')) {
                            $modules[] = 'settings';
                        }
                    }
                }

                $modules = array_values(array_unique(array_map(fn($m) => strtolower($m), $modules)));

                if (empty($modules)) {
                    $request->session()->forget('allowed_modules');
                } else {
                    $request->session()->put('allowed_modules', $modules);
                }
            } else {
                $request->session()->forget('allowed_modules');
            }
        } catch (\Throwable $e) {
            $request->session()->forget('allowed_modules');
        }

        // If retailer or distributor logged in, send them to the catalog
        try {
            if ($user->hasRole(['retailer', 'distributor'])) {
                return redirect()->intended(route('catalog', absolute: false));
            }
        } catch (\Throwable $e) {
            // ignore and fall back to dashboard
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