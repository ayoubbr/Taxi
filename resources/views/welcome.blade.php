@extends('layout')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection
@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Réservez votre taxi en quelques clics</h1>
            <p class="hero-subtitle">Service de taxi rapide et fiable pour votre hôtel. Générez votre code QR et voyagez
                en toute simplicité.</p>

            <div class="hero-actions">
                <button class="btn btn-primary" (click)="navigateToBooking()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 10C21 17 12 23 12 23S3 17 3 10A9 9 0 0 1 21 10Z" stroke="currentColor"
                            stroke-width="2" />
                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" />
                    </svg>
                    Réserver maintenant
                </button>
                <button class="btn btn-secondary" (click)="scanQRCode()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="3" width="5" height="5" stroke="currentColor" stroke-width="2" />
                        <rect x="16" y="3" width="5" height="5" stroke="currentColor" stroke-width="2" />
                        <rect x="3" y="16" width="5" height="5" stroke="currentColor" stroke-width="2" />
                        <path d="M21 16H16V21" stroke="currentColor" stroke-width="2" />
                        <path d="M21 21L16 16" stroke="currentColor" stroke-width="2" />
                    </svg>
                    Scanner QR Code
                </button>
            </div>
        </div>

        <div class="hero-image">
            <div class="taxi-illustration">
                <img width="100%" src="/images/taxi-2.webp" alt="">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-content">
            <h2 class="section-title">Pourquoi choisir notre service ?</h2>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                            <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="feature-title">Rapide</h3>
                    <p class="feature-description">Réservation en moins de 2 minutes avec confirmation instantanée</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="feature-title">Fiable</h3>
                    <p class="feature-description">Chauffeurs professionnels et véhicules entretenus régulièrement</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="5" height="5" stroke="currentColor" stroke-width="2" />
                            <rect x="16" y="3" width="5" height="5" stroke="currentColor" stroke-width="2" />
                            <rect x="3" y="16" width="5" height="5" stroke="currentColor" stroke-width="2" />
                            <path d="M21 16H16V21" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="feature-title">Code QR</h3>
                    <p class="feature-description">Système de codes QR pour une gestion simplifiée des réservations</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22S2 15 2 8A10 10 0 0 1 22 8C22 15 12 22 12 22Z" stroke="currentColor"
                                stroke-width="2" />
                            <path d="M9 8H15" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <h3 class="feature-title">Sécurisé</h3>
                    <p class="feature-description">Paiements sécurisés et données personnelles protégées</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works Section -->
    <section class="how-it-works">
        <div class="how-it-works-content">
            <h2 class="section-title">Comment ça marche ?</h2>

            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3 class="step-title">Réservez</h3>
                        <p class="step-description">Sélectionnez votre destination et l'heure de départ souhaitée</p>
                    </div>
                </div>

                <div class="step-connector"></div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">Recevez votre QR</h3>
                        <p class="step-description">Un code QR unique est généré pour votre réservation</p>
                    </div>
                </div>

                <div class="step-connector"></div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3 class="step-title">Voyagez</h3>
                        <p class="step-description">Présentez votre code QR au chauffeur et profitez du trajet</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-content">
            <h2 class="cta-title">Prêt à réserver votre taxi ?</h2>
            <p class="cta-subtitle">Commencez dès maintenant et profitez d'un service de qualité</p>
            <button class="btn btn-primary btn-large" (click)="navigateToBooking()">
                Commencer ma réservation
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4 class="footer-title">TaxiHotel</h4>
                <p class="footer-description">Service de réservation de taxi pour votre hôtel</p>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Contact</h4>
                <p class="footer-link">+33 1 23 45 67 89</p>
                <p class="footer-link">contact&commat;taxihotel.com</p>
            </div>

            <div class="footer-section">
                <h4 class="footer-title">Aide</h4>
                <a href="#" class="footer-link">FAQ</a>
                <a href="#" class="footer-link">Support</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 TaxiHotel. Tous droits réservés.</p>
        </div>
    </footer>
@endsection
