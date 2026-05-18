@extends('layouts.app')
@section('title', 'Sign In to ParkEase')

@section('content')
<style>
    /* Premium Auth Layout */
    .auth-wrapper {
        display: flex;
        min-height: calc(100vh - 80px); /* Adjust based on navbar */
        background: var(--bg-base);
        position: relative;
        overflow: hidden;
    }

    /* Cinematic Glowing Orbs */
    .orb-primary {
        position: absolute;
        top: -10%;
        left: -5%;
        width: 50vw;
        height: 50vw;
        background: radial-gradient(circle, rgba(46, 196, 182, 0.04) 0%, rgba(14, 94, 111, 0) 70%);
        border-radius: 50%;
        z-index: 0;
        pointer-events: none;
        animation: float 10s ease-in-out infinite alternate;
    }
    
    .orb-secondary {
        position: absolute;
        bottom: -20%;
        right: -10%;
        width: 60vw;
        height: 60vw;
        background: radial-gradient(circle, rgba(14, 94, 111, 0.06) 0%, rgba(4, 17, 22, 0) 70%);
        border-radius: 50%;
        z-index: 0;
        pointer-events: none;
        animation: float 12s ease-in-out infinite alternate-reverse;
    }

    @keyframes float {
        0% { transform: translateY(0) scale(1); }
        100% { transform: translateY(-30px) scale(1.05); }
    }

    /* Split Screen Grid */
    .auth-grid {
        display: grid;
        grid-template-columns: 1fr;
        width: 100%;
        z-index: 1;
    }

    @media (min-width: 992px) {
        .auth-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .auth-left {
        display: none;
        flex-direction: column;
        justify-content: center;
        padding: var(--space-12) var(--space-16);
        position: relative;
    }

    @media (min-width: 992px) {
        .auth-left { display: flex; }
    }

    .auth-right {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--space-6);
        position: relative;
    }

    /* Glassmorphism Container for Auth */
    .auth-glass-container {
        width: 100%;
        max-width: 500px;
        background: var(--glass-bg);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: var(--space-4);
        box-shadow: var(--shadow-lg);
        animation: fadeUpIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        position: relative;
    }

    /* Removed inner glow for classy look */
    .auth-glass-container::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        border-radius: 24px;
        pointer-events: none;
    }

    @keyframes fadeUpIn {
        0% { opacity: 0; transform: translateY(40px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* Hero text - no glow, clean typography */
    .hero-text-glow {
        line-height: 1.1;
    }

    /* Feature Badge */
    .feature-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: var(--bg-surface);
        border: 1px solid var(--border-default);
        border-radius: 99px;
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.85rem;
        margin-bottom: var(--space-6);
    }
    
    .feature-item-icon {
        width: 48px; 
        height: 48px; 
        background: var(--bg-elevated);
        border: 1px solid var(--border-default);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-secondary);
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* Override Clerk default spinner/loading if needed */
    .clerk-loading-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
    }
</style>

<div class="auth-wrapper">
    <div class="orb-primary"></div>
    <div class="orb-secondary"></div>

    <div class="auth-grid">
        <!-- Left Side: Futuristic Marketing -->
        <div class="auth-left">
            <div style="max-width: 520px;">
                <div class="feature-badge">
                    <i class="bi bi-shield-check"></i> Secure Authentication
                </div>
                
                <h1 class="text-h1 mb-4 hero-text-glow">
                    Welcome Back to <br>
                    <span class="text-gradient">Smart Mobility.</span>
                </h1>
                
                <p class="text-lead mb-5" style="color: var(--text-secondary);">
                    Access your personalized dashboard. Track your parking sessions, manage your hosted spots, and experience seamless urban navigation.
                </p>
                
                <div class="d-flex flex-column gap-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="feature-item-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-h6 text-primary">Lightning Fast Access</h4>
                            <p class="mb-0 text-small">Sign in securely with one click using Google or Email.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="feature-item-icon">
                            <i class="bi bi-p-circle"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold text-h6 text-primary">Your Dashboard Awaits</h4>
                            <p class="mb-0 text-small">View live analytics, earnings, and active sessions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Glassmorphism Auth Container -->
        <div class="auth-right">
            <div class="auth-glass-container">
                <div id="sign-in-loading" class="clerk-loading-container">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="sign-in"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function renderClerkComponent() {
        const loadingDiv = document.getElementById('sign-in-loading');
        if (loadingDiv) loadingDiv.style.display = 'none';

        if (Clerk.user) {
            window.location.href = '/dashboard';
        } else {
            const signInDiv = document.getElementById('sign-in');
            
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            
            // Premium Clerk Appearance Configuration
            const clerkAppearance = {
                layout: {
                    socialButtonsPlacement: 'bottom',
                    socialButtonsVariant: 'blockButton',
                    logoImageUrl: '/images/favicon.png', // Ensure this uses your new logo
                    logoPlacement: 'inside'
                },
                variables: {
                    colorPrimary: '#2EC4B6',
                    colorBackground: 'transparent',
                    colorText: isDark ? '#f8fafc' : '#0f172a',
                    colorInputText: isDark ? '#f8fafc' : '#0f172a',
                    colorInputBackground: isDark ? '#1e293b' : '#ffffff',
                    colorTextSecondary: isDark ? '#94a3b8' : '#64748b',
                    fontFamily: "'Outfit', sans-serif",
                    borderRadius: '12px',
                    colorDanger: '#EF4444',
                    colorSuccess: '#10B981'
                },
                elements: {
                    rootBox: {
                        width: '100%'
                    },
                    card: {
                        boxShadow: 'none',
                        background: 'transparent',
                        padding: '1rem'
                    },
                    headerTitle: {
                        fontSize: '1.75rem',
                        fontWeight: '800',
                        letterSpacing: '-0.02em',
                        color: 'var(--text-primary)',
                        textAlign: 'left'
                    },
                    headerSubtitle: {
                        color: 'var(--text-secondary)',
                        fontSize: '1rem',
                        textAlign: 'left'
                    },
                    formButtonPrimary: {
                        background: '#0E5E6F',
                        border: 'none',
                        boxShadow: 'none',
                        transition: 'all 0.2s ease',
                        textTransform: 'uppercase',
                        letterSpacing: '0.5px',
                        fontWeight: '600',
                        fontSize: '0.9rem',
                        padding: '12px 20px',
                        color: '#FFFFFF'
                    },
                    formFieldInput: {
                        border: '1px solid var(--border-strong)',
                        backgroundColor: 'var(--bg-surface)',
                        transition: 'all 0.3s ease',
                        color: 'var(--text-primary)',
                        padding: '12px 16px',
                        fontSize: '1rem'
                    },
                    formFieldLabel: {
                        color: 'var(--text-secondary)',
                        fontWeight: '500'
                    },
                    dividerLine: {
                        background: 'var(--border-strong)'
                    },
                    dividerText: {
                        color: 'var(--text-muted)',
                        fontSize: '0.875rem'
                    },
                    socialButtonsBlockButton: {
                        border: '1px solid var(--border-strong)',
                        backgroundColor: 'var(--bg-surface)',
                        color: 'var(--text-primary)',
                        transition: 'all 0.3s ease'
                    },
                    socialButtonsBlockButtonText: {
                        fontWeight: '600'
                    },
                    footerActionText: {
                        color: 'var(--text-secondary)'
                    },
                    footerActionLink: {
                        color: 'var(--brand-aqua)',
                        fontWeight: '600'
                    },
                    identityPreview: {
                        background: 'var(--bg-surface)',
                        border: '1px solid var(--border-strong)'
                    },
                    identityPreviewText: {
                        color: 'var(--text-primary)'
                    },
                    identityPreviewEditButtonIcon: {
                        color: 'var(--brand-aqua)'
                    }
                }
            };

            Clerk.mountSignIn(signInDiv, {
                appearance: clerkAppearance,
                signInForceRedirectUrl: "/dashboard",
                signUpForceRedirectUrl: "/dashboard",
                afterSignInUrl: "/dashboard",
                afterSignUpUrl: "/dashboard"
            });
        }
    }
</script>
@endpush
