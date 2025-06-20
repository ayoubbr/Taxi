@extends('super-admin.layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-dashboard.css') }}">
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
                            <h1><i class="fas fa-building"></i> Gestion des Agences</h1>
                            <p>Gérer toutes les agences partenaires</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('super-admin.agencies.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Agence
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="page-content">
            <div class="container">
                <!-- Filters & Search -->
                <div class="filters-section">
                    <form method="GET" class="filters-form">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Rechercher par nom ou address ...">
                        </div>

                        <div class="filter-group">
                            <select name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                                <option value="suspendu" {{ request('status') === 'suspendu' ? 'selected' : '' }}>Suspendu
                                </option>
                            </select>
                        </div>

                        {{-- <div class="filter-group">
                            <select name="city">
                                <option value="">Toutes les villes</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->name }}"
                                        {{ request('city') === $city->name ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}

                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>

                        @if (request()->hasAny(['search', 'status', 'city']))
                            <a href="{{ route('super-admin.agencies.index') }}" class="btn btn-outline">
                                <i class="fas fa-times"></i> Effacer
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Agencies Grid -->
                <div class="agencies-grid">
                    @forelse($agencies as $agency)
                        <div class="agency-card">
                            <div class="agency-header">
                                <div class="agency-logo">
                                    @if ($agency->logo)
                                        <img src="{{ Storage::url($agency->logo) }}" alt="{{ $agency->name }}">
                                    @else
                                        <div class="logo-placeholder">
                                            {{ strtoupper(substr($agency->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="agency-status status-{{ $agency->status }}">
                                    {{ ucfirst($agency->status) }}
                                </div>
                            </div>

                            <div class="agency-info">
                                <h3>{{ $agency->name }}</h3>
                                <p class="agency-email">
                                    <i class="fas fa-envelope"></i> {{ $agency->email ?? '-email-' }}
                                </p>
                                <p class="agency-location">
                                    <i class="fas fa-map-marker-alt"></i> {{ $agency->address }}
                                </p>
                                @if ($agency->phone)
                                    <p class="agency-phone">
                                        <i class="fas fa-phone"></i> {{ $agency->phone }}
                                    </p>
                                @endif
                            </div>

                            <div class="agency-stats">
                                <div class="stat-item">
                                    <span class="stat-number">{{ $agency->drivers_count }}</span>
                                    <span class="stat-label">Chauffeurs</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number">{{ $agency->bookings_count }}</span>
                                    <span class="stat-label">Réservations</span>
                                </div>
                            </div>

                            <div class="agency-actions">
                                <a href="{{ route('super-admin.agencies.show', $agency) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <a href="{{ route('super-admin.agencies.edit', $agency) }}"
                                    class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('super-admin.agencies.toggle-status', $agency) }}" method="POST"
                                    class="inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <button style="min-width: -webkit-fill-available;" type="submit"
                                        class="btn btn-sm {{ $agency->status === 'active' ? 'btn-warning' : 'btn-success' }}">
                                        <i class="fas fa-{{ $agency->status === 'active' ? 'pause' : 'play' }}"></i>
                                        {{ $agency->status === 'active' ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                @if ($agency->status != 'suspendu')
                                    <form action="{{ route('super-admin.agencies.suspend', $agency) }}" method="POST"
                                        class="inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <button style="min-width: -webkit-fill-available;" type="submit"
                                            class="btn btn-sm btn-danger">
                                            <i class="fas fa-ban"></i>
                                            Suspend
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3>Aucune agence trouvée</h3>
                            <p>{{ request()->hasAny(['search', 'status', 'city']) ? 'Aucune agence ne correspond à vos critères.' : 'Aucune agence enregistrée pour le moment.' }}
                            </p>
                            @if (!request()->hasAny(['search', 'status', 'city']))
                                <a href="{{ route('super-admin.agencies.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Créer la première agence
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if ($agencies->hasPages())
                    <div class="pagination-container">
                        {{ $agencies->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </main>
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
                            `Êtes-vous sûr de vouloir ${action.toLowerCase()} cette agence ?`)) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection
