<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_uuid',
        'client_id',
        'client_name',
        'pickup_location',
        'pickup_city_id',
        'destination_city_id',
        'pickup_datetime',
        'status',
        'assigned_taxi_id',
        'assigned_driver_id',
        'estimated_fare',
        'qr_code_data',
        'search_tier',
        'taxi_type',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'qr_code_data' => 'json', // Cast QR code data to JSON
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function taxi()
    {
        return $this->belongsTo(Taxi::class, 'assigned_taxi_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function pickupCity()
    {
        return $this->belongsTo(City::class, 'pickup_city_id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    public function applications()
    {
        return $this->hasMany(BookingApplication::class);
    }

    public function hasDriverApplied($driverId)
    {
        return $this->applications()->where('driver_id', $driverId)->exists();
    }
}
