@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
@endsection

@section('content')
    <div class="super-admin-dashboard">
        <!-- Header Section -->
        <header class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-left">
                        <a href="{{ route('super-admin.dashboard') }}" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="header-info">
                            <h1><i class="fas fa-users-cog"></i> Gestion des Utilisateurs</h1>
                            <p>Gérer tous les utilisateurs de la plateforme</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Nouvel Utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Overview -->
        <div class="container">
            <div class="stats-overview">
                <div class="stat-card mini">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($stats['total_users']) }}</h4>
                        <p>Total Utilisateurs</p>
                    </div>
                </div>
                <div class="stat-card mini">
                    <div class="stat-icon">
                        <i class="fas fa-taxi"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($stats['drivers']) }}</h4>
                        <p>Chauffeurs</p>
                    </div>
                </div>
                <div class="stat-card mini">
                    <div class="stat-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($stats['clients']) }}</h4>
                        <p>Clients</p>
                    </div>
                </div>
                <div class="stat-card mini danger">
                    <div class="stat-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-content">
                        <h4>{{ number_format($stats['banned_users']) }}</h4>
                        <p>Bannis</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="page-content">
            <div class="container">
                <!-- Filters & Search -->
                <div class="filters-section">
                    <form method="GET" class="filters-form">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Rechercher par nom, email, username...">
                        </div>

                        <div class="filter-group">
                            <select name="role">
                                <option value="">Tous les rôles</option>
                                <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Client</option>
                                <option value="driver" {{ request('role') === 'driver' ? 'selected' : '' }}>Chauffeur
                                </option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <select name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif
                                </option>
                                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banni
                                </option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <select name="agency">
                                <option value="">Toutes les agences</option>
                                @foreach ($agencies as $agency)
                                    <option value="{{ $agency->id }}"
                                        {{ request('agency') == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>

                        @if (request()->hasAny(['search', 'role', 'status', 'agency']))
                            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline">
                                <i class="fas fa-times"></i> Effacer
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Users Table -->
                <div class="users-table-container">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Contact</th>
                                    <th>Rôle</th>
                                    <th>Agence</th>
                                    <th>Statut</th>
                                    <th>Inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    @if ($user->profile_photo)
                                                        <img src="{{ $user->profile_photo }}" alt="Avatar">
                                                    @else
                                                        {{ strtoupper(substr($user->firstname, 0, 1)) }}{{ strtoupper(substr($user->lastname, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div class="user-details">
                                                    <h4>{{ $user->firstname }} {{ $user->lastname }}</h4>
                                                    <span class="username">@{{ $user - > username }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="contact-info">
                                                <div class="email">
                                                    <i class="fas fa-envelope"></i> {{ $user->email }}
                                                </div>
                                                @if ($user->phone)
                                                    <div class="phone">
                                                        <i class="fas fa-phone"></i> {{ $user->phone }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="role-badge role-{{ $user->role }}">
                                                <i
                                                    class="fas fa-{{ $user->role === 'driver' ? 'taxi' : ($user->role === 'admin' ? 'user-shield' : 'user') }}"></i>
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($user->agency)
                                                <span class="agency-name">{{ $user->agency->name }}</span>
                                            @else
                                                <span class="no-agency">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge status-{{ $user->status }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="date">{{ $user->created_at->format('d/m/Y') }}</span>
                                            <span class="time">{{ $user->created_at->diffForHumans() }}</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('super-admin.users.show', $user) }}"
                                                    class="btn btn-sm btn-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('super-admin.users.edit', $user) }}"
                                                    class="btn btn-sm btn-secondary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if ($user->status === 'banned')
                                                    <form action="{{ route('super-admin.users.unban', $user) }}"
                                                        method="POST" class="inline-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            title="Débannir">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('super-admin.users.ban', $user) }}"
                                                        method="POST" class="inline-form">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Bannir">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="fas fa-users"></i>
                                                <h3>Aucun utilisateur trouvé</h3>
                                                <p>{{ request()->hasAny(['search', 'role', 'status', 'agency']) ? 'Aucun utilisateur ne correspond à vos critères.' : 'Aucun utilisateur enregistré.' }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if ($users->hasPages())
                    <div class="pagination-container">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <!-- Ban Confirmation Modal -->
    <div class="modal" id="banModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirmer le bannissement</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir bannir cet utilisateur ?</p>
                <p><strong>Cette action empêchera l'utilisateur d'accéder à la plateforme.</strong></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary close-modal">Annuler</button>
                <button class="btn btn-danger" id="confirmBan">Oui, bannir</button>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change
            const filterSelects = document.querySelectorAll('.filter-group select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });

            // Ban confirmation modal
            const modal = document.getElementById('banModal');
            const banForms = document.querySelectorAll('.inline-form');
            const closeButtons = document.querySelectorAll('.close-modal');
            let currentForm = null;

            banForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('button');
                    if (button.title === 'Bannir') {
                        e.preventDefault();
                        currentForm = this;
                        modal.classList.add('active');
                    }
                });
            });

            document.getElementById('confirmBan').addEventListener('click', function() {
                if (currentForm) {
                    currentForm.submit();
                }
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modal.classList.remove('active');
                });
            });

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
@endsection
