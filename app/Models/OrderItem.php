<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_master_id','item_id','article_number','item_name','description','color','size','size_quantities','quantity','rate','tax_rate','total','status'
    ];

    protected $casts = [
        'size_quantities' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(OrderMaster::class, 'order_master_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function isComplete(): bool
    {
        return in_array($this->status, ['complete', 'delivered']);
    }
}
