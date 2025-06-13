<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class DriverController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated users with 'driver' role can access these routes
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->user_type !== 'DRIVER') {
                abort(403, 'Unauthorized action.'); // Or redirect
            }
            return $next($request);
        })->except(['scanQrCodeForm', 'processQrCodeScan']); // Exclude public QR scan for now
    }

    /**
     * Display a list of bookings assigned to the authenticated driver.
     */
    public function assignedBookings()
    {
        $driverId = Auth::id();

        $bookings = Booking::where('assigned_driver_id', $driverId)
            // ->whereIn('status', ['ASSIGNED', 'IN_PROGRESS']) // Show relevant statuses
            ->orderBy('pickup_datetime', 'asc')
            ->get();

        return view('driver.dashboard', compact('bookings'));
    }

    /**
     * Show form for driver to scan QR code.
     */
    public function scanQrCodeForm($uuid)
    {
        return view('driver.scan_qr', compact('uuid'));
    }

    /**
     * Process QR code scan and update booking status.
     */
    public function processQrCodeScan(Request $request)
    {
        $scannedQrData = $request->input('qr_data'); // Get the scanned data
        $expectedBookingUuid = $request->input('expected_booking_uuid'); // Get the expected UUID from hidden input
        // dd($scannedQrData);
        // dd($expectedBookingUuid);
        Log::info('QR Scan Data Received: ' . $scannedQrData . ', Expected UUID: ' . $expectedBookingUuid);

        // Basic validation
        if (empty($scannedQrData)) {
            return back()->with('error', 'QR data is empty.');
        }

        $bookingUuidFromScan = null;

        // Attempt to parse JSON or treat as raw UUID
        $decodedData = json_decode($scannedQrData, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decodedData['bookingId'])) {
            $bookingUuidFromScan = $decodedData['bookingId'];
        } elseif (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $scannedQrData)) {
            // If it's a raw UUID string
            $bookingUuidFromScan = $scannedQrData;
        } else {
            return back()->with('error', 'Invalid QR data format or content.');
        }

        // Find the booking based on the extracted UUID
        $booking = Booking::where('booking_uuid', $bookingUuidFromScan)->first();

        if (!$booking) {
            return back()->with('error', 'Booking not found for scanned QR code.');
        }

        // --- Validation Logic for Problem 2 ---
        $currentUser = Auth::user();

        // 1. Check if the booking is assigned to the current driver
        if ($booking->assigned_driver_id !== $currentUser->id) {
            Log::warning("Unauthorized scan attempt: Driver {$currentUser->id} tried to scan booking {$booking->booking_uuid} not assigned to them.");
            return back()->with('error', 'This booking is not assigned to you.');
        }

        // 2. Check if the scanned booking matches the expected booking UUID (if provided)
        if (!empty($expectedBookingUuid) && $booking->booking_uuid !== $expectedBookingUuid) {
            Log::warning("Mismatch scan attempt: Driver {$currentUser->id} expected {$expectedBookingUuid} but scanned {$booking->booking_uuid}.");
            return back()->with('error', 'Scanned QR code does not match the expected booking.');
        }

        // --- Update Booking Status Logic ---
        try {
            // Example: Update status from ASSIGNED to IN_PROGRESS
            if ($booking->status === 'ASSIGNED') {
                $booking->status = 'IN_PROGRESS';
                $booking->save();
                return redirect()->route('driver.dashboard')->with('success', 'Booking ' . $booking->booking_uuid . ' is now IN PROGRESS!');
            } else {
                return redirect()->route('driver.dashboard')->with('info', 'Booking status is already ' . $booking->status . '.');
            }
        } catch (\Exception $e) {
            Log::error('Error updating booking status: ' . $e->getMessage());
            return back()->with('error', 'An internal server error occurred while updating booking status.');
        }
    }

    // You'll add methods for 'Arrived', 'Complete', etc. later
    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:IN_PROGRESS,COMPLETED,CANCELLED', // Define allowed transitions
        ]);

        if ($booking->assigned_driver_id !== Auth::id()) {
            abort(403, 'You are not authorized to update this booking.');
        }

        // Add logic for valid status transitions
        if ($booking->status === 'IN_PROGRESS' && $request->status === 'COMPLETED') {
            $booking->status = 'COMPLETED';
            $booking->save();
            return back()->with('success', 'Ride completed!');
        }

        return back()->with('error', 'Invalid status transition.');
    }
}
