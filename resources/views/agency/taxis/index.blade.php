@extends('agency.layout')

@section('title', 'Gestion des Taxis')

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <span>Taxis</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-taxis.css') }}">
@endsection

@section('content')
    <div class="taxis-container">
        <!-- Page Header -->
        <div class="agency-page-header">
            <div class="agency-header-content">
                <div class="agency-header-left">
                    <div class="agency-header-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="agency-header-info">
                        <h1>Gestion des Taxis</h1>
                        <p>Gérer la flotte de véhicules de votre agence</p>
                    </div>
                </div>
                <div class="agency-header-actions">
                    <a href="{{ route('agency.taxis.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Taxi
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
                        placeholder="Plaque, modèle..." class="form-control">
                </div>

                <div class="filter-group">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">Tous les types</option>
                        <option value="standard" {{ request('type') === 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="van" {{ request('type') === 'van' ? 'selected' : '' }}>Van</option>
                        <option value="luxe" {{ request('type') === 'luxe' ? 'selected' : '' }}>Luxe</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="availability">Disponibilité</label>
                    <select name="availability" id="availability" class="form-control">
                        <option value="">Tous</option>
                        <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Disponible
                        </option>
                        <option value="occupied" {{ request('availability') === 'occupied' ? 'selected' : '' }}>Occupé
                        </option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="city_id">Ville</label>
                    <select name="city_id" id="city_id" class="form-control">
                        <option value="">Toutes les villes</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('agency.taxis.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Taxis Grid -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-car"></i>
                    Liste des Taxis
                </h2>
                <span class="count-badge">{{ $taxis->total() }} taxis</span>
            </div>

            @if ($taxis->count() > 0)
                <div class="taxis-grid">
                    @foreach ($taxis as $taxi)
                        <div class="taxi-card">
                            <div class="taxi-header">
                                <div class="taxi-plate">
                                    <i class="fas fa-car"></i>
                                    {{ $taxi->license_plate }}
                                </div>
                                <div class="taxi-status status-{{ $taxi->is_available ? 'available' : 'occupied' }}">
                                    {{ $taxi->is_available ? 'Disponible' : 'Occupé' }}
                                </div>
                            </div>

                            <div class="taxi-info">
                                <h3>{{ $taxi->model }}</h3>
                                <div class="taxi-details">
                                    <p class="taxi-type">
                                        <i class="fas fa-tag"></i>
                                        <span
                                            class="type-badge type-{{ $taxi->type }}">{{ ucfirst($taxi->type) }}</span>
                                    </p>
                                    <p class="taxi-city">
                                        <i class="fas fa-map-marker-alt"></i> {{ $taxi->city->name }}
                                    </p>
                                    <p class="taxi-capacity">
                                        <i class="fas fa-users"></i> {{ $taxi->capacity }} places
                                    </p>
                                    @if ($taxi->driver)
                                        <p class="taxi-driver">
                                            <i class="fas fa-user"></i> {{ $taxi->driver->firstname }}
                                            {{ $taxi->driver->lastname }}
                                        </p>
                                    @else
                                        <p class="taxi-driver no-driver">
                                            <i class="fas fa-user-slash"></i> Aucun chauffeur assigné
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="taxi-stats">
                                <div class="stat-item">
                                    <span class="stat-number">{{ $taxi->bookings()->count() }}</span>
                                    <span class="stat-label">Courses</span>
                                </div>
                                <div class="stat-item">
                                    <span
                                        class="stat-number">{{ $taxi->bookings()->where('status', 'COMPLETED')->count() }}</span>
                                    <span class="stat-label">Terminées</span>
                                </div>
                            </div>

                            <div class="taxi-actions">
                                <a href="{{ route('agency.taxis.show', $taxi) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <a href="{{ route('agency.taxis.edit', $taxi) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('agency.taxis.toggle-availability', $taxi) }}" method="POST"
                                    class="inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $taxi->is_available ? 'btn-warning' : 'btn-success' }}">
                                        <i class="fas fa-{{ $taxi->is_available ? 'pause' : 'play' }}"></i>
                                        {{ $taxi->is_available ? 'Occuper' : 'Libérer' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $taxis->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-car"></i>
                    <h3>Aucun taxi trouvé</h3>
                    <p>{{ request()->hasAny(['search', 'type', 'availability', 'city_id']) ? 'Aucun taxi ne correspond à vos critères.' : 'Aucun taxi enregistré pour le moment.' }}
                    </p>
                    @if (!request()->hasAny(['search', 'type', 'availability', 'city_id']))
                        <a href="{{ route('agency.taxis.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter le premier taxi
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

            // Confirm availability toggle
            const availabilityForms = document.querySelectorAll('.inline-form');
            availabilityForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const button = this.querySelector('button');
                    const action = button.textContent.trim();

                    if (confirm(`Êtes-vous sûr de vouloir ${action.toLowerCase()} ce taxi ?`)) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection
