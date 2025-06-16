<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingApplication;
use App\Models\User;
use App\Notifications\BookingAssignedNotification;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\NewBookingAvailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('client_id', Auth::id())->get();
        return view('client.bookings.index', compact('bookings'));
    }

    public function show($uuid)
    {
        $booking = Booking::where('booking_uuid', $uuid)->first();
        $qrCodeSvg = QrCode::size(250)->generate(json_encode($booking->qr_code_data));

        return view('client.bookings.show', compact('booking', 'qrCodeSvg'));
    }

    public function cancel($uuid)
    {
        $booking = Booking::where('booking_uuid', $uuid)->firstOrFail();

        // Ensure only the booking owner can cancel
        if ($booking->client_id !== Auth::id()) {
            abort(403, 'You are not authorized to cancel this booking.');
        }

        // Only allow cancellation if status is PENDING or ASSIGNED
        if (!in_array($booking->status, ['PENDING', 'ASSIGNED'])) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled.');
        }

        // If the booking was assigned, notify the driver
        if ($booking->status === 'ASSIGNED' && $booking->assigned_driver_id) {
            $driver = User::find($booking->assigned_driver_id);
            Notification::send($driver, new BookingCancelledNotification($booking));
        }

        // Mark booking as cancelled
        $booking->status = 'CANCELLED';
        $booking->save();

        // Optional: Delete applications if still pending
        $booking->applications()->delete();

        return redirect()->route('client.bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    public function create()
    {
        return view('client.bookings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:100',
            'pickup_location' => 'required|string|max:255',
            'pickup_city' => 'required|string|max:100',
            'destination' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'taxi_type' => 'required|in:standard,van,luxe',
        ]);

        $pickupDateTime = $request->input('date') . ' ' . $request->input('time');
        $bookingUuid = (string) Str::uuid();

        $qrData = [
            'bookingId' => $bookingUuid,
            'clientName' => $request->client_name,
            'pickupLocation' => $request->pickup_location,
            'destination' => $request->destination,
            'pickupDateTime' => $pickupDateTime,
        ];

        $booking = Booking::create([
            'booking_uuid' => $bookingUuid,
            'client_id' => Auth::id(),
            'client_name' => $request->client_name,
            'pickup_location' => $request->pickup_location,
            'pickup_city' => $request->pickup_city,
            'destination' => $request->destination,
            'pickup_datetime' => $pickupDateTime,
            'status' => 'PENDING',
            'estimated_fare' => rand(20, 100),
            'qr_code_data' => $qrData,
            'search_tier' => 0, // Start at tier 0
            'taxi_type' => $request->taxi_type,
        ]);

        // Notify drivers in the same city
        $driversInCity = User::where('user_type', 'DRIVER')
            ->whereHas('taxi', function ($query) use ($booking) {
                $query->where('city', $booking->pickup_city)
                    ->where('type', $booking->taxi_type);
            })->get();

        Notification::send($driversInCity, new NewBookingAvailable($booking));

        session()->flash('success', 'Your booking has been received and is now available for drivers to apply. You will be notified once a driver is assigned.');

        return redirect()->route('bookings.confirmation', ['uuid' => $booking->booking_uuid]);
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

    public function showApplications(Booking $booking)
    {
        // Authorization: Ensure the logged-in user is the client for this booking
        if (Auth::id() !== $booking->client_id) {
            abort(403);
        }

        if ($booking->status != 'PENDING') {
            abort(403);
        }

        $applications = $booking->applications()->with('driver.taxi')->get();
        return view('client.applications', compact('booking', 'applications'));
    }

    public function acceptApplication(Request $request, Booking $booking, BookingApplication $application)
    {
        // Authorization
        if (Auth::id() !== $booking->client_id || $booking->status !== 'PENDING') {
            abort(403);
        }

        // Assign driver and taxi to the booking
        $booking->update([
            'assigned_driver_id' => $application->driver_id,
            'assigned_taxi_id' => $application->taxi_id,
            'status' => 'ASSIGNED',
        ]);

        // Mark the assigned taxi as unavailable
        $application->taxi->update(['is_available' => false]);

        // Notify the assigned driver
        Notification::send($application->driver, new BookingAssignedNotification($booking));

        // Delete other applications for this booking
        $booking->applications()->where('id', '!=', $application->id)->delete();

        return redirect()->route('client.bookings.show', $booking->booking_uuid)->with('success', 'Driver has been assigned to your booking!');
    }
}
