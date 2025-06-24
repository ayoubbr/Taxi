@extends('super-admin.layout')

@section('title', 'Utilisateurs - ' . $agency->name)

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.index') }}">Agences</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.show', $agency) }}">{{ $agency->name }}</a>
    <i class="fas fa-chevron-right"></i>
    <span>Utilisateurs</span>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-agency-details.css') }}">
@endsection
@section('content')
    <div class="agency-details-container">
        <!-- Header Agence -->
        <div class="agency-header">
            <div class="agency-info">
                <div class="agency-logo">
                    @if ($agency->logo)
                        <img src="{{ asset('storage/' . $agency->logo) }}" alt="{{ $agency->name }}">
                    @else
                        <div class="logo-placeholder">
                            {{ strtoupper(substr($agency->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="agency-details">
                    <h1>{{ $agency->name }}</h1>
                    <span class="status-badge status-{{ $agency->status }}">
                        {{ ucfirst($agency->status) }}
                    </span>
                    <p class="agency-address">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $agency->address }}
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('super-admin.agencies.show', $agency) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Total Utilisateurs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['active_users'] }}</h3>
                    <p>Utilisateurs Actifs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon drivers">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['drivers_count'] }}</h3>
                    <p>Chauffeurs</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon clients">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['clients_count'] }}</h3>
                    <p>Clients</p>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group search-box">
                    <input type="text" name="search" placeholder="Rechercher par nom, email..."
                        value="{{ request('search') }}" class="form-control">
                </div>
                <div class="filter-group">
                    <select name="role" class="form-control">
                        <option value="">Tous les rôles</option>
                        <option value="DRIVER" {{ request('role') == 'DRIVER' ? 'selected' : '' }}>Chauffeur</option>
                        <option value="CLIENT" {{ request('role') == 'CLIENT' ? 'selected' : '' }}>Client</option>
                        <option value="AGENCY_ADMIN" {{ request('role') == 'AGENCY_ADMIN' ? 'selected' : '' }}>Admin Agence
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="status" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspendu" {{ request('status') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('super-admin.agencies.users', $agency) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des Utilisateurs -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-users"></i>
                    Utilisateurs de l'agence
                </h2>
                <span class="count-badge">{{ $users->total() }} utilisateurs</span>
            </div>

            @if ($users->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Inscription</th>
                                {{-- <th>Actions</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($user->firstname ?? $user->username, 0, 1)) }}
                                            </div>
                                            <div class="user-details">
                                                <strong>{{ $user->firstname }} {{ $user->lastname }}</strong>
                                                <small>{{ $user->username }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="role-badge role-{{ $user->role->name }}">
                                            {{ $user->role->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="status-badge {{ $user->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    {{-- <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('super-admin.users.show', $user) }}"
                                                class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('super-admin.users.edit', $user) }}"
                                                class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Aucun utilisateur trouvé</h3>
                    <p>Cette agence n'a pas encore d'utilisateurs ou vos filtres ne correspondent à aucun résultat.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    {{-- <script src="{{ asset('js/super-admin-agency-show.js') }}"></script> --}}
@endsection
