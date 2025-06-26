<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Agency Admin') - {{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/status.css') }}">
    <link rel="stylesheet" href="{{ asset('css/agency-admin-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flash-messages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    @yield('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="agency-admin-layout">
    <!-- Sidebar -->
    <aside class="agency-sidebar" id="agencySidebar">
        <div class="agency-sidebar-header">
            <div class="agency-logo">
                <i class="fas fa-taxi"></i>
                <span class="agency-logo-text">{{ Auth::user()->agency->name ?? 'Agency Admin' }}</span>
            </div>
            <button class="agency-sidebar-toggle" id="agencySidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="agency-nav">
            <div class="agency-nav-section">
                <h3 class="agency-nav-title">Principal</h3>
                <ul class="agency-nav-list">
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.dashboard') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Tableau de Bord</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="agency-nav-section">
                <h3 class="agency-nav-title">Gestion</h3>
                <ul class="agency-nav-list">
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.drivers.index') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.drivers*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Chauffeurs</span>
                            @if (Auth::user()->agency)
                                <span class="agency-nav-badge">{{ Auth::user()->agency->drivers()->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.taxis.index') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.taxis*') ? 'active' : '' }}">
                            <i class="fas fa-car"></i>
                            <span>Taxis</span>
                            @if (Auth::user()->agency)
                                <span class="agency-nav-badge">{{ Auth::user()->agency->taxis()->count() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.bookings.index') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.bookings*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Réservations</span>
                            @if (Auth::user()->agency)
                                <span class="agency-nav-badge">{{ Auth::user()->agency->bookings()->count() }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>

            {{-- <div class="agency-nav-section">
                <h3 class="agency-nav-title">Rapports</h3>
                <ul class="agency-nav-list">
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.reports.index') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.reports*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Statistiques</span>
                        </a>
                    </li>
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.revenue.index') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.revenue*') ? 'active' : '' }}">
                            <i class="fas fa-euro-sign"></i>
                            <span>Revenus</span>
                        </a>
                    </li>
                </ul>
            </div> --}}

            {{-- <div class="agency-nav-section">
                <h3 class="agency-nav-title">Configuration</h3>
                <ul class="agency-nav-list">
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.profile.index') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.profile*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>Profil Agence</span>
                        </a>
                    </li>
                    <li class="agency-nav-item">
                        <a href="{{ route('agency.settings.index') }}"
                            class="agency-nav-link {{ request()->routeIs('agency.settings*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                    </li>
                </ul>
            </div> --}}
        </nav>

        <div class="agency-sidebar-footer">
            <div class="agency-profile">
                @auth
                    <div class="agency-profile-avatar">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}{{ strtoupper(substr(Auth::user()->lastname, 0, 1)) }}
                    </div>
                    <div class="agency-profile-info">
                        <span class="agency-profile-name">{{ Auth::user()->firstname }}
                            {{ Auth::user()->lastname }}</span>
                        <span class="agency-profile-role">Admin d'Agence</span>
                    </div>
                @endauth
            </div>
            <div class="agency-sidebar-actions">
                <a href="{{ route('home') }}" class="agency-action-btn" title="Retour au site">
                    <i class="fas fa-home"></i>
                </a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="agency-action-btn" title="Déconnexion">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="agency-main" id="agencyMain">
        <header class="agency-topbar">
            <div class="agency-topbar-left">
                <button class="agency-mobile-toggle" id="agencyMobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="agency-breadcrumb">
                    @yield('breadcrumb')
                </div>
            </div>
            <div class="agency-topbar-right">
                <!-- Quick Stats -->
                <div class="agency-quick-stats">
                    @if (Auth::user()->agency)
                        <div class="agency-stat-item">
                            <i class="fas fa-users"></i>
                            <span>{{ Auth::user()->agency->drivers()->where('status', 'active')->count() }}</span>
                            <small>Chauffeurs actifs</small>
                        </div>
                        <div class="agency-stat-item">
                            <i class="fas fa-car"></i>
                            <span>{{ Auth::user()->agency->taxis()->where('is_available', true)->count() }}</span>
                            <small>Taxis disponibles</small>
                        </div>
                        <div class="agency-stat-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>{{ Auth::user()->agency->bookings()->whereDate('bookings.created_at', today())->count() }}</span>
                            <small>Réservations aujourd'hui</small>
                        </div>
                    @endif
                </div>

                <!-- User Menu -->
                <div class="admin-menu-dropdown agency-user-menu">
                    <button class="admin-menu-btn" id="adminMenuBtn">
                        @auth
                            <div class="admin-avatar agency-user-avatar">
                                {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                            </div>
                        @endauth
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="admin-dropdown" id="adminDropdown">
                        <div class="dropdown-header">
                            <div class="admin-info">
                                @auth
                                    <strong>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</strong>
                                    <span>{{ Auth::user()->email }}</span>
                                @endauth
                            </div>
                        </div>
                        <div class="dropdown-menu">
                            {{-- {{ route('super-admin.profile') }} --}}
                            <a href="" class="dropdown-item">
                                <i class="fas fa-user"></i> Mon Profil
                            </a>
                            {{-- {{ route('super-admin.settings.index') }} --}}
                            <a href="" class="dropdown-item">
                                <i class="fas fa-cog"></i> Paramètres
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('home') }}" class="dropdown-item">
                                <i class="fas fa-home"></i> Retour au site
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="dropdown-form">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>



            </div>
        </header>

        <div class="flash-messages-container">
            @include('partials.flash-messages')
        </div>

        <div class="agency-content">
            @yield('content')
        </div>
    </main>

    <!-- Mobile Overlay -->
    <div class="agency-mobile-overlay" id="agencyMobileOverlay"></div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Sidebar functionality
            const sidebar = document.getElementById("agencySidebar")
            const sidebarToggle = document.getElementById("agencySidebarToggle")
            const mobileToggle = document.getElementById("agencyMobileToggle")
            const mobileOverlay = document.getElementById("agencyMobileOverlay")
            const adminMain = document.getElementById("adminMain")

            // Desktop sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener("click", () => {
                    sidebar.classList.toggle("collapsed")
                    localStorage.setItem("agencySidebarCollapsed", sidebar.classList.contains("collapsed"))
                })
            }

            // Mobile sidebar toggle
            if (mobileToggle) {
                mobileToggle.addEventListener("click", () => {
                    sidebar.classList.add("mobile-open")
                    mobileOverlay.classList.add("active")
                    document.body.style.overflow = "hidden"
                })
            }

            // Close mobile sidebar
            if (mobileOverlay) {
                mobileOverlay.addEventListener("click", () => {
                    sidebar.classList.remove("mobile-open")
                    mobileOverlay.classList.remove("active")
                    document.body.style.overflow = ""
                })
            }

            const adminMenuBtn = document.getElementById("adminMenuBtn")
            const adminDropdown = document.getElementById("adminDropdown")

            if (adminMenuBtn && adminDropdown) {
                adminMenuBtn.addEventListener("click", (e) => {
                    e.stopPropagation()
                    adminDropdown.classList.toggle("active")
                })
            }

            // Close dropdowns when clicking outside
            document.addEventListener("click", () => {
                if (adminDropdown) {
                    adminDropdown.classList.remove("active")
                }
            })

            // Close dropdowns when clicking outside
            document.addEventListener("click", () => {
                if (adminDropdown) {
                    adminDropdown.classList.remove("active")
                }
            })


            if (adminDropdown) {
                adminDropdown.addEventListener("click", (e) => {
                    e.stopPropagation()
                })
            }

            // Restore sidebar state
            const sidebarCollapsed = localStorage.getItem("agencySidebarCollapsed")
            if (sidebarCollapsed === "true") {
                sidebar.classList.add("collapsed")
            }
        })

        // CSRF Token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @yield('js')
</body>

</html>
