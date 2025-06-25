@extends('super-admin.layout')

@section('title', 'Modifier l\'Utilisateur')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-user-forms.css') }}">
@endsection
{{-- 
@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.users.index') }}">Users</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.users.show', $user) }}">{{ $user->username }}</a>
    <i class="fas fa-chevron-right"></i>
    <span>User</span>
@endsection --}}

@section('content')
    <div class="user-form-container">
        <!-- Page Header -->
        <div class="page-header-card">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="header-info">
                        <h1>Modifier l'Utilisateur</h1>
                        <p>{{ $user->firstname }} {{ $user->lastname }} ({{ $user->username }})</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-secondary">
                        <i class="fas fa-eye font-awesome-icon"></i>
                        <span>Voir</span>
                    </a>
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left font-awesome-icon"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('super-admin.users.update', $user) }}" method="POST" class="user-form" id="editUserForm">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- Informations Actuelles -->
                <div class="form-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-info-circle"></i>
                            Informations Actuelles
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="current-info">
                            <div class="info-item">
                                <label>ID Utilisateur</label>
                                <span class="user-id">#{{ $user->id }}</span>
                            </div>
                            <div class="info-item">
                                <label>Statut actuel</label>
                                <span class="status-badge status-{{ $user->status }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Créé le</label>
                                <span>{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="info-item">
                                <label>Modifié le</label>
                                <span>{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                            </div>

                        </div>
                    </div>
                </div>

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
                                    class="form-input @error('firstname') error @enderror"
                                    value="{{ old('firstname', $user->firstname) }}" placeholder="Entrez le prénom">
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
                                    class="form-input @error('lastname') error @enderror"
                                    value="{{ old('lastname', $user->lastname) }}" placeholder="Entrez le nom de famille">
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
                                class="form-input @error('username') error @enderror"
                                value="{{ old('username', $user->username) }}" placeholder="Entrez le nom d'utilisateur"
                                required>
                            @error('username')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label required">
                                <i class="fas fa-envelope"></i>
                                Adresse Email
                            </label>
                            <input type="email" id="email" name="email"
                                class="form-input @error('email') error @enderror" value="{{ old('email', $user->email) }}"
                                placeholder="exemple@email.com" required>
                            @error('email')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Changement de Mot de Passe -->
                <div class="form-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-lock"></i>
                            Changement de Mot de Passe
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="form-row form-row-password">
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Nouveau mot de passe
                                </label>
                                <input type="password" id="password" name="password"
                                    class="form-input @error('password') error @enderror"
                                    placeholder="Nouveau mot de passe">
                                @error('password')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Confirmer le nouveau mot de passe
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-input" placeholder="Confirmez le nouveau mot de passe">
                            </div>
                        </div>
                        <div class="input-help">
                            <p><i class="fas fa-info-circle"></i> Laissez vide pour conserver le mot de passe actuel</p>
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
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                                            {{ old('agency_id', $user->agency_id) == $agency->id ? 'selected' : '' }}>
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
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label required">
                                    <i class="fas fa-toggle-on"></i>
                                    Statut
                                </label>
                                <select id="status" name="status"
                                    class="form-select @error('status') error @enderror" required>
                                    <option value="active"
                                        {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>
                                        Actif
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactif
                                    </option>
                                    <option value="suspended"
                                        {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>
                                        Suspendu
                                    </option>
                                </select>
                                @error('status')
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Agency Info -->
                        @if ($user->agency)
                            <div class="current-agency">
                                <h4>Agence Actuelle</h4>
                                <div class="agency-card">
                                    <div class="agency-info">
                                        <strong>{{ $user->agency->name }}</strong>
                                        <small>{{ $user->agency->email ?? 'Pas d\'email' }}</small>
                                    </div>
                                    <div class="agency-status">
                                        <span class="status-badge status-{{ $user->agency->status }}">
                                            {{ ucfirst($user->agency->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

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
                    <a href="{{ route('super-admin.users.show', $user) }}" class="btn btn-secondary">
                        <i class="fas fa-times font-awesome-icon"></i>
                        <span>Annuler</span>
                    </a>
                    {{-- <button type="button" class="btn btn-warning" id="resetPassword">
                        <i class="fas fa-key font-awesome-icon"></i>
                        <span>Réinitialiser MDP</span>
                    </button> --}}
                </div>
                <div class="actions-right">
                    <button type="button" class="btn btn-outline" id="resetForm">
                        <i class="fas fa-undo font-awesome-icon"></i>
                        <span>Annuler les modifications</span>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save font-awesome-icon"></i>
                        <span>Sauvegarder</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editUserForm');
            const resetBtn = document.getElementById('resetForm');
            // const resetPasswordBtn = document.getElementById('resetPassword');
            const submitBtn = document.getElementById('submitBtn');

            // Reset form
            resetBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir annuler toutes les modifications ?')) {
                    location.reload();
                }
            });

            // Reset password
            // resetPasswordBtn.addEventListener('click', function() {
            //     if (confirm('Générer un nouveau mot de passe temporaire pour cet utilisateur ?')) {
            //         // You can implement this functionality
            //         alert('Fonctionnalité à implémenter');
            //     }
            // });

            // Form submission
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sauvegarde...</span>';
                submitBtn.disabled = true;
            });

            // Password confirmation validation
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            function validatePassword() {
                if (password.value && password.value !== passwordConfirm.value) {
                    passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    passwordConfirm.setCustomValidity('');
                }
            }

            password.addEventListener('input', validatePassword);
            passwordConfirm.addEventListener('input', validatePassword);
        });
    </script>
@endsection
