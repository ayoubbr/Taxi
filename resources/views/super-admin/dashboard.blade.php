@extends('super-admin.layout')

@section('title', 'Titre de la page')

@section('breadcrumb')
    <a href="{{ route('super-admin.dashboard') }}">Dashboard</a>
    <i class="fas fa-chevron-right"></i>
    <span>Page actuelle</span>
@endsection

@section('css')
    {{-- <link rel="stylesheet" href="{{ asset('css/super-admin-dashboard.css') }}"> --}}
@endsection

@section('content')
    <div class="super-admin-dashboard">
        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="container">
                <!-- Quick Stats -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($stats['total_users']) }}</h3>
                            <p>Utilisateurs Total</p>
                            <div class="stat-breakdown">
                                <span>{{ $stats['total_drivers'] }} Chauffeurs</span>
                                <span>{{ $stats['total_clients'] }} Clients</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($stats['total_agencies']) }}</h3>
                            <p>Agences Partenaires</p>
                            {{-- <div class="stat-trend positive">
                                <i class="fas fa-arrow-up"></i> +12% ce mois
                            </div> --}}
                        </div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-taxi"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($stats['total_bookings']) }}</h3>
                            <p>Réservations Total</p>
                            <div class="stat-breakdown">
                                <span>{{ $stats['pending_bookings'] }} En attente</span>
                                <span>{{ $stats['completed_bookings'] }} Complet</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card danger">
                        <div class="stat-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($stats['banned_users']) }}</h3>
                            <p>Utilisateurs Bannis</p>
                            @if ($stats['banned_users'] > 0)
                                {{-- {{ route('super-admin.users.index', ['status' => 'banned']) }} --}}
                                <a href="" class="stat-link">
                                    Voir la liste
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="stat-card info">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($stats['revenue_today'], 2) }} DH</h3>
                            <p>Revenus Aujourd'hui</p>
                            <div class="stat-breakdown">
                                <span>Mois: ${{ number_format($stats['revenue_month'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card accent">
                        <div class="stat-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ number_format($stats['total_taxis']) }}</h3>
                            <p>Véhicules Enregistrés</p>
                            {{-- <div class="stat-trend positive">
                                <i class="fas fa-arrow-up"></i> Actifs
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h2><i class="fas fa-bolt"></i> Actions Rapides</h2>
                    <div class="actions-grid">
                        <a href="{{ route('super-admin.agencies.index') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h3>Gérer Agences</h3>
                            <p>Ajouter, modifier ou supprimer des agences</p>
                        </a>

                        <a href="{{ route('super-admin.users.index') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <h3>Gérer Utilisateurs</h3>
                            <p>Voir, bannir ou débannir des utilisateurs</p>
                        </a>

                        <a href="{{ route('super-admin.agencies.create') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <h3>Nouvelle Agence</h3>
                            <p>Ajouter une nouvelle agence partenaire</p>
                        </a>

                        <a href="{{ route('super-admin.users.create') }}" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h3>Nouvel Utilisateur</h3>
                            <p>Créer un nouveau compte utilisateur</p>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity & Top Agencies -->
                <div class="dashboard-grid">
                    <!-- Recent Bookings -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-clock"></i> Réservations Récentes</h3>
                            <a href="{{ route('super-admin.bookings.index') }}" class="view-all">Voir tout</a>
                        </div>
                        <div class="card-content">
                            @forelse($recentBookings as $booking)
                                <div class="activity-item">
                                    <div class="activity-icon status-{{ $booking->status }}">
                                        <i class="fas fa-taxi"></i>
                                    </div>
                                    <div class="activity-details">
                                        <h4>{{ $booking->client_name }}</h4>
                                        <p>{{ Str::limit($booking->pickup_location, 30) }}({{ Str::limit($booking->pickupCity->name, 30) }})
                                            →
                                            {{ Str::limit($booking->destinationCity->name, 30) }}</p>
                                        <span class="activity-time">{{ $booking->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="activity-status status-{{ $booking->status }}">
                                        {{ str_replace('_', ' ', $booking->status) }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>Aucune réservation récente</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-user-plus"></i> Nouveaux Utilisateurs</h3>
                            <a href="{{ route('super-admin.users.index') }}" class="view-all">Voir tout</a>
                        </div>
                        <div class="card-content">
                            @forelse($recentUsers as $user)
                                <div class="activity-item">
                                    <div class="activity-icon role-{{ $user->role->name }}">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="activity-details">
                                        <h4>{{ $user->firstname }} {{ $user->lastname }}</h4>
                                        <p>{{ ucfirst($user->role->name) }} - {{ $user->email }}</p>
                                        <span class="activity-time">{{ $user->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="activity-status status-{{ $user->status }}">
                                        {{ ucfirst($user->status) }}
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-user-times"></i>
                                    <p>Aucun nouvel utilisateur</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Agencies -->
                    <div class="dashboard-card full-width">
                        <div class="card-header">
                            <h3><i class="fas fa-trophy"></i> Agences les Plus Actives</h3>
                            <a href="{{ route('super-admin.agencies.index') }}" class="view-all">Voir tout</a>
                        </div>
                        <div class="card-content">
                            <div class="agencies-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                                @forelse($topAgencies as $agency)
                                    <div class="agency-card">
                                        <div class="agency-info">
                                            <div
                                                style="display: flex; justify-content: center; align-items: start; gap: 14px; min-height: 90px;">
                                                <div class="agency-logo">
                                                    @if ($agency->logo)
                                                        <img src="{{ Storage::url($agency->logo) }}" alt="{{ $agency->name }}">
                                                    @else
                                                        <div class="logo-placeholder">
                                                            {{ strtoupper(substr($agency->name, 0, 2)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4>{{ $agency->name }}</h4>
                                                    <p>{{ $agency->address }}</p>
                                                </div>
                                            </div>
                                            <div class="agency-stats">
                                                <span><i class="fas fa-users"></i> {{ $agency->drivers_count }}
                                                    chauffeurs</span>
                                                <span><i class="fas fa-taxi"></i> {{ $agency->bookings_count }}
                                                    courses</span>
                                            </div>
                                        </div>
                                        <div class="agency-status status-{{ $agency->status }}">
                                            {{ ucfirst($agency->status) }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <i class="fas fa-building"></i>
                                        <p>Aucune agence enregistrée</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh stats every 30 seconds
            // setInterval(function() {
            //     // Only refresh if user is still on the page
            //     if (document.visibilityState === 'visible') {
            //         location.reload();
            //     }
            // }, 30000);

            // Animate stat numbers on load
            const statNumbers = document.querySelectorAll('.stat-content h3');
            statNumbers.forEach(stat => {
                const finalValue = parseInt(stat.textContent.replace(/[^0-9]/g, ''));
                if (finalValue > 0) {
                    animateNumber(stat, finalValue);
                }
            });

            function animateNumber(element, target) {
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    element.textContent = Math.floor(current).toLocaleString();
                }, 20);
            }
        });
    </script>
@endsection
