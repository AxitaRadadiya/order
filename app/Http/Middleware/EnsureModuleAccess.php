<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureModuleAccess
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('module:catalog')
     */
    public function handle(Request $request, Closure $next, string $module)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        // Super-admin / admin bypass
        if ($user->hasRole(['super-admin', 'admin'])) {
            return $next($request);
        }

        // Check session-stored modules
        $allowed = $request->session()->get('allowed_modules');

        if (is_array($allowed) && in_array(strtolower($module), $allowed, true)) {
            return $next($request);
        }

        // Fallback: derive from role permissions
        try {
            $role = $user->role;
            if ($role) {
                $perms = $role->permissions()->pluck('name')->map(fn($n) => strtolower($n))->toArray();
                foreach ($perms as $p) {
                    if (str_starts_with($p, $module . '-') || $p === $module) {
                        return $next($request);
                    }
                    // special mapping
                    if ($module === 'items' && str_starts_with($p, 'item-')) {
                        return $next($request);
                    }
                    if ($module === 'orders' && str_starts_with($p, 'order-')) {
                        return $next($request);
                    }
                    if ($module === 'customers' && str_starts_with($p, 'customer-')) {
                        return $next($request);
                    }

                    if ($module === 'settings' && (str_starts_with($p, 'role-') || str_starts_with($p, 'permission-') || str_starts_with($p, 'setting-'))) {
                        return $next($request);
                    }

                    if ($module === 'reports' && str_starts_with($p, 'report-')) {
                        return $next($request);
                    }
                    if ($module === 'catalog' && (str_starts_with($p, 'catalog-') || $p === 'catalog')) {
                        return $next($request);
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore and deny
        }

        abort(403);
    }
}
