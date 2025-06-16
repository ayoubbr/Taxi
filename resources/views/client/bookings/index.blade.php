@extends('client.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/client-bookings.css') }}">
@endsection

@section('content')
    <div class="client-dashboard mt-70">
        <!-- Header Section -->
        <header class="dashboard-header">
            <div class="container">
                <h1>My Bookings</h1>
                <p>Manage and track all your taxi reservations</p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="container">
                <!-- Booking Stats -->
                <div class="booking-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $bookings->count() }}</h3>
                            <p>Total Bookings</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon active-icon">
                            <i class="fas fa-taxi"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $bookings->whereIn('status', ['ASSIGNED', 'IN_PROGRESS'])->count() }}</h3>
                            <p>Active Rides</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon completed-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $bookings->where('status', 'COMPLETED')->count() }}</h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Actions -->
                <div class="booking-actions">
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary" style="width: 210px;">
                        <i class="fas fa-plus"></i> New Booking
                    </a>

                    <div class="filter-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="bookingSearch" placeholder="Search bookings...">
                        </div>

                        <div class="filter-dropdown">
                            <button class="filter-btn">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <div class="filter-menu">
                                <button class="filter-option active" data-filter="all">All Bookings</button>
                                <button class="filter-option" data-filter="upcoming">Upcoming</button>
                                <button class="filter-option" data-filter="active">Active</button>
                                <button class="filter-option" data-filter="completed">Completed</button>
                                <button class="filter-option" data-filter="cancelled">Cancelled</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings List -->
                <div class="bookings-list">
                    @forelse ($bookings as $booking)
                        <div class="booking-card" data-status="{{ $booking->status }}">
                            <div class="booking-status status-{{ $booking->status }}">
                                {{ str_replace('_', ' ', $booking->status) }}
                            </div>

                            <div class="booking-info">
                                <div class="booking-date">
                                    <div class="date-day">
                                        {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d') }}</div>
                                    <div class="date-month">
                                        {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('M') }}</div>
                                </div>

                                <div class="booking-details">
                                    <div class="booking-time">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('H:i') }}
                                    </div>

                                    <div class="booking-locations">
                                        <div class="location pickup">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>{{ Str::limit($booking->pickup_location, 30) }}</span>
                                        </div>
                                        <div class="location-divider">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                        <div class="location destination">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>{{ Str::limit($booking->destination, 30) }}</span>
                                        </div>
                                    </div>

                                    <div class="booking-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-taxi"></i>
                                            <span>{{ ucfirst($booking->taxi_type) }}</span>
                                        </div>

                                        @if ($booking->estimated_fare)
                                            <div class="meta-item">
                                                <i class="fas fa-dollar-sign"></i>
                                                <span>${{ number_format($booking->estimated_fare, 2) }}</span>
                                            </div>
                                        @endif

                                        <div class="meta-item">
                                            <i class="fas fa-hashtag"></i>
                                            <span>{{ substr($booking->booking_uuid, 0, 8) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-actions">
                                <a href="{{ route('client.bookings.show', $booking->booking_uuid) }}" class="btn btn-view">
                                    <i class="fas fa-eye"></i> View Details
                                </a>

                                @if (in_array($booking->status, ['PENDING', 'ASSIGNED']))
                                    <button class="btn btn-cancel" data-booking-id="{{ $booking->booking_uuid }}">
                                        <i class="fas fa-times"></i> Cancel
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
                                <p>You haven't made any taxi bookings yet.</p>
                                <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Book a Taxi Now
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- <!-- Pagination -->
                @if ($bookings->count() > 0)
                    <div class="pagination-container">
                        {{ $bookings->links() }}
                    </div>
                @endif --}}
            </div>
        </main>
    </div>

    <!-- Cancel Booking Modal -->
    @if (isset($booking))
        <div class="modal" id="cancelModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Cancel Booking</h3>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this booking? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary close-modal">No, Keep It</button>
                    <form id="cancelForm" action="{{ route('bookings.cancel', $booking->booking_uuid) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="CANCELLED">
                        <button type="submit" class="btn btn-danger">Yes, Cancel Booking</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('bookingSearch');
            const bookingCards = document.querySelectorAll('.booking-card');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();

                    bookingCards.forEach(card => {
                        const cardText = card.textContent.toLowerCase();
                        if (cardText.includes(searchTerm)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // Filter dropdown
            const filterBtn = document.querySelector('.filter-btn');
            const filterMenu = document.querySelector('.filter-menu');

            if (filterBtn && filterMenu) {
                filterBtn.addEventListener('click', function() {
                    filterMenu.classList.toggle('active');
                });

                // Close filter menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('.filter-dropdown')) {
                        filterMenu.classList.remove('active');
                    }
                });

                // Filter options
                const filterOptions = document.querySelectorAll('.filter-option');

                filterOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        // Update active state
                        filterOptions.forEach(opt => opt.classList.remove('active'));
                        this.classList.add('active');

                        // Apply filter
                        const filter = this.getAttribute('data-filter');

                        bookingCards.forEach(card => {
                            const status = card.getAttribute('data-status');

                            if (filter === 'all') {
                                card.style.display = 'flex';
                            } else if (filter === 'upcoming' && status === 'PENDING') {
                                card.style.display = 'flex';
                            } else if (filter === 'active' && ['ASSIGNED', 'IN_PROGRESS']
                                .includes(status)) {
                                card.style.display = 'flex';
                            } else if (filter === 'completed' && status === 'COMPLETED') {
                                card.style.display = 'flex';
                            } else if (filter === 'cancelled' && status === 'CANCELLED') {
                                card.style.display = 'flex';
                            } else {
                                card.style.display = 'none';
                            }
                        });

                        // Update filter button text
                        filterBtn.innerHTML = `<i class="fas fa-filter"></i> ${this.textContent}`;

                        // Close menu
                        filterMenu.classList.remove('active');
                    });
                });
            }

            // Cancel booking modal
            const modal = document.getElementById('cancelModal');
            const cancelButtons = document.querySelectorAll('.btn-cancel');
            const closeButtons = document.querySelectorAll('.close-modal');
            const cancelForm = document.getElementById('cancelForm');

            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    cancelForm.action = `/bookings/${bookingId}/cancel`;
                    modal.classList.add('active');
                });
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modal.classList.remove('active');
                });
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
@endsection
