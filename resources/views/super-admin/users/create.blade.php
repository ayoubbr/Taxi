@extends('super-admin.layout')

@section('title', 'Ajouter un Utilisateur')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-user-forms.css') }}">
@endsection

@section('content')
    <div class="user-form-container">
        <!-- Page Header -->
        <div class="page-header-card">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-user-plus font-awesome-icon"></i>
                    </div>
                    <div class="header-info">
                        <h1>Ajouter un Utilisateur</h1>
                        <p>Créer un nouveau compte utilisateur dans le système</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left font-awesome-icon"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('super-admin.users.store') }}" method="POST" class="user-form" id="createUserForm">
            @csrf

            <div class="form-grid">
                <!-- Informations Personnelles -->
                <div class="form-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-user"></i>
                            Informations Personnelles
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstname" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Prénom
                                </label>
                                <input type="text" id="firstname" name="firstname"
                                    class="form-input @error('firstname') error @enderror" value="{{ old('firstname') }}" placeholder="Entrez le prénom">
                                @error('firstname')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="lastname" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Nom de famille
                                </label>
                                <input type="text" id="lastname" name="lastname"
                                    class="form-input @error('lastname') error @enderror" value="{{ old('lastname') }}"
                                    placeholder="Entrez le nom de famille">
                                @error('lastname')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de Connexion -->
                <div class="form-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-key"></i>
                            Informations de Connexion
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="username" class="form-label required">
                                <i class="fas fa-at"></i>
                                Nom d'utilisateur
                            </label>
                            <input type="text" id="username" name="username"
                                class="form-input @error('username') error @enderror" value="{{ old('username') }}"
                                placeholder="Entrez le nom d'utilisateur" required>
                            @error('username')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="input-help">
                                <p>Le nom d'utilisateur doit être unique</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label required">
                                <i class="fas fa-envelope"></i>
                                Adresse Email
                            </label>
                            <input type="email" id="email" name="email"
                                class="form-input @error('email') error @enderror" value="{{ old('email') }}"
                                placeholder="exemple@email.com" required>
                            @error('email')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="password" class="form-label required">
                                    <i class="fas fa-lock"></i>
                                    Mot de passe
                                </label>
                                <input type="password" id="password" name="password"
                                    class="form-input @error('password') error @enderror"
                                    placeholder="Entrez le mot de passe" required>
                                @error('password')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="form-label required">
                                    <i class="fas fa-lock"></i>
                                    Confirmer le mot de passe
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-input" placeholder="Confirmez le mot de passe" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Affectation -->
                <div class="form-card full-width">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-users-cog"></i>
                            Affectation et Rôle
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="role_id" class="form-label required">
                                    <i class="fas fa-user-tag"></i>
                                    Rôle
                                </label>
                                <select id="role_id" name="role_id"
                                    class="form-select @error('role_id') error @enderror" required>
                                    <option value="">Sélectionnez un rôle</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="agency_id" class="form-label">
                                    <i class="fas fa-building"></i>
                                    Agence
                                </label>
                                <select id="agency_id" name="agency_id"
                                    class="form-select @error('agency_id') error @enderror">
                                    <option value="">Aucune agence</option>
                                    @foreach ($agencies as $agency)
                                        <option value="{{ $agency->id }}"
                                            {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                                            {{ $agency->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('agency_id')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="input-help">
                                    <p>Optionnel - Assignez l'utilisateur à une agence spécifique</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label required">
                                    <i class="fas fa-toggle-on"></i>
                                    Statut
                                </label>
                                <select id="status" name="status"
                                    class="form-select @error('status') error @enderror" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                        Actif
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Inactif
                                    </option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>
                                        Suspendu
                                    </option>
                                </select>
                                @error('status')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle font-awesome-icon"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Help -->
                        <div class="status-help">
                            <div class="status-item">
                                <span class="status-badge status-active">Actif</span>
                                <span>L'utilisateur peut se connecter et utiliser le système</span>
                            </div>
                            <div class="status-item">
                                <span class="status-badge status-inactive">Inactif</span>
                                <span>L'utilisateur ne peut pas se connecter</span>
                            </div>
                            <div class="status-item">
                                <span class="status-badge status-suspended">Suspendu</span>
                                <span>L'utilisateur est temporairement bloqué</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <div class="actions-left">
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times font-awesome-icon"></i>
                        <span>Annuler</span>
                    </a>
                </div>
                <div class="actions-right">
                    <button type="button" class="btn btn-outline" id="resetForm">
                        <i class="fas fa-undo font-awesome-icon"></i>
                        <span>Réinitialiser</span>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save font-awesome-icon"></i>
                        <span>Créer l'utilisateur</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createUserForm');
            const resetBtn = document.getElementById('resetForm');
            const submitBtn = document.getElementById('submitBtn');

            // Reset form
            resetBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser le formulaire ?')) {
                    form.reset();
                }
            });

            // Form submission
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin font-awesome-icon"></i> <span>Création...</span>';
                submitBtn.disabled = true;
            });

            // Password confirmation validation
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            function validatePassword() {
                if (password.value !== passwordConfirm.value) {
                    passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    passwordConfirm.setCustomValidity('');
                }
            }

            // password.addEventListener('input', validatePassword);
            // passwordConfirm.addEventListener('input', validatePassword);
        });
    </script>
@endsection
