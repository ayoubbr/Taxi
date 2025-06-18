@extends('driver.layout')

@section('css')
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
                <h1>Driver Dashboard</h1>
            </div>
        </header>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $bookings->where('status', 'ASSIGNED')->count() }}</h3>
                    <p>Assigned Rides</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon in-progress-icon">
                    <i class="fas fa-route"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $bookings->where('status', 'IN_PROGRESS')->count() }}</h3>
                    <p>In Progress</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $bookings->where('status', 'COMPLETED')->count() }}</h3>
                    <p>Completed Rides</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>${{ number_format($bookings->where('status', 'COMPLETED')->sum('estimated_fare'), 2) }}</h3>
                    <p>Today's Earnings</p>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            {{-- Filter Form --}}
            <form action="{{ route('driver.dashboard') }}" method="GET" class="filter-form">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="{{ request('date') }}">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="ALL" {{ request('status') == 'ALL' ? 'selected' : '' }}>All</option>
                        <option value="ASSIGNED" {{ request('status') == 'ASSIGNED' ? 'selected' : '' }}>Assigned</option>
                        <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="client_name">Client Name</label>
                    <input type="text" id="client_name" name="client_name" placeholder="Client Name"
                        value="{{ request('client_name') }}">
                </div>
                <div class="form-group">
                    <label for="pickup_city">Pickup City</label>
                    <input type="text" id="pickup_city" name="pickup_city" placeholder="Pickup City"
                        value="{{ request('pickup_city') }}">
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
                    <div class="booking-card" data-status="{{ $booking->status }}">
                        <div class="booking-header">
                            <div class="booking-id">
                                <span class="label">Booking ID:</span>
                                <span class="value">{{ substr($booking->booking_uuid, 0, 8) }}</span>
                            </div>
                            <div class="booking-status status-{{ $booking->status }}">
                                {{ str_replace('_', ' ', $booking->status) }}
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
                            @if ($booking->status === 'ASSIGNED')
                                <a href="{{ route('driver.scan.qr.form', $booking->booking_uuid) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-qrcode"></i> Scan QR
                                </a>
                                <button class="btn btn-secondary btn-directions">
                                    <i class="fas fa-map-marked-alt"></i> Get Directions
                                </button>
                            @elseif ($booking->status === 'IN_PROGRESS')
                                <form action="{{ route('driver.booking.update-status', $booking) }}" method="POST">
                                    @csrf
                                    @method('POST') {{-- Use POST for status update --}}
                                    <input type="hidden" name="status" value="COMPLETED">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i> Complete Ride
                                    </button>
                                </form>
                                <button class="btn btn-secondary btn-navigate">
                                    <i class="fas fa-map-marked-alt"></i> Navigate
                                </button>
                            @endif
                            {{-- @if ($booking->status === 'ASSIGNED' || $booking->status === 'IN_PROGRESS')
                                <form action="{{ route('driver.booking.update-status', $booking) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="status" value="CANCELLED">
                                    <button type="submit" class="btn btn-danger"
                                        style="width: -webkit-fill-available;">
                                        <i class="fas fa-times-circle"></i> Cancel Booking
                                    </button>
                                </form>
                            @endif --}}
                        </div>
                    </div>
                @empty
                    <div class="no-bookings">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h3>No Bookings Found</h3>
                            <p>You don't have any assigned bookings at the moment.</p>
                            <p>Take a break or check back later!</p>
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
