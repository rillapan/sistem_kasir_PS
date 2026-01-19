<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'fnb_id',
        'type',
        'qty',
        'date',
        'note'
    ];

    public function fnb()
    {
        return $this->belongsTo(Fnb::class);
    }
}
