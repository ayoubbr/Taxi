@extends('driver.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/driver-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Enhanced Filter Styles - Mobile First */
        .filters-section {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .filters-header h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filters-toggle {
            background: none;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filters-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-input,
        .filter-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .filter-input::placeholder {
            color: #999;
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-filter {
            flex: 1;
            /* min-width: fit-content; */
            min-width: -webkit-fill-available;
            height: 45px;
            align-self: end;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .btn-outline {
            background: white;
            color: #007bff;
            border: 2px solid #007bff;
        }

        .btn-outline:hover {
            background: #007bff;
            color: white;
        }

        /* Active Filters */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .filter-tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-tag .remove {
            background: none;
            border: none;
            color: #1976d2;
            cursor: pointer;
            padding: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-tag .remove:hover {
            background: #1976d2;
            color: white;
        }

        /* Responsive Breakpoints */
        @media (min-width: 576px) {
            .filters-section {
                padding: 20px;
            }

            .filters-form {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-actions {
                justify-content: flex-end;
            }

            .btn-filter {
                flex: none;
                /* min-width: 140px; */
            }
        }

        @media (min-width: 768px) {
            .filters-form {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .filters-section {
                padding: 24px;
            }

            .filters-form {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Enhanced Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state .empty-icon {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            margin: 0 0 12px 0;
            color: #333;
            font-size: 24px;
        }

        .empty-state p {
            margin: 0 0 8px 0;
            font-size: 16px;
            line-height: 1.5;
        }

        .empty-state .btn {
            margin-top: 20px;
        }

        /* Loading States */
        .btn.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .btn.loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Flatpickr Customization */
        .flatpickr-calendar {
            font-family: inherit;
        }

        .flatpickr-day.selected {
            background: #007bff;
            border-color: #007bff;
        }

        .flatpickr-day.selected:hover {
            background: #0056b3;
            border-color: #0056b3;
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
            <div class="header-right">
                <div class="booking-count">
                    <span class="count">{{ $bookings->total() }}</span>
                    <span class="label">Available</span>
                </div>
            </div>
        </header>

        <div class="dashboard-content">
            <!-- Enhanced Filters Section -->
            <div class="filters-section">
                <div class="filters-header">
                    <h3>
                        <i class="fas fa-filter"></i>
                        Search Filters
                    </h3>
                    <button type="button" class="filters-toggle" id="filtersToggle">
                        <i class="fas fa-chevron-down"></i>
                        <span>Toggle Filters</span>
                    </button>
                </div>

                <!-- Active Filters Display -->
                @if (request()->hasAny(['date', 'pickup_city_id', 'destination_city_id', 'client_name', 'taxi_type']))
                    <div class="active-filters">
                        @if (request('date'))
                            <div class="filter-tag">
                                <i class="fas fa-calendar"></i>
                                Date: {{ \Carbon\Carbon::parse(request('date'))->format('M d, Y') }}
                                <a href="{{ request()->fullUrlWithQuery(['date' => null]) }}" class="remove">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif

                        @if (request('pickup_city_id'))
                            <div class="filter-tag">
                                <i class="fas fa-map-marker-alt"></i>
                                Pickup: {{ $cities->find(request('pickup_city_id'))->name ?? 'Unknown' }}
                                <a href="{{ request()->fullUrlWithQuery(['pickup_city_id' => null]) }}" class="remove">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif

                        @if (request('destination_city_id'))
                            <div class="filter-tag">
                                <i class="fas fa-location-arrow"></i>
                                Destination: {{ $cities->find(request('destination_city_id'))->name ?? 'Unknown' }}
                                <a href="{{ request()->fullUrlWithQuery(['destination_city_id' => null]) }}" class="remove">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif

                        @if (request('client_name'))
                            <div class="filter-tag">
                                <i class="fas fa-user"></i>
                                Client: {{ request('client_name') }}
                                <a href="{{ request()->fullUrlWithQuery(['client_name' => null]) }}" class="remove">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif

                        @if (request('taxi_type'))
                            <div class="filter-tag">
                                <i class="fas fa-taxi"></i>
                                Type: {{ ucfirst(request('taxi_type')) }}
                                <a href="{{ request()->fullUrlWithQuery(['taxi_type' => null]) }}" class="remove">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('driver.bookings.available') }}" method="GET" class="filters-form"
                    id="filtersForm">
                    <div class="filter-group">
                        <label for="date">
                            <i class="fas fa-calendar"></i>
                            Date
                        </label>
                        <input type="text" id="date" name="date" class="filter-input" placeholder="Select date"
                            value="{{ request('date') }}" readonly>
                    </div>

                    <div class="filter-group">
                        <label for="pickup_city_id">
                            <i class="fas fa-map-marker-alt"></i>
                            Pickup City
                        </label>
                        <select id="pickup_city_id" name="pickup_city_id" class="filter-select">
                            <option value="">All Cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}"
                                    {{ request('pickup_city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="destination_city_id">
                            <i class="fas fa-location-arrow"></i>
                            Destination City
                        </label>
                        <select id="destination_city_id" name="destination_city_id" class="filter-select">
                            <option value="">All Cities</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}"
                                    {{ request('destination_city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="client_name">
                            <i class="fas fa-user"></i>
                            Client Name
                        </label>
                        <input type="text" id="client_name" name="client_name" class="filter-input"
                            placeholder="Search by client name" value="{{ request('client_name') }}">
                    </div>

                    <div class="filter-group">
                        <label for="taxi_type">
                            <i class="fas fa-taxi"></i>
                            Taxi Type
                        </label>
                        <select id="taxi_type" name="taxi_type" class="filter-select">
                            <option value="">All Types</option>
                            <option value="standard" {{ request('taxi_type') == 'standard' ? 'selected' : '' }}>Standard
                            </option>
                            <option value="van" {{ request('taxi_type') == 'van' ? 'selected' : '' }}>Van</option>
                            <option value="luxe" {{ request('taxi_type') == 'luxe' ? 'selected' : '' }}>Luxe</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter btn-primary">
                            <i class="fas fa-search"></i>
                            Apply Filters
                        </button>
                    </div>
                    <div class="filter-actions">
                        <a href="{{ route('driver.bookings.available') }}" class="btn-filter btn-outline">
                            <i class="fas fa-undo"></i>
                            Reset All
                        </a>
                    </div>
                </form>
            </div>

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
                                        <span class="city">{{ $booking->pickupCity->name ?? 'Unknown City' }}</span>
                                    </div>
                                </div>
                                <div class="route-line"><i class="fas fa-ellipsis-v"></i></div>
                                <div class="route-point destination">
                                    <div class="point-details">
                                        <h5>Destination</h5>
                                        <p>{{ $booking->destinationCity->name ?? 'Unknown Destination' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-details">
                                <div class="detail-item">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $booking->client_name }}</span>
                                </div>
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
                                @if ($booking->estimated_fare)
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
                            <p>There are no pending bookings matching your criteria right now.</p>
                            <p>Try adjusting your filters or check back later!</p>
                            @if (request()->hasAny(['date', 'pickup_city_id', 'destination_city_id', 'client_name', 'taxi_type']))
                                <a href="{{ route('driver.bookings.available') }}" class="btn btn-primary">
                                    <i class="fas fa-undo"></i> Clear Filters
                                </a>
                            @else
                                <button class="btn btn-primary" onclick="location.reload()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Enhanced Pagination -->
            @if ($bookings->hasPages())
                <div class="custom-pagination">
                    <div class="pagination-summary">
                        Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }}
                        bookings
                    </div>
                    <div class="pagination-links">
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr for date input
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                locale: "fr",
                allowInput: false,
                clickOpens: true,
                disableMobile: true,
                placeholder: "Select date...",
                onChange: function(selectedDates, dateStr, instance) {
                    // Auto-submit form when date is selected (optional)
                    // document.getElementById('filtersForm').submit();
                }
            });

            // Filters toggle functionality
            const filtersToggle = document.getElementById('filtersToggle');
            const filtersForm = document.getElementById('filtersForm');

            if (filtersToggle && filtersForm) {
                filtersToggle.addEventListener('click', function() {
                    filtersForm.style.display = filtersForm.style.display === 'none' ? 'grid' : 'none';
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                });
            }

            // Double-click to clear inputs
            document.querySelectorAll('.filter-input, .filter-select').forEach(input => {
                input.addEventListener('dblclick', function() {
                    if (this.type === 'text') {
                        this.value = '';
                    } else if (this.tagName === 'SELECT') {
                        this.selectedIndex = 0;
                    }
                });
            });

            // Add loading state to form submission
            document.getElementById('filtersForm').addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.classList.add('loading');
            });

            // Add loading state to pagination links
            document.querySelectorAll('.pagination-links a').forEach(link => {
                link.addEventListener('click', function() {
                    if (!this.classList.contains('disabled')) {
                        this.classList.add('loading');
                    }
                });
            });

            // Auto-refresh every 60 seconds for new bookings
            setInterval(function() {
                // Only refresh if user is on first page and no filters applied
                const currentUrl = new URL(window.location.href);
                const hasFilters = currentUrl.search.length > 0;
                const isFirstPage = !currentUrl.searchParams.has('page') || currentUrl.searchParams.get(
                    'page') === '1';

                if (!hasFilters && isFirstPage) {
                    location.reload();
                }
            }, 60000);

            // Mobile menu toggle
            const menuToggle = document.getElementById('menuToggle');
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    document.querySelector('.dashboard-sidebar')?.classList.toggle('active');
                });
            }
        });
    </script>
@endsection
