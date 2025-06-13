<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Driver - Taxi</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/taxi-app.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flash-messages.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/home.css') }}"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')
</head>

<body>
    {{-- @include('partials.header') --}}

    <div class="flash-messages-container">
        @include('partials.flash-messages')
    </div>
    <div class="driver-dashboard">
        <aside class="dashboard-sidebar">
            <div class="driver-profile">
                <div class="driver-avatar">
                    <span>{{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}</span>
                </div>
                <div class="driver-info">
                    <h3>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h3>
                    <span class="driver-status online">Online</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="{{ Request::routeIs('driver.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('driver.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="{{ Request::routeIs('driver.bookings.available') ? 'active' : '' }}">
                        <a href="{{ route('driver.bookings.available') }}"><i class="fas fa-route"></i> My Rides</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-history"></i> History</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-chart-line"></i> Earnings</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-cog"></i> Settings</a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        @yield('content')

    </div>

    @yield('js')
    <script src="{{ asset('js/flash-messages.js') }}"></script>
    {{-- <script src="{{ asset('js/home.js') }}"></script> --}}
</body>

</html>
