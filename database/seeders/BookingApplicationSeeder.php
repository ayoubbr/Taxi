<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingApplication;
use App\Models\Taxi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // BookingApplication::factory()->count(12)->create();
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
    }
}
