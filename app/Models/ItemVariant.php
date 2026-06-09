<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class ItemVariant extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'item_id',
        'color_id',
        'size_id',
        'quantity',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
