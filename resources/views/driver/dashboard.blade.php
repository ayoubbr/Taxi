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
                <h1>Driver Dashboard</h1>
            </div>
            {{-- <div class="header-right">
                    <div class="header-actions">
                        <button class="btn-icon">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <button class="btn-icon">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div> --}}
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
                <div class="stat-icon">
                    <i class="fas fa-road"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $bookings->where('status', 'IN_PROGRESS')->count() }}</h3>
                    <p>In Progress</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $bookings->where('status', 'COMPLETED')->count() }}</h3>
                    <p>Completed Today</p>
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
            <div class="content-header">
                <h2>Current Bookings</h2>
                <div class="filter-controls">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="ASSIGNED">Assigned</button>
                    <button class="filter-btn" data-filter="IN_PROGRESS">In Progress</button>
                </div>
            </div>

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
                            <div class="booking-client">
                                <div class="client-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="client-info">
                                    <h4>{{ $booking->client_name }}</h4>
                                    <div class="taxi-type">
                                        <i class="fas fa-taxi"></i> {{ ucfirst($booking->taxi_type) }}
                                    </div>
                                </div>
                            </div>

                            <div class="booking-route">
                                <div class="route-point pickup">
                                    <div class="point-marker">A</div>
                                    <div class="point-details">
                                        <h5>Pickup Location</h5>
                                        <p>{{ $booking->pickup_location }}</p>
                                        <span class="city">{{ $booking->pickup_city }}</span>
                                    </div>
                                </div>

                                <div class="route-line">
                                    <i class="fas fa-ellipsis-v"></i>
                                </div>

                                <div class="route-point destination">
                                    <div class="point-marker">B</div>
                                    <div class="point-details">
                                        <h5>Destination</h5>
                                        <p>{{ $booking->destination }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y') }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('H:i') }}</span>
                                </div>
                                @if ($booking->estimated_fare)
                                    <div class="detail-item">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span>${{ number_format($booking->estimated_fare, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="booking-actions">
                            @if ($booking->status === 'ASSIGNED')
                                <a href="{{ route('driver.scan.qr.form', ['booking_uuid' => $booking->booking_uuid]) }}"
                                    class="btn btn-primary">
                                    <i class="fas fa-qrcode"></i> Scan QR Code
                                </a>
                                <button class="btn btn-secondary btn-directions">
                                    <i class="fas fa-directions"></i> Get Directions
                                </button>
                            @elseif ($booking->status === 'IN_PROGRESS')
                                <form action="{{ route('driver.booking.update-status', $booking) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="COMPLETED">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check-circle"></i> Complete Ride
                                    </button>
                                </form>
                                <button class="btn btn-secondary btn-navigate">
                                    <i class="fas fa-map-marked-alt"></i> Navigate
                                </button>
                            @endif
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
        </div>
    </main>
@endsection
