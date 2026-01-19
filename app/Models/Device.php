<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['playstation_names'];

    public function playstations()
    {
        return $this->belongsToMany(Playstation::class, 'device_playstation');
    }

    // For backward compatibility - get the first PlayStation
    public function playstation()
    {
        return $this->belongsTo(Playstation::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope to get only available devices
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Tersedia');
    }

    // Helper method to get PlayStation names as string
    public function getPlaystationNamesAttribute()
    {
        $names = $this->playstations->pluck('nama');
        
        if ($names->isEmpty() && $this->playstation) {
            return $this->playstation->nama;
        }
        
        return $names->implode(', ');
    }
}
