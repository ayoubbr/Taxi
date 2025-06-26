<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'address',
        'logo',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function taxis()
    {
        return $this->hasMany(Taxi::class);
    }

    // Relation pour obtenir les chauffeurs (users avec role de driver)
    public function drivers()
    {
        return $this->users()->whereHas('role', function ($query) {
            $query->where('name', 'DRIVER');
        });
    }

    // Relation pour obtenir les bookings via les taxis de l'agence
    public function bookings()
    {
        return $this->hasManyThrough(
            Booking::class,
            Taxi::class,
            'agency_id', // Foreign key on taxis table
            'assigned_taxi_id', // Foreign key on bookings table
            'id', // Local key on agencies table
            'id' // Local key on taxis table
        );
    }

    // Méthode pour obtenir les statistiques de l'agence
    public function getStats()
    {
        return [
            'total_drivers' => $this->drivers()->count(),
            'active_drivers' => $this->drivers()->where('status', 'active')->count(),
            'total_taxis' => $this->taxis()->count(),
            'available_taxis' => $this->taxis()->where('is_available', true)->count(),
            'total_bookings' => $this->bookings()->count(),
            'completed_bookings' => $this->bookings()->where('status', 'COMPLETED')->count(),
            'pending_bookings' => $this->bookings()->where('status', 'PENDING')->count(),
            'revenue_total' => $this->bookings()->where('status', 'COMPLETED')->sum('estimated_fare'),
            'revenue_month' => $this->bookings()
                ->where('status', 'COMPLETED')
                ->whereMonth('bookings.created_at', now()->month)
                ->sum('estimated_fare'),
        ];
    }
}
