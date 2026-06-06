<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class TaxMaster extends Model
{
    use LogsActivity;
    protected $fillable = [
        'tax_name',
        'tax_percentage',
    ];
}
