<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Super Admin') - {{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/status.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flash-messages.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin-forms.css') }}">
    @yield('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="super-admin-layout">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Chargement...</p>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-crown"></i>
                <span class="logo-text">Super Admin</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <h3 class="nav-title">Principal</h3>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('super-admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Tableau de Bord</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-section">
                <h3 class="nav-title">Gestion</h3>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('super-admin.agencies.index') }}"
                            class="nav-link {{ request()->routeIs('super-admin.agencies*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>Agences</span>
                            <span class="nav-badge">{{ \App\Models\Agency::count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.users.index') }}"
                            class="nav-link {{ request()->routeIs('super-admin.users*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>Utilisateurs</span>
                            <span class="nav-badge">{{ \App\Models\User::count() }}</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('super-admin.drivers.index') }}"
                            class="nav-link {{ request()->routeIs('super-admin.drivers*') ? 'active' : '' }}">
                            <i class="fas fa-taxi"></i>
                            <span>Chauffeurs</span>
                            <span class="nav-badge">{{ \App\Models\User::where('role', 'driver')->count() }}</span>
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a href="{{ route('super-admin.bookings.index') }}"
                            class="nav-link {{ request()->routeIs('super-admin.bookings*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Réservations</span>
                            <span class="nav-badge">{{ \App\Models\Booking::count() }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- <div class="nav-section">
                <h3 class="nav-title">Modération</h3>
                <ul class="nav-list">
                </ul>
            </div>

            <div class="nav-section">
                <h3 class="nav-title">Configuration</h3>
                <ul class="nav-list">
                </ul>
            </div> --}}
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                @auth

                    <div class="profile-avatar">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}{{ strtoupper(substr(Auth::user()->lastname, 0, 1)) }}
                    </div>
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::user()->firstname }}</span>
                        <span class="profile-role">Super Admin</span>
                    </div>
                @endauth
            </div>
            <div class="sidebar-actions">
                <a href="{{ route('home') }}" class="action-btn" title="Retour au site">
                    <i class="fas fa-home"></i>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="action-btn" title="Déconnexion">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main" id="adminMain">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="breadcrumb">
                    @yield('breadcrumb')
                </div>
            </div>
            <div class="topbar-right">
                <!-- Quick Stats -->
                <div class="quick-stats">
                    <div class="stat-item">
                        <i class="fas fa-users"></i>
                        <span>{{ \App\Models\User::whereDate('created_at', today())->count() }}</span>
                        <small>Users aujourd'hui</small>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-taxi"></i>
                        <span>{{ \App\Models\Booking::whereDate('created_at', today())->count() }}</span>
                        <small>Réservations aujourd'hui</small>
                    </div>
                </div>

                <!-- Admin Menu -->
                <div class="admin-menu-dropdown">
                    <button class="admin-menu-btn" id="adminMenuBtn">
                        @auth
                            <div class="admin-avatar">
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
        @yield('content')
        </div>
    </main>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Sidebar functionality
            const sidebar = document.getElementById("adminSidebar")
            const sidebarToggle = document.getElementById("sidebarToggle")
            const mobileSidebarToggle = document.getElementById("mobileSidebarToggle")
            const mobileOverlay = document.getElementById("mobileOverlay")
            const adminMain = document.getElementById("adminMain")

            // Desktop sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener("click", () => {
                    sidebar.classList.toggle("collapsed")
                    localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"))
                })
            }

            // Mobile sidebar toggle
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener("click", () => {
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

            // Restore sidebar state
            const sidebarCollapsed = localStorage.getItem("sidebarCollapsed")
            if (sidebarCollapsed === "true") {
                sidebar.classList.add("collapsed")
            }

            // Notification dropdown
            const notificationBtn = document.getElementById("notificationBtn")
            const notificationDropdown = document.getElementById("notificationDropdown")

            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener("click", (e) => {
                    e.stopPropagation()
                    notificationDropdown.classList.toggle("active")

                    // Close admin dropdown if open
                    const adminDropdown = document.getElementById("adminDropdown")
                    if (adminDropdown) {
                        adminDropdown.classList.remove("active")
                    }
                })
            }

            // Admin menu dropdown
            const adminMenuBtn = document.getElementById("adminMenuBtn")
            const adminDropdown = document.getElementById("adminDropdown")

            if (adminMenuBtn && adminDropdown) {
                adminMenuBtn.addEventListener("click", (e) => {
                    e.stopPropagation()
                    adminDropdown.classList.toggle("active")

                    // Close notification dropdown if open
                    if (notificationDropdown) {
                        notificationDropdown.classList.remove("active")
                    }
                })
            }

            // Close dropdowns when clicking outside
            document.addEventListener("click", () => {
                if (notificationDropdown) {
                    notificationDropdown.classList.remove("active")
                }
                if (adminDropdown) {
                    adminDropdown.classList.remove("active")
                }
            })

            // Prevent dropdown close when clicking inside
            if (notificationDropdown) {
                notificationDropdown.addEventListener("click", (e) => {
                    e.stopPropagation()
                })
            }

            if (adminDropdown) {
                adminDropdown.addEventListener("click", (e) => {
                    e.stopPropagation()
                })
            }


            // Show loading on navigation links
            const navLinks = document.querySelectorAll(".nav-link, .action-card")
            navLinks.forEach((link) => {
                link.addEventListener("click", function() {
                    if (!this.getAttribute("href").startsWith("#")) {
                        showLoading()
                    }
                })
            })

            // Alert system
            window.showAlert = (message, type = "info") => {
                const alertContainer = document.createElement("div")
                alertContainer.className = `alert alert-${type}`
                alertContainer.innerHTML = `
                                            <i class="fas fa-${type === "success" ? "check-circle" : type === "error" ? "exclamation-circle" :                              "info-circle"}"></i>
                                            ${message}
                                            <button class="alert-close">&times;</button>`

                const adminContent = document.querySelector(".admin-content")
                if (adminContent) {
                    adminContent.insertBefore(alertContainer, adminContent.firstChild)

                    // Auto-hide after 5 seconds
                    setTimeout(() => {
                        alertContainer.style.opacity = "0"
                        setTimeout(() => alertContainer.remove(), 300)
                    }, 5000)

                    // Close button functionality
                    const closeBtn = alertContainer.querySelector(".alert-close")
                    closeBtn.addEventListener("click", () => {
                        alertContainer.style.opacity = "0"
                        setTimeout(() => alertContainer.remove(), 300)
                    })
                }
            }

            // Smooth scrolling for anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"]')
            anchorLinks.forEach((link) => {
                link.addEventListener("click", function(e) {
                    e.preventDefault()
                    const target = document.querySelector(this.getAttribute("href"))
                    if (target) {
                        target.scrollIntoView({
                            behavior: "smooth",
                            block: "start",
                        })
                    }
                })
            })
        })

        // CSRF Token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const closeBtn = alert.querySelector('.alert-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    });
                }

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }
                }, 5000);
            });
        });
    </script>
    @yield('js')
</body>

</html>
