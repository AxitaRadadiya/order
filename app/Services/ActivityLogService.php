<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log any action.
     *
     * @param  string       $action      login|logout|created|updated|deleted
     * @param  string|null  $description Human-readable summary
     * @param  Model|null   $model       The Eloquent model affected (optional)
     * @param  array        $oldValues   Previous values (for updates)
     * @param  array        $newValues   New values (for creates / updates)
     */
    public static function log(
        string  $action,
        ?string $description = null,
        ?Model  $model       = null,
        array   $oldValues   = [],
        array   $newValues   = []
    ): void {
        try {
            $user = Auth::user();

            ActivityLog::create([
                'user_id'     => $user?->id,
                'user_name'   => $user?->name ?? 'System',
                'action'      => $action,
                'description' => $description,
                'model_type'  => $model ? get_class($model) : null,
                'model_id'    => $model?->getKey(),
                'model_label' => $model ? self::modelLabel($model) : null,
                'old_values'  => empty($oldValues) ? null : $oldValues,
                'new_values'  => empty($newValues) ? null : $newValues,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the app
            \Illuminate\Support\Facades\Log::error('ActivityLog failed: ' . $e->getMessage());
        }
    }

    // ── Convenience shortcuts ─────────────────────────────────────

    public static function login(): void
    {
        $user = Auth::user();
        self::log('login', "User [{$user->name}] logged in.");
    }

    public static function logout(): void
    {
        $user = Auth::user();
        self::log('logout', "User [{$user->name}] logged out.");
    }

    public static function created(Model $model): void
    {
        $label = self::modelLabel($model);
        self::log(
            'created',
            class_basename($model) . " [{$label}] was created.",
            $model,
            [],
            self::filterAttributes($model->getAttributes())
        );
    }

    public static function updated(Model $model): void
    {
        $dirty    = $model->getDirty();
        $original = array_intersect_key($model->getOriginal(), $dirty);

        if (empty($dirty)) return;

        $label = self::modelLabel($model);
        self::log(
            'updated',
            class_basename($model) . " [{$label}] was updated.",
            $model,
            self::filterAttributes($original),
            self::filterAttributes($dirty)
        );
    }

    public static function deleted(Model $model): void
    {
        $label = self::modelLabel($model);
        self::log(
            'deleted',
            class_basename($model) . " [{$label}] was deleted.",
            $model,
            self::filterAttributes($model->getAttributes()),
            []
        );
    }

    private static function modelLabel(Model $model): string
    {
        foreach (['name', 'title', 'email', 'label', 'subject'] as $field) {
            if (! empty($model->{$field})) {
                return $model->{$field};
            }
        }
        return '#' . $model->getKey();
    }

    /**
     * Remove sensitive fields before storing.
     */
    private static function filterAttributes(array $attrs): array
    {
        $hidden = ['password', 'remember_token', 'token', 'secret', 'api_key'];
        return array_diff_key($attrs, array_flip($hidden));
    }
}