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
        'description',
        'category_id',
        'group_id',
        'sub_category',
        'sub_group',
        'unit',
        'price',
        'tax_percent',
        'image',
        'status',
        'show_item_on_web',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'status' => 'boolean',
        'show_item_on_web' => 'boolean',
        'sizes' => 'array',
    ];

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

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return asset('storage/' . $this->image);
    }

    public static function generateItemCode(?string $name = null): string
    {
        $prefix = 'ITM';

        if ($name) {
            $clean = preg_replace('/[^A-Za-z0-9]/', '', $name);
            $prefix = strtoupper(substr($clean, 0, 3)) ?: 'ITM';
        }

        do {
            $code = $prefix . strtoupper(Str::random(6));
        } while (self::where('item_code', $code)->exists());

        return $code;
    }
}
