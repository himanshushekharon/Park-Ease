@extends('layouts.app')

@section('title', 'Intelligent Urban Parking')

@push('styles')
<!-- Lottie Player for Local Animation -->
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style>
    .hero-section {
        padding: var(--space-16) 0 var(--space-12);
        background: radial-gradient(circle at top right, rgba(46, 196, 182, 0.04), transparent 40%),
                    radial-gradient(circle at bottom left, rgba(56, 189, 248, 0.02), transparent 40%);
    }

    .trust-badge {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-1) var(--space-4);
        background: var(--bg-surface);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius-full);
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-primary);
        margin-bottom: var(--space-8);
        box-shadow: var(--shadow-sm);
    }

    .trust-badge i {
        color: var(--brand-aqua);
    }

    .search-block {
        padding: var(--space-6);
        max-width: 580px;
    }

    .stat-block {
        text-align: center;
        padding: var(--space-6);
    }

    .lottie-container {
        position: relative;
        perspective: 1000px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Explanation Sections */
    .app-section {
        padding: var(--space-20) 0;
        border-top: 1px solid var(--border-subtle);
    }

    .app-section.alt-bg {
        background: var(--bg-surface);
    }

    .app-screenshot-container {
        border-radius: var(--radius-card);
        padding: var(--space-4);
        background: var(--gradient-primary);
        box-shadow: var(--shadow-lg);
        transform: rotateY(-5deg) rotateX(5deg);
        transition: transform var(--transition-smooth);
    }
    
    .app-screenshot-container:hover {
        transform: rotateY(0deg) rotateX(0deg);
    }

    .app-screenshot-container.reverse {
        transform: rotateY(5deg) rotateX(5deg);
        background: var(--gradient-secondary);
    }
    
    .app-screenshot-container.reverse:hover {
        transform: rotateY(0deg) rotateX(0deg);
    }

    .app-screenshot {
        width: 100%;
        border-radius: var(--radius-sm);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .section-badge {
        display: inline-block;
        padding: var(--space-1) var(--space-3);
        background: var(--bg-elevated);
        color: var(--text-secondary);
        border: 1px solid var(--border-default);
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: var(--space-3);
    }

    @media (max-width: 991px) {
        .hero-section { text-align: center; padding: var(--space-10) 0; }
        .search-block { margin: 0 auto; }
        .trust-badge { margin: 0 auto var(--space-6); }
        .app-screenshot-container { transform: none !important; margin-top: var(--space-8); }
    }

    .hero-lottie-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
        margin-top: -50px; /* Pull up to balance whitespace in JSON */
    }

    lottie-player {
        filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.12));
    }
</style>
@endpush

@section('content')
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="trust-badge">
                    <i class="bi bi-shield-check"></i>
                    Eco-Tech Smart Mobility Infrastructure
                </div>
                
                <h1 class="text-h1 mb-4">Intelligent Urban<br><span class="text-gradient">Parking Solutions.</span></h1>
                <p class="text-lead mb-5" style="max-width: 520px;">Experience the future of seamless urban mobility with India's most advanced cost-effective parking management ecosystem.</p>
                
                <div class="surface-glass search-block">
                    @auth
                        <div class="d-flex flex-column gap-3 mb-4 text-center">
                            <h4 class="text-h4">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h4>
                            <p class="text-secondary small">Manage your sessions, track earnings, or find a new spot instantly.</p>
                            
                            <a href="{{ auth()->user()->role === 'owner' ? '/owner/dashboard' : '/dashboard' }}" class="btn btn-brand w-100 py-3 text-h5 mb-2">
                                <i class="bi bi-grid-fill"></i> Go to Dashboard
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-3 opacity-50 mb-3">
                            <hr class="flex-grow-1" style="border-color: var(--text-muted);">
                            <span class="text-small fw-bold text-uppercase">OR</span>
                            <hr class="flex-grow-1" style="border-color: var(--text-muted);">
                        </div>
                    @endauth

                    <form action="/search" method="GET" id="searchForm">
                        <div class="mb-4">
                            <label class="text-h6 mb-3 d-block">Find a secure spot</label>
                            <div class="d-flex gap-2">
                                <div class="position-relative flex-grow-1">
                                    <i class="bi bi-geo-alt position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" class="form-control-premium ps-5" name="pincode" placeholder="Enter Pincode (e.g. 110001)" id="pincodeInput">
                                </div>
                                <button class="btn btn-brand px-4" type="submit">Search</button>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3 opacity-50 mb-3">
                            <hr class="flex-grow-1" style="border-color: var(--text-muted);">
                            <span class="text-small fw-bold text-uppercase">OR</span>
                            <hr class="flex-grow-1" style="border-color: var(--text-muted);">
                        </div>
                        
                        <button type="button" id="gpsBtn" class="btn btn-secondary w-100 py-3">
                            <i class="bi bi-cursor-fill text-primary"></i>
                            Use Current Location
                        </button>
                        
                        <input type="hidden" name="lat" id="latInput">
                        <input type="hidden" name="lng" id="lngInput">
                    </form>
                </div>
            </div>
            
            <div class="col-lg-6 text-center">
                <div class="hero-lottie-wrapper">
                    <lottie-player 
                        src="/Animations/Delivery Service-Delivery man.json" 
                        background="transparent" 
                        speed="1" 
                        style="width: 100%; max-width: 850px; height: 700px; margin: 0 auto; transform: scale(1.1);"
                        loop 
                        autoplay>
                    </lottie-player>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: For Parkers -->
