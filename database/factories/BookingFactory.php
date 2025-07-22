<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $taxi = \App\Models\Taxi::inRandomOrder()->first();
        $status = $this->faker->randomElement(['PENDING', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED']);

        return [
            'booking_uuid' => \Illuminate\Support\Str::uuid(),
            'client_id' => \App\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'CLIENT');
            })->inRandomOrder()->first()->id,
            'client_name' => $this->faker->name,
            'pickup_location' => $this->faker->address,
            'pickup_city_id' => \App\Models\City::inRandomOrder()->first()->id,
            'destination_city_id' => \App\Models\City::inRandomOrder()->first()->id,
            'pickup_datetime' => $this->faker->dateTimeBetween('+1 hour', '+3 days'),
            'status' => $status,
            'assigned_taxi_id' => $status === 'PENDING' ? null : $taxi->id,
            'assigned_driver_id' => $status === 'PENDING' ? null : \App\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'DRIVER');
            })->inRandomOrder()->first()->id,
            'estimated_fare' => $this->faker->randomFloat(2, 10, 100),
            'qr_code_data' => json_encode(['code' => \Illuminate\Support\Str::uuid()]),
            'search_tier' => $this->faker->numberBetween(0, 2),
            'taxi_type' => $taxi->type,
        ];
    }
}
