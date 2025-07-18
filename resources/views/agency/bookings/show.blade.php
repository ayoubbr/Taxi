@extends('agency.layout')

@section('title', 'Réservation ' . substr($booking->booking_uuid, 0, 8))

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.bookings.index') }}">Réservations</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ substr($booking->booking_uuid, 0, 8) }}...</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-driver-show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/agency-admin-bookings.css') }}">
@endsection

@section('content')
    <div class="driver-show-container">
        <!-- Booking Header -->
        <div class="driver-profile-header">
            <div class="driver-profile-content">
                <div class="driver-avatar-large"
                    style="background: linear-gradient(135deg, var(--agency-primary-dark), var(--agency-primary));">
                    <i class="fas fa-calendar-check" style="font-size: 2rem;"></i>
                </div>
                <div class="driver-profile-info">
                    <h1>Réservation {{ $booking->booking_uuid }}</h1>
                    <div class="driver-profile-meta">
                        <div class="driver-meta-item">
                            <i class="fas fa-user"></i>
                            <span>
                                @if ($booking->client)
                                    {{ $booking->client->firstname }} {{ $booking->client->lastname }}
                                @else
                                    {{ $booking->client_name }}
                                @endif
                            </span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $booking->pickup_datetime->format('d/m/Y à H:i') }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-route"></i>
                            <span>{{ $booking->pickup_location }} ({{ $booking->pickupCity->name }}) →
                                {{ $booking->destinationCity->name ?? 'Non spécifiée' }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-euro-sign"></i>
                            <span>{{ number_format($booking->estimated_fare, 0, ',', ' ') }}€</span>
                        </div>
                    </div>
                    <div class="driver-status-large status-{{ $booking->status }}">
                        {{ $booking->status }}
                    </div>
                </div>
            </div>

            <div class="driver-actions-header">
                @if (!in_array($booking->status, ['COMPLETED', 'CANCELLED', 'PENDING']))
                    <form action="{{ route('agency.bookings.update-status', $booking) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status"
                            value="{{ $booking->status === 'PENDING' ? 'ASSIGNED' : ($booking->status === 'ASSIGNED' ? 'IN_PROGRESS' : 'COMPLETED') }}">
                        <button type="submit" class="driver-action-btn success">
                            <i class="fas fa-arrow-right"></i>
                            @if($booking->status === 'ASSIGNED')
                                Démarrer
                            @else
                                Terminer
                            @endif
                        </button>
                    </form>
                @endif

                @if ($booking->status !== 'CANCELLED')
                    <form action="{{ route('agency.bookings.update-status', $booking) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="CANCELLED">
                        <button type="submit" class="driver-action-btn warning">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </form>
                @endif

                <form action="{{ route('agency.bookings.destroy', $booking) }}" method="POST" style="display: inline;"
                    class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="driver-action-btn"
                        style="background: var(--agency-danger); color: white;">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>

                <a href="{{ route('agency.bookings.index') }}" class="driver-action-btn secondary">
                    <i class="fas fa-list"></i> Retour à la Liste
                </a>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="driver-content-grid">
            <!-- Main Content -->
            <div class="driver-main-content">
                <!-- Booking Details -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-info-circle"></i> Détails de la Réservation</h3>
                    </div>
                    <div class="driver-info-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>UUID Complet</label>
                                <span
                                    style="font-family: monospace; font-size: 0.85rem;">{{ $booking->booking_uuid }}</span>
                            </div>
                            <div class="info-item">
                                <label>Type de Taxi</label>
                                <span class="type-badge type-{{ $booking->taxi_type ?? 'standard' }}">
                                    {{ ucfirst($booking->taxi_type ?? 'Standard') }}
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Niveau de Recherche</label>
                                <span>Tier {{ $booking->search_tier ?? 1 }}</span>
                            </div>
                            <div class="info-item">
                                <label>Créée le</label>
                                <span>{{ $booking->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <h4 style="margin-bottom: 1rem; color: var(--agency-dark);">Trajet</h4>
                            <div class="route-info"
                                style="background: var(--agency-gray-light); padding: 1rem; border-radius: 8px;">
                                <div class="pickup" style="margin-bottom: 0.75rem;">
                                    <i class="fas fa-map-marker-alt" style="color: var(--agency-accent);"></i>
                                    <div>
                                        <strong>Départ:</strong> {{ $booking->pickup_location }}<br>
                                        <small>{{ $booking->pickupCity->name ?? 'Ville non spécifiée' }}</small>
                                    </div>
                                </div>
                                <div class="destination">
                                    <i class="fas fa-flag" style="color: var(--agency-danger);"></i>
                                    <div>
                                        <strong>Destination:</strong>
                                        {{ $booking->destinationCity->name ?? 'Non spécifiée' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assignment Form -->
                @if (!in_array($booking->status, ['COMPLETED', 'CANCELLED']))
                    <div class="driver-info-card">
                        <div class="driver-info-header">
                            <h3><i class="fas fa-user-plus"></i> Attribution</h3>
                        </div>
                        <div class="driver-info-body">
                            <form action="{{ route('agency.bookings.assign', $booking) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="form-row"
                                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                    <div class="form-group">
                                        <label for="assigned_driver_id" class="form-label">Chauffeur</label>
                                        <select id="assigned_driver_id" name="assigned_driver_id"
                                            class="form-control form-select">
                                            <option value="">Sélectionner un chauffeur</option>
                                            @foreach (Auth::user()->agency->drivers as $driver)
                                                <option value="{{ $driver->id }}"
                                                    {{ $booking->assigned_driver_id == $driver->id ? 'selected' : '' }}>
                                                    {{ $driver->firstname }} {{ $driver->lastname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="assigned_taxi_id" class="form-label">Taxi</label>
                                        <select id="assigned_taxi_id" name="assigned_taxi_id"
                                            class="form-control form-select">
                                            <option value="">Sélectionner un taxi</option>
                                            @foreach (Auth::user()->agency->taxis as $taxi)
                                                <option value="{{ $taxi->id }}"
                                                    {{ $booking->assigned_taxi_id == $taxi->id ? 'selected' : '' }}>
                                                    {{ $taxi->license_plate }} - {{ $taxi->model }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Mettre à Jour l'Attribution
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Content -->
            <div class="driver-sidebar-content">
                <!-- Client Information -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-user"></i> Informations Client</h3>
                    </div>
                    <div class="driver-info-body">
                        @if ($booking->client)
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Nom Complet</label>
                                    <span>{{ $booking->client->firstname }} {{ $booking->client->lastname }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Email</label>
                                    <span>{{ $booking->client->email }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Username</label>
                                    <span>{{ $booking->client->username }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Statut</label>
                                    <span class="status-badge status-{{ $booking->client->status }}">
                                        {{ ucfirst($booking->client->status) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Nom</label>
                                    <span>{{ $booking->client_name }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Type</label>
                                    <span>Client externe</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assigned Driver -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-user-tie"></i> Chauffeur Assigné</h3>
                    </div>
                    <div class="driver-info-body">
                        @if ($booking->driver)
                            <div class="taxi-assignment-card assigned">
                                <div class="taxi-card-header">
                                    <div class="taxi-info">
                                        <h4>{{ $booking->driver->firstname }} {{ $booking->driver->lastname }}</h4>
                                        <span class="status-badge status-active">Assigné</span>
                                    </div>
                                </div>
                                <div class="taxi-details">
                                    <div class="taxi-detail-item">
                                        <label>Email</label>
                                        <span>{{ $booking->driver->email }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Username</label>
                                        <span>{{ $booking->driver->username }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Statut</label>
                                        <span class="status-badge status-{{ $booking->driver->status }}">
                                            {{ ucfirst($booking->driver->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="taxi-assignment-card not-assigned">
                                <div class="taxi-card-header">
                                    <div class="taxi-info">
                                        <h4>Aucun chauffeur assigné</h4>
                                        <span class="status-badge status-warning">Non assigné</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Assigned Taxi -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-car"></i> Taxi Assigné</h3>
                    </div>
                    <div class="driver-info-body">
                        @if ($booking->taxi)
                            <div class="taxi-assignment-card assigned">
                                <div class="taxi-card-header">
                                    <div class="taxi-info">
                                        <h4>{{ $booking->taxi->license_plate }}</h4>
                                        <span class="status-badge status-active">Assigné</span>
                                    </div>
                                </div>
                                <div class="taxi-details">
                                    <div class="taxi-detail-item">
                                        <label>Modèle</label>
                                        <span>{{ $booking->taxi->model }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Type</label>
                                        <span
                                            class="type-badge type-{{ $booking->taxi->type }}">{{ ucfirst($booking->taxi->type) }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Capacité</label>
                                        <span>{{ $booking->taxi->capacity }} places</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Statut</label>
                                        <span
                                            class="status-badge status-{{ $booking->taxi->is_available ? 'active' : 'warning' }}">
                                            {{ $booking->taxi->is_available ? 'Disponible' : 'Occupé' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="taxi-assignment-card not-assigned">
                                <div class="taxi-card-header">
                                    <div class="taxi-info">
                                        <h4>Aucun taxi assigné</h4>
                                        <span class="status-badge status-warning">Non assigné</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Applications -->
                @if ($booking->applications->count() > 0)
                    <div class="driver-info-card">
                        <div class="driver-info-header">
                            <h3><i class="fas fa-hand-paper"></i> Candidatures</h3>
                        </div>
                        <div class="driver-info-body">
                            @foreach ($booking->applications as $application)
                                <div class="taxi-assignment-card" style="margin-bottom: 1rem;">
                                    <div class="taxi-card-header">
                                        <div class="taxi-info">
                                            <h5>{{ $application->driver->firstname }} {{ $application->driver->lastname }}
                                            </h5>
                                            <small>{{ $application->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm booking deletion
            const deleteForm = document.querySelector('.delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const bookingUuid = '{{ substr($booking->booking_uuid, 0, 12) }}';

                    if (confirm(
                            `Êtes-vous sûr de vouloir supprimer définitivement la réservation ${bookingUuid}... ?\n\nCette action est irréversible.`
                        )) {
                        this.submit();
                    }
                });
            }

            // Confirm status changes
            const statusForms = document.querySelectorAll('form[action*="update-status"]');
            statusForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const statusInput = this.querySelector('input[name="status"]');
                    const newStatus = statusInput.value;
                    let message = '';

                    switch (newStatus) {
                        case 'CONFIRMED':
                            message = 'Confirmer cette réservation ?';
                            break;
                        case 'IN_PROGRESS':
                            message = 'Démarrer cette course ?';
                            break;
                        case 'COMPLETED':
                            message = 'Marquer cette course comme terminée ?';
                            break;
                        case 'CANCELLED':
                            message = 'Annuler cette réservation ?';
                            break;
                    }

                    if (message && !confirm(message)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection
