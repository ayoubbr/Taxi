<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'email',
        'firstname',
        'lastname',
        'status',
        'role_id',
        'is_active',
        'agency_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function taxi()
    {
        return $this->hasOne(Taxi::class, 'driver_id'); // If a driver has one taxi
    }

    public function clientBookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function assignedDriverBookings()
    {
        return $this->hasMany(Booking::class, 'assigned_driver_id');
    }

    public function applications()
    {
        return $this->hasMany(BookingApplication::class, 'driver_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role()->where('name', $roleName)->exists();
    }

    public function bookings()
    {
        return $this->clientBookings->merge($this->assignedDriverBookings);
    }
}
