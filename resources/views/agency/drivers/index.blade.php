@extends('agency.layout')

@section('title', 'Gestion des Chauffeurs')

@section('breadcrumb')
    <a href="{{ route('agency-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <span>Chauffeurs</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-drivers.css') }}">
@endsection

@section('content')
    <div class="drivers-container">
        <!-- Page Header -->
        <div class="agency-page-header">
            <div class="agency-header-content">
                <div class="agency-header-left">
                    <div class="agency-header-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="agency-header-info">
                        <h1>Gestion des Chauffeurs</h1>
                        <p>Gérer les chauffeurs de votre agence</p>
                    </div>
                </div>
                <div class="agency-header-actions">
                    <a href="{{ route('agency-admin.drivers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Chauffeur
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Nom, prénom, email..." class="form-control">
                </div>

                <div class="filter-group">
                    <label for="status">Statut</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendu
                        </option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('agency-admin.drivers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Drivers Grid -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-users"></i>
                    Liste des Chauffeurs
                </h2>
                <span class="count-badge">{{ $drivers->total() }} chauffeurs</span>
            </div>

            @if ($drivers->count() > 0)
                <div class="drivers-grid">
                    @foreach ($drivers as $driver)
                        <div class="driver-card">
                            <div class="driver-header">
                                <div class="driver-avatar">
                                    {{ strtoupper(substr($driver->firstname, 0, 1)) }}{{ strtoupper(substr($driver->lastname, 0, 1)) }}
                                </div>
                                <div class="driver-status status-{{ $driver->status }}">
                                    {{ ucfirst($driver->status) }}
                                </div>
                            </div>

                            <div class="driver-info">
                                <h3>{{ $driver->firstname }} {{ $driver->lastname }}</h3>
                                <p class="driver-username">
                                    <i class="fas fa-user"></i> {{ $driver->username }}
                                </p>
                                <p class="driver-email">
                                    <i class="fas fa-envelope"></i> {{ $driver->email }}
                                </p>
                                @if ($driver->taxi)
                                    <p class="driver-taxi">
                                        <i class="fas fa-car"></i> {{ $driver->taxi->license_plate }}
                                        ({{ ucfirst($driver->taxi->type) }})
                                    </p>
                                @else
                                    <p class="driver-taxi no-taxi">
                                        <i class="fas fa-car"></i> Aucun taxi assigné
                                    </p>
                                @endif
                            </div>

                            <div class="driver-stats">
                                <div class="stat-item">
                                    <span class="stat-number">{{ $driver->assignedDriverBookings()->count() }}</span>
                                    <span class="stat-label">Courses</span>
                                </div>
                                <div class="stat-item">
                                    <span
                                        class="stat-number">{{ $driver->assignedDriverBookings()->where('status', 'COMPLETED')->count() }}</span>
                                    <span class="stat-label">Terminées</span>
                                </div>
                            </div>

                            <div class="driver-actions">
                                <a href="{{ route('agency-admin.drivers.show', $driver) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <a href="{{ route('agency-admin.drivers.edit', $driver) }}"
                                    class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('agency-admin.drivers.toggle-status', $driver) }}" method="POST"
                                    class="inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $driver->status === 'active' ? 'btn-warning' : 'btn-success' }}">
                                        <i class="fas fa-{{ $driver->status === 'active' ? 'pause' : 'play' }}"></i>
                                        {{ $driver->status === 'active' ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $drivers->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>Aucun chauffeur trouvé</h3>
                    <p>{{ request()->hasAny(['search', 'status']) ? 'Aucun chauffeur ne correspond à vos critères.' : 'Aucun chauffeur enregistré pour le moment.' }}
                    </p>
                    @if (!request()->hasAny(['search', 'status']))
                        <a href="{{ route('agency-admin.drivers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter le premier chauffeur
                        </a>
                    @endif
                </div>
            @endif
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

            // Confirm status toggle
            const statusForms = document.querySelectorAll('.inline-form');
            statusForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const button = this.querySelector('button');
                    const action = button.textContent.trim();

                    if (confirm(
                        `Êtes-vous sûr de vouloir ${action.toLowerCase()} ce chauffeur ?`)) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection
