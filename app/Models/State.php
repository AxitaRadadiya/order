<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class State extends Model
{
    use LogsActivity;
    protected $fillable =
    [
        'country_id',
        'name'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
