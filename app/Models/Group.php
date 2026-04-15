<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Group extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
    ];
}
