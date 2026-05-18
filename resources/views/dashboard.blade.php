@extends('layouts.app')

@section('title', 'Intelligent Dashboard')

@push('styles')
<style>
    .dashboard-hero {
        padding: var(--space-12) 0 var(--space-20);
        background: radial-gradient(circle at 10% 20%, rgba(46, 196, 182, 0.03) 0%, transparent 50%);
        border-bottom: 1px solid var(--border-default);
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: var(--space-5);
        margin-top: calc(var(--space-16) * -1);
    }

    .search-input-wrapper {
        position: relative;
        max-width: 360px;
        width: 100%;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .table-container {
        background: var(--bg-surface);
        border-radius: var(--radius-card);
        border: 1px solid var(--border-default);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table th {
        background: var(--bg-elevated);
        padding: var(--space-4) var(--space-6);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-default);
    }

    .table td {
        padding: var(--space-5) var(--space-6);
        vertical-align: middle;
        border-bottom: 1px solid var(--border-subtle);
        color: var(--text-primary);
    }
</style>
@endpush

@section('content')
<div class="dashboard-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="text-h1 mb-2" id="smartGreeting">Good Evening, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
                <p class="text-lead mb-0">
                    @if($stats['active_sessions'] > 0)
                        Monitoring <span class="text-gradient fw-bold">{{ $stats['active_sessions'] }} active</span> urban mobility session{{ $stats['active_sessions'] > 1 ? 's' : '' }}.
                    @else
                        No active parking sessions detected.
                    @endif
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="/search" class="btn btn-brand">
                    <i class="bi bi-geo-alt"></i> Find New Spot
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Stats Overlay -->
    <div class="stat-grid">
        <div class="surface-card p-4 hover-lift">
            <div class="text-h2 text-primary mb-1">{{ $stats['active_sessions'] }}</div>
            <div class="text-h6">Active Hubs</div>
        </div>
        <div class="surface-card p-4 hover-lift">
            <div class="text-h2 text-primary mb-1">{{ $stats['total_bookings'] }}</div>
            <div class="text-h6">Lifetime Bookings</div>
        </div>
        <div class="surface-card p-4 hover-lift">
            <div class="text-h2 text-primary mb-1">₹{{ number_format($stats['total_spent']) }}</div>
            <div class="text-h6">Net Investment</div>
        </div>
        <div class="surface-card p-4 hover-lift">
            <div class="text-h2 text-primary mb-1">₹{{ number_format($stats['total_refunds']) }}</div>
            <div class="text-h6">Total Savings</div>
        </div>
    </div>

    <!-- Controls -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mt-5 mb-5 gap-4">
        <div class="nav nav-pills-premium" id="bookingTabs" role="tablist">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#active-pane">Active</button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#upcoming-pane">Upcoming</button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#past-pane">History</button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled-pane">Cancelled</button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transactions-pane"><i class="bi bi-ledger me-1"></i> Ledger</button>
        </div>

        <div class="search-input-wrapper">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control-premium ps-5" placeholder="Search sessions or IDs..." id="bookingSearch">
        </div>
    </div>

    <div class="tab-content">
        <!-- Active Tab -->
        <div class="tab-pane fade show active" id="active-pane">
            @if(count($categorized['active']) > 0)
                <div class="row g-4">
                    @foreach($categorized['active'] as $booking)
                        @include('partials.booking_card', ['booking' => $booking, 'type' => 'active'])
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-car-front"></i></div>
                    <h3 class="text-h3 mb-2">System Idle</h3>
                    <p class="text-secondary mb-4">No active parking sessions in progress.</p>
                    <a href="/search" class="btn btn-secondary">Find Parking Nearby</a>
                </div>
            @endif
        </div>

        <!-- Upcoming Tab -->
        <div class="tab-pane fade" id="upcoming-pane">
            @if(count($categorized['upcoming']) > 0)
                <div class="row g-4">
                    @foreach($categorized['upcoming'] as $booking)
                        @include('partials.booking_card', ['booking' => $booking, 'type' => 'upcoming'])
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-calendar4-week"></i></div>
                    <h3 class="text-h3 mb-2">No Pending Arrivals</h3>
                    <p class="text-secondary mb-4">Reserve a spot for your next destination ahead of time.</p>
                    <a href="/search" class="btn btn-secondary">Plan a Trip</a>
                </div>
            @endif
        </div>

        <!-- Past Tab -->
        <div class="tab-pane fade" id="past-pane">
            @if(count($categorized['completed']) > 0)
                <div class="row g-4">
                    @foreach($categorized['completed'] as $booking)
                        @include('partials.booking_card', ['booking' => $booking, 'type' => 'completed'])
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-clock-history"></i></div>
                    <h3 class="text-h3 mb-2">No History Recorded</h3>
                    <p class="text-secondary">Your completed parking sessions will appear here.</p>
                </div>
            @endif
        </div>

        <!-- Cancelled Tab -->
        <div class="tab-pane fade" id="cancelled-pane">
            @if(count($categorized['cancelled']) > 0)
                <div class="row g-4">
                    @foreach($categorized['cancelled'] as $booking)
                        @include('partials.booking_card', ['booking' => $booking, 'type' => 'cancelled'])
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-x-circle"></i></div>
                    <h3 class="text-h3 mb-2">Clean Slate</h3>
                    <p class="text-secondary">You haven't cancelled any bookings.</p>
                </div>
            @endif
        </div>

        <!-- Transactions Tab -->
        <div class="tab-pane fade" id="transactions-pane">
            @if(count($transactions) > 0)
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Service Description</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $tx)
                                    <tr>
                                        <td>
                                            <div class="text-h5 mb-1">{{ $tx->created_at->format('M d, Y') }}</div>
                                            <div class="text-small">{{ $tx->created_at->format('h:i A') }}</div>
                                        </td>
                                        <td>
                                            <div class="text-h5 mb-1">{{ $tx->description }}</div>
                                            <div class="text-small text-uppercase fw-bold"><i class="bi bi-credit-card me-1"></i>{{ $tx->payment_method }}</div>
                                        </td>
                                        <td class="text-muted font-monospace text-small">
                                            {{ substr($tx->_id, -12) }}
                                        </td>
                                        <td class="fw-bold fs-5 {{ $tx->type === 'refund' ? 'text-success' : '' }}">
                                            {{ $tx->type === 'refund' ? '+' : '-' }}₹{{ $tx->amount }}
                                        </td>
                                        <td>
                                            @if($tx->status === 'completed')
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 border border-success border-opacity-25 rounded-pill">Success</span>
                                            @elseif($tx->status === 'pending')
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 border border-warning border-opacity-25 rounded-pill">Processing</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 border border-danger border-opacity-25 rounded-pill">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-receipt"></i></div>
                    <h3 class="text-h3 mb-2">Ledger Empty</h3>
                    <p class="text-secondary">Your transaction history will be recorded here.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancellation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content surface-glass border-0">
            <div class="modal-body p-5 text-center">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex p-4 mb-4">
                    <i class="bi bi-exclamation-triangle fs-1"></i>
                </div>
                <h3 class="text-h3 mb-3">Cancel Session?</h3>
                <p class="text-secondary mb-4">Are you sure you want to terminate this reservation? This process is irreversible.</p>
                
                <div class="surface-card text-start mb-4 p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-h6">LOCATION</span>
                        <span class="fw-bold" id="cancelModalParking"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-h6">SLOT</span>
                        <span class="fw-bold" id="cancelModalSlot"></span>
                    </div>
                    <hr class="my-3 border-subtle">
                    <div class="d-flex justify-content-between">
                        <span class="text-success text-h6"><i class="bi bi-arrow-return-left me-1"></i>ESTIMATED REFUND</span>
                        <span class="fw-bold text-success fs-5" id="cancelModalRefund"></span>
                    </div>
                </div>
                
                <div class="d-flex gap-3">
                    <button class="btn btn-secondary flex-grow-1" data-bs-dismiss="modal">Back</button>
                    <button class="btn btn-danger flex-grow-1" id="confirmCancelBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ticket Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content surface-glass border-0 overflow-hidden">
            <div class="modal-body p-0">
                <div class="p-4" style="background: var(--bg-surface); border-bottom: 1px solid var(--border-default); text-align: center;">
                    <h5 class="text-h5 mb-1">E-TICKET</h5>
                    <div class="text-small text-muted" id="ticketId"></div>
                </div>
                <div class="p-5 text-center">
                    <div class="bg-white p-3 d-inline-block rounded-3 mb-4 shadow-sm border">
                        <img id="ticketQrImage" src="" width="160" height="160" alt="Ticket QR Code">
                    </div>
                    <h4 class="text-h4 mb-1" id="ticketParking"></h4>
                    <p class="text-secondary" id="ticketTime"></p>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <div class="surface-card py-3 px-4 flex-grow-1 border-0" style="background: var(--bg-hover);">
                            <div class="text-h6">SLOT</div>
                            <div class="text-h3" id="ticketSlot"></div>
                        </div>
                        <div class="surface-card py-3 px-4 flex-grow-1 border-0" style="background: var(--bg-hover);">
                            <div class="text-h6">VEHICLE</div>
                            <div class="text-h3" id="ticketVehicle"></div>
                        </div>
                    </div>
                </div>
                <div class="p-3 border-top text-center" style="background: var(--bg-surface); border-color: var(--border-default) !important;">
                    <button class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extension Modal -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content surface-glass border-0">
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-4 mb-4">
                        <i class="bi bi-clock-history fs-1"></i>
                    </div>
                    <h3 class="text-h3 mb-2">Extend Session</h3>
                    <p class="text-secondary" id="extendModalInfo"></p>
                </div>

                <div class="mb-4">
                    <label class="form-label text-h6 mb-3">ADDITIONAL DURATION</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="extendMins" id="ext15" value="15">
                            <label class="btn btn-outline-premium w-100 py-3" for="ext15">15m</label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="extendMins" id="ext30" value="30" checked>
                            <label class="btn btn-outline-premium w-100 py-3" for="ext30">30m</label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="extendMins" id="ext60" value="60">
                            <label class="btn btn-outline-premium w-100 py-3" for="ext60">60m</label>
                        </div>
                    </div>
                </div>

                <div class="surface-card p-4 mb-4" style="background: var(--bg-hover);">
                    <div class="d-flex justify-content-between">
                        <span class="text-h6">ESTIMATED COST</span>
                        <span class="fw-bold text-primary" id="extendCostPreview">₹0</span>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <button class="btn btn-secondary flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-brand flex-grow-1" id="confirmExtendBtn">Confirm & Pay</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const hour = new Date().getHours();
    let greeting = "Good Evening";
    if (hour < 12) greeting = "Good Morning";
    else if (hour < 17) greeting = "Good Afternoon";
    const greetingEl = document.getElementById('smartGreeting');
    if(greetingEl) greetingEl.innerHTML = `${greeting}, {{ explode(' ', auth()->user()->name)[0] }} 👋`;

    let activeCancelId = null;
    function openCancelModal(id, parking, slot, paid, refund) {
        activeCancelId = id;
        document.getElementById('cancelModalParking').innerText = parking;
        document.getElementById('cancelModalSlot').innerText = slot;
        document.getElementById('cancelModalRefund').innerText = '₹' + refund;
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }

    document.getElementById('confirmCancelBtn').addEventListener('click', async function() {
        const btn = this; btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        try {
            const res = await fetch(`/api/bookings/${activeCancelId}/cancel`, { 
                method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            if (res.ok) window.location.reload();
            else { alert('Error'); btn.disabled = false; btn.innerHTML = 'Confirm'; }
        } catch (e) { alert('Error'); btn.disabled = false; btn.innerHTML = 'Confirm'; }
    });

    function openTicketModal(id, parking, date, time, slot, vehicle, bookingId) {
        const finalBookingId = bookingId || id;
        document.getElementById('ticketId').innerText = '#' + finalBookingId.toUpperCase();
        document.getElementById('ticketParking').innerText = parking;
        document.getElementById('ticketTime').innerText = `${date} | ${time}`;
        document.getElementById('ticketSlot').innerText = slot;
        document.getElementById('ticketVehicle').innerText = vehicle;
        
        // Dynamically request high-quality QR code image generated from real-time ticket code
        document.getElementById('ticketQrImage').src = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(finalBookingId.toUpperCase())}`;
        
        new bootstrap.Modal(document.getElementById('ticketModal')).show();
    }

    let activeExtendId = null;
    function openExtendModal(id, parking, slot) {
        activeExtendId = id;
        document.getElementById('extendModalInfo').innerText = `${parking} | Slot ${slot}`;
        new bootstrap.Modal(document.getElementById('extendModal')).show();
    }

    document.getElementById('confirmExtendBtn').addEventListener('click', async function() {
        const mins = document.querySelector('input[name="extendMins"]:checked').value;
        const btn = this; btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Extending...';
        
        try {
            const res = await fetch(`/api/bookings/${activeExtendId}/extend`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ minutes: mins })
            });
            const data = await res.json();
            if (res.ok) window.location.reload();
            else { alert(data.message || 'Error'); btn.disabled = false; btn.innerHTML = 'Confirm & Pay'; }
        } catch (e) { alert('Network Error'); btn.disabled = false; btn.innerHTML = 'Confirm & Pay'; }
    });

    document.getElementById('bookingSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.col-lg-4, tbody tr').forEach(el => {
            el.style.display = el.innerText.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    // Live Booking Timers
    function updateTimers() {
        document.querySelectorAll('.live-timer').forEach(timer => {
            const endTime = new Date(timer.dataset.endtime);
            const now = new Date();
            const diff = endTime - now;

            if (diff <= 0) {
                timer.innerHTML = "SESSION ENDED";
                timer.classList.remove('text-primary');
                timer.classList.add('text-danger');
                return;
            }

            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);

            timer.innerHTML = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        });
    }
    setInterval(updateTimers, 1000);
    updateTimers();
</script>
@endpush
