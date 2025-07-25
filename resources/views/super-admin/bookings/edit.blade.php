@extends('super-admin.layout')

@section('title', 'Modifier la Réservation')

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.bookings.index') }}">Réservations</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.bookings.show', $booking) }}">{{ substr($booking->booking_uuid, 0, 8) }}...</a>
    <i class="fas fa-chevron-right"></i>
    <span>Modifier</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-bookings.css') }}">
@endsection

@section('content')
    <div class="edit-booking-container">
        <!-- Page Header -->
        <div class="page-header-card">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="header-info">
                        <h1>Modifier la Réservation</h1>
                        <p>Réservation #{{ substr($booking->booking_uuid, 0, 8) }} - {{ $booking->client_name }}</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('super-admin.bookings.show', $booking) }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Retour aux détails
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('super-admin.bookings.update', $booking) }}" method="POST" class="booking-form">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- Current Information -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-info-circle"></i> Informations Actuelles</h3>
                    </div>
                    <div class="card-content">
                        <div class="current-info">
                            <div class="info-item">
                                <label>Client:</label>
                                <span>{{ $booking->client_name }}</span>
                            </div>
                            {{-- <div class="info-item">
                                <label>Téléphone:</label>
                                <span>{{ $booking->client_phone }}</span>
                            </div> --}}
                            <div class="info-item">
                                <label>Trajet:</label>
                                <span>{{ $booking->pickupCity->name ?? 'N/A' }} →
                                    {{ $booking->destinationCity->name ?? 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Date/Heure:</label>
                                <span>{{ $booking->pickup_datetime->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="info-item">
                                <label>Statut actuel:</label>
                                <span class="status-badge status-{{ $booking->status }}">
                                    {{ $booking->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Information Update -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit"></i> Informations Client</h3>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="client_name" class="form-label required">
                                <i class="fas fa-user"></i>
                                Nom du Client
                            </label>
                            <input type="text" id="client_name" name="client_name"
                                class="form-input @error('client_name') error @enderror"
                                value="{{ old('client_name', $booking->client_name) }}" required>
                            @error('client_name')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Trip Details Update -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-route"></i> Détails du Trajet</h3>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="pickup_location" class="form-label required">
                                <i class="fas fa-map-marker-alt"></i>
                                Lieu de Départ
                            </label>
                            <input type="text" id="pickup_location" name="pickup_location"
                                class="form-input @error('pickup_location') error @enderror"
                                value="{{ old('pickup_location', $booking->pickup_location) }}" required>
                            @error('pickup_location')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="destination_location" class="form-label required">
                                <i class="fas fa-flag-checkered"></i>
                                Destination
                            </label>
                            {{-- <input type="text" id="destination_location" name="destination_location"
                            class="form-input @error('destination_location') error @enderror"
                            value="{{ old('destination_location', $booking->destinationCity->name) }}" required> --}}
                            <select type="text" id="destination_city_id" name="destination_city_id"
                                class="form-input @error('destination_city_id') error @enderror" required>
                                <option value="">Aucun destination</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('destination_city_id', $booking->destination_city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('destination_location')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pickup_datetime" class="form-label required">
                                <i class="fas fa-calendar-alt"></i>
                                Date et Heure de Départ
                            </label>
                            <input type="datetime-local" id="pickup_datetime" name="pickup_datetime"
                                class="form-input @error('pickup_datetime') error @enderror"
                                value="{{ old('pickup_datetime', $booking->pickup_datetime->format('Y-m-d\TH:i')) }}"
                                required>
                            @error('pickup_datetime')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="taxi_type" class="form-label required">
                                <i class="fas fa-taxi"></i>
                                Type de Taxi
                            </label>
                            <select id="taxi_type" name="taxi_type" class="form-select @error('taxi_type') error @enderror"
                                required>
                                <option value="standard"
                                    {{ old('taxi_type', $booking->taxi_type) === 'standard' ? 'selected' : '' }}>Standard
                                </option>
                                <option value="luxe"
                                    {{ old('taxi_type', $booking->taxi_type) === 'luxe' ? 'selected' : '' }}>Luxe
                                </option>
                                <option value="van"
                                    {{ old('taxi_type', $booking->taxi_type) === 'van' ? 'selected' : '' }}>Van</option>
                            </select>
                            @error('taxi_type')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Update -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-toggle-on"></i> Modifier le Statut</h3>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="status" class="form-label required">
                                <i class="fas fa-flag"></i>
                                Nouveau Statut
                            </label>
                            <select id="status" name="status" class="form-select @error('status') error @enderror"
                                required>
                                <option value="PENDING"
                                    {{ old('status', $booking->status) === 'PENDING' ? 'selected' : '' }}>En Attente
                                </option>
                                <option value="ASSIGNED"
                                    {{ old('status', $booking->status) === 'ASSIGNED' ? 'selected' : '' }}>Assignée
                                </option>
                                <option value="IN_PROGRESS"
                                    {{ old('status', $booking->status) === 'IN_PROGRESS' ? 'selected' : '' }}>En Cours
                                </option>
                                <option value="COMPLETED"
                                    {{ old('status', $booking->status) === 'COMPLETED' ? 'selected' : '' }}>Terminée
                                </option>
                                <option value="CANCELLED"
                                    {{ old('status', $booking->status) === 'CANCELLED' ? 'selected' : '' }}>Annulée
                                </option>
                                <option value="NO_TAXI_FOUND"
                                    {{ old('status', $booking->status) === 'NO_TAXI_FOUND' ? 'selected' : '' }}>Aucun Taxi
                                    Trouvé</option>
                            </select>
                            @error('status')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="status-help">
                            <div class="status-item">
                                <span class="status-badge status-PENDING">PENDING</span>
                                <span>En attente d'assignation</span>
                            </div>
                            <div class="status-item">
                                <span class="status-badge status-ASSIGNED">ASSIGNED</span>
                                <span>Chauffeur assigné</span>
                            </div>
                            <div class="status-item">
                                <span class="status-badge status-IN_PROGRESS">IN_PROGRESS</span>
                                <span>Trajet en cours</span>
                            </div>
                            <div class="status-item">
                                <span class="status-badge status-COMPLETED">COMPLETED</span>
                                <span>Trajet terminé</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Driver Assignment -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-tie"></i> Assignation Chauffeur</h3>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="assigned_driver_id" class="form-label">
                                <i class="fas fa-taxi"></i>
                                Chauffeur Assigné
                            </label>
                            <select id="assigned_driver_id" name="assigned_driver_id"
                                class="form-select @error('assigned_driver_id') error @enderror">
                                <option value="">Aucun chauffeur assigné</option>
                                @foreach ($availableDrivers as $driver)
                                    <option value="{{ $driver->id }}"
                                        {{ old('assigned_driver_id', $booking->assigned_driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->firstname }} {{ $driver->lastname }}
                                        ({{ $driver->taxi->license_plate ?? 'Pas de taxi' }})
                                        - {{ $driver->taxi->type ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_driver_id')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                            <div class="input-help">
                                <p>Seuls les chauffeurs disponibles dans la ville de départ sont affichés.</p>
                            </div>
                        </div>

                        @if ($booking->driver)
                            <div class="current-driver">
                                <h4>Chauffeur Actuel</h4>
                                <div class="driver-card">
                                    <div class="driver-info">
                                        <strong>{{ $booking->driver->firstname }}
                                            {{ $booking->driver->lastname }}</strong>
                                        <small>{{ $booking->driver->email }}</small>
                                    </div>
                                    @if ($booking->taxi)
                                        <div class="taxi-info">
                                            <span class="license-plate">{{ $booking->taxi->license_plate }}</span>
                                            <small>{{ $booking->taxi->model }} -
                                                {{ ucfirst($booking->taxi->type) }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pricing Update -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-money-bill-wave"></i> Tarification</h3>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="estimated_fare" class="form-label">
                                <i class="fas fa-calculator"></i>
                                Tarif Estimé (€)
                            </label>
                            <input type="number" id="estimated_fare" name="estimated_fare"
                                class="form-input @error('estimated_fare') error @enderror"
                                value="{{ old('estimated_fare', $booking->estimated_fare) }}" step="0.01"
                                min="0" placeholder="0.00">
                            @error('estimated_fare')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="actions-left">
                    <a href="{{ route('super-admin.bookings.show', $booking) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
                <div class="actions-right">
                    <button type="button" class="btn btn-outline" onclick="resetForm()">
                        <i class="fas fa-undo"></i>
                        Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        <span class="btn-text">Enregistrer les Modifications</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </form>

    @endsection

    @section('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('.booking-form');
                const submitBtn = document.getElementById('submitBtn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');
                const statusSelect = document.getElementById('status');
                const cancellationSection = document.getElementById('cancellation-section');

                // Handle form submission
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline-flex';
                });

                // Show/hide cancellation section based on status
                function toggleCancellationSection() {
                    if (statusSelect.value === 'CANCELLED') {
                        cancellationSection.style.display = 'block';
                    } else {
                        cancellationSection.style.display = 'none';
                    }
                }

                statusSelect.addEventListener('change', toggleCancellationSection);
                toggleCancellationSection(); // Initial check

                // Status change warnings
                statusSelect.addEventListener('change', function() {
                    const status = this.value;
                    let message = '';

                    switch (status) {
                        case 'CANCELLED':
                            message = 'Attention: Annuler cette réservation libérera le taxi assigné.';
                            break;
                        case 'COMPLETED':
                            message =
                                'Attention: Marquer comme terminée libérera le taxi pour de nouvelles réservations.';
                            break;
                        case 'IN_PROGRESS':
                            message = 'Le trajet sera marqué comme en cours.';
                            break;
                    }

                    if (message) {
                        console.log(message);
                        // You can show a toast notification here
                    }
                });

                // Auto-save draft functionality
                let autoSaveTimer;
                const formInputs = form.querySelectorAll('input, select, textarea');

                formInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        clearTimeout(autoSaveTimer);
                        autoSaveTimer = setTimeout(saveDraft, 2000);
                    });
                });

                function saveDraft() {
                    const formData = new FormData(form);
                    const draftData = {};

                    for (let [key, value] of formData.entries()) {
                        draftData[key] = value;
                    }

                    localStorage.setItem('booking_edit_draft_{{ $booking->id }}', JSON.stringify(draftData));
                    console.log('Draft saved');
                }

                // Load draft on page load
                function loadDraft() {
                    const draft = localStorage.getItem('booking_edit_draft_{{ $booking->id }}');
                    if (draft) {
                        const draftData = JSON.parse(draft);

                        Object.keys(draftData).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input && input.value === '') {
                                input.value = draftData[key];
                            }
                        });
                    }
                }

                // Clear draft on successful submission
                form.addEventListener('submit', function() {
                    localStorage.removeItem('booking_edit_draft_{{ $booking->id }}');
                });
            });

            function resetForm() {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
                    document.querySelector('.booking-form').reset();
                    localStorage.removeItem('booking_edit_draft_{{ $booking->id }}');
                }
            }

            function duplicateBooking() {
                if (confirm('Créer une nouvelle réservation basée sur celle-ci ?')) {
                    // Implement duplication logic
                    console.log('Duplicate booking');
                }
            }

            function sendNotification() {
                if (confirm('Envoyer une notification au client ?')) {
                    // Implement notification logic
                    console.log('Send notification');
                }
            }

            // Real-time validation
            document.getElementById('pickup_datetime').addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const now = new Date();

                if (selectedDate < now) {
                    alert('La date de départ ne peut pas être dans le passé.');
                    this.focus();
                }
            });

            // Phone number formatting
            // document.getElementById('client_phone').addEventListener('input', function() {
            //     let value = this.value.replace(/\D/g, '');
            //     if (value.length >= 10) {
            //         value = value.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
            //     }
            //     this.value = value;
            // });
        </script>
    @endsection
