<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\Taxi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log; // For logging assignment attempts

class AssignTaxiToBooking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;
    protected $currentSearchTier;
    protected $maxSearchTiers;

    /**
     * Create a new job instance.
     *
     * @param Booking $booking The booking to assign a taxi to.
     * @param int $currentSearchTier The current tier (hops) to search for taxis.
     * @param int $maxSearchTiers The maximum tier to search before giving up.
     */
    public function __construct(Booking $booking, int $currentSearchTier = 0, int $maxSearchTiers = 2)
    {
        $this->booking = $booking;
        $this->currentSearchTier = $currentSearchTier;
        $this->maxSearchTiers = $maxSearchTiers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Reload the booking to ensure we have the latest state
        $this->booking->refresh();

        // If booking is already assigned or cancelled, do nothing.
        if ($this->booking->status !== 'PENDING') {
            Log::info("Booking {$this->booking->booking_uuid} already handled. Status: {$this->booking->status}");
            return;
        }

        $pickupCity = $this->booking->pickup_city;
        $taxiType = $this->booking->taxi_type;

        Log::info("Attempting to assign taxi for booking {$this->booking->booking_uuid} in tier {$this->currentSearchTier}.");

        $citiesToSearch = $this->getCitiesForTier($pickupCity, $this->currentSearchTier);

        $assignedTaxi = Taxi::whereIn('city', $citiesToSearch)
            ->where('type', $taxiType)
            ->where('is_available', true)
            ->inRandomOrder() // Or use more sophisticated logic (e.g., nearest available via coordinates)
            ->first();

        if ($assignedTaxi) {
            // Taxi found, assign it
            $assignedTaxi->update(['is_available' => false]);
            $this->booking->update([
                'assigned_taxi_id' => $assignedTaxi->id,
                'assigned_driver_id' => $assignedTaxi->driver_id,
                'status' => 'ASSIGNED',
            ]);
            Log::info("Taxi {$assignedTaxi->license_plate} assigned to booking {$this->booking->booking_uuid} from city {$assignedTaxi->city}.");

            // You might want to send a real-time notification to the driver here using WebSockets
            // E.g., event(new \App\Events\NewBookingAssigned($this->booking));
        } else {
            Log::info("No taxi found for booking {$this->booking->booking_uuid} in tier {$this->currentSearchTier}.");

            // No taxi found in current tier, try next tier if available
            if ($this->currentSearchTier < $this->maxSearchTiers) {
                // Dispatch the job again for the next tier after a delay
                $delay = ($this->currentSearchTier + 1) * 30; // e.g., 30s delay for tier 1, 60s for tier 2
                Log::info("Retrying assignment for booking {$this->booking->booking_uuid} in next tier (Tier " . ($this->currentSearchTier + 1) . ") in {$delay} seconds.");
                AssignTaxiToBooking::dispatch($this->booking, $this->currentSearchTier + 1, $this->maxSearchTiers)->delay(now()->addSeconds($delay));
            } else {
                // No taxi found after all attempts
                $this->booking->update(['status' => 'NO_TAXI_FOUND']); // Or a different status like 'FAILED_ASSIGNMENT'
                Log::warning("No taxi could be assigned for booking {$this->booking->booking_uuid} after all attempts.");

                // You might want to send a notification to the client or an operator here
            }
        }
    }

    /**
     * Get cities to search based on the current tier.
     * This is a simplified BFS-like approach based on the proximity map.
     * @param string $startCity
     * @param int $tier
     * @return array
     */
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
