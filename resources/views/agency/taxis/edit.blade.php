@extends('agency.layout')

@section('title', 'Modifier Taxi')

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.taxis.index') }}">Taxis</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.taxis.show', $taxi) }}">{{ $taxi->license_plate }}</a>
    <i class="fas fa-chevron-right"></i>
    <span>Modifier</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-forms.css') }}">
@endsection

@section('content')
    <div class="form-container">
        <!-- Form Header -->
        <div class="form-header">
            <div class="form-header-content">
                <div class="form-header-left">
                    <a href="{{ route('agency.taxis.show', $taxi) }}" class="form-back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="form-header-info">
                        <h1>Modifier {{ $taxi->license_plate }}</h1>
                        <p>Mettre à jour les informations du véhicule</p>
                    </div>
                </div>
                <div class="form-header-actions">
                    <a href="{{ route('agency.taxis.show', $taxi) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> Voir Détails
                    </a>
                    <a href="{{ route('agency.taxis.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('agency.taxis.update', $taxi) }}" method="POST" id="taxiEditForm">
            @csrf
            @method('PUT')

            <!-- Form Grid -->
            <div class="form-grid">
                <!-- Main Form -->
                <div class="form-main">
                    <!-- Vehicle Information -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-car"></i> Informations du Véhicule</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="current-info">
                                <h4>Informations actuelles</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Plaque actuelle</label>
                                        <span>{{ $taxi->license_plate }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Modèle actuel</label>
                                        <span>{{ $taxi->model }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Type actuel</label>
                                        <span
                                            class="type-badge type-{{ $taxi->type }}">{{ ucfirst($taxi->type) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Ajouté le</label>
                                        <span>{{ $taxi->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="license_plate" class="form-label required">Plaque d'immatriculation</label>
                                    <input type="text" id="license_plate" name="license_plate"
                                        class="form-control @error('license_plate') error @enderror"
                                        value="{{ old('license_plate', $taxi->license_plate) }}" required>
                                    @error('license_plate')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="model" class="form-label required">Modèle</label>
                                    <input type="text" id="model" name="model"
                                        class="form-control @error('model') error @enderror"
                                        value="{{ old('model', $taxi->model) }}" required>
                                    @error('model')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="type" class="form-label required">Type de véhicule</label>
                                    <select id="type" name="type"
                                        class="form-control form-select @error('type') error @enderror" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="standard"
                                            {{ old('type', $taxi->type) === 'standard' ? 'selected' : '' }}>Standard
                                        </option>
                                        <option value="van" {{ old('type', $taxi->type) === 'van' ? 'selected' : '' }}>
                                            Van</option>
                                        <option value="luxe" {{ old('type', $taxi->type) === 'luxe' ? 'selected' : '' }}>
                                            Luxe</option>
                                    </select>
                                    @error('type')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="capacity" class="form-label required">Capacité (places)</label>
                                    <input type="number" id="capacity" name="capacity"
                                        class="form-control @error('capacity') error @enderror"
                                        value="{{ old('capacity', $taxi->capacity) }}" min="1" max="9"
                                        required>
                                    @error('capacity')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="city_id" class="form-label required">Ville d'opération</label>
                                <select id="city_id" name="city_id"
                                    class="form-control form-select @error('city_id') error @enderror" required>
                                    <option value="">Sélectionner une ville</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ old('city_id', $taxi->city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Driver Assignment -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-user"></i> Attribution de Chauffeur</h3>
                        </div>
                        <div class="form-card-body">
                            @if ($taxi->driver)
                                <div class="taxi-assignment-card assigned">
                                    <div class="taxi-info">
                                        <div class="taxi-details">
                                            <h5>Chauffeur actuellement assigné</h5>
                                            <p>{{ $taxi->driver->firstname }} {{ $taxi->driver->lastname }}</p>
                                            <p>{{ $taxi->driver->email }} ({{ $taxi->driver->username }})</p>
                                        </div>
                                        <div class="taxi-actions">
                                            <span class="status-badge status-active">Assigné</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="driver_id" class="form-label">Changer l'attribution de chauffeur</label>
                                <select id="driver_id" name="driver_id"
                                    class="form-control form-select @error('driver_id') error @enderror">
                                    <option value="">Aucun chauffeur assigné</option>
                                    @foreach ($availableDrivers as $driver)
                                        <option value="{{ $driver->id }}"
                                            {{ old('driver_id', $taxi->driver_id) == $driver->id ? 'selected' : '' }}>
                                            {{ $driver->firstname }} {{ $driver->lastname }} ({{ $driver->username }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('driver_id')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-help">
                                    Seuls les chauffeurs disponibles ou le chauffeur actuellement assigné sont affichés
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="form-sidebar">
                    <!-- Availability -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-toggle-on"></i> Disponibilité</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="current-info">
                                <h4>Statut actuel</h4>
                                <div class="info-item">
                                    <span class="status-badge status-{{ $taxi->is_available ? 'active' : 'occupied' }}">
                                        {{ $taxi->is_available ? 'Disponible' : 'Occupé' }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="is_available" class="form-label required">Nouveau statut</label>
                                <select id="is_available" name="is_available"
                                    class="form-control form-select @error('is_available') error @enderror" required>
                                    <option value="1"
                                        {{ old('is_available', $taxi->is_available ? '1' : '0') === '1' ? 'selected' : '' }}>
                                        Disponible</option>
                                    <option value="0"
                                        {{ old('is_available', $taxi->is_available ? '1' : '0') === '0' ? 'selected' : '' }}>
                                        Occupé</option>
                                </select>
                                @error('is_available')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="status-help">
                                <h5>Statuts disponibles :</h5>
                                <div class="status-options">
                                    <div class="status-option">
                                        <span class="status-badge status-active">Disponible</span>
                                        <span>Peut recevoir des courses</span>
                                    </div>
                                    <div class="status-option">
                                        <span class="status-badge status-occupied">Occupé</span>
                                        <span>En course ou maintenance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Taxi Statistics -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Total Courses</label>
                                    <span>{{ $taxi->bookings()->count() }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Courses Terminées</label>
                                    <span>{{ $taxi->bookings()->where('status', 'COMPLETED')->count() }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Courses En Cours</label>
                                    <span>{{ $taxi->bookings()->where('status', 'IN_PROGRESS')->count() }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Revenus Générés</label>
                                    <span>{{ number_format($taxi->bookings()->where('status', 'COMPLETED')->sum('estimated_fare'), 0, ',', ' ') }}€</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-info-circle"></i> Informations</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Ville Actuelle</label>
                                    <span>{{ $taxi->city->name }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Agence</label>
                                    <span>{{ $taxi->agency->name }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Dernière Mise à Jour</label>
                                    <span>{{ $taxi->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="form-actions-left">
                    <a href="{{ route('agency.taxis.show', $taxi) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <a href="{{ route('agency.taxis.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Retour à la Liste
                    </a>
                </div>
                <div class="form-actions-right">
                    <button type="button" id="resetBtn" class="btn btn-warning">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary" id="updateBtn">
                        <i class="fas fa-save"></i> Mettre à Jour
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('taxiEditForm');
            const updateBtn = document.getElementById('updateBtn');
            const resetBtn = document.getElementById('resetBtn');

            // Store original values for reset functionality
            const originalValues = {};
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                originalValues[input.name] = input.value;
            });

            // Auto-format license plate
            const licensePlateInput = document.getElementById('license_plate');
            licensePlateInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Update capacity based on type
            const typeSelect = document.getElementById('type');
            const capacityInput = document.getElementById('capacity');

            typeSelect.addEventListener('change', function() {
                if (confirm(
                    'Voulez-vous ajuster automatiquement la capacité selon le type sélectionné ?')) {
                    switch (this.value) {
                        case 'standard':
                            capacityInput.value = 4;
                            break;
                        case 'van':
                            capacityInput.value = 7;
                            break;
                        case 'luxe':
                            capacityInput.value = 4;
                            break;
                    }
                }
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
                updateBtn.disabled = true;
                form.classList.add('form-loading');
            });

            // Reset form to original values
            resetBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les changements ?')) {
                    inputs.forEach(input => {
                        if (originalValues[input.name] !== undefined) {
                            input.value = originalValues[input.name];
                        }
                    });

                    // Remove error classes
                    inputs.forEach(input => {
                        input.classList.remove('error');
                    });
                }
            });

            // Track changes to show unsaved changes warning
            let hasUnsavedChanges = false;
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    hasUnsavedChanges = true;
                });
            });

            // Warn about unsaved changes when leaving page
            window.addEventListener('beforeunload', function(e) {
                if (hasUnsavedChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Remove warning when form is submitted
            form.addEventListener('submit', function() {
                hasUnsavedChanges = false;
            });

            // Real-time validation feedback
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.checkValidity()) {
                        this.classList.remove('error');
                    }
                });
            });
        });
    </script>
@endsection
