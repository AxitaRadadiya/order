<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, LogsActivity;               // ✅ LogsActivity added

    protected $fillable = ['name'];

    // ── Relationships ─────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
}