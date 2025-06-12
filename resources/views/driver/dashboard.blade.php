@extends('layout')

@section('css')
    {{-- <link rel="stylesheet" href="{{ asset('css/home.css') }}"> --}}
    <style>
        .driver-dashboard {
            padding: 40px 20px;
            min-height: 80vh;
            background-color: #f8f9fa;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .dashboard-header h2 {
            color: #007bff;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            color: #6c757d;
            font-size: 1.1em;
        }

        .bookings-list {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .bookings-list h3 {
            color: #343a40;
            margin-bottom: 25px;
            font-size: 1.8em;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            display: inline-block;
        }

        .booking-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
        }

        .booking-item:hover {
            transform: translateY(-5px);
        }

        .booking-info {
            flex: 2;
            min-width: 280px;
            margin-right: 20px;
            margin-bottom: 15px;
        }

        .booking-info p {
            margin: 5px 0;
            color: #495057;
            font-size: 0.95em;
        }

        .booking-info p strong {
            color: #343a40;
            min-width: 100px;
            display: inline-block;
        }

        .booking-actions {
            flex: 1;
            min-width: 180px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .booking-actions .btn {
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .btn-scan-qr {
            background-color: #28a745;
            color: white;
        }

        .btn-scan-qr:hover {
            background-color: #218838;
        }

        .btn-update-status {
            background-color: #007bff;
            color: white;
        }

        .btn-update-status:hover {
            background-color: #0056b3;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.85em;
            color: white;
        }

        .status-PENDING {
            background-color: #ffc107;
        }

        /* Yellow */
        .status-ASSIGNED {
            background-color: #007bff;
        }

        /* Blue */
        .status-IN_PROGRESS {
            background-color: #6f42c1;
        }

        /* Purple */
        .status-COMPLETED {
            background-color: #28a745;
        }

        /* Green */
        .status-CANCELLED {
            background-color: #dc3545;
        }

        /* Red */
        .status-NO_TAXI_FOUND {
            background-color: #6c757d;
        }

        /* Gray */

        @media (max-width: 768px) {
            .booking-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .booking-info {
                margin-right: 0;
                width: 100%;
            }

            .booking-actions {
                width: 100%;
                margin-top: 15px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="driver-dashboard mt-70">
        <div class="container">
            <div class="dashboard-header">
                <h2>Driver Dashboard</h2>
                <p>Welcome, {{ Auth::user()->firstname }}. Here are your assigned bookings.</p>
            </div>

            <div class="bookings-list">
                <h3>Your Current Bookings</h3>
                @forelse ($bookings as $booking)
                    <div class="booking-item">
                        <div class="booking-info">
                            <p><strong>Booking ID:</strong> {{ $booking->booking_uuid }}</p>
                            <p><strong>Client:</strong> {{ $booking->client_name }}</p>
                            <p><strong>Pickup:</strong> {{ $booking->pickup_location }} ({{ $booking->pickup_city }})</p>
                            <p><strong>Destination:</strong> {{ $booking->destination }}</p>
                            <p><strong>Time:</strong>
                                {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y H:i') }}</p>
                            <p><strong>Status:</strong> <span
                                    class="status-badge status-{{ $booking->status }}">{{ $booking->status }}</span></p>
                            <p><strong>Taxi Type:</strong> {{ ucfirst($booking->taxi_type) }}</p>
                            @if ($booking->estimated_fare)
                                <p><strong>Estimated Fare:</strong> ${{ number_format($booking->estimated_fare, 2) }}</p>
                            @endif
                        </div>
                        <div class="booking-actions">
                            @if ($booking->status === 'ASSIGNED')
                                <form action="{{ route('driver.booking.update-status', $booking) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="IN_PROGRESS">
                                    <button type="submit" class="btn btn-update-status">
                                        <i class="fas fa-play"></i> Start Ride
                                    </button>
                                </form>
                                {{-- For actual QR scanning, you'd navigate to a specific page or open a modal --}}
                                <a href="{{ route('driver.scan.qr.form', ['booking_uuid' => $booking->booking_uuid]) }}"
                                    class="btn btn-scan-qr">
                                    <i class="fas fa-qrcode"></i> Scan QR
                                </a>
                            @elseif ($booking->status === 'IN_PROGRESS')
                                <form action="{{ route('driver.booking.update-status', $booking) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="COMPLETED">
                                    <button type="submit" class="btn btn-update-status">
                                        <i class="fas fa-check-circle"></i> Complete Ride
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center">No assigned bookings at the moment. Check back later!</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
