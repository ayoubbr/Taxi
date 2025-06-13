@extends('client.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container mt-70">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo">
                    <img src="{{ asset('images/taxi-icon.png') }}" alt="Taxi Logo">
                    <h1>TaxiGo</h1>
                </div>
                <h2>Welcome Back</h2>
                <p>Sign in to your account to continue</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf
                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        placeholder="Enter your email address" required autofocus autocomplete="username"
                        class="@error('email') error @enderror">
                    @error('email')
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
                        <input id="password" type="password" name="password" placeholder="Enter your password" required
                            autocomplete="current-password" class="@error('password') error @enderror">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>

                <!-- Forgot Password Link -->
                <div class="forgot-password">
                    @if (Route::has('password.request'))
                        {{-- {{ route('password.request') }} --}}
                        <a href="" class="link">
                            <i class="fas fa-key"></i>
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <!-- Register Link -->
                <div class="auth-footer">
                    <p>Don't have an account? <a href="{{ route('register') }}" class="link">Create one here</a></p>
                </div>
            </form>

            <!-- Social Login (Optional) -->
            <div class="social-login">
                <div class="divider">
                    <span>Or continue with</span>
                </div>
                <div class="social-buttons">
                    <button type="button" class="btn-social btn-google">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button type="button" class="btn-social btn-facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                </div>
            </div>
        </div>

        <!-- Side Image -->
        <div class="auth-image">
            <img src="{{ asset('images/taxi-2.webp') }}" alt="Login illustration">
            <div class="auth-image-content">
                <h3>Welcome Back!</h3>
                <p>Access your dashboard, manage your bookings, and enjoy seamless taxi services with our secure platform.
                </p>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/auth.js') }}"></script>
@endsection
