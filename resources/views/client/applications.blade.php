@extends('client.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/client-applications.css') }}">
@endsection

@section('content')
    <div class="applications-page mt-82">
        <!-- Header Section -->
        <header class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-left">
                        <a href="{{ route('client.bookings.show', $booking->booking_uuid) }}" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="header-info">
                            <h1>Driver Applications</h1>
                            <p>Booking #{{ substr($booking->booking_uuid, 0, 8) }}</p>
                        </div>
                    </div>

                    <div class="booking-summary">
                        <div class="summary-item">
                            <i class="fas fa-calendar"></i>
                            <span>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('d M Y') }}</span>
                        </div>
                        <div class="summary-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ \Carbon\Carbon::parse($booking->pickup_datetime)->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="page-content">
            <div class="container">
                <!-- Trip Summary Card -->
                <div class="trip-summary-card">
                    <h2>Trip Details</h2>
                    <div class="trip-route">
                        <div class="route-point pickup">
                            <div class="point-marker">A</div>
                            <div class="point-details">
                                <h4>Pickup</h4>
                                <p>{{ $booking->pickup_location }}</p>
                                <span class="city">{{ $booking->pickup_city }}</span>
                            </div>
                        </div>

                        <div class="route-divider">
                            <div class="route-line"></div>
                            <div class="route-distance">
                                @if ($booking->distance)
                                    <span>{{ $booking->distance }} km</span>
                                @else
                                    <i class="fas fa-ellipsis-h"></i>
                                @endif
                            </div>
                        </div>

                        <div class="route-point destination">
                            <div class="point-marker">B</div>
                            <div class="point-details">
                                <h4>Destination</h4>
                                <p>{{ $booking->destination }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="trip-meta">
                        <div class="meta-item">
                            <i class="fas fa-taxi"></i>
                            <span>{{ ucfirst($booking->taxi_type) }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $booking->passengers }} passenger{{ $booking->passengers > 1 ? 's' : '' }}</span>
                        </div>
                        @if ($booking->estimated_fare)
                            <div class="meta-item">
                                <i class="fas fa-dollar-sign"></i>
                                <span>${{ number_format($booking->estimated_fare, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Applications Section -->
                <div class="applications-section">
                    <div class="section-header">
                        <h2>Available Drivers</h2>
                        <div class="applications-count">
                            <span class="count">{{ $applications->count() }}</span>
                            <span class="label">{{ $applications->count() === 1 ? 'Application' : 'Applications' }}</span>
                        </div>
                    </div>

                    @if ($applications->isEmpty())
                        <div class="no-applications">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <h3>No Applications Yet</h3>
                                <p>No drivers have applied for your trip yet.</p>
                                <p>Please check back in a few minutes, or consider adjusting your pickup time.</p>
                                <div class="empty-actions">
                                    <a href="{{ route('client.bookings.show', $booking->booking_uuid) }}"
                                        class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Booking
                                    </a>
                                    <button class="btn btn-primary" onclick="location.reload()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="applications-grid">
                            @foreach ($applications as $application)
                                <div class="application-card">
                                    <div class="driver-header">
                                        <div class="driver-profile">
                                            <div class="driver-avatar">
                                                @if ($application->driver->profile_photo)
                                                    <img src="{{ $application->driver->profile_photo }}"
                                                        alt="Driver Photo">
                                                @else
                                                    <span>{{ strtoupper(substr($application->driver->firstname, 0, 1)) }}</span>
                                                @endif
                                            </div>

                                            <div class="driver-info">
                                                <h3>{{ $application->driver->firstname }}
                                                    {{ $application->driver->lastname }}</h3>
                                                {{-- <div class="driver-rating">
                                                    @php
                                                        $rating = $application->driver->rating ?? 4.5;
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $rating)
                                                            <i class="fas fa-star"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="rating-value">{{ number_format($rating, 1) }}</span>
                                                </div>
                                                <div class="driver-experience">
                                                    <i class="fas fa-road"></i>
                                                    <span>{{ $application->driver->years_experience ?? 3 }} years
                                                        experience</span>
                                                </div> --}}
                                            </div>
                                        </div>

                                        <div class="application-time">
                                            <span class="time-label">Applied</span>
                                            <span class="time-value">{{ $application->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    <div class="taxi-details">
                                        <h4>Vehicle Information</h4>
                                        <div class="taxi-info">
                                            <div class="taxi-image">
                                                @if ($application->taxi->photo)
                                                    <img src="{{ $application->taxi->photo }}" alt="Taxi Photo">
                                                @else
                                                    <div class="taxi-placeholder">
                                                        <i class="fas fa-taxi"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="taxi-specs">
                                                <div class="spec-item">
                                                    <span class="spec-label">Type:</span>
                                                    <span class="spec-value">{{ ucfirst($application->taxi->type) }}</span>
                                                </div>
                                                <div class="spec-item">
                                                    <span class="spec-label">Model:</span>
                                                    <span
                                                        class="spec-value">{{ $application->taxi->model ?? 'Toyota Camry' }}</span>
                                                </div>
                                                <div class="spec-item">
                                                    <span class="spec-label">License:</span>
                                                    <span class="spec-value">{{ $application->taxi->license_plate }}</span>
                                                </div>
                                                <div class="spec-item">
                                                    <span class="spec-label">Capacity:</span>
                                                    <span class="spec-value">{{ $application->taxi->capacity ?? 0 }}
                                                        passengers</span>
                                                </div>
                                                {{-- <div class="spec-item">
                                                    <span class="spec-label">Year:</span>
                                                    <span
                                                        class="spec-value">{{ $application->taxi->year ?? '2020' }}</span>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="driver-features">
                                        <h4>Features & Services</h4>
                                        <div class="features-list">
                                            <div class="feature-item">
                                                <i class="fas fa-wifi"></i>
                                                <span>Free WiFi</span>
                                            </div>
                                            <div class="feature-item">
                                                <i class="fas fa-snowflake"></i>
                                                <span>Air Conditioning</span>
                                            </div>
                                            <div class="feature-item">
                                                <i class="fas fa-music"></i>
                                                <span>Music System</span>
                                            </div>
                                            <div class="feature-item">
                                                <i class="fas fa-charging-station"></i>
                                                <span>Phone Charger</span>
                                            </div>
                                            @if ($application->driver->speaks_english ?? true)
                                                <div class="feature-item">
                                                    <i class="fas fa-language"></i>
                                                    <span>English Speaking</span>
                                                </div>
                                            @endif
                                            @if ($application->driver->accepts_pets ?? false)
                                                <div class="feature-item">
                                                    <i class="fas fa-paw"></i>
                                                    <span>Pet Friendly</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div> --}}

                                    {{-- @if ($application->message)
                                        <div class="driver-message">
                                            <h4>Message from Driver</h4>
                                            <p>"{{ $application->message }}"</p>
                                        </div>
                                    @endif --}}

                                    <div class="application-actions">
                                        <div class="price-info">
                                            @if ($application->proposed_fare)
                                                <span class="price-label">Proposed Fare:</span>
                                                <span
                                                    class="price-value">${{ number_format($application->proposed_fare, 2) }}</span>
                                            @endif
                                        </div>

                                        <div class="action-buttons">
                                            {{-- <button class="btn btn-secondary btn-contact"
                                                data-phone="{{ $application->driver->phone }}">
                                                <i class="fas fa-phone"></i> Contact
                                            </button> --}}
                                            @if ($booking->status == 'PENDING')
                                                <form
                                                    action="{{ route('client.bookings.accept_application', ['booking' => $booking, 'application' => $application]) }}"
                                                    method="POST" class="accept-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-accept">
                                                        <i class="fas fa-check"></i> Accept & Assign
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Contact Modal -->
    <div class="modal" id="contactModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Contact Driver</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>You can contact the driver using the following methods:</p>
                <div class="contact-options">
                    <a href="#" id="callDriver" class="contact-option">
                        <i class="fas fa-phone"></i>
                        <span>Call Driver</span>
                    </a>
                    <a href="#" id="smsDriver" class="contact-option">
                        <i class="fas fa-sms"></i>
                        <span>Send SMS</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Accept Confirmation Modal -->
    <div class="modal" id="acceptModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Driver Selection</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to accept this driver for your trip?</p>
                <p><strong>Note:</strong> Once accepted, other applications will be automatically declined.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal">Cancel</button>
                <button class="btn btn-primary" id="confirmAccept">Yes, Accept Driver</button>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Contact modal functionality
            const contactModal = document.getElementById('contactModal');
            const acceptModal = document.getElementById('acceptModal');
            const contactButtons = document.querySelectorAll('.btn-contact');
            const acceptButtons = document.querySelectorAll('.btn-accept');
            const closeButtons = document.querySelectorAll('.close-modal');

            let currentForm = null;

            // Contact driver
            contactButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const phone = this.getAttribute('data-phone');
                    const callLink = document.getElementById('callDriver');
                    const smsLink = document.getElementById('smsDriver');

                    callLink.href = `tel:${phone}`;
                    smsLink.href = `sms:${phone}`;

                    contactModal.classList.add('active');
                });
            });

            // Accept driver confirmation
            acceptButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentForm = this.closest('.accept-form');
                    acceptModal.classList.add('active');
                });
            });

            // Confirm accept
            document.getElementById('confirmAccept').addEventListener('click', function() {
                if (currentForm) {
                    // Add loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    this.disabled = true;

                    // Submit form
                    currentForm.submit();
                }
            });

            // Close modals
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    contactModal.classList.remove('active');
                    acceptModal.classList.remove('active');
                });
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === contactModal) {
                    contactModal.classList.remove('active');
                }
                if (event.target === acceptModal) {
                    acceptModal.classList.remove('active');
                }
            });

            // Auto-refresh every 30 seconds to check for new applications
            setInterval(function() {
                // Only refresh if no modals are open
                if (!contactModal.classList.contains('active') && !acceptModal.classList.contains(
                        'active')) {
                    location.reload();
                }
            }, 30000);
        });
    </script>
@endsection
