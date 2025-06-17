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
    // public function assignedBookings()
    // {
    //     $driverId = Auth::id();

    //     $bookings = Booking::where('assigned_driver_id', $driverId)
    //         ->whereIn('status', ['ASSIGNED', 'IN_PROGRESS', 'COMPLETED']) // Show relevant statuses
    //         ->orderBy('pickup_datetime', 'asc')
    //         ->get();

    //     return view('driver.dashboard', compact('bookings'));
    // }

    public function assignedBookings(Request $request)
    {
        $driverId = Auth::id();

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
            ->when($request->filled('pickup_location'), function ($query) use ($request) {
                $query->where('pickup_location', 'like', '%' . $request->input('pickup_location') . '%');
            })
            ->when($request->filled('destination'), function ($query) use ($request) {
                $query->where('destination', 'like', '%' . $request->input('destination') . '%');
            })
            ->orderBy('pickup_datetime', 'asc')
            ->paginate(6); // Paginate with 10 items per page

        return view('driver.dashboard', compact('bookings'));
    }

    /**
     * Display a list of available bookings that a driver can apply for.
     */
    // public function availableBookings()
    // {
    //     $driver = Auth::user();
    //     $driverTaxi = $driver->taxi;

    //     if (!$driverTaxi) {
    //         // Handle case where driver has no taxi assigned
    //         return view('driver.available-bookings')->with('bookings', collect());
    //     }

    //     $driverCity = $driverTaxi->city;
    //     $proximityMap = config('cities.proximity_map');
    //     $neighborCities = $proximityMap[$driverCity] ?? [];

    //     $bookings = Booking::where('status', 'PENDING')
    //         ->where('taxi_type', $driverTaxi->type)
    //         ->where(function ($query) use ($driverCity, $neighborCities) {

    //             // Condition 1: Show bookings located in the driver's own city.
    //             // These are visible immediately (search_tier >= 0).
    //             $query->where('pickup_city', $driverCity);

    //             // Condition 2: Show bookings in neighboring cities ONLY IF their
    //             // search tier has been increased to 1 or higher.
    //             $query->orWhere(function ($q) use ($neighborCities) {
    //                 $q->whereIn('pickup_city', $neighborCities)
    //                     ->where('search_tier', '>=', 1);
    //             });

    //             // Condition 3: Show bookings if their search tier has become global (2 or higher),
    //             // regardless of the driver's location. This covers cities that are not immediate neighbors.
    //             $query->orWhere('search_tier', '>=', 2);
    //         })
    //         // Only show bookings that are in the future.
    //         // I will move sub minutes in PRODUCTION ***************************** 
    //         ->where('pickup_datetime', '>', now()->subMinutes(1440))
    //         ->orderBy('pickup_datetime', 'asc')
    //         ->paginate(2);

    //     return view('driver.available-bookings', compact('bookings'));
    // }

    // public function availableBookings(Request $request)
    // {
    //     $driver = Auth::user();
    //     $driverTaxi = $driver->taxi;
    //     $driverId = Auth::id();

    //     if (!$driverTaxi) {
    //         return view('driver.available-bookings')->with('error', 'You must have a taxi assigned to view available bookings.');
    //     }

    //     $query = Booking::where('status', 'PENDING')
    //         ->where('taxi_type', $driverTaxi->taxi_type)
    //         ->whereDoesntHave('applications', function ($q) use ($driverId) {
    //             $q->where('driver_id', $driverId);
    //         })
    //         ->when($request->filled('date'), function ($q) use ($request) {
    //             $q->whereDate('pickup_datetime', $request->input('date'));
    //         })
    //         ->when($request->filled('pickup_location'), function ($q) use ($request) {
    //             $q->where('pickup_location', 'like', '%' . $request->input('pickup_location') . '%');
    //         })
    //         ->when($request->filled('destination'), function ($q) use ($request) {
    //             $q->where('destination', 'like', '%' . $request->input('destination') . '%');
    //         })
    //         ->when($request->filled('client_name'), function ($q) use ($request) {
    //             $q->where('client_name', 'like', '%' . $request->input('client_name') . '%');
    //         });


    //     // Implement city-based filtering if needed, potentially from a configuration or Cities model
    //     if ($request->filled('pickup_city')) {
    //         $query->where('pickup_city', $request->input('pickup_city'));
    //     }


    //     $bookings = $query->orderBy('pickup_datetime', 'asc')
    //         ->paginate(2); // Paginate with 10 items per page

    //     return view('driver.available-bookings', compact('bookings'));
    // }

    public function availableBookings(Request $request)
    {
        $driver = Auth::user();
        $driverTaxi = $driver->taxi;
        $driverId = Auth::id();
        if (!$driverTaxi) {
            return view('driver.available-bookings')->with('error', 'You must have a taxi assigned to view available bookings.');
        }
        
        $query = Booking::where('status', 'PENDING')
        ->where('taxi_type', $driverTaxi->type)
        // ->whereDoesntHave('applications', function ($q) use ($driverId) {
        //     $q->where('driver_id', Auth::id());
        // })
        ->when($request->filled('date'), function ($q) use ($request) {
            $q->whereDate('pickup_datetime', $request->input('date'));
        })
        ->when($request->filled('pickup_location'), function ($q) use ($request) {
            $q->where('pickup_location', 'like', '%' . $request->input('pickup_location') . '%');
        })
        ->when($request->filled('destination'), function ($q) use ($request) {
            $q->where('destination', 'like', '%' . $request->input('destination') . '%');
        })
        ->when($request->filled('client_name'), function ($q) use ($request) {
            $q->where('client_name', 'like', '%' . $request->input('client_name') . '%');
        })
        ;
        
        
        // Implement city-based filtering if needed, potentially from a configuration or Cities model
        if ($request->filled('pickup_city')) {
            $query->where('pickup_city', $request->input('pickup_city'));
        }
        
        
        $bookings = $query->orderBy('pickup_datetime', 'asc')
        ->paginate(6); // Paginate with 10 items per page
        
        // dd($request->date);

        return view('driver.available-bookings', compact('bookings'));
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

    // public function updateBookingStatus(Request $request, Booking $booking)
    // {
    //     $request->validate([
    //         'status' => 'required|in:IN_PROGRESS,COMPLETED,CANCELLED', // Define allowed transitions
    //     ]);

    //     if ($booking->assigned_driver_id !== Auth::id()) {
    //         abort(403, 'You are not authorized to update this booking.');
    //     }

    //     // Add logic for valid status transitions
    //     if ($booking->status === 'IN_PROGRESS' && $request->status === 'COMPLETED') {
    //         $booking->status = 'COMPLETED';
    //         $booking->save();
    //         return back()->with('success', 'Ride completed!');
    //     }

    //     return back()->with('error', 'Invalid status transition.');
    // }
}
