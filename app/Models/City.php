<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function taxis()
    {
        return $this->hasMany(Taxi::class);
    }

    public function bookings_pickup()
    {
        return $this->hasMany(Booking::class, 'pickup_city_id');
    }

    public function bookings_destination()
    {
        return $this->hasMany(Booking::class, 'destination_city_id');
    }
}
