<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ParkEase - @yield('title', 'Smart Mobility')</title>

    <!-- Theme Initialization -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Leaflet.js CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <link rel="apple-touch-icon" href="/images/favicon-180x180.png">

    <!-- Premium Design System -->
    <link rel="stylesheet" href="/css/parkease.css">
    
    <style>
        .navbar-premium {
            background: var(--glass-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--border-default);
            padding: var(--space-3) 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            transition: all var(--transition-base);
        }

        .navbar-brand {
            font-weight: 800;
            color: var(--text-primary) !important;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: var(--space-2);
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        .brand-typewriter {
            display: inline-flex;
            align-items: center;
            min-width: 140px; /* Prevent layout shift during typing */
        }

        .brand-park {
            color: var(--text-primary) !important;
            transition: color var(--transition-base);
        }

        .brand-ease {
            color: var(--brand-aqua) !important;
            transition: color var(--transition-base);
        }

        .typewriter-cursor {
            color: var(--brand-aqua);
            animation: cursor-blink 0.8s infinite;
            font-weight: 400;
            margin-left: 2px;
        }

        @keyframes cursor-blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            padding: var(--space-2) var(--space-4) !important;
            border-radius: var(--radius-sm);
            transition: all var(--transition-fast);
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text-primary) !important;
            background: var(--bg-hover);
        }

        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-surface);
            border: 1px solid var(--border-default);
            color: var(--text-primary);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .theme-toggle-btn:hover {
            border-color: var(--brand-aqua);
            background: var(--bg-hover);
            transform: scale(1.05);
        }

        [data-theme="dark"] .theme-icon-light { display: none; }
        [data-theme="light"] .theme-icon-dark { display: none; }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: var(--space-2);
            color: var(--text-secondary);
            transition: color var(--transition-fast);
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-surface);
            border: 1px solid var(--border-default);
        }
        
        .notification-bell:hover {
            color: var(--text-primary);
            border-color: var(--border-strong);
        }

        .notification-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            background: #10B981;
            border-radius: var(--radius-full);
            border: 2px solid var(--bg-base);
        }

        /* Notification Dropdown Panel */
        .notification-dropdown {
            width: 360px;
            max-height: 480px;
            overflow-y: auto;
            padding: 0;
            border-radius: var(--radius-dropdown);
            border: 1px solid var(--border-default);
            box-shadow: var(--shadow-lg);
            background: var(--bg-elevated);
            animation: dropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-8px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .notif-header {
            padding: 16px 20px 12px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-subtle);
            transition: background var(--transition-fast);
            cursor: default;
        }

        .notif-item:last-child { border-bottom: none; }

        .notif-item:hover { background: var(--bg-hover); }

        .notif-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
            background: var(--bg-surface);
            border: 1px solid var(--border-default);
            color: var(--text-secondary);
        }

        .notif-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .notif-empty i {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
            opacity: 0.4;
        }

    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Splash Screen -->
    <div id="splash-screen" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: var(--bg-base); z-index: 9999; display: flex; align-items: center; justify-content: center; transition: opacity 0.4s ease;">
        <div style="text-align: center;">
            <img src="/images/favicon.png" style="width: 80px; height: 80px; opacity: 0.8;" alt="Loading">
        </div>
    </div>

    <!-- Premium Navbar -->
    <nav class="navbar navbar-expand-lg navbar-premium" id="mainNavbar">
        <div class="container">
            <div class="d-flex align-items-center">
                <a class="navbar-brand me-5" href="/">
                    <img src="/images/favicon.png" alt="Logo" style="width: 50px; height: 50px;">
                    <span class="brand-typewriter">
                        <span class="brand-park">Park</span><span class="brand-ease">Ease</span>
                    </span>
                </a>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto gap-2">
                        <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->is('search*') ? 'active' : '' }}" href="/search">Find Parking</a></li>
                        @auth
                            <li class="nav-item"><a class="nav-link {{ request()->is('dashboard*') || request()->is('owner/dashboard*') ? 'active' : '' }}" href="{{ auth()->user()->role === 'owner' ? '/owner/dashboard' : '/dashboard' }}">Dashboard</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="d-flex align-items-center gap-4">
                <!-- Theme Toggle -->
                <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Theme">
                    <i class="bi bi-sun theme-icon-light"></i>
                    <i class="bi bi-moon-stars theme-icon-dark"></i>
                </button>

                @auth
                <div class="dropdown d-none d-md-block">
                    <div class="notification-bell" id="notifBell" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Notifications">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="notification-badge" id="notifDot" style="display:none;"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span class="fw-bold" style="font-size: 0.95rem; color: var(--text-primary);">Notifications</span>
                            <span class="text-small" style="color: var(--text-muted);" id="notifCount">—</span>
                        </div>
                        <div id="notif-list">
                            <div class="notif-empty">
                                <i class="bi bi-bell-slash"></i>
                                <div class="fw-medium" style="font-size: 0.9rem;">No notifications</div>
                                <div class="text-small mt-1">You're all caught up!</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth

                <div id="nav-auth-container" class="d-flex align-items-center gap-3">
                    <!-- Auth elements injected via JS -->
                </div>
                
                <button class="navbar-toggler border-0 shadow-none d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="bi bi-list fs-2 text-primary"></i>
                </button>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="py-5 mt-auto" style="background: var(--bg-surface); border-top: 1px solid var(--border-default);">
        <div class="container text-center">
            <h3 class="fw-bold mb-3 d-flex align-items-center justify-content-center gap-2" style="font-size: 1.8rem; letter-spacing: -0.02em;">
                <img src="/images/favicon.png" alt="Logo" style="width: 40px; height: 40px;">
                <span class="brand-typewriter">
                    <span class="brand-park">Park</span><span class="brand-ease">Ease</span>
                </span>
            </h3>
            <p class="text-muted text-small mb-4">Intelligent urban SaaS for smart mobility.</p>
            <div class="d-flex justify-content-center gap-4 mb-4">
                <a href="#" class="text-secondary hover-lift"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="text-secondary hover-lift"><i class="bi bi-linkedin"></i></a>
                <a href="#" class="text-secondary hover-lift"><i class="bi bi-github"></i></a>
            </div>
            <p class="text-muted text-small mb-0">&copy; {{ date('Y') }} ParkEase. Sustainable Mobility.</p>
        </div>
    </footer>

    <!-- Floating Active Session Widget -->
    @auth
        @php
            $activeBookingWidget = \App\Models\Booking::where('user_id', auth()->user()->_id)
                ->where('status', 'confirmed')
                ->where('date', date('Y-m-d'))
                ->with(['parkingLot:id,name,latitude,longitude', 'slot:id,slot_number'])
                ->get()
                ->filter(function($b) {
                    $times = explode('-', $b->time_slot_id);
                    $start = \Carbon\Carbon::parse($b->date . ' ' . $times[0]);
                    $end = \Carbon\Carbon::parse($b->date . ' ' . $times[1]);
                    return now()->between($start, $end);
                })
                ->first();
        @endphp

        @if($activeBookingWidget)
            @php
                $endTimeStrWidget = explode('-', $activeBookingWidget->time_slot_id)[1];
                $endWidget = \Carbon\Carbon::parse($activeBookingWidget->date . ' ' . $endTimeStrWidget);
            @endphp
            <div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1050;">
                <div class="surface-glass p-4 hover-lift" style="width: 320px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--brand-aqua); animation: pulse 2s infinite;"></div>
                        <span class="text-h6 mb-0">Active Session</span>
                    </div>
                    <h5 class="fw-bold mb-3 text-truncate">{{ $activeBookingWidget->parkingLot->name ?? 'Parking' }}</h5>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-small">Slot <span class="fw-bold text-primary">{{ $activeBookingWidget->slot->slot_number ?? '?' }}</span></div>
                        <span class="fw-bold live-timer" data-endtime="{{ $endWidget->toIso8601String() }}" style="font-family: 'Outfit', monospace; font-size: 1.1rem; color: var(--brand-aqua);">--:--:--</span>
                    </div>
                    
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $activeBookingWidget->parkingLot->latitude }},{{ $activeBookingWidget->parkingLot->longitude }}" target="_blank" class="btn btn-secondary w-100">
                        <i class="bi bi-cursor"></i> Directions
                    </a>
                </div>
            </div>
        @endif

        @php
            // Fetch recent bookings for notifications (last 5, upcoming or recent)
            $notifBookings = \App\Models\Booking::where('user_id', auth()->user()->_id)
                ->whereIn('status', ['confirmed', 'cancelled'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->with(['parkingLot:id,name'])
                ->get()
                ->map(fn($b) => [
                    'id'       => (string) $b->_id,
                    'lot'      => $b->parkingLot->name ?? 'Parking Spot',
                    'date'     => $b->date,
                    'slot'     => $b->time_slot_id,
                    'status'   => $b->status,
                    'pay'      => $b->payment_status ?? 'unknown',
                    'price'    => $b->price,
                ]);
        @endphp
        <script>
            window.__notifBookings = @json($notifBookings);
        </script>
    @endauth

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script data-clerk-publishable-key="{{ env('VITE_CLERK_PUBLISHABLE_KEY') }}" src="{{ env('CLERK_JS_URL') }}" type="text/javascript"></script>

    <script>
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }

        document.addEventListener("DOMContentLoaded", async function () {
            await Clerk.load({
                signInForceRedirectUrl: "/dashboard",
                signUpForceRedirectUrl: "/dashboard",
                afterSignInUrl: "/dashboard",
                afterSignUpUrl: "/dashboard"
            });
            const authContainer = document.getElementById('nav-auth-container');

            if (Clerk.user) {
                let isLaravelAuth = false;
                @auth
                    isLaravelAuth = true;
                    const currentRole = '{{ auth()->user()->role }}';
                    const kycStatus = '{{ auth()->user()->kyc_status }}';
                    const hasOnboarded = '{{ auth()->user()->onboarding_completed }}' === '1' || '{{ auth()->user()->onboarding_completed }}' === 'true';
                    
                    if (hasOnboarded) {
                        // Role Switcher Logic - ONLY for Verified Hosts
                        if (kycStatus === 'verified') {
                            const switcher = document.createElement('div');
                            switcher.className = 'nav-pills-premium d-none d-lg-flex me-2';
                            switcher.innerHTML = `
                                <a href="/switch-role" class="nav-link ${currentRole === 'user' ? 'active' : ''}">User</a>
                                <a href="/switch-role" class="nav-link ${currentRole === 'owner' ? 'active' : ''}">Host</a>
                            `;
                            authContainer.appendChild(switcher);
                        }

                        // Profile Dropdown Setup
                        const profile = document.createElement('div');
                        profile.className = 'dropdown';
                        
                        // Construct the dropdown based on host status
                        let hostDropdownOption = '';
                        if (kycStatus === 'verified') {
                            hostDropdownOption = `<a class="dropdown-item-premium" href="/owner/dashboard"><i class="bi bi-buildings"></i> Host Dashboard</a>`;
                        } else {
                            hostDropdownOption = `<a class="dropdown-item-premium" href="/switch-role"><i class="bi bi-shop"></i> Become a Host</a>`;
                        }

                        profile.innerHTML = `
                            <img src="${Clerk.user.imageUrl}" class="rounded-circle border border-2 hover-lift" style="width: 44px; height: 44px; cursor:pointer; border-color: var(--border-default);" data-bs-toggle="dropdown">
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-premium">
                                <div class="px-3 py-3 d-flex align-items-center gap-3">
                                    <img src="${Clerk.user.imageUrl}" class="rounded-circle" style="width: 40px; height: 40px;">
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-truncate" style="font-size: 1rem; color: var(--text-primary); line-height: 1.2;">${Clerk.user.fullName || 'User'}</div>
                                        <div class="text-truncate text-small" style="color: var(--text-muted);">${Clerk.user.primaryEmailAddress.emailAddress}</div>
                                    </div>
                                </div>
                                <div class="dropdown-divider-premium"></div>
                                <div class="px-2 py-1">
                                    <div class="text-h6 px-3 py-2">General</div>
                                    <a class="dropdown-item-premium" href="/dashboard"><i class="bi bi-grid"></i> Dashboard</a>
                                    <a class="dropdown-item-premium" href="/dashboard?tab=transactions"><i class="bi bi-receipt"></i> Transactions</a>
                                </div>
                                <div class="dropdown-divider-premium"></div>
                                <div class="px-2 py-1">
                                    <div class="text-h6 px-3 py-2">Hosting</div>
                                    ${hostDropdownOption}
                                </div>
                                <div class="dropdown-divider-premium"></div>
                                <div class="px-2 py-1">
                                    <a class="dropdown-item-premium" href="/settings"><i class="bi bi-gear"></i> Settings</a>
                                    <a class="dropdown-item-premium text-danger mt-1" href="javascript:void(0)" onclick="handleGlobalLogout()"><i class="bi bi-box-arrow-right text-danger"></i> Sign Out</a>
                                </div>
                            </div>
                        `;
                        authContainer.appendChild(profile);
                    }
                @endauth

                // Safe Auth Redirect Logic: If authenticated on both, prevent viewing login/register
                const currentPath = window.location.pathname;
                if (isLaravelAuth && (currentPath === '/login' || currentPath === '/register')) {
                    console.log("[Auth] User authenticated on Clerk & Laravel. Redirecting to /dashboard.");
                    window.location.href = '/dashboard';
                    return;
                }

                // If Clerk says logged in, but Laravel is not, force sync by clearing the flag
                if (!isLaravelAuth) {
                    sessionStorage.removeItem('clerk_synced');
                }

                // Sync Logic
                if (!sessionStorage.getItem('clerk_synced')) {
                    console.log("[Auth] Sync needed. Fetching Clerk session token...");
                    const token = await Clerk.session.getToken();
                    console.log("[Auth] Clerk token fetched. Syncing session with Laravel backend...");
                    try {
                        const response = await fetch('/api/auth/clerk-sync', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                clerk_id: Clerk.user.id,
                                email: Clerk.user.primaryEmailAddress.emailAddress,
                                first_name: Clerk.user.firstName,
                                last_name: Clerk.user.lastName
                            })
                        });
                        if (response.ok) {
                            console.log("[Auth] Session synced successfully. Reloading...");
                            sessionStorage.setItem('clerk_synced', 'true');
                            window.location.reload();
                            return; // PREVENT execution of renderClerkComponent before reload completes
                        } else {
                            console.error("[Auth] Session sync failed on backend with status: " + response.status);
                            let errMessage = "Authentication failed. Please try again.";
                            try {
                                const errData = await response.json();
                                errMessage = errData.error || errMessage;
                            } catch (eJson) {
                                try { errMessage = await response.text(); } catch (eText) {}
                            }
                            
                            const errorContainer = document.getElementById('sync-error');
                            const loadingDiv = document.getElementById('sign-in-loading') || document.getElementById('sign-up-loading');
                            
                            if (errorContainer) {
                                errorContainer.innerHTML = `
                                    <div class="alert alert-danger border border-danger-subtle p-3 rounded-3 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; border-color: rgba(239, 68, 68, 0.2) !important;">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <strong>Authentication Error:</strong> ${errMessage}
                                    </div>
                                `;
                                errorContainer.style.display = 'block';
                            } else {
                                alert("Authentication Error: " + errMessage);
                            }
                            
                            if (loadingDiv) loadingDiv.style.display = 'none';
                            
                            // If sync fails on backend, sign out of Clerk so they can start fresh
                            try {
                                await Clerk.signOut();
                            } catch (eSignOut) {}
                            
                            if (typeof window.renderClerkComponent === 'function') {
                                window.renderClerkComponent();
                            }
                            return;
                        }
                    } catch (e) { 
                        console.error("[Auth] Connection error during session sync: ", e); 
                        const errorContainer = document.getElementById('sync-error');
                        const loadingDiv = document.getElementById('sign-in-loading') || document.getElementById('sign-up-loading');
                        
                        if (errorContainer) {
                            errorContainer.innerHTML = `
                                <div class="alert alert-danger border border-danger-subtle p-3 rounded-3 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #EF4444; border-color: rgba(239, 68, 68, 0.2) !important;">
                                    <i class="bi bi-wifi-off me-2"></i>
                                    <strong>Connection Error:</strong> Failed to connect to authentication server. Please check your network connection.
                                </div>
                            `;
                            errorContainer.style.display = 'block';
                        }
                        
                        if (loadingDiv) loadingDiv.style.display = 'none';
                        
                        try {
                            await Clerk.signOut();
                        } catch (eSignOut) {}
                        
                        if (typeof window.renderClerkComponent === 'function') {
                            window.renderClerkComponent();
                        }
                    }
                }
            } else {
                authContainer.innerHTML = `
                    <a href="/login" class="nav-link fw-bold px-0 me-3">Log in</a>
                    <a href="/register" class="btn btn-brand">Sign up</a>
                `;
            }
            
            if (typeof window.renderClerkComponent === 'function') {
                window.renderClerkComponent();
            }

            setTimeout(() => {
                const splash = document.getElementById('splash-screen');
                if (splash) {
                    splash.style.opacity = '0';
                    setTimeout(() => splash.style.display = 'none', 400);
                }
            }, 400);

            // Timer logic
            if (document.querySelector('.live-timer')) {
                setInterval(() => {
                    document.querySelectorAll('.live-timer').forEach(el => {
                        const endTime = new Date(el.getAttribute('data-endtime')).getTime();
                        const now = new Date().getTime();
                        const distance = endTime - now;
                        if (distance < 0) { el.innerHTML = "EXPIRED"; return; }
                        const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const s = Math.floor((distance % (1000 * 60)) / 1000);
                        el.innerHTML = (h < 10 ? "0"+h : h) + "h " + (m < 10 ? "0"+m : m) + "m " + (s < 10 ? "0"+s : s) + "s";
                    });
                }, 1000);
            }
        });

        window.handleGlobalLogout = async function() {
            await Clerk.signOut();
            fetch('/api/logout', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).finally(() => window.location.href = '/');
        }

        // ── Notification Bell ──────────────────────────────────────────────
        function loadNotifications() {
            const list    = document.getElementById('notif-list');
            const dot     = document.getElementById('notifDot');
            const counter = document.getElementById('notifCount');
            if (!list) return;

            const bookings = window.__notifBookings || [];

            if (!bookings.length) {
                counter.textContent = 'Empty';
                return; // keep empty state
            }

            counter.textContent = bookings.length + ' recent';
            dot.style.display = 'block'; // show badge

            const statusMeta = {
                confirmed:  { icon: 'bi-calendar-check',  label: 'Booking Confirmed' },
                cancelled:  { icon: 'bi-calendar-x',      label: 'Booking Cancelled'  },
            };

            list.innerHTML = bookings.map(b => {
                const meta   = statusMeta[b.status] || { icon: 'bi-calendar', label: 'Booking' };
                const isCancel = b.status === 'cancelled';

                // Format date nicely
                let dateStr = b.date;
                try { dateStr = new Date(b.date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }); } catch(e) {}

                // Format slot
                const slotStr = b.slot || '';

                return `
                <div class="notif-item">
                    <div class="notif-icon">
                        <i class="bi ${meta.icon}"></i>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size: 0.875rem; color: var(--text-primary);">${b.lot}</div>
                        <div class="text-small text-truncate" style="color: var(--text-secondary);">${meta.label} &middot; ${dateStr}</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span style="font-size: 0.78rem; color: var(--text-muted);">${slotStr}</span>
                            <span style="
                                font-size: 0.72rem;
                                font-weight: 600;
                                padding: 1px 7px;
                                border-radius: 99px;
                                background: ${isCancel ? 'rgba(239,68,68,0.1)' : 'rgba(16,185,129,0.1)'};
                                color: ${isCancel ? '#EF4444' : '#10B981'};
                            ">${b.status.charAt(0).toUpperCase() + b.status.slice(1)}</span>
                        </div>
                    </div>
                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); white-space: nowrap;">₹${b.price || '—'}</div>
                </div>`;
            }).join('');

            // Footer link
            list.innerHTML += `
                <a href="/dashboard" class="d-block text-center py-3" style="font-size: 0.85rem; font-weight: 600; color: var(--brand-aqua); text-decoration: none; border-top: 1px solid var(--border-subtle);">
                    View all in Dashboard &rarr;
                </a>`;
        }

        // Call after DOM ready (data is already embedded in page)
        document.addEventListener('DOMContentLoaded', () => {
            loadNotifications();

            // ── Brand Typewriter Typing Loop ───────────────────────────────
            const targets = document.querySelectorAll('.brand-typewriter');
            targets.forEach(target => {
                const textPark = "Park";
                const textEase = "Ease";
                let isDeleting = false;
                let charIndex = textPark.length + textEase.length; // Start fully typed to prevent layout shift
                let pauseTimer = null;

                function type() {
                    const totalLength = textPark.length + textEase.length;
                    
                    let currentPark = "";
                    let currentEase = "";

                    if (charIndex <= textPark.length) {
                        currentPark = textPark.substring(0, charIndex);
                    } else {
                        currentPark = textPark;
                        currentEase = textEase.substring(0, charIndex - textPark.length);
                    }

                    target.innerHTML = `
                        <span class="brand-park">${currentPark}</span><span class="brand-ease">${currentEase}</span><span class="typewriter-cursor">|</span>
                    `;

                    let delay = isDeleting ? 75 : 150;

                    if (!isDeleting && charIndex === totalLength) {
                        isDeleting = true;
                        delay = 3500; // Keep fully typed visible for 3.5 seconds
                    } else if (isDeleting && charIndex === 0) {
                        isDeleting = false;
                        delay = 500;  // Pause at empty for 0.5s before re-typing
                    }

                    charIndex = isDeleting ? charIndex - 1 : charIndex + 1;
                    pauseTimer = setTimeout(type, delay);
                }

                // Initial trigger
                setTimeout(type, 1000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
