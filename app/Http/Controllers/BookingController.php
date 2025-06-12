<?php

namespace App\Http\Controllers;

use App\Jobs\AssignTaxiToBooking;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Taxi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{

    public function create()
    {
        return view('bookings.create'); // If you want a dedicated booking page
    }


    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:100',
            'pickup_location' => 'required|string|max:255',
            'pickup_city' => 'required|string|max:100', // Must be a valid city name from your config
            'destination' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'taxi_type' => 'required|in:standard,van,luxe',
            // 'client_phone' => 'required|string|max:20', // Add this if you want a phone field
        ]);

        $pickupDateTime = $request->input('date') . ' ' . $request->input('time');
        $bookingUuid = (string) Str::uuid();

        // Prepare QR Code Data (JSON format)
        $qrData = [
            'bookingId' => $bookingUuid,
            'clientName' => $request->client_name,
            'pickupLocation' => $request->pickup_location,
            'destination' => $request->destination,
            'pickupDateTime' => $pickupDateTime,
        ];

        // Generate QR Code as SVG string (can be saved to disk if preferred)
        $qrCodeSvg = QrCode::size(250)->generate(json_encode($qrData));

        // Create the booking
        $booking = Booking::create([
            'booking_uuid' => $bookingUuid,
            'client_id' => Auth::id(), // Will be null if guest, or user_id if logged in
            'client_name' => $request->client_name,
            'pickup_location' => $request->pickup_location,
            'pickup_city' => $request->pickup_city, // Store the pickup city
            'destination' => $request->destination,
            'pickup_datetime' => $pickupDateTime,
            'status' => 'PENDING',
            'estimated_fare' => rand(20, 100), // Simple random fare for now
            'qr_code_data' => $qrData, // Store the array, it will be cast to JSON
        ]);


        // // --- Taxi Assignment Logic (Simplified) ---
        // // 1. Try to find an available taxi in the same city
        // $assignedTaxi = Taxi::where('city', $request->pickup_city)
        //     ->where('type', $request->taxi_type)
        //     ->where('is_available', true)
        //     ->inRandomOrder() // Simple random selection for now
        //     ->first();

        // // 2. If no taxi found in the primary city, consider broader areas (placeholder logic)
        // // This is where you would implement your "next areas" logic.
        // // For example, if 'Marrakesh' has no taxis, you might check 'Safi' next.
        // // This would require a predefined city proximity map or distance calculation.
        // if (!$assignedTaxi) {
        //     // Placeholder for "next areas" logic
        //     // For now, let's just pick any available taxi if none in the exact city
        //     $assignedTaxi = Taxi::where('type', $request->taxi_type)
        //         ->where('is_available', true)
        //         ->inRandomOrder()
        //         ->first();

        //     if ($assignedTaxi) {
        //         // Log or notify that booking was assigned to a taxi from a different city
        //         session()->flash('warning', 'No taxis found in ' . $request->pickup_city . '. Assigned a taxi from ' . $assignedTaxi->city . '.');
        //     }
        // }


        // if ($assignedTaxi) {
        //     $assignedTaxi->update(['is_available' => false]); // Mark as unavailable
        //     $booking->update([
        //         'assigned_taxi_id' => $assignedTaxi->id,
        //         'assigned_driver_id' => $assignedTaxi->driver_id,
        //         'status' => 'ASSIGNED',
        //     ]);
        //     session()->flash('success', 'Booking assigned to ' . $assignedTaxi->license_plate . '!');
        // } else {
        //     session()->flash('error', 'No available taxis found for your request at this time. Your booking is pending.');
        // }

        // Dispatch the assignment job
        // It will start searching from tier 0 (the exact pickup city)
        AssignTaxiToBooking::dispatch($booking, $request->taxi_type, 0, config('cities.search_tiers.2')); // Search up to tier 2

        session()->flash('success', 'Your booking has been received and is awaiting assignment. Please wait for a confirmation.');

        // Pass the QR code SVG directly to the confirmation view
        return redirect()->route('bookings.confirmation', ['uuid' => $booking->booking_uuid])
            ->with('qrCodeSvg', $qrCodeSvg)
            ->with('bookingDetails', $booking->toArray());
    }


    public function showConfirmation($uuid)
    {
        $booking = Booking::where('booking_uuid', $uuid)->firstOrFail();

        // Retrieve QR code SVG and booking details from session if available
        $qrCodeSvg = session('qrCodeSvg');
        $bookingDetails = session('bookingDetails');

        if (!$qrCodeSvg || !$bookingDetails || $bookingDetails['booking_uuid'] !== $uuid) {
            // If direct access or session expired, regenerate QR code (for simplicity)
            // In a real app, you might save QR code images or use a more robust retrieval
            $qrData = $booking->qr_code_data;
            $qrCodeSvg = QrCode::size(250)->generate(json_encode($qrData));
            $bookingDetails = $booking->toArray();
        }

        return view('bookings.confirmation', compact('booking', 'qrCodeSvg', 'bookingDetails'));
    }
}
