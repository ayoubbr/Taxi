@extends('agency.layout')

@section('title', $taxi->license_plate)

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.taxis.index') }}">Taxis</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $taxi->license_plate }}</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-driver-show.css') }}">
@endsection

@section('content')
    <div class="driver-show-container">
        <!-- Taxi Profile Header -->
        <div class="driver-profile-header">
            <div class="driver-profile-content">
                <div class="driver-avatar-large"
                    style="background: linear-gradient(135deg, var(--agency-info), var(--agency-primary));">
                    <i class="fas fa-car" style="font-size: 2rem;"></i>
                </div>
                <div class="driver-profile-info">
                    <h1>{{ $taxi->license_plate }}</h1>
                    <div class="driver-profile-meta">
                        <div class="driver-meta-item">
                            <i class="fas fa-car"></i>
                            <span>{{ $taxi->model }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-tag"></i>
                            <span>{{ ucfirst($taxi->type) }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $taxi->city->name }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $taxi->capacity }} places</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>Ajouté le {{ $taxi->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="driver-status-large status-{{ $taxi->is_available ? 'active' : 'warning' }}">
                        {{ $taxi->is_available ? 'Disponible' : 'Occupé' }}
                    </div>
                </div>
            </div>

            <div class="driver-actions-header">
                <a href="{{ route('agency.taxis.edit', $taxi) }}" class="driver-action-btn primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <form action="{{ route('agency.taxis.toggle-availability', $taxi) }}" method="POST"
                    style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="driver-action-btn {{ $taxi->is_available ? 'warning' : 'success' }}">
                        <i class="fas fa-{{ $taxi->is_available ? 'pause' : 'play' }}"></i>
                        {{ $taxi->is_available ? 'Marquer Occupé' : 'Marquer Disponible' }}
                    </button>
                </form>
                <a href="{{ route('agency.taxis.index') }}" class="driver-action-btn secondary">
                    <i class="fas fa-list"></i> Retour à la Liste
                </a>
                <form action="{{ route('agency.taxis.destroy', $taxi) }}" method="POST" style="display: inline;"
                    class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="driver-action-btn"
                        style="background: var(--agency-danger); color: white;">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="driver-stats-grid">
            <div class="driver-stat-card bookings">
                <div class="driver-stat-header">
                    <div class="driver-stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
                <div class="driver-stat-value">{{ $stats['total_bookings'] }}</div>
                <div class="driver-stat-label">Total des Courses</div>
                <div class="driver-stat-trend {{ $stats['this_month_bookings'] > 0 ? 'up' : 'neutral' }}">
                    <i class="fas fa-arrow-{{ $stats['this_month_bookings'] > 0 ? 'up' : 'right' }}"></i>
                    <span>{{ $stats['this_month_bookings'] }} ce mois</span>
                </div>
            </div>

            <div class="driver-stat-card completed">
                <div class="driver-stat-header">
                    <div class="driver-stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="driver-stat-value">{{ $stats['completed_bookings'] }}</div>
                <div class="driver-stat-label">Courses Terminées</div>
                <div
                    class="driver-stat-trend {{ $stats['completion_rate'] >= 80 ? 'up' : ($stats['completion_rate'] >= 60 ? 'neutral' : 'down') }}">
                    <i class="fas fa-percentage"></i>
                    <span>{{ number_format($stats['completion_rate'], 1) }}% de réussite</span>
                </div>
            </div>

            <div class="driver-stat-card revenue">
                <div class="driver-stat-header">
                    <div class="driver-stat-icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                </div>
                <div class="driver-stat-value">{{ number_format($stats['total_revenue'], 0, ',', ' ') }}€</div>
                <div class="driver-stat-label">Revenus Générés</div>
                <div class="driver-stat-trend {{ $stats['this_month_revenue'] > 0 ? 'up' : 'neutral' }}">
                    <i class="fas fa-arrow-{{ $stats['this_month_revenue'] > 0 ? 'up' : 'right' }}"></i>
                    <span>{{ number_format($stats['this_month_revenue'], 0, ',', ' ') }}€ ce mois</span>
                </div>
            </div>

            <div class="driver-stat-card rating">
                <div class="driver-stat-header">
                    <div class="driver-stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="driver-stat-value">{{ number_format($stats['average_duration'], 0) }}</div>
                <div class="driver-stat-label">Durée Moyenne (min)</div>
                <div class="driver-stat-trend neutral">
                    <i class="fas fa-route"></i>
                    <span>{{ number_format($stats['total_distance'], 0) }} km total</span>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="driver-content-grid">
            <!-- Main Content -->
            <div class="driver-main-content">
                <!-- Recent Bookings -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-history"></i> Courses Récentes</h3>
                        <a href="{{ route('agency.bookings.index', ['taxi_id' => $taxi->id]) }}"
                            class="driver-action-btn primary">
                            <i class="fas fa-eye" style="color: white;"></i> Voir Toutes
                        </a>
                    </div>
                    <div class="driver-info-body">
                        @if ($recentBookings->count() > 0)
                            <div class="recent-bookings-list">
                                @foreach ($recentBookings as $booking)
                                    <div class="booking-item">
                                        <div class="booking-status-icon status-{{ $booking->status }}">
                                            <i
                                                class="fas fa-{{ $booking->status === 'COMPLETED' ? 'check' : ($booking->status === 'IN_PROGRESS' ? 'car' : ($booking->status === 'CANCELLED' ? 'times' : 'clock')) }}"></i>
                                        </div>
                                        <div class="booking-details">
                                            <h5>{{ $booking->pickup_location }} ({{ $booking->pickupCity->name }}) →
                                                {{ $booking->destinationCity->name }}</h5>
                                            <p>
                                                {{ $booking->created_at->format('d/m/Y H:i') }} •
                                                {{ ucfirst(strtolower(str_replace('_', ' ', $booking->status))) }}
                                                @if ($booking->client)
                                                    • {{ $booking->client->firstname }}
                                                    {{ $booking->client->lastname }}
                                                @endif
                                                @if ($booking->driver)
                                                    • Chauffeur: {{ $booking->driver->firstname }}
                                                    {{ $booking->driver->lastname }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="booking-fare">
                                            {{ number_format($booking->estimated_fare, 0, ',', ' ') }}€
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state-small">
                                <i class="fas fa-calendar-times"></i>
                                <p>Aucune course récente</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Performance Chart -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-chart-line"></i> Performance Mensuelle</h3>
                    </div>
                    <div class="driver-info-body">
                        <div class="performance-chart">
                            <p>Graphique de performance du taxi (à implémenter)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Content -->
            <div class="driver-sidebar-content">
                <!-- Driver Assignment -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-user"></i> Chauffeur Assigné</h3>
                    </div>
                    <div class="driver-info-body">
                        @if ($taxi->driver)
                            <div class="taxi-assignment-card assigned">
                                <div class="taxi-card-header">
                                    <div class="taxi-info">
                                        <h4>{{ $taxi->driver->firstname }} {{ $taxi->driver->lastname }}</h4>
                                        <span class="status-badge status-active">Assigné</span>
                                    </div>
                                </div>
                                <div class="taxi-details">
                                    <div class="taxi-detail-item">
                                        <label>Email</label>
                                        <span>{{ $taxi->driver->email }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Username</label>
                                        <span>{{ $taxi->driver->username }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Statut</label>
                                        <span class="status-badge status-{{ $taxi->driver->status }}">
                                            {{ ucfirst($taxi->driver->status) }}
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
                                <p style="margin: 1rem 0 0 0; color: var(--agency-gray); font-size: 0.9rem;">
                                    Ce taxi n'a pas de chauffeur assigné. Vous pouvez lui en assigner un via la page de
                                    modification.
                                </p>
                            </div>
                        @endif

                        <div style="margin-top: 1rem;">
                            <a href="{{ route('agency.taxis.edit', $taxi) }}" class="driver-action-btn primary"
                                style="width: 100%; justify-content: center;">
                                <i class="fas fa-edit"></i> Modifier l'Attribution
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-tachometer-alt"></i> Statistiques Rapides</h3>
                    </div>
                    <div class="driver-info-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Courses en Cours</label>
                                <span class="stat-highlight">{{ $stats['in_progress_bookings'] }}</span>
                            </div>
                            <div class="info-item">
                                <label>Courses Annulées</label>
                                <span class="stat-highlight">{{ $stats['cancelled_bookings'] }}</span>
                            </div>
                            <div class="info-item">
                                <label>Taux d'Annulation</label>
                                <span class="stat-highlight">{{ number_format($stats['cancellation_rate'], 1) }}%</span>
                            </div>
                            <div class="info-item">
                                <label>Revenus Moyens/Course</label>
                                <span
                                    class="stat-highlight">{{ number_format($stats['average_fare'], 0, ',', ' ') }}€</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Confirm availability toggle
            const availabilityForm = document.querySelector('form[action*="toggle-availability"]');
            if (availabilityForm) {
                availabilityForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const button = this.querySelector('button');
                    const action = button.textContent.trim();
                    const taxiPlate = '{{ $taxi->license_plate }}';

                    if (confirm(
                            `Êtes-vous sûr de vouloir ${action.toLowerCase()} le taxi ${taxiPlate} ?`)) {
                        this.submit();
                    }
                });
            }

            // Auto-refresh stats every 60 seconds
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    console.log('Refreshing taxi stats...');
                }
            }, 60000);

            // Confirm taxi deletion
            const deleteForm = document.querySelector('.delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const taxiPlate = '{{ $taxi->license_plate }}';

                    if (confirm(
                            `Êtes-vous sûr de vouloir supprimer définitivement le taxi ${taxiPlate} ?\n\nCette action est irréversible.`
                        )) {
                        this.submit();
                    }
                });
            }
        });
    </script>
@endsection
