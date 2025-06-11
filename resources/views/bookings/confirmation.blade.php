<!-- resources/views/bookings/confirmation.blade.php -->
@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
@endsection

@section('content')
    <div class="confirmation-container">
        <div class="confirmation-card">
            <h2><i class="fas fa-check-circle"></i> Booking Confirmed!</h2>
            <p>Your taxi has been booked successfully. Please find your booking details and QR code below.</p>
            <p>Your booking ID is: <strong>{{ $booking->booking_uuid }}</strong></p>

            <div class="booking-details">
                <h3>Booking Summary</h3>
                <ul>
                    <li><strong>Client Name:</strong> {{ $booking->client_name }}</li>
                    <li><strong>Pickup Location:</strong> {{ $booking->pickup_location }}, {{ $booking->pickup_city }}</li>
                    <li><strong>Destination:</strong> {{ $booking->destination }}</li>
                    <li><strong>Date & Time:</strong>
                        {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y H:i') }}</li>
                    <li><strong>Status:</strong> <span
                            style="font-weight: bold; color: {{ $booking->status == 'ASSIGNED' ? 'green' : ($booking->status == 'PENDING' ? 'orange' : 'inherit') }}">{{ $booking->status }}</span>
                    </li>
                    @if ($booking->estimated_fare)
                        <li><strong>Estimated Fare:</strong> ${{ number_format($booking->estimated_fare, 2) }}</li>
                    @endif
                    @if ($booking->assignedTaxi)
                        <li><strong>Assigned Taxi:</strong> {{ $booking->assignedTaxi->license_plate }}
                            ({{ $booking->assignedTaxi->type }})</li>
                        <li><strong>Assigned Driver:</strong>
                            {{ $booking->assignedDriver ? $booking->assignedDriver->firstname . ' ' . $booking->assignedDriver->lastname : 'N/A' }}
                        </li>
                    @else
                        <li><strong>Assigned Taxi:</strong> Searching for an available taxi...</li>
                    @endif
                </ul>
            </div>

            <div class="qr-code-section">
                <h3>Your QR Code</h3>
                <p>Present this QR code to your driver for validation.</p>
                <div class="qr-code-display">
                    {!! $qrCodeSvg !!}
                </div>
            </div>

            <div class="print-save-buttons flex-center">
                <button type="button" class="btn-primary flex-center" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Details
                </button>
                {{-- You can add more complex save functionality here, e.g., generating a PDF --}}
                <a href="{{ route('home') }}" class="btn-primary flex-center" style="background-color: #98a1a8;">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
