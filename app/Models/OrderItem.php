<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_master_id','item_id','item_name','description','unit','quantity','rate','tax_rate','final_price','total'
    ];

    public function order()
    {
        return $this->belongsTo(OrderMaster::class, 'order_master_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
