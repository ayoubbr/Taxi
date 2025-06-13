<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\NewBookingAvailable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class BroadenBookingSearch extends Command
{
    protected $signature = 'bookings:broaden-search';
    protected $description = 'Broaden the search tier for pending bookings that have not been applied for';

    public function handle()
    {
        $this->info('Checking for bookings to broaden search...');

        $maxTiers = config('cities.search_tiers.2'); // Assuming 2 is the max tier

        // Find pending bookings older than X minutes that haven't reached max tier
        $bookings = Booking::where('status', 'PENDING')
            ->where('applications_count', 0) // Only if no one has applied
            ->where('search_tier', '<', $maxTiers)
            ->where('created_at', '<', now()->subMinutes(15)) // e.g., 15 minutes
            ->withCount('applications')
            ->get();

        foreach ($bookings as $booking) {
            $booking->increment('search_tier');
            $newTier = $booking->search_tier;

            $this->info("Broadening search for Booking #{$booking->id} to Tier {$newTier}");

            // Find drivers in the new tier and notify them
            $citiesToSearch = $this->getCitiesForTier($booking->pickup_city, $newTier);

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
        // You can copy the logic from your AssignTaxiToBooking job here
        $proximityMap = config('cities.proximity_map');
        if ($tier === 0) return [$startCity];

        // Simplified logic for example
        if (isset($proximityMap[$startCity])) {
            return $proximityMap[$startCity];
        }

        return [];
    }
}
