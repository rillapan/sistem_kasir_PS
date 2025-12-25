<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_shift',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i:s',
        'jam_selesai' => 'datetime:H:i:s',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if a given time is within this shift
     */
    public function isWithinShiftTime($time)
    {
        $time = \Carbon\Carbon::parse($time);
        $start = \Carbon\Carbon::parse($this->jam_mulai);
        $end = \Carbon\Carbon::parse($this->jam_selesai);

        // Handle overnight shifts (e.g., 22:00 to 06:00)
        if ($end->lessThan($start)) {
            return $time->greaterThanOrEqualTo($start) || $time->lessThanOrEqualTo($end);
        }

        return $time->between($start, $end);
    }

    /**
     * Get shift duration in hours
     */
    public function getDurationInHours()
    {
        $start = \Carbon\Carbon::parse($this->jam_mulai);
        $end = \Carbon\Carbon::parse($this->jam_selesai);
        
        // Handle overnight shifts
        if ($end->lessThan($start)) {
            $end = $end->addDay();
        }
        
        return $start->diffInHours($end);
    }
}
