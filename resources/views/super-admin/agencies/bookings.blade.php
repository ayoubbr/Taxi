@extends('super-admin.layout')

@section('title', 'Réservations - ' . $agency->name)

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.index') }}">Agences</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.show', $agency) }}">{{ $agency->name }}</a>
    <i class="fas fa-chevron-right"></i>
    <span>Réservations</span>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-agency-details.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                            <i class="fas fa-building"></i>
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
                <div class="stat-icon bookings">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['total_bookings'] }}</h3>
                    <p>Total Réservations</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['completed_bookings'] }}</h3>
                    <p>Terminées</p>
                </div>
            </div>
            {{-- <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $stats['pending_bookings'] }}</h3>
                    <p>En Attente</p>
                </div>
            </div> --}}
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['total_revenue'], 0, ',', ' ') }} DH</h3>
                    <p>Revenus Total</p>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group search-box">
                    <label for="search">Recherche</label>
                    <input id="search" type="text" name="search" placeholder="Rechercher par client, destination..."
                        value="{{ request('search') }}" class="form-control">
                </div>
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>En attente</option>
                        <option value="ASSIGNED" {{ request('status') == 'ASSIGNED' ? 'selected' : '' }}>Assignée</option>
                        <option value="IN_PROGRESS" {{ request('status') == 'IN_PROGRESS' ? 'selected' : '' }}>En cours
                        </option>
                        <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Terminée
                        </option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="taxi_type">Taxi type</label>
                    <select name="taxi_type" id="taxi_type" class="form-control">
                        <option value="">Tous les types</option>
                        <option value="standard" {{ request('taxi_type') == 'standard' ? 'selected' : '' }}>Standard
                        </option>
                        <option value="van" {{ request('taxi_type') == 'van' ? 'selected' : '' }}>Van</option>
                        <option value="luxe" {{ request('taxi_type') == 'luxe' ? 'selected' : '' }}>Luxe</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="date_from">Start Date</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                        class="form-control datepicker" placeholder="Date de début">
                </div>
                <div class="filter-group">
                    <label for="date_to">End Date</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                        class="form-control datepicker" placeholder="Date de fin">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filtrer
                    </button>
                    <a href="{{ route('super-admin.agencies.bookings', $agency) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des Réservations -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-calendar-check"></i>
                    Réservations de l'agence
                </h2>
                <span class="count-badge">{{ $bookings->total() }} réservations</span>
            </div>

            @if ($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Trajet</th>
                                <th>Date/Heure</th>
                                <th>Chauffeur</th>
                                <th>Taxi</th>
                                <th>Statut</th>
                                <th>Montant</th>
                                {{-- <th>Actions</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>
                                        <strong class="booking-id">#{{ $booking->id }}</strong>
                                        <small>{{ substr($booking->booking_uuid, 0, 8) }}</small>
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
                                                {{ Str::limit($booking->pickup_location, 30) }}
                                            </div>
                                            <div class="destination">
                                                <i class="fas fa-flag text-danger"></i>
                                                {{ Str::limit($booking->destinationCity->name, 30) }}
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
                                        @if ($booking->driver)
                                            <div class="driver-info">
                                                <strong>{{ $booking->driver->firstname }}
                                                    {{ $booking->driver->lastname }}</strong>
                                                <small>{{ $booking->driver->username }}</small>
                                            </div>
                                        @else
                                            <span class="no-driver">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($booking->taxi)
                                            <div class="taxi-info">
                                                <strong>{{ $booking->taxi->license_plate }}</strong>
                                                <small>{{ ucfirst($booking->taxi->type) }}</small>
                                            </div>
                                        @else
                                            <span class="no-taxi">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $booking->status }}">
                                            {{ $booking->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($booking->estimated_fare)
                                            <strong
                                                class="fare">{{ number_format($booking->estimated_fare, 0, ',', ' ') }}
                                                DH</strong>
                                        @else
                                            <span class="no-fare">Non estimé</span>
                                        @endif
                                    </td>
                                    {{-- <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('client.bookings.show', $booking->booking_uuid) }}"
                                                class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="fas fa-eye"></i>
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
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Aucune réservation trouvée</h3>
                    <p>Cette agence n'a pas encore de réservations ou vos filtres ne correspondent à aucun résultat.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
@section('js')
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
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
    </script>

@endsection
{{-- <script src="{{ asset('js/super-admin-agency-show.js') }}"></script> --}}
