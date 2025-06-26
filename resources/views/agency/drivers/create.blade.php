@extends('agency-admin.layout')

@section('title', 'Nouveau Chauffeur')

@section('breadcrumb')
    <a href="{{ route('agency-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency-admin.drivers.index') }}">Chauffeurs</a>
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
                    <a href="{{ route('agency-admin.drivers.index') }}" class="form-back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="form-header-info">
                        <h1>Nouveau Chauffeur</h1>
                        <p>Ajouter un nouveau chauffeur à votre agence</p>
                    </div>
                </div>
                <div class="form-header-actions">
                    <a href="{{ route('agency-admin.drivers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('agency-admin.drivers.store') }}" method="POST" id="driverForm">
            @csrf

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
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstname" class="form-label required">Prénom</label>
                                    <input type="text" id="firstname" name="firstname"
                                        class="form-control @error('firstname') error @enderror"
                                        value="{{ old('firstname') }}" required>
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
                                        value="{{ old('lastname') }}" required>
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
                                    class="form-control @error('email') error @enderror" value="{{ old('email') }}"
                                    required>
                                @error('email')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-help">
                                    L'email sera utilisé pour la connexion du chauffeur
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-key"></i> Informations de Compte</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="form-group">
                                <label for="username" class="form-label required">Nom d'utilisateur</label>
                                <input type="text" id="username" name="username"
                                    class="form-control @error('username') error @enderror" value="{{ old('username') }}"
                                    required>
                                @error('username')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="form-help">
                                    Le nom d'utilisateur doit être unique et contenir au moins 3 caractères
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password" class="form-label required">Mot de passe</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') error @enderror" required>
                                    @error('password')
                                        <div class="error-message">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label required">Confirmer le mot de
                                        passe</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control" required>
                                </div>
                            </div>

                            <div class="form-help">
                                Le mot de passe doit contenir au moins 8 caractères
                            </div>
                        </div>
                    </div>

                    <!-- Taxi Assignment -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3><i class="fas fa-car"></i> Attribution de Taxi</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="form-group">
                                <label for="taxi_id" class="form-label">Taxi assigné</label>
                                <select id="taxi_id" name="taxi_id"
                                    class="form-control form-select @error('taxi_id') error @enderror">
                                    <option value="">Aucun taxi assigné</option>
                                    @foreach ($availableTaxis as $taxi)
                                        <option value="{{ $taxi->id }}"
                                            {{ old('taxi_id') == $taxi->id ? 'selected' : '' }}>
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
                                    Vous pouvez assigner un taxi maintenant ou plus tard. Seuls les taxis disponibles sont
                                    affichés.
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
                            <div class="form-group">
                                <label for="status" class="form-label required">Statut du chauffeur</label>
                                <select id="status" name="status"
                                    class="form-control form-select @error('status') error @enderror" required>
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                        Actif</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactif
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
                                Le chauffeur sera automatiquement assigné à votre agence
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
                                    <label>Total Chauffeurs</label>
                                    <span>{{ Auth::user()->agency->drivers()->count() ?? 0 }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Chauffeurs Actifs</label>
                                    <span>{{ Auth::user()->agency->drivers()->where('status', 'active')->count() ?? 0 }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Taxis Disponibles</label>
                                    <span>{{ $availableTaxis->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="form-actions-left">
                    <a href="{{ route('agency-admin.drivers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
                <div class="form-actions-right">
                    <button type="button" id="previewBtn" class="btn btn-info">
                        <i class="fas fa-eye"></i> Aperçu
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Créer le Chauffeur
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('driverForm');
            const submitBtn = document.getElementById('submitBtn');
            const previewBtn = document.getElementById('previewBtn');

            // Auto-generate username from first and last name
            const firstnameInput = document.getElementById('firstname');
            const lastnameInput = document.getElementById('lastname');
            const usernameInput = document.getElementById('username');

            function generateUsername() {
                const firstname = firstnameInput.value.trim().toLowerCase();
                const lastname = lastnameInput.value.trim().toLowerCase();

                if (firstname && lastname) {
                    const username = firstname + '.' + lastname;
                    usernameInput.value = username;
                }
            }

            firstnameInput.addEventListener('blur', generateUsername);
            lastnameInput.addEventListener('blur', generateUsername);

            // Password confirmation validation
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');

            function validatePasswordMatch() {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            }

            passwordInput.addEventListener('input', validatePasswordMatch);
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
                submitBtn.disabled = true;
                form.classList.add('form-loading');
            });

            // Preview functionality
            previewBtn.addEventListener('click', function() {
                const formData = new FormData(form);
                let previewContent = '<h4>Aperçu du chauffeur :</h4><ul>';

                previewContent +=
                    `<li><strong>Nom complet :</strong> ${formData.get('firstname')} ${formData.get('lastname')}</li>`;
                previewContent += `<li><strong>Email :</strong> ${formData.get('email')}</li>`;
                previewContent +=
                    `<li><strong>Nom d'utilisateur :</strong> ${formData.get('username')}</li>`;
                previewContent += `<li><strong>Statut :</strong> ${formData.get('status')}</li>`;

                const taxiSelect = document.getElementById('taxi_id');
                const selectedTaxi = taxiSelect.options[taxiSelect.selectedIndex];
                previewContent +=
                    `<li><strong>Taxi assigné :</strong> ${selectedTaxi.value ? selectedTaxi.text : 'Aucun'}</li>`;

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
