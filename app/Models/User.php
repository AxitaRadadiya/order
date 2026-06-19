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
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, LogsActivity, SoftDeletes;

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function ($user) {
            if (empty($user->created_by) && auth()->check()) {
                $user->created_by = auth()->id();
            }
        });

        static::saving(function ($user) {
            $user->name = trim(
                ($user->first_name ?? '') . ' ' .
                ($user->last_name ?? '')
            );
        });
    }


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

    public function hasPermission(string|array $permissions): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        $role = $this->relationLoaded('role')
            ? $this->role
            : $this->role()->with('permissions')->first();

        if (! $role) {
            return false;
        }

        $grantedPermissions = $role->relationLoaded('permissions')
            ? $role->permissions
            : $role->permissions()->get();

        $grantedNames = $grantedPermissions
            ->pluck('name')
            ->map(fn ($name) => $this->normalizePermissionName($name))
            ->filter()
            ->values()
            ->all();

        foreach ((array) $permissions as $permission) {
            if (in_array($this->normalizePermissionName($permission), $grantedNames, true)) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        return $this->hasPermission($permissions);
    }

    protected function normalizePermissionName(?string $permission): string
    {
        $permission = Str::of((string) $permission)->lower()->trim()->value();

        if ($permission === '') {
            return '';
        }

        $parts = explode('-', $permission, 2);

        if (count($parts) === 1) {
            return Str::singular($parts[0]);
        }

        return Str::singular($parts[0]) . '-' . $parts[1];
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
        'first_name', 'last_name',
        'email',
        'password',
        'profile_image',
        'role_id',
        'distributor_id',
        'created_by',
        'status',
        'mobile',
        'note',
        'is_active',
        'company_name',
        'phone',
        'website',
        'payment_terms',
        'gst_number',
        'discount',
        'gst_treatment',
        'place_of_supply',
        'pan_number',
        'credit_limit',
        'distributor_verified',
        'distributor_verified_at',
        'shop_name',
        'state_id',
        'city_id',
        'shop_image',
        'pan_card_image',
        'gst_certificate_image',
        'google_location_link',
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
            'distributor_verified' => 'boolean',
            'distributor_verified_at' => 'datetime',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function distributor()
    {
        return $this->belongsTo(self::class, 'distributor_id');
    }

    public function retailers()
    {
        return $this->hasMany(self::class, 'distributor_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'user_id');
    }

    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class, 'user_id');
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

    public function getShopImageUrlAttribute(): string
    {
        if (!$this->shop_image) {
            return '';
        }

        if (Storage::disk('public')->exists($this->shop_image)) {
            return Storage::url($this->shop_image);
        }

        return asset($this->shop_image);
    }

    public function getPanCardImageUrlAttribute(): string
    {
        if (!$this->pan_card_image) {
            return '';
        }

        if (Storage::disk('public')->exists($this->pan_card_image)) {
            return Storage::url($this->pan_card_image);
        }

        return asset($this->pan_card_image);
    }

    public function getGstCertificateImageUrlAttribute(): string
    {
        if (!$this->gst_certificate_image) {
            return '';
        }

        if (Storage::disk('public')->exists($this->gst_certificate_image)) {
            return Storage::url($this->gst_certificate_image);
        }

        return asset($this->gst_certificate_image);
    }



    public function getNameAttribute($value): string
    {
        $first = $this->attributes['first_name'] ?? null;
        $last = $this->attributes['last_name'] ?? null;

        $full = trim((string) ($first ? $first : '') . ' ' . ($last ? $last : ''));

        return $full !== '' ? $full : ($value ?? '');
    }

    public function setNameAttribute($value): void
    {
        $name = trim((string) $value);
        $parts = preg_split('/\s+/', $name);

        $this->attributes['first_name'] = $parts[0] ?? null;
        $this->attributes['last_name'] = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;
        $this->attributes['name'] = $name;
    }
}
