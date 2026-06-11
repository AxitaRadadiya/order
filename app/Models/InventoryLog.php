<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    protected $fillable = [
        'item_variant_id',
        'order_master_id',
        'type',
        'qty',
        'note',
        'created_by',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function itemVariant(): BelongsTo
    {
        return $this->belongsTo(ItemVariant::class, 'item_variant_id');
    }

    public function orderMaster(): BelongsTo
    {
        return $this->belongsTo(OrderMaster::class, 'order_master_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

