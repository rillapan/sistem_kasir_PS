<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HourlyPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'playstation_id',
        'hour',
        'price'
    ];

    public function playstation()
    {
        return $this->belongsTo(Playstation::class);
    }
}
