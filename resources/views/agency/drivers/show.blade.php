@extends('agency.layout')

@section('title', $driver->firstname . ' ' . $driver->lastname)

@section('breadcrumb')
    <a href="{{ route('agency.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <a href="{{ route('agency.drivers.index') }}">Chauffeurs</a>
    <i class="fas fa-chevron-right"></i>
    <span>{{ $driver->firstname }} {{ $driver->lastname }}</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-driver-show.css') }}">
@endsection

@section('content')
    <div class="driver-show-container">
        <!-- Driver Profile Header -->
        <div class="driver-profile-header">
            <div class="driver-profile-content">
                <div class="driver-avatar-large">
                    {{ strtoupper(substr($driver->firstname, 0, 1)) }}{{ strtoupper(substr($driver->lastname, 0, 1)) }}
                </div>
                <div class="driver-profile-info">
                    <h1>{{ $driver->firstname }} {{ $driver->lastname }}</h1>
                    <div class="driver-profile-meta">
                        <div class="driver-meta-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $driver->email }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-user"></i>
                            <span>{{ $driver->username }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>Membre depuis {{ $driver->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Dernière activité {{ $driver->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="driver-meta-item">
                            <div class="driver-status-large status-{{ $driver->status }}">
                                {{ ucfirst($driver->status) }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="driver-actions-header">
                <a href="{{ route('agency.drivers.edit', $driver) }}" class="driver-action-btn primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <form action="{{ route('agency.drivers.toggle-status', $driver) }}" method="POST"
                    style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="driver-action-btn {{ $driver->status === 'active' ? 'warning' : 'success' }}">
                        <i class="fas fa-{{ $driver->status === 'active' ? 'pause' : 'play' }}"></i>
                        {{ $driver->status === 'active' ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
                <a href="{{ route('agency.drivers.index') }}" class="driver-action-btn secondary">
                    <i class="fas fa-list"></i> Retour à la Liste
                </a>
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

            {{-- <div class="driver-stat-card rating">
                <div class="driver-stat-header">
                    <div class="driver-stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="driver-stat-value">{{ number_format($stats['average_rating'], 1) }}</div>
                <div class="driver-stat-label">Note Moyenne</div>
                <div
                    class="driver-stat-trend {{ $stats['average_rating'] >= 4 ? 'up' : ($stats['average_rating'] >= 3 ? 'neutral' : 'down') }}">
                    <i class="fas fa-star"></i>
                    <span>{{ $stats['total_ratings'] }} évaluations</span>
                </div>
            </div> --}}
        </div>

        <!-- Content Grid -->
        <div class="driver-content-grid">
            <!-- Main Content -->
            <div class="driver-main-content">
                <!-- Recent Bookings -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-history"></i> Courses Récentes</h3>
                        <a href="{{ route('agency.bookings.index', ['driver_id' => $driver->id]) }}"
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
                                                class="fas fa-{{ $booking->status === 'COMPLETED'
                                                    ? 'check'
                                                    : ($booking->status === 'IN_PROGRESS'
                                                        ? 'car'
                                                        : ($booking->status === 'CANCELLED'
                                                            ? 'times'
                                                            : 'clock')) }}"></i>
                                        </div>
                                        <div class="booking-details">
                                            <h5>{{ $booking->pickup_location }} ({{ $booking->pickupCity->name }}) →
                                                {{ $booking->destinationCity->name }}
                                            </h5>
                                            <p>
                                                {{ $booking->created_at->format('d/m/Y H:i') }} •
                                                {{ ucfirst(strtolower(str_replace('_', ' ', $booking->status))) }}
                                                @if ($booking->customer)
                                                    • {{ $booking->customer->firstname }}
                                                    {{ $booking->customer->lastname }}
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
                            <p>Graphique de performance (à implémenter)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Content -->
            <div class="driver-sidebar-content">
                <!-- Taxi Assignment -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-car"></i> Taxi Assigné</h3>
                    </div>
                    <div class="driver-info-body">
                        @if ($driver->taxi)
                            <div class="taxi-assignment-card assigned">
                                <div class="taxi-card-header">
                                    <div class="taxi-info">
                                        <h4>{{ $driver->taxi->license_plate }}</h4>
                                        <span class="status-badge status-active">Assigné</span>
                                    </div>
                                </div>
                                <div class="taxi-details">
                                    <div class="taxi-detail-item">
                                        <label>Type</label>
                                        <span>{{ ucfirst($driver->taxi->type) }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Modèle</label>
                                        <span>{{ $driver->taxi->model ?? 'Non spécifié' }}</span>
                                    </div>
                                    <div class="taxi-detail-item">
                                        <label>Statut</label>
                                        <span
                                            class="status-badge status-{{ $driver->taxi->is_available ? 'active' : 'inactive' }}">
                                            {{ $driver->taxi->is_available ? 'Disponible' : 'Occupé' }}
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
                                <p style="margin: 1rem 0 0 0; color: var(--agency-gray); font-size: 0.9rem;">
                                    Ce chauffeur n'a pas de taxi assigné. Vous pouvez lui en assigner un via la page de
                                    modification.
                                </p>
                            </div>
                        @endif

                        <div style="margin-top: 1rem;">
                            <a href="{{ route('agency.drivers.edit', $driver) }}" class="driver-action-btn primary"
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

                <!-- Account Information -->
                <div class="driver-info-card">
                    <div class="driver-info-header">
                        <h3><i class="fas fa-user-cog"></i> Informations Compte</h3>
                    </div>
                    <div class="driver-info-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Statut</label>
                                <span
                                    class="status-badge status-{{ $driver->status }}">{{ ucfirst($driver->status) }}</span>
                            </div>
                            <div class="info-item">
                                <label>Rôle</label>
                                <span>Chauffeur</span>
                            </div>
                            <div class="info-item">
                                <label>Agence</label>
                                <span>{{ $driver->agency->name ?? 'Non définie' }}</span>
                            </div>
                            <div class="info-item">
                                <label>Dernière Connexion</label>
                                <span>{{ $driver->updated_at->diffForHumans() }}</span>
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
            // Confirm status toggle
            const statusForm = document.querySelector('form[action*="toggle-status"]');
            if (statusForm) {
                statusForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const button = this.querySelector('button');
                    const action = button.textContent.trim();
                    const driverName = '{{ $driver->firstname }} {{ $driver->lastname }}';

                    if (confirm(`Êtes-vous sûr de vouloir ${action.toLowerCase()} ${driverName} ?`)) {
                        this.submit();
                    }
                });
            }

            // Auto-refresh stats every 60 seconds
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    // You can add AJAX call here to refresh stats
                    console.log('Refreshing driver stats...');
                }
            }, 60000);

            // Add hover effects to stat cards
            const statCards = document.querySelectorAll('.driver-stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(-4px)';
                });
            });
        });
    </script>
@endsection
