<?php

namespace App\Models;

use App\Models\Role;
use App\Traits\LogsActivity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity, SoftDeletes;

    public function hasRole(string|array $roles): bool
    {
        $role = $this->role;

        if (! $role) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($role->name, $roles, true);
        }

        return strcasecmp($role->name, $roles) === 0;
    }

    public function assignRole(string|int $role): void
    {
        if (is_int($role)) {
            $this->role_id = $role;
            $this->save();

            return;
        }

        $resolvedRole = Role::where('name', $role)->first();

        if ($resolvedRole) {
            $this->role_id = $resolvedRole->id;
            $this->save();
        }
    }

    public function syncRoles(array $roles = []): void
    {
        if (empty($roles)) {
            $this->role_id = null;
            $this->save();

            return;
        }

        $first = $roles[0];

        if (is_int($first)) {
            $this->role_id = $first;
            $this->save();

            return;
        }

        $resolvedRole = Role::where('name', $first)->first();

        if ($resolvedRole) {
            $this->role_id = $resolvedRole->id;
            $this->save();
        }
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'role_id',
        'customer_type_id',
        'status',
        'mobile',
        'note',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
            'mobile' => 'string',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function getProfileImagePathAttribute(): string
    {
        return $this->profile_image ?: 'admin/dist/img/logo1.png';
    }

    public function getProfileImageUrlAttribute(): string
    {
        $path = $this->profile_image_path;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        return asset($path);
    }
}
