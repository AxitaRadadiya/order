<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'customer_id',
        'bank_name',
        'ifsc_code',
        'branch_name',
        'account_no'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
