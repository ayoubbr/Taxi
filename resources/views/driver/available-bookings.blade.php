@extends('driver.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/driver-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    <style>
        .filter-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-form .form-group {
            flex: 1;
            min-width: 180px;
        }

        .filter-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .filter-form input[type="date"],
        .filter-form input[type="text"],
        .filter-form select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }
    </style>
@endsection

@section('content')
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-left" id="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Available Bookings</h1>
            </div>
        </header>

        <div class="dashboard-content">
            {{-- Filter Form --}}
            <form action="{{ route('driver.bookings.available') }}" method="GET" class="filter-form">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="{{ request('date') }}">
                </div>
                <div class="form-group">
                    <label for="client_name">Client Name</label>
                    <input type="text" id="client_name" name="client_name" placeholder="Client Name"
                        value="{{ request('client_name') }}">
                </div>
                <div class="form-group">
                    <label for="pickup_location">Pickup Location</label>
                    <input type="text" id="pickup_location" name="pickup_location" placeholder="Pickup Location"
                        value="{{ request('pickup_location') }}">
                </div>
                <div class="form-group">
                    <label for="destination">Destination</label>
                    <input type="text" id="destination" name="destination" placeholder="Destination"
                        value="{{ request('destination') }}">
                </div>
                <button type="submit">Apply Filters</button>
            </form>

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
                            <div class="booking-route">
                                <div class="route-point pickup">
                                    <div class="point-details">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Pickup: {{ $booking->pickup_location }}</span>
                                    </div>
                                    <span class="city">{{ $booking->pickup_city }}</span>
                                </div>
                                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
                                <div class="route-point destination">
                                    <div class="point-details">
                                        <i class="fas fa-location-arrow"></i>
                                        <span>Destination: {{ $booking->destination }}</span>
                                    </div>
                                    <span class="city">{{ $booking->destination_city }}</span>
                                </div>
                            </div>
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $booking->client_name }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $booking->pickup_datetime->format('M d, Y') }}</span>
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

            {{-- Pagination Links --}}
            <div class="pagination-links">
                {{ $bookings->links() }}
            </div>
        </div>
    </main>
@endsection
