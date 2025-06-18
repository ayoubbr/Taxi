@extends('client.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/client-profile.css') }}">
@endsection

@section('content')
    <div class="client-dashboard mt-70">
        <div class="container">
            <div class="profile-container">
                <div class="profile-header">
                    {{-- <div class="profile-avatar">
                        <div class="avatar-container">
                            <div class="avatar">
                                @if ($client->profile_photo)
                                    <img src="{{ $client->profile_photo }}" alt="Profile Photo">
                                @else
                                    {{ strtoupper(substr($client->firstname, 0, 1)) }}{{ strtoupper(substr($client->lastname, 0, 1)) }}
                                @endif
                            </div>
                            <div class="avatar-upload" title="Upload Photo">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                    </div> --}}
                    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
                    <p>Manage your personal information and account settings.</p>
                </div>

                {{-- @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif --}}

                <!-- Quick Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">{{ $client->clientBookings()->count() }}</div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $client->clientBookings()->where('status', 'COMPLETED')->count() }}</div>
                        <div class="stat-label">Completed Rides</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">
                            {{ $client->clientBookings()->whereIn('status', ['PENDING', 'ASSIGNED', 'IN_PROGRESS'])->count() }}
                        </div>
                        <div class="stat-label">Active Bookings</div>
                    </div>
                </div>

                <form action="{{ route('client.profile.update') }}" method="POST" id="profileForm">
                    @csrf
                    @method('PUT')

                    <div class="form-row">
                        <div class="form-group half {{ $errors->has('firstname') ? 'has-error' : '' }}">
                            <label for="firstname">
                                <i class="fas fa-user"></i> First Name *
                            </label>
                            <input type="text" name="firstname" id="firstname"
                                value="{{ old('firstname', $client->firstname) }}" placeholder="Enter your first name"
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
                                value="{{ old('lastname', $client->lastname) }}" placeholder="Enter your last name"
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
                            value="{{ old('username', $client->username) }}" placeholder="Choose a unique username"
                            required>
                        @error('username')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address *
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                            placeholder="your.email@example.com" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                        <label for="phone">
                            <i class="fas fa-phone"></i> Phone Number
                        </label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $client->phone) }}"
                            placeholder="+1 (555) 123-4567">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr>

                    <div class="form-row">
                        <div class="form-group half {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label for="password">
                                <i class="fas fa-lock"></i> New Password
                            </label>
                            <input type="password" name="password" id="password" placeholder="Leave blank to keep current">
                            @error('password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group half">
                            <label for="password_confirmation">
                                <i class="fas fa-lock"></i> Confirm New Password
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Confirm new password">
                        </div>
                    </div>

                    <div class="form-group form-actions" style="text-align: center; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary" id="updateBtn">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <button type="button" class="btn btn-outline" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset Changes
                        </button>
                    </div>
                </form>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3>Booking History</h3>
                        <p>View all your past and current taxi bookings</p>
                        <a href="{{ route('client.bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> View Bookings
                        </a>
                    </div>

                    <div class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3>New Booking</h3>
                        <p>Book a new taxi ride quickly and easily</p>
                        <a href="{{ route('client.bookings.create') }}" class="btn btn-primary">
                            <i class="fas fa-taxi"></i> Book Now
                        </a>
                    </div>
                </div>

                {{-- <div class="bookings-link-card">
                    <div class="action-icon" style="margin-bottom: 1rem;">
                        <i class="fas fa-route"></i>
                    </div>
                    <p class="mb-2">Need help with your account or have questions?</p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="" class="btn btn-secondary">
                            <i class="fas fa-headset"></i> Contact Support
                        </a>
                        <a href="" class="btn btn-outline">
                            <i class="fas fa-question-circle"></i> FAQ
                        </a>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const updateBtn = document.getElementById('updateBtn');

            // Form submission with loading state
            form.addEventListener('submit', function() {
                updateBtn.classList.add('loading');
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                updateBtn.disabled = true;

                // Add form submission animation
                form.classList.add('form-submitting');
            });

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
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });

                input.addEventListener('input', function() {
                    // Remove error state on input
                    const formGroup = this.closest('.form-group');
                    if (formGroup.classList.contains('has-error')) {
                        formGroup.classList.remove('has-error');
                    }
                });
            });

            function validateField(field) {
                const formGroup = field.closest('.form-group');

                // Remove existing validation classes
                formGroup.classList.remove('has-error', 'has-success');

                if (field.checkValidity() && field.value.trim() !== '') {
                    formGroup.classList.add('has-success');
                } else if (field.value.trim() !== '' && !field.checkValidity()) {
                    formGroup.classList.add('has-error');
                }
            }

            // Password confirmation validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            if (passwordInput && confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    if (passwordInput.value !== this.value && this.value !== '') {
                        this.setCustomValidity('Passwords do not match');
                        this.closest('.form-group').classList.add('has-error');
                    } else {
                        this.setCustomValidity('');
                        this.closest('.form-group').classList.remove('has-error');
                        if (this.value !== '') {
                            this.closest('.form-group').classList.add('has-success');
                        }
                    }
                });
            }

            // // Auto-hide alerts after 5 seconds
            // const alerts = document.querySelectorAll('.alert');
            // alerts.forEach(alert => {
            //     setTimeout(() => {
            //         alert.style.opacity = '0';
            //         alert.style.transform = 'translateY(-20px)';
            //         setTimeout(() => {
            //             alert.remove();
            //         }, 300);
            //     }, 5000);
            // });
        });

        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.getElementById('profileForm').reset();

                // Remove validation classes
                const formGroups = document.querySelectorAll('.form-group');
                formGroups.forEach(group => {
                    group.classList.remove('has-error', 'has-success');
                });

                // // Show confirmation
                // const alert = document.createElement('div');
                // alert.className = 'alert alert-info';
                // alert.textContent = 'Form has been reset to original values.';
                // document.querySelector('.profile-header').after(alert);

                // setTimeout(() => {
                //     alert.remove();
                // }, 3000);
            }
        }
    </script>
@endsection
