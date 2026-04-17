<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;
use App\Models\User;

class BankDetail extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id',
        'bank_name',
        'ifsc_code',
        'branch_name',
        'account_no'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
