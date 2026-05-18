@extends('layouts.app')

@section('title', 'System Entry - Select Protocol')

@section('content')
<div class="container py-5 my-5">
    <div class="row justify-content-center text-center mb-5">
        <div class="col-md-8">
            <div class="small fw-bold text-uppercase text-gradient mb-3" style="letter-spacing: 2px;">Onboarding Phase</div>
            <h1 class="greeting-text mb-3">Welcome to the Hub, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h1>
            <p class="text-secondary fs-5">Initialize your account profile. Choose your primary interaction mode.</p>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        <!-- User Role Selection -->
        <div class="col-md-5">
            <div class="card-premium h-100 p-5 text-center role-card-refined" id="card-user" onclick="selectRole('user')">
                <div class="feature-icon-box mx-auto mb-4" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="bi bi-car-front"></i>
                </div>
                <h3 class="fw-bold mb-2">Driver Protocol</h3>
                <p class="text-secondary">Find and secure premium parking slots across the urban grid instantly.</p>
            </div>
        </div>

        <!-- Owner Role Selection -->
        <div class="col-md-5">
            <div class="card-premium h-100 p-5 text-center role-card-refined" id="card-owner" onclick="selectRole('owner')">
                <div class="feature-icon-box mx-auto mb-4" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="bi bi-building-up"></i>
                </div>
                <h3 class="fw-bold mb-2">Host Protocol</h3>
                <p class="text-secondary">Monetize your infrastructure and manage parking assets with our smart tools.</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <button id="continueBtn" class="btn-premium btn-premium-primary w-100 justify-content-center py-3 fs-5" disabled>
                Initialize Account
            </button>
        </div>
    </div>
</div>

<style>
    .role-card-refined {
        cursor: pointer;
        border: 2px solid var(--border-color);
        transition: all var(--transition-base);
    }
    .role-card-refined:hover {
        border-color: var(--brand-aqua);
        background: var(--bg-elevated);
    }
    .role-card-refined.selected {
        border-color: var(--brand-aqua);
        background: var(--bg-elevated);
        box-shadow: 0 0 0 4px rgba(46, 196, 182, 0.1);
    }
    .role-card-refined.selected .feature-icon-box {
        background: var(--brand-aqua);
        color: white;
    }
</style>
@endsection

@push('scripts')
<script>
    let selectedRole = null;

    function selectRole(role) {
        selectedRole = role;
        document.querySelectorAll('.role-card-refined').forEach(el => el.classList.remove('selected'));
        document.getElementById('card-' + role).classList.add('selected');
        document.getElementById('continueBtn').disabled = false;
    }

    document.getElementById('continueBtn').addEventListener('click', function() {
        if (!selectedRole) return;
        const btn = this;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Synchronizing...';
        btn.disabled = true;

        fetch('/api/onboarding', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ role: selectedRole })
        })
        .then(res => res.json())
        .then(data => {
            if (data.redirect) window.location.href = data.redirect;
        })
        .catch(err => {
            console.error(err);
            alert("Protocol failure.");
            btn.innerHTML = 'Initialize Account';
            btn.disabled = false;
        });
    });
</script>
@endpush
