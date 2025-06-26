@extends('agency.layout')

@section('title', 'Tableau de Bord')

@section('breadcrumb')
    <span>Tableau de Bord</span>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/agency-admin-dashboard.css') }}">
@endsection

@section('content')
    <div class="agency-dashboard">
        <!-- Page Header -->
        <div class="agency-page-header">
            <div class="agency-header-content">
                <div class="agency-header-left">
                    <div class="agency-header-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="agency-header-info">
                        <h1>Tableau de Bord</h1>
                        <p>Vue d'ensemble de votre agence {{ Auth::user()->agency->name ?? '' }}</p>
                    </div>
                </div>
                <div class="agency-header-actions">
                    <a href="{{ route('agency.bookings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Réservation
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="agency-stats-grid">
            <div class="agency-stat-card drivers">
                <div class="agency-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="agency-stat-content">
                    <h3>{{ $stats['total_drivers'] ?? 0 }}</h3>
                    <p>Chauffeurs</p>
                    <div class="agency-stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ $stats['active_drivers'] ?? 0 }} actifs</span>
                    </div>
                </div>
            </div>

            <div class="agency-stat-card taxis">
                <div class="agency-stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="agency-stat-content">
                    <h3>{{ $stats['total_taxis'] ?? 0 }}</h3>
                    <p>Taxis</p>
                    <div class="agency-stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ $stats['available_taxis'] ?? 0 }} disponibles</span>
                    </div>
                </div>
            </div>

            <div class="agency-stat-card bookings">
                <div class="agency-stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="agency-stat-content">
                    <h3>{{ $stats['total_bookings'] ?? 0 }}</h3>
                    <p>Réservations</p>
                    <div class="agency-stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ $stats['completed_bookings'] ?? 0 }} terminées</span>
                    </div>
                </div>
            </div>

            <div class="agency-stat-card revenue">
                <div class="agency-stat-icon">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="agency-stat-content">
                    <h3>{{ number_format($stats['revenue_total'] ?? 0, 0, ',', ' ') }}€</h3>
                    <p>Revenus Total</p>
                    <div class="agency-stat-trend up">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ number_format($stats['revenue_month'] ?? 0, 0, ',', ' ') }}€ ce mois</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="agency-quick-actions">
            <h2><i class="fas fa-bolt"></i> Actions Rapides</h2>
            <div class="agency-actions-grid">
                <a href="{{ route('agency.drivers.create') }}" class="agency-action-card drivers">
                    <div class="agency-action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="agency-action-content">
                        <h4>Ajouter Chauffeur</h4>
                        <p>Enregistrer un nouveau chauffeur</p>
                    </div>
                </a>

                <a href="{{ route('agency.taxis.create') }}" class="agency-action-card taxis">
                    <div class="agency-action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="agency-action-content">
                        <h4>Ajouter Taxi</h4>
                        <p>Enregistrer un nouveau véhicule</p>
                    </div>
                </a>

                <a href="{{ route('agency.bookings.index') }}" class="agency-action-card bookings">
                    <div class="agency-action-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="agency-action-content">
                        <h4>Voir Réservations</h4>
                        <p>Gérer les réservations</p>
                    </div>
                </a>

                <a href="{{ route('agency.reports.index') }}" class="agency-action-card settings">
                    <div class="agency-action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="agency-action-content">
                        <h4>Rapports</h4>
                        <p>Voir les statistiques détaillées</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="agency-recent-activity">
            <h2><i class="fas fa-clock"></i> Activité Récente</h2>
            <div class="agency-activity-list">
                @forelse($recentActivities ?? [] as $activity)
                    <div class="agency-activity-item">
                        <div class="agency-activity-icon {{ $activity['type'] }}">
                            <i class="fas fa-{{ $activity['icon'] }}"></i>
                        </div>
                        <div class="agency-activity-content">
                            <p>{{ $activity['message'] }}</p>
                            <small>{{ $activity['time'] }}</small>
                        </div>
                        <div class="agency-activity-time">
                            {{ $activity['date'] }}
                        </div>
                    </div>
                @empty
                    <div class="agency-activity-item">
                        <div class="agency-activity-icon booking">
                            <i class="fas fa-info"></i>
                        </div>
                        <div class="agency-activity-content">
                            <p>Aucune activité récente</p>
                            <small>Commencez par ajouter des chauffeurs et des taxis</small>
                        </div>
                        <div class="agency-activity-time">
                            Maintenant
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh stats every 30 seconds
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    // You can add AJAX call here to refresh stats
                    console.log('Refreshing stats...');
                }
            }, 30000);

            // Add click animations to action cards
            const actionCards = document.querySelectorAll('.agency-action-card');
            actionCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
        });
    </script>
@endsection
