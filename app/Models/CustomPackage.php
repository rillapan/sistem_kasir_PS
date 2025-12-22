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
        'price_group_id',
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

    public function devices()
    {
        return $this->belongsToMany(Playstation::class, 'custom_package_playstation')
            ->withPivot('lama_main')
            ->withTimestamps();
    }

    public function priceGroup()
    {
        return $this->belongsTo(PriceGroup::class);
    }

    public function priceGroups()
    {
        return $this->belongsToMany(PriceGroup::class, 'custom_package_price_group')
            ->withTimestamps();
    }

    /**
     * Get FnB items filtered by the package's price group
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableFnbs()
    {
        if ($this->price_group_id) {
            return Fnb::where('price_group_id', $this->price_group_id)->get();
        }
        
        return Fnb::all();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
