@extends('driver.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/driver-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
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
            <div class="header-right">
                <div class="booking-count">
                    <span class="count">{{ $bookings->total() }}</span>
                    <span class="label">Available</span>
                </div>
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
                                @if($booking->estimated_fare)
                                <div class="detail-item">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>${{ number_format($booking->estimated_fare, 2) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="booking-actions">
                            @if ($booking->hasDriverApplied(Auth::id()))
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-check"></i> Already Applied
                                </button>
                            @else
                                <form action="{{ route('driver.bookings.apply', $booking) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="no-bookings">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h3>No Available Bookings</h3>
                            <p>There are no pending bookings matching your profile right now.</p>
                            <p>Check back later or make sure your profile is complete!</p>
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Enhanced Pagination -->
            @if($bookings->hasPages())
            <div class="custom-pagination">
                <div class="pagination-summary">
                    Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
                </div>
                <div class="pagination-links">
                    {{ $bookings->links() }}
                </div>
            </div>
            @endif
        </div>
    </main>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading state to pagination links
        const paginationLinks = document.querySelectorAll('.pagination-links a');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (!this.classList.contains('disabled')) {
                    this.classList.add('loading');
                }
            });
        });
        
        // Auto-refresh every 60 seconds for new bookings
        setInterval(function() {
            // Only refresh if user is on first page to avoid disruption
            const currentUrl = new URL(window.location.href);
            if (!currentUrl.searchParams.has('page') || currentUrl.searchParams.get('page') === '1') {
                location.reload();
            }
        }, 60000);
        
        // Mobile menu toggle (if you have a sidebar)
        const menuToggle = document.getElementById('menuToggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                // Add your sidebar toggle logic here
                document.querySelector('.dashboard-sidebar')?.classList.toggle('active');
            });
        }
    });
</script>
@endsection
