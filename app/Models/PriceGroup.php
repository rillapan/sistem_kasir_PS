<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'deskripsi',
    ];

    public function fnbs()
    {
        return $this->hasMany(Fnb::class);
    }
}
