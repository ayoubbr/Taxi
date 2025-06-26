@extends('agency.layout')

@section('title', 'Modifier Chauffeur')

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.drivers.index') }}">Chauffeurs</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.drivers.show', $driver) }}">{{ $driver->firstname }} {{ $driver->lastname }}</a>
    <i class="fas fa-chevron-right"></i>
    <span>Modifier</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-forms.css') }}">
    <style>
        @media (max-width: 768px) {

            .form-actions-left .btn-secondary,
            .form-actions-right .btn-primary,
            .form-actions-right .btn-warning {
                width: 100%;
            }
        }

        @media (max-width: 468px) {

            .form-actions-left,
            .form-actions-right {
                flex-direction: column;
            }
        }
    </style>
@endsection

@section('content')
    <div class="form-container">
        <!-- Form Header -->
        <div class="form-header">
            <div class="form-header-content">
                <div class="form-header-left">
                    <a href="{{ route('agency.drivers.index', $driver) }}" class="form-back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="form-header-info">
                        <h1>Modifier {{ $driver->firstname }} {{ $driver->lastname }}</h1>
                        <p>Mettre à jour les informations du chauffeur</p>
                    </div>
                </div>
                <div class="form-header-actions">
                    <a href="{{ route('agency.drivers.show', $driver) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i> Voir Profil
                    </a>
                    <a href="{{ route('agency.drivers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('agency.drivers.update', $driver) }}" method="POST" id="driverEditForm">
            @csrf
            @method('PUT')

            <!-- Form Grid -->
            <div class="form-grid">
                <!-- Main Form -->
                <div class="form-main">
                    <!-- Personal Information -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-user"></i> Informations Personnelles</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="current-info">
                                <h4>Informations actuelles</h4>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Nom complet</label>
                                        <span>{{ $driver->firstname }} {{ $driver->lastname }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Email actuel</label>
                                        <span>{{ $driver->email }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Nom d'utilisateur</label>
                                        <span>{{ $driver->username }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Membre depuis</label>
                                        <span>{{ $driver->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstname" class="form-label required">Prénom</label>
                                    <input type="text" id="firstname" name="firstname"
                                        class="form-control @error('firstname') error @enderror"
                                        value="{{ old('firstname', $driver->firstname) }}" required>
                                    @error('firstname')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="lastname" class="form-label required">Nom</label>
                                    <input type="text" id="lastname" name="lastname"
                                        class="form-control @error('lastname') error @enderror"
                                        value="{{ old('lastname', $driver->lastname) }}" required>
                                    @error('lastname')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') error @enderror"
                                    value="{{ old('email', $driver->email) }}" required>
                                @error('email')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-help">
                                    Changer l'email affectera la connexion du chauffeur
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="username" class="form-label required">Nom d'utilisateur</label>
                                <input type="text" id="username" name="username"
                                    class="form-control @error('username') error @enderror"
                                    value="{{ old('username', $driver->username) }}" required>
                                @error('username')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-help">
                                    Le nom d'utilisateur doit être unique
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Change -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-key"></i> Changer le Mot de Passe</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="form-help" style="margin-bottom: 1rem;">
                                Laissez vide pour conserver le mot de passe actuel
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') error @enderror">
                                    @error('password')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de
                                        passe</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Taxi Assignment -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-car"></i> Attribution de Taxi</h3>
                        </div>
                        <div class="form-card-body">
                            @if ($driver->taxi)
                                <div class="taxi-assignment assigned">
                                    <div class="taxi-info">
                                        <div class="taxi-details">
                                            <h5>Taxi actuellement assigné</h5>
                                            <p>{{ $driver->taxi->license_plate }} - {{ ucfirst($driver->taxi->type) }}</p>
                                            <p>{{ $driver->taxi->model ?? 'Modèle non spécifié' }}</p>
                                        </div>
                                        <div class="taxi-actions">
                                            <span class="status-badge status-active">Assigné</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="taxi_id" class="form-label">Changer l'attribution de taxi</label>
                                <select id="taxi_id" name="taxi_id"
                                    class="form-control form-select @error('taxi_id') error @enderror">
                                    <option value="">Aucun taxi assigné</option>
                                    @foreach ($availableTaxis as $taxi)
                                        <option value="{{ $taxi->id }}"
                                            {{ old('taxi_id', $driver->taxi?->id ?? '') == $taxi->id ? 'selected' : '' }}>
                                            {{ $taxi->license_plate }} - {{ ucfirst($taxi->type) }}
                                            ({{ $taxi->model ?? 'Modèle non spécifié' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('taxi_id')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-help">
                                    Seuls les taxis disponibles ou le taxi actuellement assigné sont affichés
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="form-sidebar">
                    <!-- Status -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-toggle-on"></i> Statut</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="current-info">
                                <h4>Statut actuel</h4>
                                <div class="info-item">
                                    <span class="status-badge status-{{ $driver->status }}">
                                        {{ ucfirst($driver->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label required">Nouveau statut</label>
                                <select id="status" name="status"
                                    class="form-control form-select @error('status') error @enderror" required>
                                    <option value="active"
                                        {{ old('status', $driver->status) === 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive"
                                        {{ old('status', $driver->status) === 'inactive' ? 'selected' : '' }}>Inactif
                                    </option>
                                    <option value="suspended"
                                        {{ old('status', $driver->status) === 'suspended' ? 'selected' : '' }}>Suspendu
                                    </option>
                                </select>
                                @error('status')
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
                                        <span class="status-badge status-active">Actif</span>
                                        <span>Peut recevoir des courses</span>
                                    </div>
                                    <div class="status-option">
                                        <span class="status-badge status-inactive">Inactif</span>
                                        <span>Ne peut pas recevoir de courses</span>
                                    </div>
                                    <div class="status-option">
                                        <span class="status-badge status-suspended">Suspendu</span>
                                        <span>Compte temporairement bloqué</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Statistics -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-chart-bar"></i> Statistiques</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Total Courses</label>
                                    <span>{{ $driver->assignedDriverBookings()->count() }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Courses Terminées</label>
                                    <span>{{ $driver->assignedDriverBookings()->where('status', 'COMPLETED')->count() }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Courses En Cours</label>
                                    <span>{{ $driver->assignedDriverBookings()->where('status', 'IN_PROGRESS')->count() }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Revenus Générés</label>
                                    <span>{{ number_format($driver->assignedDriverBookings()->where('status', 'COMPLETED')->sum('estimated_fare'), 0, ',', ' ') }}€</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Last Activity -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-clock"></i> Dernière Activité</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Dernière Connexion</label>
                                    <span>{{ $driver->updated_at->diffForHumans() }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Dernière Course</label>
                                    <span>
                                        @if ($driver->assignedDriverBookings()->latest()->first())
                                            {{ $driver->assignedDriverBookings()->latest()->first()->created_at->diffForHumans() }}
                                        @else
                                            Aucune course
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="form-actions-left">
                    <a href="{{ route('agency.drivers.show', $driver) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <a href="{{ route('agency.drivers.index') }}" class="btn btn-secondary">
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
            const form = document.getElementById('driverEditForm');
            const updateBtn = document.getElementById('updateBtn');
            const resetBtn = document.getElementById('resetBtn');

            // Store original values for reset functionality
            const originalValues = {};
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                originalValues[input.name] = input.value;
            });

            // Password confirmation validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            function validatePasswordMatch() {
                if (passwordInput.value && passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            }

            passwordInput.addEventListener('input', validatePasswordMatch);
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);

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

                    // Clear password fields
                    passwordInput.value = '';
                    confirmPasswordInput.value = '';

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
