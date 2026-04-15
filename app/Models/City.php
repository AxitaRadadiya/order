<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class City extends Model
{
    use LogsActivity;
    protected $fillable =
    [
        'country_id',
        'state_id',
        'name'
    ];  

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