<div class="app-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-2 order-lg-1">
                <div class="app-screenshot-container">
                    <img src="/images/user_app_screenshot.png" alt="User Parking App Interface" class="app-screenshot">
                </div>
            </div>
            <div class="col-lg-5 offset-lg-1 order-1 order-lg-2 mb-5 mb-lg-0">
                <span class="section-badge">For Parkers</span>
                <h2 class="text-h2 mb-4">Find & Book Instantly.</h2>
                <p class="text-lead mb-4">No more driving in circles. Our smart mobility app gives you real-time visibility into available parking hubs across the city.</p>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-5">
                    <li class="d-flex align-items-center gap-3"><i class="bi bi-check-circle-fill text-primary fs-5"></i> <span class="text-secondary fw-medium">Live availability map</span></li>
                    <li class="d-flex align-items-center gap-3"><i class="bi bi-check-circle-fill text-primary fs-5"></i> <span class="text-secondary fw-medium">One-tap contactless reservations</span></li>
                    <li class="d-flex align-items-center gap-3"><i class="bi bi-check-circle-fill text-primary fs-5"></i> <span class="text-secondary fw-medium">Dynamic, cost-effective pricing</span></li>
                </ul>
                <a href="/search" class="btn btn-primary px-5 py-3"><i class="bi bi-geo-fill"></i> Explore Map</a>
            </div>
        </div>
    </div>
</div>

<!-- Section: For Hosts -->
<div class="app-section alt-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <span class="section-badge">For Hosts</span>
                <h2 class="text-h2 mb-4">Monetize Your Space.</h2>
                <p class="text-lead mb-4">Turn your empty driveway, commercial lot, or unused garage into a steady stream of passive income with our powerful host tools.</p>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-5">
                    <li class="d-flex align-items-center gap-3"><i class="bi bi-graph-up-arrow text-primary fs-5"></i> <span class="text-secondary fw-medium">Real-time revenue analytics</span></li>
                    <li class="d-flex align-items-center gap-3"><i class="bi bi-shield-lock-fill text-primary fs-5"></i> <span class="text-secondary fw-medium">Secure digital ledger</span></li>
                    <li class="d-flex align-items-center gap-3"><i class="bi bi-sliders text-primary fs-5"></i> <span class="text-secondary fw-medium">Total control over pricing & hours</span></li>
                </ul>
                <a href="/register" class="btn btn-secondary px-5 py-3">Become a Host</a>
            </div>
            <div class="col-lg-6 offset-lg-1">
                <div class="app-screenshot-container reverse">
                    <img src="/images/host_app_screenshot.png" alt="Host Analytics Dashboard Interface" class="app-screenshot">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5 my-5 border-top" style="border-color: var(--border-default) !important;">
    <div class="row g-4 justify-content-center text-center">
        <div class="col-6 col-md-3 stat-block">
            <div class="text-h2 mb-1 text-primary">500+</div>
            <div class="text-h6">Smart Hubs</div>
        </div>
        <div class="col-6 col-md-3 stat-block">
            <div class="text-h2 mb-1 text-primary">12k+</div>
            <div class="text-h6">Happy Parkers</div>
        </div>
        <div class="col-6 col-md-3 stat-block">
            <div class="text-h2 mb-1 text-primary">₹2M+</div>
            <div class="text-h6">User Savings</div>
        </div>
        <div class="col-6 col-md-3 stat-block">
            <div class="text-h2 mb-1 text-primary">4.9/5</div>
            <div class="text-h6">Trust Score</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
    function useIpFallback(btn) {
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Locating via IP...';
        fetch('https://ipapi.co/json/')
            .then(res => res.json())
            .then(data => {
                if(data.latitude && data.longitude) {
                    document.getElementById('latInput').value = data.latitude;
                    document.getElementById('lngInput').value = data.longitude;
                    document.getElementById('searchForm').submit();
                } else { throw new Error("Location failed"); }
            })
            .catch(err => {
                alert('Location detection failed. Please enter a pincode.');
                btn.innerHTML = '<i class="bi bi-cursor-fill text-primary"></i> Use Current Location';
            });
    }

    document.getElementById('gpsBtn').addEventListener('click', function() {
        const btn = this;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Precision Tuning...';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                p => {
                    document.getElementById('latInput').value = p.coords.latitude;
                    document.getElementById('lngInput').value = p.coords.longitude;
                    document.getElementById('searchForm').submit();
                },
                e => useIpFallback(btn),
                { timeout: 8000 }
            );
        } else { useIpFallback(btn); }
    });
</script>
@endpush
