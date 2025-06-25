@extends('driver.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            font-size: 14px;
        }

        .filter-form input[type="date"],
        .filter-form input[type="text"],
        .filter-form select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
            background: white;
        }

        .filter-form input:focus,
        .filter-form select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .filter-form .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: fit-content;
            justify-content: center;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            transform: translateY(-1px);
        }

        .filter-summary {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-summary .active-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-tag {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .filter-tag .remove {
            cursor: pointer;
            font-weight: bold;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .filter-form {
                padding: 16px;
                gap: 12px;
            }

            .filter-form .form-group {
                min-width: 100%;
                flex: none;
            }

            .filter-actions {
                width: 100%;
                justify-content: space-between;
            }

            .filter-form .btn {
                flex: 1;
                min-width: auto;
            }
        }

        @media (max-width: 480px) {
            .filter-actions {
                flex-direction: column;
                width: 100%;
            }

            .filter-form .btn {
                width: 100%;
            }
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
            <form action="{{ route('driver.dashboard') }}" method="GET" class="filter-form" id="filterForm">
                <div class="form-group">
                    <label for="date">
                        <i class="fas fa-calendar-alt"></i> Date
                    </label>
                    <input type="text" id="date" name="date" value="{{ request('date') }}"
                        placeholder="Select date" readonly>
                </div>

                <div class="form-group">
                    <label for="status">
                        <i class="fas fa-info-circle"></i> Status
                    </label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
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
                    <label for="pickup_city_id">
                        <i class="fas fa-map-marker-alt"></i> Pickup City
                    </label>
                    <select id="pickup_city_id" name="pickup_city_id">
                        <option value="">All Cities</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ request('pickup_city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="destination_city_id">
                        <i class="fas fa-location-arrow"></i> Destination City
                    </label>
                    <select id="destination_city_id" name="destination_city_id">
                        <option value="">All Cities</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ request('destination_city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="taxi_type">
                        <i class="fas fa-taxi"></i> Taxi Type
                    </label>
                    <select id="taxi_type" name="taxi_type">
                        <option value="">All Types</option>
                        <option value="standard" {{ request('taxi_type') == 'standard' ? 'selected' : '' }}>Standard
                        </option>
                        <option value="van" {{ request('taxi_type') == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="luxe" {{ request('taxi_type') == 'luxe' ? 'selected' : '' }}>Luxe</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="client_name">
                        <i class="fas fa-user"></i> Client Name
                    </label>
                    <input type="text" id="client_name" name="client_name" placeholder="Search by client name"
                        value="{{ request('client_name') }}">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="{{ route('driver.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>

            {{-- Active Filters Summary --}}
            @if (request()->hasAny(['date', 'status', 'client_name', 'pickup_city_id', 'destination_city_id', 'taxi_type']))
                <div class="filter-summary">
                    <i class="fas fa-filter"></i>
                    <span>Active filters:</span>
                    <div class="active-filters">
                        @if (request('date'))
                            <span class="filter-tag">
                                Date: {{ request('date') }}
                                <span class="remove" onclick="removeFilter('date')">&times;</span>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-tag">
                                Status: {{ request('status') }}
                                <span class="remove" onclick="removeFilter('status')">&times;</span>
                            </span>
                        @endif
                        @if (request('client_name'))
                            <span class="filter-tag">
                                Client: {{ request('client_name') }}
                                <span class="remove" onclick="removeFilter('client_name')">&times;</span>
                            </span>
                        @endif
                        @if (request('pickup_city_id'))
                            <span class="filter-tag">
                                Pickup: {{ $cities->find(request('pickup_city_id'))->name ?? 'Unknown' }}
                                <span class="remove" onclick="removeFilter('pickup_city_id')">&times;</span>
                            </span>
                        @endif
                        @if (request('destination_city_id'))
                            <span class="filter-tag">
                                Destination: {{ $cities->find(request('destination_city_id'))->name ?? 'Unknown' }}
                                <span class="remove" onclick="removeFilter('destination_city_id')">&times;</span>
                            </span>
                        @endif
                        @if (request('taxi_type'))
                            <span class="filter-tag">
                                Type: {{ ucfirst(request('taxi_type')) }}
                                <span class="remove" onclick="removeFilter('taxi_type')">&times;</span>
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bookings-container">
                @forelse ($bookings as $booking)
                    <div class="booking-card" data-status="{{ $booking->status }}">
                        <div class="booking-header">
                            <div class="booking-id">
                                <span class="label">Booking ID:</span>
                                <span class="value">{{ substr($booking->booking_uuid, 0, 8) }}</span>
                            </div>
                            <div class="status-badge status-{{ $booking->status }}">
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
                                    <span class="city">{{ $booking->pickupCity->name ?? 'Unknown City' }}</span>
                                </div>
                                <div class="arrow-icon"><i class="fas fa-arrow-right"></i></div>
                                <div class="route-point destination">
                                    <div class="point-details">
                                        <i class="fas fa-location-arrow"></i>
                                        <span>Destination: {{ $booking->destinationCity->name ?? 'Unknown City' }}</span>
                                    </div>
                                    {{-- <span class="city">{{ $booking->destinationCity->name ?? 'Unknown City' }}</span> --}}
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
                                    class="btn btn-primary" style="min-width: max-content">
                                    <i class="fas fa-qrcode"></i> Scan QR
                                </a>
                                <button class="btn btn-secondary btn-directions" style="width: -webkit-fill-available;">
                                    <i class="fas fa-map-marked-alt"></i> Get Directions
                                </button>
                            @elseif ($booking->status === 'IN_PROGRESS')
                                <form action="{{ route('driver.booking.update-status', $booking) }}" method="POST"
                                    style="width: -webkit-fill-available;">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="status" value="COMPLETED">
                                    <button type="submit" class="btn btn-success"
                                        style="min-width: -webkit-fill-available;">
                                        <i class="fas fa-check-circle"></i> Complete Ride
                                    </button>
                                </form>
                                <button class="btn btn-secondary btn-navigate" style="width: -webkit-fill-available;">
                                    <i class="fas fa-map-marked-alt"></i> Navigate
                                </button>
                            @endif
                            @if ($booking->status === 'ASSIGNED' || $booking->status === 'IN_PROGRESS')
                                <form action="{{ route('driver.booking.update-status', $booking) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to cancel this booking?');"
                                    style="width: -webkit-fill-available;">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="status" value="CANCELLED">
                                    <button type="submit" class="btn btn-danger" style="width: -webkit-fill-available;">
                                        <i class="fas fa-times-circle"></i> Cancel Booking
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
                            <h3>No Bookings Found</h3>
                            <p>No bookings match your current filters.</p>
                            <p>Try adjusting your search criteria or <a href="{{ route('driver.dashboard') }}">reset
                                    filters</a>.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            <div class="pagination-links">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                allowInput: true,
                placeholder: "Select date...",
                locale: {
                    firstDayOfWeek: 1 // Monday
                }
            });

            // Remove individual filter function
            window.removeFilter = function(filterName) {
                const url = new URL(window.location);
                url.searchParams.delete(filterName);
                window.location.href = url.toString();
            };

            // Auto-submit form on select change (optional)
            const selects = document.querySelectorAll('#status, #pickup_city_id, #destination_city_id, #taxi_type');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    // Uncomment the line below if you want auto-submit on change
                    document.getElementById('filterForm').submit();
                });
            });

            // Clear individual inputs with double-click
            const inputs = document.querySelectorAll('input[type="text"], select');
            inputs.forEach(input => {
                input.addEventListener('dblclick', function() {
                    if (this.type === 'text') {
                        this.value = '';
                    } else if (this.tagName === 'SELECT') {
                        this.selectedIndex = 0;
                    }
                });
            });
        });
    </script>
@endsection
