<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Customer extends Model
{
    use LogsActivity;
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
        'credit_limit',
        'role_id',
    ];

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class);
    }

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
