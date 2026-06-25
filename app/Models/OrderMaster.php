<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderMaster extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'eway_bill_number',
        'transport_number',
        'lr_number',
        'billing_address',
        'shipping_address',
        'subtotal',
        'discount',
        'markdown',
        'adjustment',
        'tax_id',
        'tax_percentage',
        'grand_total',
        'terms',
        'notes',
        'status',
        'expected_date',
        'distributor_id',
        'distributor_approved',
        'distributor_approved_at',
        'visible_to_superadmin',
        'approval_level'
    ];

    protected $casts = [
        'date' => 'date',
        'expected_date' => 'date',
        'distributor_approved' => 'boolean',
        'distributor_approved_at' => 'datetime',
        'visible_to_superadmin' => 'boolean',
        'approval_level' => 'integer',
        'tax_percentage' => 'decimal:2',
    ];

  
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function tax()
    {
        return $this->belongsTo(TaxMaster::class, 'tax_id');
    }

   
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class, 'order_master_id');
    }

    public function getOrderNumberAttribute(): string
    {
        return 'ORD-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}