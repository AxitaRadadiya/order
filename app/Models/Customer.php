<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'website',
        'password',
        'payment_terms',
        'gst_number',
        'discount',
        'gst_treatment',
        'place_of_supply',
        'pan_number',
        'credit_limit'
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class);
    }
}
