@extends('super-admin.layout')

@section('title', 'Gestion des Réservations')

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <span>Réservations</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-bookings.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    <div class="bookings-container">
        <!-- Page Header -->
        <div class="page-header-card">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="header-info">
                        <h1>Gestion des Réservations</h1>
                        <p>Gérez toutes les réservations de taxi du système</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn btn-outline" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Global Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['total_bookings']) }}</h3>
                    <p>Total Réservations</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['pending_bookings']) }}</h3>
                    <p>En Attente</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon progress">
                    <i class="fas fa-route"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['in_progress_bookings']) }}</h3>
                    <p>En Cours</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['completed_bookings']) }}</h3>
                    <p>Terminées</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['total_revenue'], 2) }} DH</h3>
                    <p>Revenus Total</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon today">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['bookings_today']) }}</h3>
                    <p>Aujourd'hui</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon rate">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['completion_rate'] }}%</h3>
                    <p>Taux de Réussite</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon average">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['average_fare'] ?? 0, 0) }} DH</h3>
                    <p>Tarif Moyen</p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Client, UUID, lieu..." class="form-control">
                </div>

                <div class="filter-group">
                    <label for="status">Statut</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>En Attente</option>
                        <option value="ASSIGNED" {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>Assignée</option>
                        <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>En Cours
                        </option>
                        <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Terminée
                        </option>
                        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Annulée
                        </option>
                        <option value="NO_TAXI_FOUND" {{ request('status') === 'NO_TAXI_FOUND' ? 'selected' : '' }}>Aucun
                            Taxi</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="pickup_city">Ville Départ</label>
                    <select id="pickup_city" name="pickup_city" class="form-control">
                        <option value="">Toutes les villes</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}"
                                {{ request('pickup_city') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="taxi_type">Type Taxi</label>
                    <select id="taxi_type" name="taxi_type" class="form-control">
                        <option value="">Tous les types</option>
                        <option value="standard" {{ request('taxi_type') === 'standard' ? 'selected' : '' }}>Standard
                        </option>
                        <option value="van" {{ request('taxi_type') === 'van' ? 'selected' : '' }}>Van</option>
                        <option value="luxe" {{ request('taxi_type') === 'luxe' ? 'selected' : '' }}>Luxe</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_from">Date Début</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                        class="form-control" placeholder="Date de début">
                </div>

                <div class="filter-group">
                    <label for="date_to">Date Fin</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                        class="form-control" placeholder="Date de fin">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('super-admin.bookings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-list"></i>
                    Liste des Réservations
                    <span class="count-badge">{{ $bookings->total() }}</span>
                </h2>
            </div>

            @if ($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>UUID</th>
                                <th>Client</th>
                                <th>Trajet</th>
                                <th>Date/Heure</th>
                                <th>Statut</th>
                                <th>Chauffeur</th>
                                <th>Tarif</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>
                                        <span class="booking-id">{{ substr($booking->booking_uuid, 0, 8) }}...</span>
                                    </td>
                                    <td>
                                        <div class="client-info">
                                            <strong>{{ $booking->client_name }}</strong>
                                            @if ($booking->client)
                                                <small>{{ $booking->client->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="trip-info">
                                            <div class="pickup">
                                                <i class="fas fa-map-marker-alt text-success"></i>
                                                {{ $booking->pickupCity->name ?? 'N/A' }}
                                            </div>
                                            <div class="destination">
                                                <i class="fas fa-flag-checkered text-danger"></i>
                                                {{ $booking->destinationCity->name ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="datetime-info">
                                            <strong>{{ $booking->pickup_datetime->format('d/m/Y') }}</strong>
                                            <small>{{ $booking->pickup_datetime->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $booking->status }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($booking->driver)
                                            <div class="driver-info">
                                                <strong>{{ $booking->driver->firstname }}
                                                    {{ $booking->driver->lastname }}</strong>
                                                @if ($booking->taxi)
                                                    <small>{{ $booking->taxi->license_plate }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="no-driver">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($booking->estimated_fare)
                                            <span class="fare">{{ number_format($booking->estimated_fare, 2) }}
                                                DH</span>
                                        @else
                                            <span class="no-fare">Non défini</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('super-admin.bookings.show', $booking) }}"
                                                class="btn btn-sm btn-primary" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('super-admin.bookings.edit', $booking) }}"
                                                class="btn btn-sm btn-secondary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Aucune réservation trouvée</h3>
                    <p>Aucune réservation ne correspond à vos critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh every 30 seconds
            // setInterval(function() {
            //     if (document.visibilityState === 'visible') {
            //         window.location.reload();
            //     }
            // }, 30000);

            flatpickr("#date_from", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                allowInput: true
            });

            flatpickr("#date_to", {
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                allowInput: true
            });
        });
    </script>
@endsection
