<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, LogsActivity;               // ✅ LogsActivity added

    protected $fillable = ['name'];

    // ── Relationships ─────────────────────────────

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
}