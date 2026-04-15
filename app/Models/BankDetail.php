<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class BankDetail extends Model
{
    use LogsActivity;
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
