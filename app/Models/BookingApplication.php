<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingApplication extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'driver_id', 'taxi_id'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function taxi()
    {
        return $this->belongsTo(Taxi::class);
    }
}
