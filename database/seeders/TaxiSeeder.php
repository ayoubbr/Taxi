<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Taxi;
use App\Models\User;

class TaxiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driver1 = User::where('user_type', 'DRIVER')->first();
        $driver2 = User::where('username', 'another.driver')->first();

        Taxi::create([
            'license_plate' => 'ABC-123',
            'model' => 'Toyota Camry',
            'type' => 'standard',
            'capacity' => 4,
            'city' => 'Marrakesh', // Example city
            'driver_id' => $driver1 ? $driver1->id : null,
            'is_available' => true,
        ]);

        Taxi::create([
            'license_plate' => 'XYZ-789',
            'model' => 'Mercedes Sprinter',
            'type' => 'van',
            'capacity' => 7,
            'city' => 'Casablanca', // Another example city
            'driver_id' => $driver2 ? $driver2->id : null,
            'is_available' => true,
        ]);

        Taxi::create([
            'license_plate' => 'LXC-456',
            'model' => 'BMW 7 Series',
            'type' => 'luxe',
            'capacity' => 4,
            'city' => 'Marrakesh', // Another example city
            'driver_id' => null, // No driver assigned yet
            'is_available' => true,
        ]);
    }
}
