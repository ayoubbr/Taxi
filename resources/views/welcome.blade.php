@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Book Your Taxi <span>Instantly</span></h2>
                <p>Fast, reliable and secure taxi service at your fingertips</p>
                <a href="#booking" class="btn-primary">Book Now</a>
            </div>
            <div class="hero-image">
                <img src="{{ asset('images/taxi-2.webp') }}" alt="Taxi illustration">
            </div>
        </div>
    </section>

    <section id="booking" class="booking-section">
        <div class="container">
            <div class="booking-card">
                <h3>Book Your Ride</h3>
                {{-- {{ route('reservation.create') }} --}}
                <form action="" method="GET" class="booking-form">
                    <div class="form-group">
                        <label for="pickup">
                            <i class="fas fa-map-marker-alt"></i>
                            Pickup Location
                        </label>
                        <input type="text" id="pickup" name="pickup" placeholder="Enter pickup address" required>
                    </div>

                    <div class="form-group">
                        <label for="destination">
                            <i class="fas fa-location-arrow"></i>
                            Destination
                        </label>
                        <input type="text" id="destination" name="destination" placeholder="Enter destination" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="date">
                                <i class="fas fa-calendar"></i>
                                Date
                            </label>
                            <input type="date" id="date" name="date" required>
                        </div>

                        <div class="form-group half">
                            <label for="time">
                                <i class="fas fa-clock"></i>
                                Time
                            </label>
                            <input type="time" id="time" name="time" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="taxi-type">
                            <i class="fas fa-taxi"></i>
                            Taxi Type
                        </label>
                        <select id="taxi-type" name="taxi_type" required>
                            <option value="">Select taxi type</option>
                            <option value="standard">Standard</option>
                            <option value="van">Van</option>
                            <option value="luxe">Luxe</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary btn-block">
                        <i class="fas fa-search"></i>
                        Find a Taxi
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose Us</h2>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3>QR Code Validation</h3>
                    <p>Secure and quick validation with our QR code system</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Fast Booking</h3>
                    <p>Book your taxi in seconds with our streamlined process</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Safe Rides</h3>
                    <p>All our drivers are verified and trained professionals</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Premium Service</h3>
                    <p>Experience comfort and quality with every ride</p>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>

            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Book Your Taxi</h3>
                        <p>Enter your pickup location, destination, and preferred time</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Get Your QR Code</h3>
                        <p>Receive a unique QR code for your booking</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Meet Your Driver</h3>
                        <p>Your driver will scan your QR code to confirm the ride</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Enjoy Your Ride</h3>
                        <p>Sit back, relax and enjoy your comfortable journey</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Book Your Taxi?</h2>
                <p>Fast, reliable and secure taxi service at your fingertips</p>
                <a href="#booking" class="btn-primary">Book Now</a>
            </div>
        </div>
    </section>

    <footer class="app-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="{{ asset('images/taxi-icon.png') }}" alt="Taxi Logo">
                    <h2>TaxiGo</h2>
                </div>

                <div class="footer-links">
                    <div class="link-group">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Services</a></li>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>

                    <div class="link-group">
                        <h3>Services</h3>
                        <ul>
                            <li><a href="#">Standard Taxi</a></li>
                            <li><a href="#">Van Service</a></li>
                            <li><a href="#">Luxe Experience</a></li>
                            <li><a href="#">Corporate</a></li>
                        </ul>
                    </div>

                    <div class="link-group">
                        <h3>Contact</h3>
                        <ul>
                            <li><a href="tel:+123456789"><i class="fas fa-phone"></i> +123 456 789</a></li>
                            <li><a href="mailto:info@taxigo.com"><i class="fas fa-envelope"></i> info@taxigo.com</a></li>
                            <li><a href="#"><i class="fas fa-map-marker-alt"></i> 123 Street, City</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 TaxiGo. All rights reserved.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>
@endsection

@section('js')
@endsection
