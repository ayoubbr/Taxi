@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <img src="{{ asset('images/taxi-icon.png') }}" alt="Taxi Logo">
                    <h1>TaxiGo</h1>
                </div>
                <h2>Create Account</h2>
                <p>Join us and start booking your rides today</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf
                <!-- Name -->
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        placeholder="Enter your full name" required autofocus autocomplete="name"
                        class="@error('name') error @enderror">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        placeholder="Enter your email address" required autocomplete="username"
                        class="@error('email') error @enderror">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i>
                        Phone Number
                    </label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                        placeholder="Enter your phone number" required autocomplete="tel"
                        class="@error('phone') error @enderror">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <div class="password-input">
                        <input id="password" type="password" name="password" placeholder="Create a strong password"
                            required autocomplete="new-password" class="@error('password') error @enderror">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="fas fa-lock"></i>
                        Confirm Password
                    </label>
                    <div class="password-input">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            placeholder="Confirm your password" required autocomplete="new-password"
                            class="@error('password_confirmation') error @enderror">
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye" id="password_confirmation-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- User Type -->
                <div class="form-group">
                    <label for="user_type">
                        <i class="fas fa-users"></i>
                        Account Type
                    </label>
                    <select id="user_type" name="user_type" required class="@error('user_type') error @enderror">
                        <option value="">Select account type</option>
                        <option value="client" {{ old('user_type') == 'client' ? 'selected' : '' }}>Client</option>
                        <option value="driver" {{ old('user_type') == 'driver' ? 'selected' : '' }}>Driver</option>
                    </select>
                    @error('user_type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Terms and Conditions -->
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#" class="link">Terms of Service</a> and <a href="#"
                            class="link">Privacy Policy</a>
                    </label>
                    @error('terms')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary btn-block">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>

                <!-- Login Link -->
                <div class="auth-footer">
                    <p>Already have an account? <a href="{{ route('login') }}" class="link">Sign in here</a></p>
                </div>
            </form>
        </div>

        <!-- Side Image -->
        <div class="auth-image">
            <img src="{{ asset('images/auth-illustration.svg') }}" alt="Registration illustration">
            <div class="auth-image-content">
                <h3>Join Our Community</h3>
                <p>Experience the convenience of modern taxi booking with QR code validation and real-time tracking.</p>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/auth.js') }}"></script>
@endsection
