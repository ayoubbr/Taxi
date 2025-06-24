@extends('super-admin.layout')

@section('title', 'Détails de l\'agence')

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('super-admin.agencies.index') }}">Agences</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $agency->name }}</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/super-admin-agency-show.css') }}">
@endsection

@section('content')
    <div class="agency-show-container">
        <!-- Header de l'agence -->
        <div class="agency-header">
            <div class="agency-info">
                <div class="agency-logo">
                    @if ($agency->logo)
                        <img src="{{ Storage::url($agency->logo) }}" alt="Logo {{ $agency->name }}">
                    @else
                        <div class="logo-placeholder">
                            {{ strtoupper(substr($agency->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="agency-details">
                    <h1>{{ $agency->name }}</h1>
                    <div class="agency-status">
                        <span class="status-badge status-{{ $agency->status }}">
                            @if ($agency->status === 'active')
                                <i class="fas fa-check-circle"></i> Active
                            @elseif($agency->status === 'inactive')
                                <i class="fas fa-pause-circle"></i> Inactive
                            @else
                                <i class="fas fa-ban"></i> Suspendu
                            @endif
                        </span>
                    </div>
                    @if ($agency->address)
                        <div class="agency-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $agency->address }}</span>
                        </div>
                    @endif
                    <div class="agency-created">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Créée le {{ $agency->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="agency-actions">
                <a href="{{ route('super-admin.agencies.edit', $agency) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    <span>Modifier</span>
                </a>
                {{-- {{ route('super-admin.agencies.toggle-status', $agency) }} --}}
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
                    <form action="{{ route('super-admin.agencies.suspend', $agency) }}" method="POST" class="inline-form">
                        @csrf
                        @method('PATCH')
                        <button style="min-width: -webkit-fill-available;" type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-ban"></i>
                            Suspend
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['total_users']) }}</div>
                    <div class="stat-label">Utilisateurs Total</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon drivers">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['total_drivers']) }}</div>
                    <div class="stat-label">Chauffeurs</div>
                    <div class="stat-sub">{{ $stats['active_drivers'] }} actifs</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon taxis">
                    <i class="fas fa-taxi"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['total_taxis']) }}</div>
                    <div class="stat-label">Taxis</div>
                    <div class="stat-sub">{{ $stats['available_taxis'] }} disponibles</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bookings">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['total_bookings']) }}</div>
                    <div class="stat-label">Réservations</div>
                    <div class="stat-sub">{{ $stats['completed_bookings'] }} terminées</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['assigned_bookings']) }}</div>
                    <div class="stat-label">En Cours</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon cancelled">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['cancelled_bookings']) }}</div>
                    <div class="stat-label">Annulées</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['revenue_total'], 2) }} DH</div>
                    <div class="stat-label">Revenus Total</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon revenue-month">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['revenue_month'], 2) }} DH</div>
                    <div class="stat-label">Ce Mois</div>
                    <div class="stat-sub">{{ number_format($stats['revenue_today'], 2) }} DH aujourd'hui</div>
                </div>
            </div>
        </div>

        <!-- Sections détaillées -->
        <div class="details-grid">
            <!-- Utilisateurs récents -->
            <div class="detail-section">
                <div class="section-header">
                    <h3><i class="fas fa-users"></i> Utilisateurs Récents</h3>
                    <a href="{{ route('super-admin.agencies.users', ['agency' => $agency]) }}" class="view-all">
                        Voir tout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="section-content">
                    @if ($agency->users->count() > 0)
                        <div class="users-list">
                            @foreach ($agency->users as $user)
                                <div class="user-item">
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name">{{ $user->firstname }} {{ $user->lastname }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                    <div class="user-badges">
                                        <span class="role-badge role-{{ $user->role->name ?? 'user' }}">
                                            {{ $user->role->name ?? 'USER' }}
                                        </span>
                                        <span class="status-badge status-{{ $user->status }}">
                                            {{ $user->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Aucun utilisateur trouvé</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Taxis -->
            <div class="detail-section">
                <div class="section-header">
                    <h3><i class="fas fa-taxi"></i> Taxis de l'Agence</h3>
                    <a href="{{ route('super-admin.agencies.taxis', ['agency' => $agency]) }}" class="view-all">
                        Voir tout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="section-content">
                    @if ($agency->taxis->count() > 0)
                        <div class="taxis-list">
                            @foreach ($agency->taxis as $taxi)
                                <div class="taxi-item">
                                    <div class="taxi-icon">
                                        <i class="fas fa-taxi"></i>
                                    </div>
                                    <div class="taxi-info">
                                        <div class="taxi-plate">{{ $taxi->license_plate }}</div>
                                        <div class="taxi-details">
                                            {{ $taxi->model }} - {{ ucfirst($taxi->type) }}
                                            @if ($taxi->driver)
                                                <br><small>Chauffeur: {{ $taxi->driver->firstname }}
                                                    {{ $taxi->driver->lastname }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="taxi-status">
                                        <span class="availability-badge {{ $taxi->is_available ? 'available' : 'busy' }}">
                                            {{ $taxi->is_available ? 'Disponible' : 'Occupé' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-taxi"></i>
                            <p>Aucun taxi trouvé</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Réservations récentes -->
            <div class="detail-section full-width">
                <div class="section-header">
                    <h3><i class="fas fa-calendar-check"></i> Réservations Récentes</h3>
                    <a href="{{ route('super-admin.agencies.bookings', ['agency' => $agency]) }}" class="view-all">
                        Voir tout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="section-content">
                    @if ($agency->bookings->count() > 0)
                        <div class="bookings-table">
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Trajet</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Montant</th>
                                            <th>Chauffeur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($agency->bookings as $booking)
                                            <tr>
                                                <td>
                                                    <div class="client-info">
                                                        <div class="client-name">{{ $booking->client_name }}</div>
                                                        @if ($booking->client)
                                                            <div class="client-email">{{ $booking->client->email }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="trip-info">
                                                        <div class="pickup">
                                                            {{ Str::limit($booking->pickup_location, 30) }}
                                                            ({{ Str::limit($booking->pickupCity->name, 30) }})
                                                        </div>
                                                        <div class="destination">→
                                                            {{ Str::limit($booking->destinationCity->name, 30) }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="booking-date">
                                                        {{ $booking->pickup_datetime->format('d/m/Y') }}
                                                        <br><small>{{ $booking->pickup_datetime->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-{{ $booking->status }}">
                                                        {{ $booking->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($booking->estimated_fare)
                                                        <div class="fare">
                                                            {{ number_format($booking->estimated_fare, 2) }} DH</div>
                                                    @else
                                                        <span class="no-fare">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($booking->driver)
                                                        <div class="driver-info">
                                                            {{ $booking->driver->firstname }}
                                                            {{ $booking->driver->lastname }}
                                                        </div>
                                                    @else
                                                        <span class="no-driver">Non assigné</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-check"></i>
                            <p>Aucune réservation trouvée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmer la suppression</h3>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette agence ?</p>
                <p><strong>Cette action est irréversible.</strong></p>
                <div class="warning-info">
                    <i class="fas fa-info-circle"></i>
                    <span>L'agence ne peut être supprimée que si elle n'a pas de chauffeurs actifs ou de réservations en
                        cours.</span>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">Annuler</button>
                <form action="{{ route('super-admin.agencies.destroy', $agency) }}" method="POST"
                    style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/super-admin-agency-show.js') }}"></script>
@endsection
