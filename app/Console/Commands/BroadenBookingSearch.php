<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\City;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewBookingAvailable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class BroadenBookingSearch extends Command
{
    protected $signature = 'bookings:broaden-search';
    protected $description = 'Broaden the search tier for pending bookings that have not been applied for';

    public function handle()
    {
        Log::info('Checking for bookings to broaden search...');

        $maxTiers = config('cities.search_tiers.2'); // Assuming 2 is the max tier

        $bookings = Booking::with('pickupCity')
            ->withCount('applications')
            ->where('status', 'PENDING')
            ->where('search_tier', '<', $maxTiers)
            ->where('created_at', '>', now()->subHours(24))
            ->having('applications_count', '=', 0)
            ->get();


        if ($bookings->isEmpty()) {
            Log::info('No bookings to broaden.');
            return;
        }

        $driverRoleId = Role::where('name', 'DRIVER')->value('id');
        if (!$driverRoleId) {
            Log::error('Driver role not found.');
            return;
        }

        // foreach ($bookings as $key => $value) {
        //     Log::info("booking uuid :::::: $value->booking_uuid |||| ");
        //     Log::info("booking taxi type :::::: $value->taxi_type |||| ");
        // }

        foreach ($bookings as $booking) {
            // Check if pickupCity relationship is loaded to avoid errors
            if (!$booking->pickupCity) {
                Log::warning("Booking #{$booking->id} is missing a valid pickup city relationship.");
                continue;
            }

            $booking->increment('search_tier');
            // $newTier = $booking->search_tier;
            Log::info("Broadening search for Booking #{$booking->id} to Tier {$booking->search_tier}");

            // Find drivers in the new tier and notify them
            // $citiesToSearch = $this->getCitiesForTier($booking->pickup_city, $newTier);

            // 1. Get city names for the new tier
            $cityNamesToSearch = $this->getCitiesForTier($booking->pickupCity->name, $booking->search_tier);
            if (empty($cityNamesToSearch)) {
                continue;
            }
            Log::info('Search cities ::::::::: ', $cityNamesToSearch);

            // $driverRoleId = Role::where('name', 'DRIVER')->first()->id;

            // 2. Convert city names to IDs in one query
            $cityIds = City::whereIn('name', $cityNamesToSearch)->pluck('id');
            // foreach ($citiesToSearch as $key => $value) {
            //     $city = City::where('name', $value)->first();
            //     if ($city) {
            //         $cityId = $city->id;
            //         array_push($citiesIds, $cityId);
            //     }
            // }
            Log::info('Search cities ids ::::: ', $cityIds);


            // $driversToNotify = User::where('role_id', $driverRoleId)
            //     ->whereHas('taxi', function ($query) use ($citiesToSearch, $booking) {
            //         $query->whereIn('city', $citiesToSearch)
            //             ->where('type', $booking->taxi_type);
            //     })->get();


            // $driversToNotify = User::where('role_id', $driverRoleId)
            //     ->whereHas('taxi', function ($query) use ($citiesIds, $booking) {
            //         $query->whereIn('city_id', $citiesIds)
            //             ->where('type', $booking->taxi_type);
            //     })->get();

            // 3. Find drivers in those cities
            $driversToNotify = User::where('role_id', $driverRoleId)
                ->whereHas('taxi', function ($query) use ($cityIds, $booking) {
                    $query->whereIn('city_id', $cityIds)
                        ->where('type', $booking->taxi_type)
                        ->where('is_available', true);
                })->get();

            Log::info("Found " . $driversToNotify->count() . " drivers to notify for booking #{$booking->id}.");
            Log::info('Drivers :::::::::::::::  ', $driversToNotify->toArray());


            // Exclude drivers who have already been notified in a previous tier
            // (More complex logic needed here if you want to avoid re-notifying)
            // Notification::send($driversToNotify, new NewBookingAvailable($booking));
        }

        $this->info('Done.');
    }

    protected function getCitiesForTier(string $startCity, int $tier): array
    {
        $proximityMap = config('cities.proximity_map');
        $cities = [$startCity]; // Always include the start city in tier 0

        if ($tier === 0) {
            return [$startCity];
        }

        $visited = [$startCity => 0]; // City => distance from startCity
        $queue = new \SplQueue();
        $queue->enqueue($startCity);

        $currentTierCities = [];

        while (!$queue->isEmpty()) {
            $city = $queue->dequeue();
            $distance = $visited[$city];

            if ($distance == $tier) {
                $currentTierCities[] = $city;
            }

            if ($distance < $tier && isset($proximityMap[$city])) {
                foreach ($proximityMap[$city] as $neighbor) {
                    if (!isset($visited[$neighbor])) {
                        $visited[$neighbor] = $distance + 1;
                        $queue->enqueue($neighbor);
                    }
                }
            }
        }
        return $currentTierCities;
    }

    //   protected function getCitiesForTier(string $startCity, int $tier): array
    // {
    //     // This function logic remains the same as it works with names
    //     $proximityMap = config('cities.proximity_map');
    //     if (!isset($proximityMap[$startCity])) return [];

    //     if ($tier === 0) return [$startCity];
    //     if ($tier === 1) return $proximityMap[$startCity];

    //     // For more complex tiers (>=2), a full graph traversal is needed.
    //     // The previous logic for that was fine. For simplicity, this handles up to tier 1.
    //     $tierOneCities = $proximityMap[$startCity];
    //     $tierTwoCities = [];
    //     foreach($tierOneCities as $city) {
    //         if(isset($proximityMap[$city])) {
    //             $tierTwoCities = array_merge($tierTwoCities, $proximityMap[$city]);
    //         }
    //     }
    //     $allNeighbors = array_unique(array_merge($tierOneCities, $tierTwoCities));

    //     return array_values(array_diff($allNeighbors, [$startCity]));
    // }
}
