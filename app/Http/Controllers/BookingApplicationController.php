<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingApplication;
use App\Notifications\DriverAppliedNotification; // <-- 1. Import the new notification class
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification; // <-- 2. Import the Notification facade

class BookingApplicationController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $driver = Auth::user();

        // Validation
        if ($driver->user_type !== 'DRIVER') {
            return back()->with('error', 'Only drivers can apply for bookings.');
        }

        if ($booking->status !== 'PENDING') {
            return back()->with('error', 'This booking is no longer available for application.');
        }

        if ($booking->hasDriverApplied($driver->id)) {
            return back()->with('error', 'You have already applied for this booking.');
        }

        $taxi = $driver->taxi;
        if (!$taxi || !$taxi->is_available) {
            return back()->with('error', 'You do not have an available taxi to take this booking.');
        }

        // Create the application
        BookingApplication::create([
            'booking_id' => $booking->id,
            'driver_id' => $driver->id,
            'taxi_id' => $taxi->id,
        ]);

        // --- 3. Notify the client that a new driver has applied ---
        // We use the `client` relationship we defined on the Booking model
        if ($booking->client) { // Make sure a client is associated with the booking
            Notification::send($booking->client, new DriverAppliedNotification($booking, $driver));
        }

        return redirect()->route('driver.bookings.available')->with('success', 'You have successfully applied for the booking.');
    }
}
