@extends('agency.layout')

@section('title', 'Gestion des Réservations')

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <span>Réservations</span>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('css/agency-admin-bookings.css') }}">
@endsection

@section('content')
    <div class="bookings-container">
        <!-- Page Header -->
        <div class="agency-page-header">
            <div class="agency-header-content">
                <div class="agency-header-left">
                    <div class="agency-header-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="agency-header-info">
                        <h1>Gestion des Réservations</h1>
                        <p>Gérer toutes les réservations de votre agence</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="search">Recherche</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="UUID, client, lieu..." class="form-control">
                </div>

                <div class="filter-group">
                    <label for="status">Statut</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>En attente</option>
                        <option value="ASSIGNED" {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>Attribué
                        </option>
                        <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>En cours
                        </option>
                        <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Terminée
                        </option>
                        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="driver_id">Chauffeur</label>
                    <select name="driver_id" id="driver_id" class="form-control">
                        <option value="">Tous les chauffeurs</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->firstname }} {{ $driver->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="taxi_id">Taxi</label>
                    <select name="taxi_id" id="taxi_id" class="form-control">
                        <option value="">Tous les taxis</option>
                        @foreach ($taxis as $taxi)
                            <option value="{{ $taxi->id }}" {{ request('taxi_id') == $taxi->id ? 'selected' : '' }}>
                                {{ $taxi->license_plate }} ({{ $taxi->model }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_from">Date début</label>
                    <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}"
                        class="form-control" placeholder="Date de début">
                </div>

                <div class="filter-group">
                    <label for="date_to">Date fin</label>
                    <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}"
                        class="form-control" placeholder="Date de fin">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('agency.bookings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="content-section">
            <div class="section-header">
                <h2>
                    <i class="fas fa-calendar-check"></i>
                    Liste des Réservations
                </h2>
                <span class="count-badge">{{ $bookings->total() }} réservations</span>
            </div>

            @if ($bookings->count() > 0)
                <div class="bookings-table-container">
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>UUID</th>
                                <th>Client</th>
                                <th>Trajet</th>
                                <th>Date/Heure</th>
                                <th>Chauffeur</th>
                                <th>Taxi</th>
                                <th>Statut</th>
                                <th>Tarif</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr class="booking-row">
                                    <td>
                                        <div class="booking-uuid">
                                            <i class="fas fa-qrcode"></i>
                                            <span>{{ substr($booking->booking_uuid, 0, 8) }}...</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="client-info">
                                            @if ($booking->client)
                                                <strong>{{ $booking->client->firstname }}
                                                    {{ $booking->client->lastname }}</strong>
                                                <small>{{ $booking->client->email }}</small>
                                            @else
                                                <strong>{{ $booking->client_name }}</strong>
                                                <small>Client externe</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="route-info">
                                            <div class="pickup">
                                                <i class="fas fa-map-marker-alt text-success"></i>
                                                {{ $booking->pickup_location }}
                                            </div>
                                            <div class="destination">
                                                <i class="fas fa-flag text-danger"></i>
                                                {{ $booking->destinationCity->name ?? 'Non spécifiée' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="datetime-info">
                                            <div class="date">{{ $booking->pickup_datetime->format('d/m/Y') }}</div>
                                            <div class="time">{{ $booking->pickup_datetime->format('H:i') }}</div>
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
                                            <span class="not-assigned">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($booking->taxi)
                                            <div class="taxi-info">
                                                <strong style="width: max-content;">{{ $booking->taxi->license_plate }}</strong>
                                                <small>{{ $booking->taxi->model }}</small>
                                            </div>
                                        @else
                                            <span class="not-assigned">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $booking->status }}">
                                            {{ ucfirst(strtolower($booking->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fare-info">
                                            <strong>{{ number_format($booking->estimated_fare, 0, ',', ' ') }}€</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="booking-actions">
                                            <a href="{{ route('agency.bookings.show', $booking) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if (!in_array($booking->status, ['COMPLETED', 'CANCELLED']))
                                                <button type="button" class="btn btn-sm btn-secondary"
                                                    onclick="openAssignModal({{ $booking->id }})">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @endif
                                            <form action="{{ route('agency.bookings.destroy', $booking) }}"
                                                method="POST" style="display: inline;" class="delete-booking-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
                    <p>{{ request()->hasAny(['search', 'status', 'driver_id', 'taxi_id', 'date_from', 'date_to']) ? 'Aucune réservation ne correspond à vos critères.' : 'Aucune réservation enregistrée pour le moment.' }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Assignment Modal -->
    <div id="assignModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Assigner Chauffeur/Taxi</h3>
                <span class="close">&times;</span>
            </div>
            <form id="assignForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal_driver_id">Chauffeur</label>
                        <select id="modal_driver_id" name="assigned_driver_id" class="form-control">
                            <option value="">Sélectionner un chauffeur</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->firstname }} {{ $driver->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modal_taxi_id">Taxi</label>
                        <select id="modal_taxi_id" name="assigned_taxi_id" class="form-control">
                            <option value="">Sélectionner un taxi</option>
                            @foreach ($taxis as $taxi)
                                <option value="{{ $taxi->id }}">{{ $taxi->license_plate }} - {{ $taxi->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close-modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            flatpickr("#date_to", {
                dateFormat: "Y-m-d",
                disableMobile: true,
            });

            flatpickr("#date_from", {
                dateFormat: "Y-m-d",
                disableMobile: true,
            });

            // Auto-submit form on filter change
            const filterSelects = document.querySelectorAll('.filter-group select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });

            // Confirm booking deletion
            const deleteForms = document.querySelectorAll('.delete-booking-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (confirm(
                            'Êtes-vous sûr de vouloir supprimer cette réservation ?\n\nCette action est irréversible.'
                        )) {
                        this.submit();
                    }
                });
            });

            // Modal functionality
            const modal = document.getElementById('assignModal');
            const closeButtons = document.querySelectorAll('.close, .close-modal');

            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            });

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        function openAssignModal(bookingId) {
            const modal = document.getElementById('assignModal');
            const form = document.getElementById('assignForm');
            form.action = `/agency/bookings/${bookingId}/assign`;
            modal.style.display = 'block';
        }
    </script>
@endsection
