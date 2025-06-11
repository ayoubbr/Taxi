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
        'pickup_city',
        'destination',
        'pickup_datetime',
        'status',
        'assigned_taxi_id',
        'assigned_driver_id',
        'estimated_fare',
        'qr_code_data',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'qr_code_data' => 'json', // Cast QR code data to JSON
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function assignedTaxi()
    {
        return $this->belongsTo(Taxi::class, 'assigned_taxi_id');
    }

    public function assignedDriver()
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }
}
