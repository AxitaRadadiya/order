<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;

class Item extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'name',
        'article_number',
        'item_code',
        'color',
        'sizes',
        'images',           // ✅ NEW: JSON array of up to 5 image paths
        'image',            // legacy single-image column (kept for backward compatibility)
        'description',
        'category_id',
        'group_id',
        'sub_category',
        'sub_group',
        'unit',
        'price',
        'tax_percent',
        'status',
        'show_item_on_web',
    ];

    protected $casts = [
        'price'            => 'decimal:2',
        'tax_percent'      => 'decimal:2',
        'status'           => 'boolean',
        'show_item_on_web' => 'boolean',
        'sizes'            => 'array',   // stored as JSON in DB
        'images'           => 'array',   // ✅ stored as JSON array in DB
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Only active items — use in any public / external-system query.
     * Example: Item::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Returns the URL of the first (primary) image, or null.
     */
    public function getImageUrlAttribute(): ?string
    {
        // Prefer the images[] array, fall back to legacy single `image` column
        $path = null;

        if (!empty($this->images) && is_array($this->images)) {
            $path = $this->images[0];
        } elseif (!empty($this->image)) {
            $path = $this->image;
        }

        return $path ? asset('storage/' . $path) : null;
    }

    /**
     * Returns an array of all image URLs (up to 5).
     */
    public function getImageUrlsAttribute(): array
    {
        $paths = [];

        if (!empty($this->images) && is_array($this->images)) {
            $paths = $this->images;
        } elseif (!empty($this->image)) {
            $paths = [$this->image];
        }

        return array_map(fn($p) => asset('storage/' . $p), $paths);
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    public static function generateItemCode(?string $name = null): string
    {
        $prefix = 'ITM';

        if ($name) {
            $clean  = preg_replace('/[^A-Za-z0-9]/', '', $name);
            $prefix = strtoupper(substr($clean, 0, 3)) ?: 'ITM';
        }

        do {
            $code = $prefix . strtoupper(Str::random(6));
        } while (self::where('item_code', $code)->exists());

        return $code;
    }

    /**
     * Generate a simple sequential item code starting from 0001.
     * Uses the current max `id` as a basis so codes are predictable.
     *
     * @param int $digits Number of digits, default 4 -> 0001
     */
    public static function generateSequentialCode(int $digits = 4): string
    {
        $maxId = (int) self::max('id');
        $next  = $maxId + 1;
        return str_pad((string) $next, $digits, '0', STR_PAD_LEFT);
    }
}