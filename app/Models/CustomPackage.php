<?php

namespace App\Models;

use App\Models\Playstation;
use App\Models\Fnb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'harga_total',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'harga_total' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function playstations()
    {
        return $this->belongsToMany(Playstation::class, 'custom_package_playstation')
            ->withPivot('lama_main')
            ->withTimestamps();
    }

    public function fnbs()
    {
        return $this->belongsToMany(Fnb::class, 'custom_package_fnb')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
