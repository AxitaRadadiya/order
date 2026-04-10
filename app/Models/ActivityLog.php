<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ⚠️  DO NOT add LogsActivity to this model.
//     Logging the log table would cause an infinite loop.

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'description',
        'model_type',
        'model_id',
        'model_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ── Relationships ──────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ────────────────────────────────────

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'login'   => 'Login',
            'logout'  => 'Logout',
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            default   => ucfirst($this->action),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'login'   => 'green',
            'logout'  => 'blue',
            'created' => 'gold',
            'updated' => 'orange',
            'deleted' => 'red',
            default   => 'grey',
        };
    }

    public function getModelNameAttribute(): string
    {
        if (! $this->model_type) return '—';
        return class_basename($this->model_type);
    }
}