<header class="app-header">
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/taxi-icon.png') }}" alt="Taxi Logo">
            <h1>TaxiGo</h1>
        </div>
        <nav class="main-nav">
            <button class="menu-toggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="{{ Request::routeIs('home') ? 'active' : '' }}">Home</a></li>
                {{-- <li><a href="#">Services</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li> --}}
                @auth
                    @if (Auth::user()->user_type == 'DRIVER')
                        <li><a href="{{ route('driver.dashboard') }}"
                                class="{{ Request::routeIs('driver.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                    @endif
                    <li class="flex-center">
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-login" style="cursor: pointer;">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="flex-center"><a href="{{ route('login') }}" class="btn-login">Login</a></li>
                @endauth
            </ul>
        </nav>
    </div>
</header>
