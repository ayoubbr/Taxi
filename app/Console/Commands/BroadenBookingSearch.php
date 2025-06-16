<?php

namespace App\Console\Commands;

use App\Models\Booking;
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

        $bookings = Booking::withCount('applications')
            ->where('status', 'PENDING')
            ->where('search_tier', '<', $maxTiers)
            ->where('created_at', '<', now()->addMinutes(15))
            ->having('applications_count', '=', 0)
            ->get();

        Log::info($bookings);

        foreach ($bookings as $booking) {
            $booking->increment('search_tier');
            $newTier = $booking->search_tier;

            Log::info("Broadening search for Booking #{$booking->id} to Tier {$newTier}");

            // Find drivers in the new tier and notify them
            $citiesToSearch = $this->getCitiesForTier($booking->pickup_city, $newTier);
            Log::info('Search cities ::::::::: ', $citiesToSearch);

            $driversToNotify = User::where('user_type', 'DRIVER')
                ->whereHas('taxi', function ($query) use ($citiesToSearch, $booking) {
                    $query->whereIn('city', $citiesToSearch)
                        ->where('type', $booking->taxi_type);
                })->get();

            // Exclude drivers who have already been notified in a previous tier
            // (More complex logic needed here if you want to avoid re-notifying)
            Notification::send($driversToNotify, new NewBookingAvailable($booking));
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
}
