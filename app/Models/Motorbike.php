<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motorbike extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'year',
        'color',
        'license_plate',
        'rental_price_hour',
        'rental_price_day',
        'rental_price_week',
        'technical_status', // ✅ tambahkan ini!
        'image',
    ];


    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function isCurrentlyRented()
    {
        $today = now()->toDateString();
        return $this->rentals()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('is_cancelled', false)
            ->exists();
    }


}
