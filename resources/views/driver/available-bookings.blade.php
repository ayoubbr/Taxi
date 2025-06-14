@extends('driver.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/driver-dashboard.css') }}">
@endsection

@section('content')
<main class="dashboard-main">
    <header class="dashboard-header">
        <div class="header-left" id="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                {{-- <h1>Driver Dashboard</h1> --}}
                <h1>Available Bookings</h1>
            </div>
    </header>

    <div class="dashboard-content">
        <div class="bookings-container">
            @forelse ($bookings as $booking)
                <div class="booking-card">
                    <div class="booking-header">
                        <div class="booking-id">
                            <span class="label">Booking ID:</span>
                            <span class="value">{{ substr($booking->booking_uuid, 0, 8) }}</span>
                        </div>
                        <div class="booking-status status-PENDING">
                            PENDING
                        </div>
                    </div>
                    <div class="booking-body">
                        <!-- Booking details similar to dashboard.blade.php -->
                         <div class="booking-route">
                            <div class="route-point pickup">
                                <div class="point-details">
                                    <h5>Pickup Location</h5>
                                    <p>{{ $booking->pickup_location }}</p>
                                    <span class="city">{{ $booking->pickup_city }}</span>
                                </div>
                            </div>
                            <div class="route-line"><i class="fas fa-ellipsis-v"></i></div>
                            <div class="route-point destination">
                                <div class="point-details">
                                    <h5>Destination</h5>
                                    <p>{{ $booking->destination }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="booking-details">
                             <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $booking->pickup_datetime->format('d M Y') }}</span>
                            </div>
                             <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $booking->pickup_datetime->format('H:i') }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-taxi"></i>
                                <span>{{ ucfirst($booking->taxi_type) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="booking-actions">
                        @if ($booking->hasDriverApplied(Auth::id()))
                            <button class="btn btn-secondary" disabled>Already Applied</button>
                        @else
                            <form action="{{ route('driver.bookings.apply', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Apply Now</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-bookings">
                    <div class="empty-state">
                        <h3>No Available Bookings</h3>
                        <p>There are no pending bookings matching your profile right now.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</main>
@endsection
