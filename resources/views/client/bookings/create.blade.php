@extends('client.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/client-bookings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
    <div class="client-dashboard mt-70">
        <!-- Header Section -->
        <header class="dashboard-header">
            <div class="container">
                <h1>Book Now</h1>
                <p>Create a booking of Taxi here</p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="container">

                @if (Auth::check() && Auth::user()->user_type != 'DRIVER' && Auth::user()->user_type != 'ADMIN')
                    <section id="booking" class="booking-section">
                        <div class="container">
                            <div class="booking-card">
                                <h3>Book Your Ride</h3>
                                <form action="{{ route('bookings.store') }}" method="POST" class="booking-form">
                                    @csrf
                                    <div class="form-group">
                                        <label for="client_name">
                                            <i class="fas fa-user"></i>
                                            Your Full Name
                                        </label>
                                        <input type="text" id="client_name" name="client_name"
                                            placeholder="Enter your name" required
                                            value="{{ Auth::check() ? Auth::user()->firstname . ' ' . Auth::user()->lastname : old('client_name') }}">
                                        @error('client_name')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- If you want a phone number input for guest bookings:
                    <div class="form-group">
                        <label for="client_phone">
                            <i class="fas fa-phone"></i>
                            Your Phone Number
                        </label>
                        <input type="tel" id="client_phone" name="client_phone" placeholder="Enter your phone number" required
                            value="{{ old('client_phone') }}">
                        @error('client_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    --}}

                                    <div class="form-group">
                                        <label for="pickup">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Pickup Location (Street, Building No.)
                                        </label>
                                        <input type="text" id="pickup" name="pickup_location"
                                            placeholder="Enter pickup address" required
                                            value="{{ old('pickup_location') }}">
                                        @error('pickup_location')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="pickup_city">
                                            <i class="fas fa-city"></i>
                                            Pickup City
                                        </label>
                                        <input type="text" id="pickup_city" name="pickup_city"
                                            placeholder="e.g., Marrakesh" required value="{{ old('pickup_city') }}">
                                        @error('pickup_city')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="destination">
                                            <i class="fas fa-location-arrow"></i>
                                            Destination
                                        </label>
                                        <input type="text" id="destination" name="destination"
                                            placeholder="Enter destination" required value="{{ old('destination') }}">
                                        @error('destination')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group half">
                                            <label for="date">
                                                <i class="fas fa-calendar"></i>
                                                Date
                                            </label>
                                            <input type="date" id="date" name="date" required
                                                value="{{ old('date', date('Y-m-d')) }}">
                                            @error('date')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group half">
                                            <label for="time">
                                                <i class="fas fa-clock"></i>
                                                Time
                                            </label>
                                            <input type="time" id="time" name="time" required
                                                value="{{ old('time', date('H:i')) }}">
                                            @error('time')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="taxi-type">
                                            <i class="fas fa-taxi"></i>
                                            Taxi Type
                                        </label>
                                        <select id="taxi-type" name="taxi_type" required
                                            class="@error('taxi_type') error @enderror">
                                            <option value="">Select taxi type</option>
                                            <option value="standard"
                                                {{ old('taxi_type') == 'standard' ? 'selected' : '' }}>Standard
                                            </option>
                                            <option value="van" {{ old('taxi_type') == 'van' ? 'selected' : '' }}>Van
                                            </option>
                                            <option value="luxe" {{ old('taxi_type') == 'luxe' ? 'selected' : '' }}>Luxe
                                            </option>
                                        </select>
                                        @error('taxi_type')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                        Find a Taxi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </main>
    </div>
@endsection
