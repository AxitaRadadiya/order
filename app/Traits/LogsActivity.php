<?php

namespace App\Traits;

use App\Services\ActivityLogService;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        // ── Created ──────────────────────────────────────
        static::created(function ($model) {
            ActivityLogService::created($model);
        });

        // ── Updated ──────────────────────────────────────
        static::updated(function ($model) {
            ActivityLogService::updated($model);
        });

        // ── Deleted ──────────────────────────────────────
        static::deleted(function ($model) {
            ActivityLogService::deleted($model);
        });
    }
}