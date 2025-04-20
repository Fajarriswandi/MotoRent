<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'motorbike_id',
        'start_date',
        'end_date',
        'total_price',
        'price_day',
        'is_approved',
        'is_completed',
        'is_cancelled',
    ];



    protected $casts = [
        'is_cancelled' => 'boolean',
    ];


    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
