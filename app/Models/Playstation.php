<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playstation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function device()
    {
        return $this->hasMany(Device::class);
    }

    public function hourlyPrices()
    {
        return $this->hasMany(HourlyPrice::class);
    }

    /**
     * Get the price for a specific hour
     *
     * @param int $hour
     * @return int|null
     */
    public function getPriceForHour($hour)
    {
        $hourlyPrice = $this->hourlyPrices()->where('hour', $hour)->first();
        return $hourlyPrice ? $hourlyPrice->price : null;
    }

    /**
     * Get all available hours with their prices
     *
     * @return array
     */
    public function getAvailableHoursWithPrices()
    {
        return $this->hourlyPrices()
            ->orderBy('hour')
            ->pluck('price', 'hour')
            ->toArray();
    }

    /**
     * Calculate total price for given hours using custom hourly pricing
     *
     * @param int $hours
     * @return int
     */
    public function calculateTotalPrice($hours)
    {
        $total = 0;
        $availablePrices = $this->getAvailableHoursWithPrices();

        // If exact hour is available, use that price
        if (isset($availablePrices[$hours])) {
            return $availablePrices[$hours];
        }

        // If not found, we can use the base price (current harga) as fallback
        // or throw an exception depending on business rule
        return $this->harga ? $this->harga * $hours : 0;
    }
}
