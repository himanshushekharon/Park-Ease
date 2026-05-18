@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
<style>
    :root {
        --settings-bg: rgba(255, 255, 255, 0.7);
        --sidebar-bg: rgba(255, 255, 255, 0.4);
        --active-tab-bg: #000000;
        --active-tab-text: #ffffff;
    }

    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
    }

    .settings-container {
        max-width: 1100px;
        margin: 40px auto;
        display: flex;
        gap: 30px;
        animation: fadeIn 0.5s ease;
    }

    .settings-sidebar {
        width: 280px;
        background: var(--sidebar-bg);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        padding: 30px 20px;
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .settings-content {
        flex: 1;
        background: var(--settings-bg);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 32px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        min-height: 600px;
    }

    .nav-link-custom {
        display: flex;
        align-items: center;
        padding: 14px 20px;
        color: #495057;
        text-decoration: none;
        border-radius: 16px;
        margin-bottom: 8px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link-custom i {
        margin-right: 15px;
        font-size: 1.2rem;
        opacity: 0.7;
    }

    .nav-link-custom:hover {
        background: rgba(0, 0, 0, 0.05);
        transform: translateX(5px);
    }

    .nav-link-custom.active {
        background: var(--active-tab-bg);
        color: var(--active-tab-text);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .nav-link-custom.active i {
        opacity: 1;
    }

    .tab-pane {
        display: none;
        animation: slideUp 0.4s ease;
    }

    .tab-pane.active {
        display: block;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .clerk-security-wrapper {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #eee;
    }

    /* Custom Scrollbar for Settings */
    .settings-content::-webkit-scrollbar {
        width: 6px;
    }
    .settings-content::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .btn-logout {
        margin-top: 40px;
        color: #dc3545;
        border: 1px solid #dc3545;
        background: transparent;
        width: 100%;
        padding: 12px;
        border-radius: 16px;
        font-weight: 700;
        transition: all 0.3s;
    }

    .btn-logout:hover {
        background: #dc3545;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="settings-container">
        <!-- Sidebar -->
        <div class="settings-sidebar shadow-sm">
            <h5 class="fw-bold mb-4 px-2">Settings</h5>
            <nav>
                <a href="javascript:void(0)" class="nav-link-custom active" data-tab="profile">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
                <a href="javascript:void(0)" class="nav-link-custom" data-tab="security">
                    <i class="bi bi-shield-lock-fill"></i> Security
                </a>
                <a href="javascript:void(0)" class="nav-link-custom" data-tab="notifications">
                    <i class="bi bi-bell-fill"></i> Notifications
                </a>
                <a href="javascript:void(0)" class="nav-link-custom" data-tab="appearance">
                    <i class="bi bi-palette-fill"></i> Appearance
                </a>
                <a href="javascript:void(0)" class="nav-link-custom" data-tab="billing">
                    <i class="bi bi-credit-card-fill"></i> Billing
                </a>
                <a href="javascript:void(0)" class="nav-link-custom" data-tab="support">
                    <i class="bi bi-headset"></i> Support
                </a>
            </nav>

            <button class="btn btn-logout" onclick="handleLogout()">
                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
            </button>
        </div>

        <!-- Content Area -->
        <div class="settings-content">
            <!-- Profile Tab -->
            <div id="profile" class="tab-pane active">
                <h3 class="fw-bold mb-1">Public Profile</h3>
                <p class="text-muted mb-5">Manage your personal information and how others see you.</p>

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Full Name</label>
                            <input type="text" class="form-control form-control-lg bg-white border-0 shadow-sm" value="{{ auth()->user()->name }}" id="profile_name">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Email Address</label>
                            <input type="email" class="form-control form-control-lg bg-light border-0" value="{{ auth()->user()->email }}" disabled>
                            <div class="form-text">Email can be changed in the Security tab via Clerk.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Role</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" value="{{ ucfirst(auth()->user()->role) }}" disabled>
                        </div>
                        
                        <button class="btn btn-dark px-5 py-3 rounded-4 fw-bold mt-3 shadow-sm">Save Changes</button>
                    </div>
                    <div class="col-md-4 text-center">
                        <label class="form-label fw-bold small text-muted text-uppercase d-block mb-3">Profile Picture</label>
                        <img id="profile_image_preview" src="" class="rounded-circle mb-3 shadow" style="width: 150px; height: 150px; object-fit: cover; border: 5px solid white;">
                        <p class="small text-muted">Synced with Clerk Profile</p>
                    </div>
                </div>
            </div>

            <!-- Security Tab -->
            <div id="security" class="tab-pane">
                <h3 class="fw-bold mb-1">Security & Access</h3>
                <p class="text-muted mb-5">Manage passwords, sessions, and connected accounts.</p>
                
                <div id="clerk-user-profile" class="clerk-security-wrapper">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Loading Security Management...</p>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications" class="tab-pane">
                <h3 class="fw-bold mb-1">Notifications</h3>
                <p class="text-muted mb-5">Choose how you receive alerts and updates.</p>
                <div class="alert alert-light border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold">Email Notifications</h6>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">Booking Confirmations</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">System Updates</label>
                    </div>
                </div>
            </div>

            <!-- Appearance Tab -->
            <div id="appearance" class="tab-pane">
                <h3 class="fw-bold mb-1">Appearance</h3>
                <p class="text-muted mb-5">Customize the look and feel of ParkEase.</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-4 border rounded-4 text-center shadow-sm bg-white cursor-pointer" onclick="alert('Light mode is current default')">
                            <i class="bi bi-sun-fill fs-2 mb-2"></i>
                            <div class="fw-bold">Light</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 border rounded-4 text-center bg-light cursor-pointer" onclick="alert('Dark mode coming soon!')">
                            <i class="bi bi-moon-stars-fill fs-2 mb-2"></i>
                            <div class="fw-bold">Dark</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing Tab -->
            <div id="billing" class="tab-pane">
                <h3 class="fw-bold mb-1">Billing & Transactions</h3>
                <p class="text-muted mb-5">View your payment history and manage saved cards.</p>
                <div class="text-center py-5 bg-light rounded-4">
                    <i class="bi bi-credit-card fs-1 text-muted mb-3"></i>
                    <h6>No billing records yet</h6>
                    <p class="small text-muted">Complete your first booking to see records here.</p>
                </div>
            </div>

            <!-- Support Tab -->
            <div id="support" class="tab-pane">
                <h3 class="fw-bold mb-1">Help & Support</h3>
                <p class="text-muted mb-5">Get help with your bookings or report an issue.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-4 border rounded-4 shadow-sm bg-white h-100">
                            <h6 class="fw-bold"><i class="bi bi-chat-dots-fill text-primary me-2"></i> Live Chat</h6>
                            <p class="small text-muted">Talk to our support team instantly.</p>
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-4">Start Chat</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 border rounded-4 shadow-sm bg-white h-100">
                            <h6 class="fw-bold"><i class="bi bi-envelope-fill text-primary me-2"></i> Email Support</h6>
                            <p class="small text-muted">Send us an email at support@parkease.com</p>
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-4">Send Email</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        await Clerk.load();

        if (Clerk.user) {
            document.getElementById('profile_image_preview').src = Clerk.user.imageUrl;
        }

        // Tab Switching Logic
        const tabs = document.querySelectorAll('.nav-link-custom');
        const panes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.getAttribute('data-tab');

                tabs.forEach(t => t.classList.remove('active'));
                panes.forEach(p => p.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(target).classList.add('active');

                // If security tab, mount Clerk UserProfile if not already mounted
                if (target === 'security') {
                    mountClerkSecurity();
                }
            });
        });

        let clerkProfileMounted = false;
        function mountClerkSecurity() {
            if (clerkProfileMounted) return;
            
            const container = document.getElementById('clerk-user-profile');
            container.innerHTML = ''; // Clear loading state
            
            Clerk.mountUserProfile(container, {
                // We want a native feel, so we can hide some elements if needed
                // or just let it render its full dashboard inside our container.
                routing: 'path',
                path: '/settings'
            });
            clerkProfileMounted = true;
        }
    });

    async function handleLogout() {
        if (confirm('Are you sure you want to sign out?')) {
            await Clerk.signOut();
            // Also call Laravel logout to clear session
            fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).finally(() => {
                window.location.href = '/';
            });
        }
    }
</script>
@endpush
