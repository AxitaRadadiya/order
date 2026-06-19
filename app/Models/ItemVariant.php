<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class ItemVariant extends Model
{
    use LogsActivity;

    protected $fillable = [
        'item_id',
        'color_id',
        'size_id',
        'quantity',          // Current stock
        'production_quantity',
        'sold_quantity',
    ];

    protected $appends = ['total_production', 'total_sold', 'current_stock'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function getTotalProductionAttribute()
    {
        return (int) $this->production_quantity;
    }

    public function getTotalSoldAttribute()
    {
        return (int) $this->sold_quantity;
    }

    public function getCurrentStockAttribute()
    {
        return (int) $this->quantity;
    }

    public function syncStockFromLogs()
    {
        $production = (int) $this->inventoryLogs()->where('type', 'restock')->sum('qty');
        $deducted = (int) $this->inventoryLogs()->where('type', 'deduct')->sum('qty');
        $restored = (int) $this->inventoryLogs()->where('type', 'restore')->sum('qty');
        
        $this->production_quantity = $production;
        $this->sold_quantity = $deducted - $restored;
        $this->quantity = $production - $this->sold_quantity;  // Current stock
        $this->save();
    }
}