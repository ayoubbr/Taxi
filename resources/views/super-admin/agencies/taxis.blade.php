@extends('super-admin.layout')

@section('title', 'Taxis - ' . $agency->name)

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.index') }}">Agences</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.show', $agency) }}">{{ $agency->name }}</a>
    <i class="fas fa-chevron-right"></i>
    <span>Taxis</span>
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
                <div class="stat-icon taxis">
                    <i class="fas fa-taxi"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_taxis'] }}</h3>
                    <p>Total Taxis</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon available">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['available_taxis'] }}</h3>
                    <p>Disponibles</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon assigned">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['assigned_taxis'] }}</h3>
                    <p>Avec Chauffeur</p>
                </div>
            </div>
            {{-- <div class="stat-card">
                <div class="stat-icon types">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['types_count'] }}</h3>
                    <p>Types Différents</p>
                </div>
            </div> --}}
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group search-box">
                    <input type="text" name="search" placeholder="Rechercher par plaque, modèle..."
                        value="{{ request('search') }}" class="form-control">
                </div>
                <div class="filter-group">
                    <select name="type" class="form-control">
                        <option value="">Tous les types</option>
                        <option value="standard" {{ request('type') == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="van" {{ request('type') == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="luxe" {{ request('type') == 'luxe' ? 'selected' : '' }}>Luxe</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="availability" class="form-control">
                        <option value="">Toute disponibilité</option>
                        <option value="true" {{ request('availability') == 'true' ? 'selected' : '' }}>Disponible</option>
                        <option value="false" {{ request('availability') == 'false' ? 'selected' : '' }}>Indisponible</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('super-admin.agencies.taxis', $agency) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des Taxis -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-taxi"></i>
                    Taxis de l'agence
                </h2>
                <span class="count-badge">{{ $taxis->total() }} taxis</span>
            </div>

            @if ($taxis->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Plaque</th>
                                <th>Modèle</th>
                                <th>Type</th>
                                <th>Capacité</th>
                                <th>Chauffeur</th>
                                <th>Ville</th>
                                <th>Disponibilité</th>
                                {{-- <th>Actions</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($taxis as $taxi)
                                <tr>
                                    <td>
                                        <strong class="license-plate">{{ $taxi->license_plate }}</strong>
                                    </td>
                                    <td>{{ $taxi->model ?? 'Non spécifié' }}</td>
                                    <td>
                                        <span class="type-badge type-{{ $taxi->type }}">
                                            {{ ucfirst($taxi->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="capacity-badge">
                                            <i class="fas fa-users"></i>
                                            {{ $taxi->capacity }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($taxi->driver)
                                            <div class="driver-info">
                                                <strong>{{ $taxi->driver->firstname }}
                                                    {{ $taxi->driver->lastname }}</strong>
                                                <small>{{ $taxi->driver->username }}</small>
                                            </div>
                                        @else
                                            <span class="no-driver">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($taxi->city)
                                            {{ $taxi->city->name }}
                                        @else
                                            <span class="no-city">Non spécifiée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="status-badge {{ $taxi->is_available ? 'status-available' : 'status-unavailable' }}">
                                            {{ $taxi->is_available ? 'Disponible' : 'Indisponible' }}
                                        </span>
                                    </td>
                                    {{-- <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $taxis->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-taxi"></i>
                    <h3>Aucun taxi trouvé</h3>
                    <p>Cette agence n'a pas encore de taxis ou vos filtres ne correspondent à aucun résultat.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('js')
    {{-- <script src="{{ asset('js/super-admin-agency-show.js') }}"></script> --}}
@endsection
