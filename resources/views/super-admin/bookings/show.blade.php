@extends('super-admin.layout')

@section('title', 'Détails de la Réservation')

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.bookings.index') }}">Réservations</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ substr($booking->booking_uuid, 0, 8) }}...</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-bookings.css') }}">
@endsection

@section('content')
    <div class="booking-details-container">
        <!-- Booking Header -->
        <div class="booking-header">
            <div class="booking-info">
                <div class="booking-main">
                    <h1>Réservation #{{ substr($booking->booking_uuid, 0, 8) }}</h1>
                    <span class="status-badge status-{{ $booking->status }}">
                        {{ $booking->status }}
                    </span>
                </div>
                <div class="booking-meta">
                    <p><i class="fas fa-calendar"></i> Créée le {{ $booking->created_at->format('d/m/Y à H:i') }}</p>
                    <p><i class="fas fa-clock"></i> Départ prévu le {{ $booking->pickup_datetime->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('super-admin.bookings.edit', $booking) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                <a href="{{ route('super-admin.bookings.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </div>
        </div>

        <div class="details-grid">
            <!-- Client Information -->
            <div class="detail-card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Informations Client</h3>
                </div>
                <div class="card-content">
                    <div class="info-row">
                        <label>Nom:</label>
                        <span>{{ $booking->client_name }}</span>
                    </div>
                    @if ($booking->client)
                        <div class="info-row">
                            <label>Email:</label>
                            <span>{{ $booking->client->email }}</span>
                        </div>
                        <div class="info-row">
                            <label>Nom complet ( compte d'utilisateur) : </label>
                            <span>{{ $booking->client->firstname }} {{ $booking->client->lastname }}</span>
                        </div>
                        <div class="info-row">
                            <label>Statut:</label>
                            <span class="status-badge status-{{ $booking->client->status }}">
                                {{ ucfirst($booking->client->status) }}
                            </span>
                        </div>
                    @else
                        <div class="info-row">
                            <span class="text-muted">Client supprimé du système</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Trip Information -->
            <div class="detail-card">
                <div class="card-header">
                    <h3><i class="fas fa-route"></i> Détails du Trajet</h3>
                </div>
                <div class="card-content">
                    <div class="trip-route">
                        <div class="route-point pickup">
                            <div class="route-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="route-info">
                                <strong>Départ</strong>
                                <p>{{ $booking->pickup_location }}</p>
                                <small>{{ $booking->pickupCity->name ?? 'Ville non définie' }}</small>
                            </div>
                        </div>
                        <div class="route-line"></div>
                        <div class="route-point destination">
                            <div class="route-icon">
                                <i class="fas fa-flag-checkered"></i>
                            </div>
                            <div class="route-info">
                                <strong>Destination</strong>
                                <p>{{ $booking->destinationCity->name ?? 'Ville non définie' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="trip-details">
                        <div class="info-row">
                            <label>Date/Heure:</label>
                            <span>{{ $booking->pickup_datetime->format('d/m/Y à H:i') }}</span>
                        </div>
                        <div class="info-row">
                            <label>Type de taxi:</label>
                            @if ($booking->taxi_type)
                                <span class="type-badge type-{{ $booking->taxi_type }}">
                                    {{ ucfirst($booking->taxi_type) }}
                                </span>
                            @else
                                <span class="text-muted">Non spécifié</span>
                            @endif
                        </div>
                        <div class="info-row">
                            <label>Tarif estimé:</label>
                            @if ($booking->estimated_fare)
                                <span class="fare">{{ number_format($booking->estimated_fare, 2) }} €</span>
                            @else
                                <span class="text-muted">Non défini</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver & Taxi Information -->
            <div class="detail-card" style="grid-column: span 2;">
                <div class="card-header">
                    <h3><i class="fas fa-taxi"></i> Chauffeur & Taxi</h3>
                </div>
                <div class="card-content">
                    @if ($booking->driver && $booking->taxi)
                        <div class="driver-taxi-info">
                            <div class="driver-section">
                                <h4>Chauffeur</h4>
                                <div class="info-row">
                                    <label>Nom:</label>
                                    <span>{{ $booking->driver->firstname }} {{ $booking->driver->lastname }}</span>
                                </div>
                                <div class="info-row">
                                    <label>Email:</label>
                                    <span>{{ $booking->driver->email }}</span>
                                </div>
                                <div class="info-row">
                                    <label>Statut:</label>
                                    <span class="status-badge status-{{ $booking->driver->status }}">
                                        {{ ucfirst($booking->driver->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="taxi-section">
                                <h4>Taxi</h4>
                                <div class="info-row">
                                    <label>Plaque:</label>
                                    <span class="license-plate">{{ $booking->taxi->license_plate }}</span>
                                </div>
                                <div class="info-row">
                                    <label>Modèle:</label>
                                    <span>{{ $booking->taxi->model ?? 'Non spécifié' }}</span>
                                </div>
                                <div class="info-row">
                                    <label>Type:</label>
                                    <span class="type-badge type-{{ $booking->taxi->type }}">
                                        {{ ucfirst($booking->taxi->type) }}
                                    </span>
                                </div>
                                <div class="info-row">
                                    <label>Capacité:</label>
                                    <span>{{ $booking->taxi->capacity }} places</span>
                                </div>
                                @if ($booking->taxi->agency)
                                    <div class="info-row">
                                        <label>Agence:</label>
                                        <span>{{ $booking->taxi->agency->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="no-assignment">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Aucun chauffeur assigné à cette réservation</p>
                            <a href="{{ route('super-admin.bookings.edit', $booking) }}" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i>
                                Assigner un chauffeur
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Applications -->
            @if ($booking->applications->count() > 0)
                <div class="detail-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-users"></i> Candidatures ({{ $booking->applications->count() }})</h3>
                    </div>
                    <div class="card-content">
                        <div class="applications-list">
                            @foreach ($booking->applications as $application)
                                <div class="application-item">
                                    <div class="driver-info">
                                        <div class="driver-avatar">
                                            {{ strtoupper(substr($application->driver->firstname, 0, 1)) }}
                                        </div>
                                        <div class="driver-details">
                                            <strong>{{ $application->driver->firstname }}
                                                {{ $application->driver->lastname }}</strong>
                                            <small>{{ $application->driver->email }}</small>
                                        </div>
                                    </div>
                                    <div class="taxi-info">
                                        <span class="license-plate">{{ $application->taxi->license_plate }}</span>
                                        <small>{{ $application->taxi->model }}</small>
                                    </div>
                                    <div class="application-date">
                                        <small>{{ $application->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    {{-- {{-- <div class="application-actions"> --}}
                                    @if ($booking->assigned_driver_id == $application->driver->id)
                                        {{-- <form action="{{ route('super-admin.bookings.assign-driver', $booking) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="driver_id"
                                                    value="{{ $application->driver->id }}">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                    Accepter
                                                </button>
                                            </form> --}}
                                        <span class="text-muted">Déjà assigné</span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </div>
                        </div>
            @endforeach
        </div>
    </div>
    </div>
    @endif

@endsection
