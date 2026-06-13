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
        'quantity',
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
        $logs = $this->relationLoaded('inventoryLogs')
            ? $this->inventoryLogs
            : $this->inventoryLogs()->get();

        return (int) $logs->where('type', 'restock')->sum('qty');
    }

   public function getTotalSoldAttribute()
    {
        $logs = $this->relationLoaded('inventoryLogs')
            ? $this->inventoryLogs
            : $this->inventoryLogs()->get();

        $deductTotal = (int) $logs->where('type', 'deduct')->sum('qty');
        $restoreTotal = (int) $logs->where('type', 'restore')->sum('qty');
        
        return $deductTotal - $restoreTotal;
    }

    public function getCurrentStockAttribute()
    {
        return $this->total_production - $this->total_sold;
    }
}


