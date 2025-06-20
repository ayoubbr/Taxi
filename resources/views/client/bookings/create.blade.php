@extends('client.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/client-bookings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <!-- 1. Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    <div class="client-dashboard mt-70">
        <!-- Header Section -->
        <header class="dashboard-header">
            <div class="container">
                <h1>Book Your Ride</h1>
                <p>Fill in the details below to schedule your taxi.</p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="container">
                @if (Auth::check() && !Auth::user()->hasRole('DRIVER') && !Auth::user()->hasRole('AGENCY_ADMIN'))
                    <section id="booking" class="booking-section">
                        <div class="booking-card-wrapper">
                            <div class="booking-card">
                                <h3>Booking Details</h3>
                                <form action="{{ route('client.bookings.store') }}" method="POST" class="booking-form">
                                    @csrf
                                    <div class="form-group">
                                        <label for="client_name"><i class="fas fa-user"></i> Your Full Name</label>
                                        <input type="text" id="client_name" name="client_name"
                                            placeholder="Enter your name" required
                                            value="{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}">
                                        @error('client_name')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="pickup_location"><i class="fas fa-map-marker-alt"></i> Pickup Street
                                            Address</label>
                                        <input type="text" id="pickup_location" name="pickup_location"
                                            placeholder="e.g., 123 Main Street, Apt 4B" required
                                            value="{{ old('pickup_location') }}">
                                        @error('pickup_location')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <!-- City Pickers -->
                                        <div class="form-group half">
                                            <label for="pickup_city"><i class="fas fa-city"></i> Pickup City</label>
                                            <select id="pickup_city" name="pickup_city" required>
                                                <option value="">Select a city</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->name }}"
                                                        {{ old('pickup_city') == $city->name ? 'selected' : '' }}>
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('pickup_city')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group half">
                                            <label for="destination"><i class="fas fa-map-marked-alt"></i> Destination
                                                City</label>
                                            <select id="destination" name="destination" required>
                                                <option value="">Select a city</option>
                                                @foreach ($cities as $city)
                                                    <option value="{{ $city->name }}"
                                                        {{ old('destination') == $city->name ? 'selected' : '' }}>
                                                        {{ $city->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('destination')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <!-- Date/Time Pickers -->
                                        <div class="form-group half">
                                            <label for="date"><i class="fas fa-calendar-alt"></i> Date</label>
                                            <input type="text" id="date-picker" name="date" required
                                                value="{{ old('date') }}" placeholder="Select a date...">
                                            @error('date')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group half">
                                            <label for="time"><i class="fas fa-clock"></i> Time</label>
                                            <input type="text" id="time-picker" name="time" required
                                                value="{{ old('time') }}" placeholder="Select a time...">
                                            @error('time')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    @error('pickup_datetime_combined')
                                        <div class="form-group"><span class="error-message">{{ $message }}</span></div>
                                    @enderror

                                    <div class="form-group">
                                        <label for="taxi-type"><i class="fas fa-taxi"></i> Taxi Type</label>
                                        <select id="taxi-type" name="taxi_type" required>
                                            <option value="">Select taxi type</option>
                                            <option value="standard"
                                                {{ old('taxi_type') == 'standard' ? 'selected' : '' }}>Standard</option>
                                            <option value="van" {{ old('taxi_type') == 'van' ? 'selected' : '' }}>Van
                                            </option>
                                            <option value="luxe" {{ old('taxi_type') == 'luxe' ? 'selected' : '' }}>Luxe
                                            </option>
                                        </select>
                                        @error('taxi_type')
                                            <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block"><i
                                            class="fas fa-paper-plane"></i> Create Booking</button>
                                </form>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </main>
    </div>
@endsection

@section('js')
    <!-- 2. Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const datePickerEl = document.getElementById('date-picker');
            const timePickerEl = document.getElementById('time-picker');

            // Function to get the current time in H:i format
            const getCurrentTime = () => {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            };

            // Initialize date picker
            const datePicker = flatpickr(datePickerEl, {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                minDate: "today",
                // When the date changes, we need to adjust the minimum time for the time picker
                onChange: function(selectedDates, dateStr, instance) {
                    // If selected date is today, set minTime to now. Otherwise, remove the restriction.
                    if (dateStr === instance.formatDate(new Date(), "Y-m-d")) {
                        timePicker.set('minTime', getCurrentTime());
                    } else {
                        timePicker.set('minTime', null);
                    }
                },
            });

            // Initialize time picker
            const timePicker = flatpickr(timePickerEl, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 15,
            });

            // On page load, if the selected date (from old input) is today, set the min time.
            if (datePicker.selectedDates.length > 0) {
                const selectedDateStr = datePicker.formatDate(datePicker.selectedDates[0], "Y-m-d");
                const todayStr = datePicker.formatDate(new Date(), "Y-m-d");
                if (selectedDateStr === todayStr) {
                    timePicker.set('minTime', getCurrentTime());
                }
            }
        });
    </script>
@endsection
