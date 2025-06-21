<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingApplication;
use App\Models\Taxi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingApplicationFactory extends Factory
{
    protected $model = BookingApplication::class;

    public function definition()
    {
        $drivers = User::whereHas('role', fn($q) => $q->where('name', 'DRIVER'))->get();
        $taxis = Taxi::all();

        Booking::all()->each(function ($booking) use ($drivers, $taxis) {
            // Random number of applications per booking
            $applicants = $drivers->random(rand(1, min(3, $drivers->count())));

            foreach ($applicants as $driver) {
                BookingApplication::create([
                    'booking_id' => $booking->id,
                    'driver_id' => $driver->id,
                    'taxi_id' => $taxis->random()->id,
                ]);
            }
        });

        
        return [
            'booking_id' => Booking::inRandomOrder()->first()?->id ?? Booking::factory(),
            'driver_id' => User::whereHas('role', fn($q) => $q->where('name', 'DRIVER'))->inRandomOrder()->first()?->id ?? User::factory(),
            'taxi_id' => Taxi::inRandomOrder()->first()?->id ?? Taxi::factory(),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
