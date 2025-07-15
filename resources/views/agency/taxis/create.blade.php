@extends('agency.layout')

@section('title', 'Nouveau Taxi')

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.taxis.index') }}">Taxis</a>
    <i class="fas fa-chevron-right"></i>
    <span>Nouveau</span>
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
                    <a href="{{ route('agency.taxis.index') }}" class="form-back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="form-header-info">
                        <h1>Nouveau Taxi</h1>
                        <p>Ajouter un nouveau véhicule à votre flotte</p>
                    </div>
                </div>
                <div class="form-header-actions">
                    <a href="{{ route('agency.taxis.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('agency.taxis.store') }}" method="POST" id="taxiForm">
            @csrf

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
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="license_plate" class="form-label required">Plaque d'immatriculation</label>
                                    <input type="text" id="license_plate" name="license_plate"
                                        class="form-control @error('license_plate') error @enderror"
                                        value="{{ old('license_plate') }}" required>
                                    @error('license_plate')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-help">
                                        Format: ABC-123-DE ou 123-ABC-45
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="model" class="form-label required">Modèle</label>
                                    <input type="text" id="model" name="model"
                                        class="form-control @error('model') error @enderror" value="{{ old('model') }}"
                                        required>
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
                                        <option value="standard" {{ old('type') === 'standard' ? 'selected' : '' }}>
                                            Standard</option>
                                        <option value="van" {{ old('type') === 'van' ? 'selected' : '' }}>Van</option>
                                        <option value="luxe" {{ old('type') === 'luxe' ? 'selected' : '' }}>Luxe</option>
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
                                        value="{{ old('capacity', 4) }}" min="1" max="9" required>
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
                                            {{ old('city_id') == $city->id ? 'selected' : '' }}>
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
                            <div class="form-group">
                                <label for="driver_id" class="form-label">Chauffeur assigné</label>
                                <select id="driver_id" name="driver_id"
                                    class="form-control form-select @error('driver_id') error @enderror">
                                    <option value="">Aucun chauffeur assigné</option>
                                    @foreach ($availableDrivers as $driver)
                                        <option value="{{ $driver->id }}"
                                            {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
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
                                    Vous pouvez assigner un chauffeur maintenant ou plus tard. Seuls les chauffeurs sans
                                    taxi sont affichés.
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
                            <div class="form-group">
                                <label for="is_available" class="form-label required">Statut de disponibilité</label>
                                <select id="is_available" name="is_available"
                                    class="form-control form-select @error('is_available') error @enderror" required>
                                    <option value="1" {{ old('is_available', '1') === '1' ? 'selected' : '' }}>
                                        Disponible</option>
                                    <option value="0" {{ old('is_available') === '0' ? 'selected' : '' }}>Occupé
                                    </option>
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
                                        <span class="status-badge status-warning">Occupé</span>
                                        <span>En course ou maintenance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agency Info -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-building"></i> Agence</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="current-info">
                                <h4>Agence actuelle</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Nom</label>
                                        <span>{{ Auth::user()->agency->name ?? 'Non définie' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Statut</label>
                                        <span
                                            class="status-badge status-{{ Auth::user()->agency->status ?? 'inactive' }}">
                                            {{ ucfirst(Auth::user()->agency->status ?? 'inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-help">
                                Le taxi sera automatiquement assigné à votre agence
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Total Taxis</label>
                                    <span>{{ Auth::user()->agency->taxis()->count() ?? 0 }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Taxis Disponibles</label>
                                    <span>{{ Auth::user()->agency->taxis()->where('is_available', true)->count() ?? 0 }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Chauffeurs Disponibles</label>
                                    <span>{{ $availableDrivers->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="form-actions-left">
                    <a href="{{ route('agency.taxis.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
                <div class="form-actions-right">
                    <button type="button" id="previewBtn" class="btn btn-info">
                        <i class="fas fa-eye"></i> Aperçu
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Créer le Taxi
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('taxiForm');
            const submitBtn = document.getElementById('submitBtn');
            const previewBtn = document.getElementById('previewBtn');

            // Auto-format license plate
            const licensePlateInput = document.getElementById('license_plate');
            licensePlateInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Update capacity based on type
            const typeSelect = document.getElementById('type');
            const capacityInput = document.getElementById('capacity');

            typeSelect.addEventListener('change', function() {
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
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
                submitBtn.disabled = true;
                form.classList.add('form-loading');
            });

            // Preview functionality
            previewBtn.addEventListener('click', function() {
                const formData = new FormData(form);
                let previewContent = '<h4>Aperçu du taxi :</h4><ul>';

                previewContent += `<li><strong>Plaque :</strong> ${formData.get('license_plate')}</li>`;
                previewContent += `<li><strong>Modèle :</strong> ${formData.get('model')}</li>`;
                previewContent += `<li><strong>Type :</strong> ${formData.get('type')}</li>`;
                previewContent += `<li><strong>Capacité :</strong> ${formData.get('capacity')} places</li>`;

                const citySelect = document.getElementById('city_id');
                const selectedCity = citySelect.options[citySelect.selectedIndex];
                previewContent +=
                    `<li><strong>Ville :</strong> ${selectedCity.value ? selectedCity.text : 'Non sélectionnée'}</li>`;

                const driverSelect = document.getElementById('driver_id');
                const selectedDriver = driverSelect.options[driverSelect.selectedIndex];
                previewContent +=
                    `<li><strong>Chauffeur :</strong> ${selectedDriver.value ? selectedDriver.text : 'Aucun'}</li>`;

                previewContent += '</ul>';

                if (confirm(previewContent + '\n\nVoulez-vous continuer avec ces informations ?')) {
                    // User confirmed, could submit or do other actions
                }
            });

            // Real-time validation feedback
            const inputs = form.querySelectorAll('.form-control');
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
