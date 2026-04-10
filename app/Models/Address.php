<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'customer_id',
        'billing_attention',
        'billing_street',
        'billing_city',
        'billing_state',
        'billing_pin_code',
        'billing_country',
        'billing_gst_number',
        'same_as',
        'shipping_attention',
        'shipping_street',
        'shipping_city',
        'shipping_state',
        'shipping_pin_code',
        'shipping_country',
        'shipping_gst_number'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
