@extends('driver.layout')

@section('css')
    {{-- <link rel="stylesheet" href="{{ asset('css/driver-dashboard.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/driver-profile.css') }}">
@endsection

@section('content')
    <main class="dashboard-main">
        <header class="dashboard-header">
            {{-- <div class="profile-avatar">
                <div class="avatar-container">
                    <div class="avatar">
                        @if ($driver->profile_photo)
                            <img src="{{ $driver->profile_photo }}" alt="Profile Photo">
                        @else
                            {{ strtoupper(substr($driver->firstname, 0, 1)) }}{{ strtoupper(substr($driver->lastname, 0, 1)) }}
                        @endif
                    </div>
                    <div class="avatar-upload" title="Upload Photo">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="status-indicator status-online">
                    <span>Online</span>
                </div>
            </div> --}}
            <div class="header-left" id="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1> My Profile</h1>
                {{-- <h1>Driver Dashboard</h1> --}}
            </div>
            {{-- <p>Manage your personal and vehicle information to provide the best service to your clients.</p> --}}
            {{-- <i class="fas fa-user-cog"></i>  --}}
        </header>

        <div class="dashboard-content">
            <form action="{{ route('driver.profile.update') }}" method="POST" id="profileForm">
                @csrf
                @method('PUT')

                <div class="profile-page">
                    <!-- Personal Information -->
                    <div class="form-card">
                        <h4><i class="fas fa-user-edit"></i> Personal Information</h4>

                        <div class="form-row">
                            <div class="form-group half {{ $errors->has('firstname') ? 'has-error' : '' }}">
                                <label for="firstname">
                                    <i class="fas fa-user"></i> First Name *
                                </label>
                                <input type="text" name="firstname" id="firstname"
                                    value="{{ old('firstname', $driver->firstname) }}" placeholder="Enter your first name"
                                    required>
                                @error('firstname')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group half {{ $errors->has('lastname') ? 'has-error' : '' }}">
                                <label for="lastname">
                                    <i class="fas fa-user"></i> Last Name *
                                </label>
                                <input type="text" name="lastname" id="lastname"
                                    value="{{ old('lastname', $driver->lastname) }}" placeholder="Enter your last name"
                                    required>
                                @error('lastname')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                            <label for="username">
                                <i class="fas fa-at"></i> Username *
                            </label>
                            <input type="text" name="username" id="username"
                                value="{{ old('username', $driver->username) }}" placeholder="Choose a unique username"
                                required>
                            @error('username')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label for="email">
                                <i class="fas fa-envelope"></i> Email Address *
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $driver->email) }}"
                                placeholder="your.email@example.com" required>
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                            <label for="phone">
                                <i class="fas fa-phone"></i> Phone Number
                            </label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $driver->phone) }}"
                                placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group half {{ $errors->has('password') ? 'has-error' : '' }}">
                                <label for="password">
                                    <i class="fas fa-lock"></i> New Password
                                </label>
                                <input type="password" name="password" id="password"
                                    placeholder="Leave blank to keep current">
                                @error('password')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group half">
                                <label for="password_confirmation">
                                    <i class="fas fa-lock"></i> Confirm Password
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>
                    @if (Auth::user()->taxi != null)
                        <!-- Taxi Information -->
                        <div class="form-card">
                            <h4><i class="fas fa-taxi"></i> Vehicle Information</h4>

                            <div class="form-group {{ $errors->has('license_plate') ? 'has-error' : '' }}">
                                <label for="license_plate">
                                    <i class="fas fa-id-card"></i> License Plate *
                                </label>
                                <input type="text" name="license_plate" id="license_plate"
                                    value="{{ old('license_plate', $taxi->license_plate ?? '') }}" placeholder="ABC-1234"
                                    style="text-transform: uppercase;" required>
                                @error('license_plate')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group {{ $errors->has('model') ? 'has-error' : '' }}">
                                <label for="model">
                                    <i class="fas fa-car"></i> Vehicle Model *
                                </label>
                                <input type="text" name="model" id="model"
                                    value="{{ old('model', $taxi->model ?? '') }}"
                                    placeholder="e.g., Toyota Prius, Honda Civic" required>
                                @error('model')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group half {{ $errors->has('type') ? 'has-error' : '' }}">
                                    <label for="type">
                                        <i class="fas fa-tags"></i> Vehicle Type *
                                    </label>
                                    <select name="type" id="type" required>
                                        <option value="">Select vehicle type</option>
                                        <option value="standard"
                                            {{ old('type', $taxi->type ?? '') == 'standard' ? 'selected' : '' }}>
                                            🚗 Standard
                                        </option>
                                        <option value="van"
                                            {{ old('type', $taxi->type ?? '') == 'van' ? 'selected' : '' }}>
                                            🚐 Van
                                        </option>
                                        <option value="luxe"
                                            {{ old('type', $taxi->type ?? '') == 'luxe' ? 'selected' : '' }}>
                                            🚙 Luxury
                                        </option>
                                    </select>
                                    @error('type')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group half {{ $errors->has('capacity') ? 'has-error' : '' }}">
                                    <label for="capacity">
                                        <i class="fas fa-users"></i> Passenger Capacity *
                                    </label>
                                    <input type="number" name="capacity" id="capacity"
                                        value="{{ old('capacity', $taxi->capacity ?? '') }}" min="1"
                                        max="8" placeholder="4" required>
                                    @error('capacity')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group half {{ $errors->has('year') ? 'has-error' : '' }}">
                                    <label for="year">
                                        <i class="fas fa-calendar"></i> Vehicle Year
                                    </label>
                                    <input type="number" name="year" id="year"
                                        value="{{ old('year', $taxi->year ?? '') }}" min="2000"
                                        max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}">
                                    @error('year')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group half {{ $errors->has('color') ? 'has-error' : '' }}">
                                    <label for="color">
                                        <i class="fas fa-palette"></i> Vehicle Color
                                    </label>
                                    <input type="text" name="color" id="color"
                                        value="{{ old('color', $taxi->color ?? '') }}"
                                        placeholder="e.g., White, Black, Silver">
                                    @error('color')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('city') ? 'has-error' : '' }}">
                                <label for="city">
                                    <i class="fas fa-map-marker-alt"></i> Operating City *
                                </label>
                                <select name="city" id="city" required>
                                    <option value="">Select your operating city</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city }}"
                                            {{ old('city', $taxi->city->name ?? '') == $city ? 'selected' : '' }}>
                                            📍 {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo"></i> Reset Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const saveBtn = document.getElementById('saveBtn');

            // Form submission with loading state
            form.addEventListener('submit', function() {
                saveBtn.classList.add('loading');
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                saveBtn.disabled = true;
            });

            // License plate formatting
            const licensePlateInput = document.getElementById('license_plate');
            if (licensePlateInput) {
                licensePlateInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            }

            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.length >= 6) {
                        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                    } else if (value.length >= 3) {
                        value = value.replace(/(\d{3})(\d{3})/, '($1) $2');
                    }
                    this.value = value;
                });
            }

            // Real-time validation
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
            });

            function validateField(field) {
                const formGroup = field.closest('.form-group');
                const errorMessage = formGroup.querySelector('.error-message');

                // Remove existing validation classes
                formGroup.classList.remove('has-error', 'has-success');

                if (field.checkValidity()) {
                    formGroup.classList.add('has-success');
                } else {
                    formGroup.classList.add('has-error');
                }
            }

            // Password confirmation validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            if (passwordInput && confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    if (passwordInput.value !== this.value) {
                        this.setCustomValidity('Passwords do not match');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });

        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.getElementById('profileForm').reset();

                // Remove validation classes
                const formGroups = document.querySelectorAll('.form-group');
                formGroups.forEach(group => {
                    group.classList.remove('has-error', 'has-success');
                });
            }
        }
    </script>
@endsection
