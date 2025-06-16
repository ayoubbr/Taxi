@extends('client.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/booking-details.css') }}">
@endsection

@section('content')
    <div class="booking-details-page mt-70">
        <!-- Header Section -->
        <header class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-left">
                        <a href="{{ route('client.bookings.index') }}" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1>Booking Details</h1>
                    </div>

                    <div class="booking-id">
                        <span class="label">Booking ID:</span>
                        <span class="value">{{ $booking->booking_uuid }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="page-content">
            <div class="container">
                <div class="booking-details-container">
                    <div class="details-grid">
                        <!-- Status Card -->
                        <div class="booking-status-card">
                            <div class="status-header">
                                <h2>Booking Status</h2>
                                <div class="status-badge status-{{ $booking->status }}">
                                    {{ str_replace('_', ' ', $booking->status) }}
                                </div>
                            </div>

                            <div class="status-timeline">
                                <div
                                    class="timeline-item {{ in_array($booking->status, ['PENDING', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED']) ? 'active' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Booking Confirmed</h4>
                                        <p>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>

                                <div
                                    class="timeline-item {{ in_array($booking->status, ['ASSIGNED', 'IN_PROGRESS', 'COMPLETED']) ? 'active' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Driver Assigned</h4>
                                        @if (in_array($booking->status, ['ASSIGNED', 'IN_PROGRESS', 'COMPLETED']))
                                            <p>{{ \Carbon\Carbon::parse($booking->assigned_at)->format('d M Y, H:i') }}</p>
                                        @else
                                            <p>Waiting for driver assignment</p>
                                        @endif
                                    </div>
                                </div>

                                <div
                                    class="timeline-item {{ in_array($booking->status, ['IN_PROGRESS', 'COMPLETED']) ? 'active' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Ride in Progress</h4>
                                        @if (in_array($booking->status, ['IN_PROGRESS', 'COMPLETED']))
                                            <p>{{ \Carbon\Carbon::parse($booking->started_at)->format('d M Y, H:i') }}</p>
                                        @else
                                            <p>Not started yet</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="timeline-item {{ $booking->status === 'COMPLETED' ? 'active' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-flag-checkered"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Ride Completed</h4>
                                        @if ($booking->status === 'COMPLETED')
                                            <p>{{ \Carbon\Carbon::parse($booking->completed_at)->format('d M Y, H:i') }}
                                            </p>
                                        @else
                                            <p>Not completed yet</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- QR Code Card -->
                        <div class="details-card qr-code-card">
                            <h2>Booking QR Code</h2>

                            <div class="qr-container">
                                <div class="qr-code">
                                    {{-- <img src="{{ $booking->qr_code_path }}" alt="Booking QR Code"> --}}
                                    {!! $qrCodeSvg !!}
                                </div>
                                <p class="qr-instructions">Show this QR code to your driver to verify your booking</p>
                                <a href="" download="booking-{{ $booking->booking_uuid }}.png"
                                    class="btn btn-secondary">
                                    <i class="fas fa-download"></i> Download QR Code
                                </a>
                            </div>
                        </div>
                        <!-- Booking Info Card -->
                        <div class="details-card booking-info-card">
                            <h2>Booking Information</h2>

                            <div class="info-group">
                                <div class="info-item">
                                    <span class="info-label">Booking Date</span>
                                    <span
                                        class="info-value">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Pickup Date</span>
                                    <span
                                        class="info-value">{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y') }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Pickup Time</span>
                                    <span
                                        class="info-value">{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('H:i') }}</span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Taxi Type</span>
                                    @if (isset($booking->taxi_type))
                                        <span class="info-value">{{ ucfirst($booking->taxi_type) }}</span>
                                    @else
                                        <span class="info-value">N/A</span>
                                    @endif
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Passengers</span>
                                    {{-- <span class="info-value">{{ $booking->passengers }}</span> --}}
                                    <span class="info-value">N/A</span>
                                </div>

                                @if ($booking->estimated_fare)
                                    <div class="info-item">
                                        <span class="info-label">Estimated Fare</span>
                                        <span class="info-value">${{ number_format($booking->estimated_fare, 2) }}</span>
                                    </div>
                                @endif

                                @if ($booking->notes)
                                    <div class="info-item full-width">
                                        <span class="info-label">Special Notes</span>
                                        <span class="info-value">{{ $booking->notes }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- Location Card -->
                        <div class="details-card location-card">
                            <h2>Journey Details</h2>

                            <div class="journey-container">
                                <div class="journey-point pickup">
                                    <div class="point-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="point-details">
                                        <h4>Pickup Location</h4>
                                        <p>{{ $booking->pickup_location }}</p>
                                        <span class="city">{{ $booking->pickup_city }}</span>
                                    </div>
                                </div>

                                <div class="journey-divider">
                                    <div class="journey-line"></div>
                                    <div class="journey-distance">
                                        @if ($booking->distance)
                                            <span>{{ $booking->distance }} km</span>
                                        @else
                                            <i class="fas fa-ellipsis-h"></i>
                                        @endif
                                    </div>
                                </div>

                                <div class="journey-point destination">
                                    <div class="point-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="point-details">
                                        <h4>Destination</h4>
                                        <p>{{ $booking->destination }}</p>
                                        @if ($booking->destination_city)
                                            <span class="city">{{ $booking->destination_city }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="map-container">
                                <div class="map-placeholder">
                                    <i class="fas fa-map"></i>
                                    <span>Map view will be available soon</span>
                                </div>
                            </div>
                        </div>
                        <!-- Driver Card -->
                        @if ($booking->driver)
                            <div class="details-card driver-card">
                                <h2>Driver Information</h2>

                                <div class="driver-container">
                                    <div class="driver-profile">
                                        <div class="driver-avatar">
                                            @if ($booking->driver->profile_photo)
                                                <img src="{{ $booking->driver->profile_photo }}" alt="Driver Photo">
                                            @else
                                                <span>{{ strtoupper(substr($booking->driver->firstname, 0, 1)) }}</span>
                                            @endif
                                        </div>

                                        <div class="driver-info">
                                            <h3>{{ $booking->driver->firstname }} {{ $booking->driver->lastname }}</h3>
                                            <div class="driver-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $booking->driver->rating)
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                                <span>{{ $booking->driver->rating }}/5</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="driver-vehicle">
                                        <div class="vehicle-info">
                                            <div class="vehicle-model">
                                                <i class="fas fa-car"></i>
                                                <span>{{ $booking->driver->vehicle_model }}</span>
                                            </div>
                                            <div class="vehicle-plate">
                                                <i class="fas fa-id-card"></i>
                                                <span>{{ $booking->driver->vehicle_plate }}</span>
                                            </div>
                                        </div>

                                        <div class="driver-contact">
                                            <a href="tel:{{ $booking->driver->phone }}" class="btn btn-contact">
                                                <i class="fas fa-phone"></i> Call Driver
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- Action Buttons -->
                    <div class="booking-actions">
                        @if (in_array($booking->status, ['PENDING', 'ASSIGNED']))
                            <button class="btn btn-danger" id="cancelBookingBtn">
                                <i class="fas fa-times"></i> Cancel Booking
                            </button>
                        @endif

                        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Booking
                        </a>
                        @if ($booking->status == 'PENDING')
                            <a href="{{ route('client.bookings.applications', $booking) }}" class="btn btn-primary">
                                <i class="fa-solid fa-eye"></i> Show Applications
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Cancel Booking Modal -->
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
                <form action="{{ route('bookings.cancel', $booking->booking_uuid) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="CANCELLED">
                    <button type="submit" class="btn btn-danger">Yes, Cancel Booking</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cancel booking modal
            const modal = document.getElementById('cancelModal');
            const cancelButton = document.getElementById('cancelBookingBtn');
            const closeButtons = document.querySelectorAll('.close-modal');

            if (cancelButton) {
                cancelButton.addEventListener('click', function() {
                    modal.classList.add('active');
                });
            }

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
