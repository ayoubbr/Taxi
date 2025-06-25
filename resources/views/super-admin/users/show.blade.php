@extends('super-admin.layout')

@section('title', 'Détails Utilisateur')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-users.css') }}">
@endsection

@section('content')
    <div class="user-details-container">
        <!-- User Header -->
        <div class="page-header-card">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="header-info">
                        <h1>{{ $user->firstname }} {{ $user->lastname }}</h1>
                        <p>{{ '@' . $user->username }} • {{ $user->email }}</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('super-admin.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['total_bookings']) }}</h3>
                    <p>Total Réservations</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon completed">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['completed_bookings']) }}</h3>
                    <p>Réservations Terminées</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon cancelled">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['cancelled_bookings']) }}</h3>
                    <p>Réservations Annulées</p>
                </div>
            </div>

            @if ($user->role->name === 'CLIENT' && $stats['total_spent'] > 0)
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['total_spent'], 2) }} €</h3>
                        <p>Total Dépensé</p>
                    </div>
                </div>
            @endif

            @if ($user->role->name === 'DRIVER' && $stats['total_earned'] > 0)
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['total_earned'], 2) }} €</h3>
                        <p>Total Gagné</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- User Details -->
        <div class="details-grid">
            <!-- Personal Information -->
            <div class="detail-card">
                <div class="card-header">
                    <h3><i class="fas fa-user"></i> Informations Personnelles</h3>
                </div>
                <div class="card-content">
                    <div class="info-row">
                        <label>Nom d'utilisateur</label>
                        <span>{{ $user->username }}</span>
                    </div>
                    <div class="info-row">
                        <label>Prénom</label>
                        <span>{{ $user->firstname ?: 'Non renseigné' }}</span>
                    </div>
                    <div class="info-row">
                        <label>Nom</label>
                        <span>{{ $user->lastname ?: 'Non renseigné' }}</span>
                    </div>
                    <div class="info-row">
                        <label>Email</label>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="info-row">
                        <label>Rôle</label>
                        <span class="role-badge role-{{ $user->role->name }}">
                            {{ ucfirst($user->role->name) }}
                        </span>
                    </div>
                    <div class="info-row">
                        <label>Statut</label>
                        <span class="status-badge status-{{ $user->status }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="detail-card">
                <div class="card-header">
                    <h3><i class="fas fa-cog"></i> Informations Compte</h3>
                </div>
                <div class="card-content">
                    <div class="info-row">
                        <label>Date de création</label>
                        <span>{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <label>Dernière modification</label>
                        <span>{{ $user->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <label>ID Utilisateur</label>
                        <span class="user-id">#{{ $user->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Agency Information -->
            @if ($user->agency)
                <div class="detail-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-building"></i> Agence Associée</h3>
                    </div>
                    <div class="card-content">
                        <div class="agency-info">
                            <div class="agency-main">
                                <h4>{{ $user->agency->name }}</h4>
                                <p>{{ $user->agency->description ?: 'Aucune description' }}</p>
                            </div>
                            <div class="agency-details">
                                <div class="info-row">
                                    <label>Adresse</label>
                                    <span>{{ $user->agency->address ?: 'Non renseignée' }}</span>
                                </div>
                                <div class="info-row">
                                    <label>Téléphone</label>
                                    <span>{{ $user->agency->phone ?: 'Non renseigné' }}</span>
                                </div>
                                <div class="info-row">
                                    <label>Email</label>
                                    <span>{{ $user->agency->email ?: 'Non renseigné' }}</span>
                                </div>
                                <div class="info-row">
                                    <label>Statut</label>
                                    <span class="status-badge status-{{ $user->agency->status }}">
                                        {{ ucfirst($user->agency->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="agency-actions">
                                <a href="{{ route('super-admin.agencies.show', $user->agency) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Voir l'agence
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Driver Specific Information -->
            @if ($user->role->name === 'DRIVER' && $user->taxi)
                <div class="detail-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-taxi"></i> Informations Taxi</h3>
                    </div>
                    <div class="card-content">
                        <div class="taxi-info">
                            <div class="info-row">
                                <label>Marque</label>
                                <span>{{ $user->taxi->brand ?: 'Non renseignée' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Modèle</label>
                                <span>{{ $user->taxi->model ?: 'Non renseigné' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Plaque d'immatriculation</label>
                                <span class="license-plate">{{ $user->taxi->license_plate ?: 'Non renseignée' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Type</label>
                                <span>{{ ucfirst($user->taxi->type) ?: 'Non renseigné' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Capacity</label>
                                <span>{{ $user->taxi->capacity ?: 'Non renseigné' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Ville</label>
                                <span>{{ $user->taxi->city->name ?: 'Non renseigné' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Couleur</label>
                                <span>{{ $user->taxi->color ?: 'Non renseignée' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Bookings -->
            @if ($stats['total_bookings'] > 0)
                <div class="detail-card full-width">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Réservations Récentes</h3>
                    </div>
                    <div class="card-content">
                        @if ($user->role->name === 'CLIENT')
                            @forelse($user->clientBookings()->latest()->take(5)->get() as $booking)
                                <div class="booking-item">
                                    <div class="booking-info">
                                        <div class="booking-route">
                                            <span class="pickup"><i class="fas fa-circle text-success"></i>
                                                {{ $booking->pickup_location }}</span>
                                            <span class="destination"><i class="fas fa-circle text-danger"></i>
                                                {{ $booking->destinationCity->name }}</span>
                                        </div>
                                        <div class="booking-meta">
                                            <span
                                                class="booking-date">{{ $booking->pickup_datetime->format('d/m/Y H:i') }}</span>
                                            <span class="status-badge status-{{ $booking->status }}">
                                                {{ $booking->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <a href="{{ route('super-admin.bookings.show', $booking) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                            Voir réservation
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>Aucune réservation trouvée</p>
                                </div>
                            @endforelse
                        @else
                            @forelse($user->assignedDriverBookings()->latest()->take(5)->get() as $booking)
                                <div class="booking-item">
                                    <div class="booking-info">
                                        <div class="booking-route">
                                            <span class="pickup"><i class="fas fa-circle text-success"></i>
                                                {{ $booking->pickup_location }}</span>
                                            <span class="destination"><i class="fas fa-circle text-danger"></i>
                                                {{ $booking->destinationCity->name }}</span>
                                        </div>
                                        <div class="booking-meta">
                                            <span
                                                class="booking-date">{{ $booking->pickup_datetime->format('d/m/Y H:i') }}</span>
                                            <span class="status-badge status-{{ $booking->status }}">
                                                {{ $booking->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="booking-actions">
                                        <a href="{{ route('super-admin.bookings.show', $booking) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                            Voir réservation
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>Aucune réservation trouvée</p>
                                </div>
                            @endforelse
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions-card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Actions Rapides</h3>
            </div>
            <div class="card-content">
                <div class="quick-actions">
                    <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier l'utilisateur
                    </a>

                    @if ($user->status === 'active')
                        <form action="{{ route('super-admin.users.toggleStatus', $user) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning"
                                onclick="return confirm('Désactiver cet utilisateur ?')">
                                <i class="fas fa-pause"></i> Désactiver
                            </button>
                        </form>
                    @else
                        <form action="{{ route('super-admin.users.toggleStatus', $user) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-play"></i> Activer
                            </button>
                        </form>
                    @endif

                    @if ($user->status !== 'suspended')
                        <form action="{{ route('super-admin.users.ban', $user) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Suspendre cet utilisateur ?')">
                                <i class="fas fa-ban"></i> Suspendre
                            </button>
                        </form>
                    @else
                        <form action="{{ route('super-admin.users.unban', $user) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-check"></i> Lever la suspension
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Supprimer définitivement cet utilisateur ? Cette action est irréversible.')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
