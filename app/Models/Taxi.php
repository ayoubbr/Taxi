<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxi extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'model',
        'type',
        'city_id',
        'agency_id',
        'capacity',
        'driver_id',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'assigned_taxi_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
