<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\BookingApplication;
use App\Models\City;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DriverController extends Controller
{
    public function __construct()
    {
        // Ensure only authenticated users with 'driver' role can access these routes
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role->name !== 'DRIVER') {
                abort(403, 'Unauthorized action.'); // Or redirect
            }
            return $next($request);
        })->except(['scanQrCodeForm', 'processQrCodeScan']); // Exclude public QR scan for now
    }

    public function assignedBookings(Request $request)
    {
        $driverId = Auth::id();
        $driverTaxi = Auth::user()->taxi;

        $bookings = Booking::where('assigned_driver_id', $driverId)
            ->whereIn('status', ['ASSIGNED', 'IN_PROGRESS', 'COMPLETED']) // Show relevant statuses
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('pickup_datetime', $request->input('date'));
            })
            ->when($request->filled('status') && $request->input('status') !== 'ALL', function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('client_name'), function ($query) use ($request) {
                $query->where('client_name', 'like', '%' . $request->input('client_name') . '%');
            })
            ->when($request->filled('pickup_city'), function ($query) use ($request) {
                $query->where('pickup_city', 'like', '%' . $request->input('pickup_city') . '%');
            })
            ->when($request->filled('destination'), function ($query) use ($request) {
                $query->where('destination', 'like', '%' . $request->input('destination') . '%');
            })
            // ->where('pickup_city', $driverTaxi->city )
            ->orderBy('pickup_datetime', 'asc')
            ->paginate(6); // Paginate with 10 items per page

        return view('driver.dashboard', compact('bookings'));
    }


    public function availableBookings(Request $request)
    {
        $driver = Auth::user();
        $driverTaxi = $driver->taxi;
        $driverId = $driver->id;

        if (!$driverTaxi || !$driverTaxi->city_id) {
            return view('driver.available-bookings', ['bookings' => collect()])
                ->with('error', 'You must have a taxi with an assigned city to view bookings.');
        }

        // --- Tiered City Logic ---
        $driverCityId = $driverTaxi->city_id;
        $driverCityName = $driverTaxi->city->name;

        // 1. Get the names of neighboring cities from the config
        $proximityMap = config('cities.proximity_map');
        $neighborCityNames = $proximityMap[$driverCityName] ?? [];

        // 2. Get the IDs of those neighboring cities in one query
        $neighborCityIds = !empty($neighborCityNames) ? City::whereIn('name', $neighborCityNames)->pluck('id') : collect();
        // --- End Tiered City Logic ---

        $query = Booking::query()
            ->where('status', 'PENDING')
            ->where('taxi_type', $driverTaxi->type)
            ->whereDoesntHave('applications', function ($q) use ($driverId) {
                $q->where('driver_id', $driverId);
            })
            ->where('pickup_datetime', '>', now());

        // --- Main Filtering Logic ---
        $query->where(function ($q) use ($driverCityId, $neighborCityIds) {
            // Condition 1: Show bookings in the driver's own city (any tier)
            $q->where('pickup_city_id', $driverCityId);

            // Condition 2: OR show bookings in neighboring cities if tier >= 1
            if ($neighborCityIds->isNotEmpty()) {
                $q->orWhere(function ($subQ) use ($neighborCityIds) {
                    $subQ->whereIn('pickup_city_id', $neighborCityIds)
                        ->where('search_tier', '>=', 1);
                });
            }

            // Condition 3: OR show any booking if tier is global (>= 2)
            $q->orWhere('search_tier', '>=', 2);
        });

        // --- Search Filters from Request ---
        if ($request->filled('date')) {
            $query->whereDate('pickup_datetime', $request->input('date'));
        }
        if ($request->filled('pickup_city')) {
            $query->where('pickup_city_id', $request->input('pickup_city'));
        }
        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->input('destination') . '%');
        }
        if ($request->filled('client_name')) {
            $query->where('client_name', 'like', '%' . $request->input('client_name') . '%');
        }

        $bookings = $query->orderBy('pickup_datetime', 'asc')->paginate(6);

        // Pass all cities to the view for the search filter dropdown
        $cities = City::orderBy('name')->get();

        return view('driver.available-bookings', compact('bookings', 'cities'));
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
        $scannedQrData = $request->input('qr_data');
        $expectedBookingUuid = $request->input('expected_booking_uuid');

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
            $booking->taxi->update(['is_available' => true]);
            $booking->save();
            return back()->with('success', 'Ride completed successfully!');
        }

        // Add logic for valid status transitions
        if ($booking->status === 'ASSIGNED' && $request->status === 'CANCELLED') {

            // Notify the client that the driver cancelled
            if ($booking->client) {
                // Notification::send($booking->client, new BookingCancelledNotification($booking, 'driver'));
            }

            $bookingApplication = BookingApplication::where('booking_id', $booking->id)
                ->where('driver_id', Auth::id())->first();
            $bookingApplication->delete();

            $booking->taxi->update(['is_available' => true]);

            $booking->status = 'PENDING';
            $booking->assigned_driver_id = NULL;
            $booking->assigned_taxi_id = NULL;
            $booking->save();

            return redirect()->route('driver.dashboard')->with('success', 'You have cancelled the booking.');
        }

        return back()->with('error', 'Invalid status transition.');
    }
}
