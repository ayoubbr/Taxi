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
                <li><a href="#" class="active">Home</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                {{-- \{{ route('login') }} --}}
                <li><a href="#" class="btn-login">Login</a></li>
            </ul>
        </nav>
    </div>
</header>
