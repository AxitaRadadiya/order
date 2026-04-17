<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderMaster extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','date','eway_bill_number','transport_number','lr_number',
        'billing_address','shipping_address','subtotal','discount','adjustment','grand_total',
        'terms','notes','status','expected_date'
    ];

    protected $casts = [
        'date' => 'date',
        'expected_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
