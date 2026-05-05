<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Size extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
    ];

    /**
     * Return an ordered list of active size labels (names) from DB.
     *
     * Used by controllers to provide a simple array/collection for views/javascript.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function activeLabels(): \Illuminate\Support\Collection
    {
        return self::orderBy('name')->pluck('name')->values();
    }

    public function sets(): BelongsToMany
    {
        return $this->belongsToMany(Set::class, 'set_size')->withTimestamps();
    }
}
