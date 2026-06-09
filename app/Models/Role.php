<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, LogsActivity;               // ✅ LogsActivity added

    protected $fillable = ['name', 'created_by'];

    protected static function booted(): void
    {
        static::creating(function ($role) {
            if (empty($role->created_by) && auth()->check()) {
                $role->created_by = auth()->id();
            }
        });
    }

    // ── Relationships ─────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}