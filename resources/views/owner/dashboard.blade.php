@extends('layouts.app')

@section('title', 'Owner Dashboard')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Owner Dashboard</h2>
            <p class="text-muted">Manage your parking areas and view bookings.</p>
        </div>
    </div>

    <!-- Host Profile Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="surface-card p-4 d-flex flex-row align-items-center gap-4">
                @if(auth()->user()->photo_path)
                    <img src="/{{ auth()->user()->photo_path }}" alt="Host Photo" class="rounded-circle border border-3 border-subtle" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-elevated text-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 24px; border: 1px solid var(--border-default);">
                        <i class="bi bi-person"></i>
                    </div>
                @endif
                <div>
                    <h5 class="text-h5 mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-0 small">Phone: {{ auth()->user()->phone }} | Aadhaar: **** **** {{ substr(auth()->user()->aadhaar_no, -4) }}</p>
                </div>
                <div class="ms-auto">
                    <a href="/owner/kyc" class="btn btn-secondary btn-sm">Edit KYC Details</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <!-- Analytics Cards -->
    <!-- Analytics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="surface-card p-4 text-center hover-lift border-0">
                <h6 class="text-muted text-uppercase fw-bold ls-1 small">Properties</h6>
                <h2 class="text-h2 mb-0 text-primary">{{ $totalParkingLots }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="surface-card p-4 text-center hover-lift border-0">
                <h6 class="text-muted text-uppercase fw-bold ls-1 small">Total Slots</h6>
                <h2 class="text-h2 mb-0 text-primary">{{ $totalSlots }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="surface-card p-4 text-center hover-lift border-0" style="background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.1) !important;">
                <h6 class="text-info text-uppercase fw-bold ls-1 small">Active Bookings</h6>
                <h2 class="text-h2 mb-0 text-info">{{ $activeBookingsCount }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="surface-card p-4 text-center hover-lift border-0" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.1) !important;">
                <h6 class="text-success text-uppercase fw-bold ls-1 small">Total Earnings</h6>
                <h2 class="text-h2 mb-0 text-success">₹{{ number_format($totalEarnings, 2) }}</h2>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills-premium justify-content-center mb-5" id="ownerDashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-5 py-3" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties" type="button" role="tab">My Parking Areas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-5 py-3" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">Attendance & Scanner</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-5 py-3" id="financials-tab" data-bs-toggle="tab" data-bs-target="#financials" type="button" role="tab">Earnings & Financials</button>
        </li>
    </ul>

    <div class="tab-content" id="ownerDashboardTabsContent">
        <!-- Properties Management -->
        <div class="tab-pane fade show active" id="properties" role="tabpanel">
            <div class="row">
                <!-- Add Parking Lot Form -->
                <div class="col-lg-7 mb-5">
                    <div class="surface-card p-4 border-0 rounded-4">
                        <h4 class="text-h4 mb-4">Register New Parking Area</h4>
                        <form id="addParkingForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Parking Name</label>
                                    <input type="text" class="form-control" name="name" required placeholder="e.g. City Center Parking">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="city" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" class="form-control" name="pincode" required>
                                </div>
                                
                                <div class="col-md-12 mt-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold mb-0">Location (Click map to pin)</label>
                                        <button type="button" class="btn btn-sm btn-outline-dark" id="detectLocationBtn">
                                            <i class="bi bi-geo-alt-fill"></i> Detect My Location
                                        </button>
                                    </div>
                                    <div id="ownerMap" style="height: 300px; border-radius: 8px; border: 1px solid var(--border-color);"></div>
                                    <input type="hidden" name="latitude" id="latInput" required>
                                    <input type="hidden" name="longitude" id="lngInput" required>
                                    <div class="text-muted small mt-1">Latitude: <span id="latDisplay">-</span>, Longitude: <span id="lngDisplay">-</span></div>
                                </div>

                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Car Price (₹/Slot)</label>
                                    <input type="number" class="form-control" name="car_price" min="0" required>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Bike Price (₹/Slot)</label>
                                    <input type="number" class="form-control" name="bike_price" min="0" required>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Bus Price (₹/Slot)</label>
                                    <input type="number" class="form-control" name="bus_price" min="0" required>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Opening Time</label>
                                    <input type="time" class="form-control" name="opening_time" required>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Closing Time</label>
                                    <input type="time" class="form-control" name="closing_time" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold">Slot Configuration</label>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Car Slots</label>
                                    <input type="number" class="form-control" name="car_slots" min="0" value="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Bike Slots</label>
                                    <input type="number" class="form-control" name="bike_slots" min="0" value="0" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Bus Slots</label>
                                    <input type="number" class="form-control" name="bus_slots" min="0" value="0" required>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-3 shadow" id="submitBtn">Register Parking Area</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Registered Parking Lots -->
                <div class="col-lg-5">
                    <h4 class="text-h4 mb-4">Your Parking Areas</h4>
                    @if($parkingLots->isEmpty())
                        <div class="empty-state text-center p-4">
                            <p class="mb-0 text-muted">You haven't registered any parking areas yet.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($parkingLots as $lot)
                                <div class="surface-card p-3 border-0 rounded-4 mb-3">
                                    <h5 class="text-h5 mb-1">{{ $lot->name }}</h5>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt text-primary"></i> {{ $lot->address }}, {{ $lot->city }} - {{ $lot->pincode }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="d-flex gap-1">
                                            <span class="badge bg-elevated border border-subtle text-primary">Car: ₹{{ $lot->car_price ?? 0 }}</span>
                                            <span class="badge bg-elevated border border-subtle text-primary">Bike: ₹{{ $lot->bike_price ?? 0 }}</span>
                                        </div>
                                        <span class="small text-muted">{{ $lot->opening_time }} - {{ $lot->closing_time }}</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="/owner/parking/{{ $lot->_id }}/manage" class="btn btn-dark btn-sm w-100 rounded-3 py-2 fw-bold">Manage & Spot Book</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
            </div>
        </div>

        <!-- Attendance & Scanner -->
        <div class="tab-pane fade" id="attendance" role="tabpanel">
            <div class="row">
                <!-- Left: Ticket Verification & Live QR Scanner -->
                <div class="col-lg-5 mb-5">
                    <div class="surface-card p-4 border-0 rounded-4 mb-4">
                        <h4 class="text-h4 mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-qr-code-scan text-primary"></i> Live Ticket Scanner
                        </h4>
                        <p class="text-muted small">Scan user booking QR codes using your device webcam or camera.</p>
                        
                        <!-- Webcam Container -->
                        <div class="bg-elevated border rounded-3 overflow-hidden mb-3 position-relative" style="min-height: 250px;">
                            <div id="qrReader" style="width: 100%;"></div>
                            <div id="scannerOverlay" class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 10;">
                                <button type="button" class="btn btn-primary" id="startScannerBtn">
                                    <i class="bi bi-camera-fill me-1"></i> Start Camera
                                </button>
                            </div>
                        </div>
                        
                        <!-- Manual Booking Lookup -->
                        <div class="border-top pt-3">
                            <label class="form-label fw-bold small text-muted">Or enter Booking ID manually</label>
                            <div class="input-group">
                                <input type="text" id="manualBookingId" class="form-control" placeholder="e.g. 9X2J1K4L5M" maxlength="10" style="text-transform: uppercase;">
                                <button class="btn btn-dark" type="button" id="lookupBookingBtn">Verify</button>
                            </div>
                        </div>
                    </div>

                    <!-- Scan Results Card -->
                    <div class="surface-card p-4 border-0 rounded-4 d-none" id="verificationResultCard">
                        <h5 class="fw-bold mb-3 border-bottom pb-2" id="verificationHeader">Verification Result</h5>
                        <div id="verificationBody">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Right: Real-time Booking Monitor -->
                <div class="col-lg-7">
                    <div class="surface-card border-0 rounded-4 overflow-hidden">
                        <div class="p-4 border-bottom border-subtle d-flex flex-md-row flex-column justify-content-between align-items-md-center gap-3">
                            <div>
                                <h4 class="text-h4 mb-1">Real-Time Booking Visibility</h4>
                                <p class="text-muted small mb-0">Bookings dynamically poll every 10 seconds</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-elevated" id="refreshBookingsBtn">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <!-- Filter and Search Row -->
                        <div class="p-3 bg-elevated border-bottom border-subtle d-flex flex-md-row flex-column gap-2">
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="bookingSearchInput" placeholder="Search by Booking ID, Vehicle, Phone, Customer...">
                                </div>
                            </div>
                            <div style="min-width: 160px;">
                                <select class="form-select" id="bookingStatusFilter">
                                    <option value="all">All Bookings</option>
                                    <option value="today">Today's Bookings</option>
                                    <option value="active_now">Active Now</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="active">Active</option>
                                    <option value="attended">Attended</option>
                                    <option value="completed">Completed</option>
                                    <option value="expired">Expired</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 550px;">
                            <table class="table mb-0 align-middle table-hover">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3">Booking ID</th>
                                        <th class="py-3">Customer & Vehicle</th>
                                        <th class="py-3">Slot & Date</th>
                                        <th class="py-3 text-center">Status</th>
                                        <th class="py-3 text-end px-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="liveBookingsList">
                                    <!-- Populated dynamically via AJAX -->
                                    <tr>
                                        <td colspan="5" class="text-center p-5 text-muted">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Loading real-time bookings...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Container -->
                        <div class="p-3 border-top border-subtle d-flex justify-content-between align-items-center" id="bookingsPaginationContainer">
                            <!-- Populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Transactions -->
        <div class="tab-pane fade" id="financials" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="surface-card border-0 rounded-4 overflow-hidden">
                        <div class="p-4 border-bottom border-subtle">
                            <h5 class="text-h5 mb-0">Transaction History</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3">Transaction ID</th>
                                        <th class="py-3">Date</th>
                                        <th class="py-3">Customer</th>
                                        <th class="py-3 text-center">Type</th>
                                        <th class="py-3 text-end">Amount</th>
                                        <th class="py-3 text-center px-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $tx)
                                        <tr>
                                            <td class="px-4 py-3 fw-mono text-muted small">#{{ substr($tx->_id, -12) }}</td>
                                            <td class="py-3 text-muted">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                                            <td class="py-3">
                                                <div class="fw-bold">{{ $tx->description }}</div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <span class="badge {{ $tx->type === 'earning' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-3">
                                                    {{ strtoupper($tx->type) }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-end fw-bold {{ $tx->type === 'earning' ? 'text-success' : 'text-danger' }}">
                                                {{ $tx->type === 'earning' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                            </td>
                                            <td class="py-3 text-center px-4">
                                                <span class="badge {{ $tx->status === 'completed' ? 'bg-success' : ($tx->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ ucfirst($tx->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-5 text-muted">
                                                <i class="bi bi-wallet2 fs-1 mb-2 d-block"></i>
                                                No transactions recorded yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Map
        let currentTileLayer = null;
        function setMapTheme() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const tileUrl = isDark 
                ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';
            
            if (currentTileLayer) map.removeLayer(currentTileLayer);
            currentTileLayer = L.tileLayer(tileUrl, { attribution: '&copy; CARTO' }).addTo(map);
        }

        const map = L.map('ownerMap').setView([20.5937, 78.9629], 5); // Center of India
        setMapTheme();

        // Listen for theme changes dynamically
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.attributeName === 'data-theme') setMapTheme();
            });
        });
        observer.observe(document.documentElement, { attributes: true });

        let marker;

        // Try to get user's location to center map
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                map.setView([position.coords.latitude, position.coords.longitude], 12);
            });
        }

        // Detect Location Button
        document.getElementById('detectLocationBtn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Detecting...';
            btn.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    const latlng = [lat, lng];
                    map.setView(latlng, 15);
                    
                    document.getElementById('latInput').value = lat;
                    document.getElementById('lngInput').value = lng;
                    document.getElementById('latDisplay').innerText = lat.toFixed(6);
                    document.getElementById('lngDisplay').innerText = lng.toFixed(6);

                    if (marker) {
                        marker.setLatLng(latlng);
                    } else {
                        marker = L.marker(latlng).addTo(map);
                    }
                    
                    btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Found!';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 3000);
                }, function(error) {
                    alert('Error detecting location. Please ensure location services are enabled in your browser.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            } else {
                alert('Geolocation is not supported by your browser.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            document.getElementById('latInput').value = lat;
            document.getElementById('lngInput').value = lng;
            document.getElementById('latDisplay').innerText = lat.toFixed(6);
            document.getElementById('lngDisplay').innerText = lng.toFixed(6);

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });

        // Form Submit
        document.getElementById('addParkingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = 'Registering...';
            btn.disabled = true;

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Wait for Clerk token
            let token = '';
            if (window.Clerk && window.Clerk.session) {
                token = await window.Clerk.session.getToken();
            }

            try {
                const response = await fetch('/api/owner/parking-lots', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert('Parking area registered successfully!');
                    window.location.reload();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Validation failed'));
                    btn.innerHTML = 'Register Parking Area';
                    btn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('Something went wrong.');
                btn.innerHTML = 'Register Parking Area';
                btn.disabled = false;
            }
        });

        // Attendance, Verification & QR Scanning Logic
        let html5QrcodeScanner = null;
        let isScannerRunning = false;

        function playSuccessBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
                gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);

                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.15); // Beep for 150ms
            } catch (e) {
                console.error("AudioContext failed: ", e);
            }
        }

        function triggerVibration() {
            if (navigator.vibrate) {
                navigator.vibrate([100, 50, 100]); // Vibrate twice
            }
        }

        // Initialize Live Bookings
        let currentBookingPage = 1;
        let bookingSearch = '';
        let bookingStatus = 'all';

        async function loadLiveBookings(page = 1) {
            currentBookingPage = page;
            const liveList = document.getElementById('liveBookingsList');
            const paginationContainer = document.getElementById('bookingsPaginationContainer');
            if (!liveList) return;

            // Wait for Clerk token
            let token = '';
            if (window.Clerk && window.Clerk.session) {
                token = await window.Clerk.session.getToken();
            }

            try {
                const res = await fetch(`/api/owner/bookings?page=${page}&search=${encodeURIComponent(bookingSearch)}&status=${bookingStatus}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (!res.ok) throw new Error('Failed to load bookings');

                const data = await res.json();
                
                if (!data.data || data.data.length === 0) {
                    liveList.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center p-4 text-muted">
                                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                No bookings found matching the selected filters.
                            </td>
                        </tr>
                    `;
                    paginationContainer.innerHTML = '';
                    return;
                }

                liveList.innerHTML = '';
                data.data.forEach(booking => {
                    const statusBadgeClass = {
                        upcoming: 'bg-primary',
                        active: 'bg-info',
                        attended: 'bg-success',
                        completed: 'bg-secondary',
                        expired: 'bg-dark',
                        cancelled: 'bg-danger'
                    }[booking.status] || 'bg-light text-dark';

                    let actionHtml = '';
                    if (booking.status === 'upcoming' || booking.status === 'active' || booking.status === 'confirmed') {
                        actionHtml = `
                            <button class="btn btn-sm btn-success mark-checkin-btn px-3 rounded-pill" data-id="${booking._id}" data-code="${booking.booking_id}">
                                <i class="bi bi-check2-circle me-1"></i> Check-in
                            </button>
                        `;
                    } else if (booking.status === 'attended') {
                        actionHtml = `<span class="badge bg-success-subtle text-success border border-success small"><i class="bi bi-clock-history"></i> Checked In</span>`;
                    } else {
                        actionHtml = `<span class="text-muted small text-capitalize">${booking.status}</span>`;
                    }

                    const slotName = booking.slot ? booking.slot.slot_number : 'N/A';
                    const lotName = booking.parking_lot ? booking.parking_lot.name : 'N/A';
                    const custName = booking.customer_name || 'Guest';
                    const vehicleNum = booking.vehicle_number || 'N/A';

                    const tr = document.createElement('tr');
                    if (booking.status === 'active') {
                        tr.style.backgroundColor = 'rgba(13, 202, 240, 0.08)';
                    }
                    tr.innerHTML = `
                        <td class="px-4 py-3">
                            <span class="fw-bold text-mono">#${booking.booking_id}</span>
                            <div class="small text-muted">${lotName}</div>
                        </td>
                        <td class="py-3">
                            <div class="fw-bold">${custName}</div>
                            <div class="small text-muted">Vehicle: <span class="badge bg-elevated border text-muted">${vehicleNum}</span> (${(booking.vehicle_type || 'N/A').toUpperCase()})</div>
                        </td>
                        <td class="py-3 small">
                            <div>Slot: <span class="fw-bold text-primary">${slotName}</span></div>
                            <div class="text-muted">${booking.date} | ${booking.time_slot_id}</div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge ${statusBadgeClass} rounded-pill px-3 py-2 small">${booking.status.toUpperCase()}</span>
                        </td>
                        <td class="py-3 text-end px-4">
                            ${actionHtml}
                        </td>
                    `;
                    liveList.appendChild(tr);
                });

                // Attach listener to new buttons
                document.querySelectorAll('.mark-checkin-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const bookingId = this.getAttribute('data-id');
                        const code = this.getAttribute('data-code');
                        confirmCheckin(bookingId, code);
                    });
                });

                // Build simple pagination links
                let paginationHtml = `<span class="small text-muted">Showing page ${data.current_page} of ${data.last_page}</span><div class="btn-group btn-group-sm">`;
                if (data.prev_page_url) {
                    paginationHtml += `<button class="btn btn-outline-secondary" onclick="changeBookingsPage(${data.current_page - 1})"><i class="bi bi-chevron-left"></i></button>`;
                }
                if (data.next_page_url) {
                    paginationHtml += `<button class="btn btn-outline-secondary" onclick="changeBookingsPage(${data.current_page + 1})"><i class="bi bi-chevron-right"></i></button>`;
                }
                paginationHtml += `</div>`;
                paginationContainer.innerHTML = paginationHtml;

            } catch (err) {
                console.error(err);
                liveList.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center p-4 text-danger">
                            <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                            Error retrieving live bookings.
                        </td>
                    </tr>
                `;
            }
        }

        window.changeBookingsPage = function(page) {
            loadLiveBookings(page);
        };

        // Real-time Poll: every 10 seconds (AJAX Poll)
        let bookingsInterval = setInterval(() => {
            // Only poll if the attendance tab is active/visible
            const attendanceTab = document.getElementById('attendance');
            if (attendanceTab && attendanceTab.classList.contains('active')) {
                loadLiveBookings(currentBookingPage);
            }
        }, 10000);

        // Bind Search & Filters
        document.getElementById('bookingSearchInput').addEventListener('input', debounce(function() {
            bookingSearch = this.value;
            loadLiveBookings(1);
        }, 400));

        document.getElementById('bookingStatusFilter').addEventListener('change', function() {
            bookingStatus = this.value;
            loadLiveBookings(1);
        });

        document.getElementById('refreshBookingsBtn').addEventListener('click', function() {
            loadLiveBookings(currentBookingPage);
        });

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Lookup & Verify ticket actions
        async function verifyTicketCode(code) {
            const resultCard = document.getElementById('verificationResultCard');
            const resultBody = document.getElementById('verificationBody');

            // Wait for Clerk token
            let token = '';
            if (window.Clerk && window.Clerk.session) {
                token = await window.Clerk.session.getToken();
            }

            resultCard.classList.remove('d-none');
            resultBody.innerHTML = `
                <div class="text-center py-3">
                    <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                    <span class="ms-2">Verifying booking ticket...</span>
                </div>
            `;

            try {
                const res = await fetch('/api/owner/verify-booking', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ booking_id: code })
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    resultBody.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle-fill me-2"></i> ${data.message || 'Verification failed.'}
                        </div>
                    `;
                    return;
                }

                // Play feedback
                playSuccessBeep();
                triggerVibration();

                const booking = data.booking;
                resultBody.innerHTML = `
                    <div class="p-3 bg-success-subtle border border-success rounded-3 mb-3 text-success">
                        <div class="d-flex align-items-center gap-2 mb-2 fw-bold">
                            <i class="bi bi-patch-check-fill fs-4"></i>
                            <span>${data.message.toUpperCase()}</span>
                        </div>
                        <h4 class="mb-0 fw-bold">Booking #${booking.booking_id}</h4>
                    </div>
                    <table class="table table-sm table-borderless small mb-3">
                        <tr>
                            <td class="text-muted w-40">Customer Name:</td>
                            <td class="fw-bold text-end text-dark">${booking.customer_name}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vehicle Details:</td>
                            <td class="text-end fw-mono"><span class="badge bg-dark">${booking.vehicle_number}</span> (${booking.vehicle_type.toUpperCase()})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Parking Slot:</td>
                            <td class="fw-bold text-end text-primary">Slot ${booking.slot_number}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date & Window:</td>
                            <td class="text-end text-muted small">${booking.date} | ${booking.time_slot}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Current Status:</td>
                            <td class="text-end"><span class="badge bg-primary text-uppercase">${booking.status}</span></td>
                        </tr>
                    </table>
                    <button class="btn btn-success w-100 py-2 rounded-3 fw-bold" id="confirmAttendanceBtn" data-id="${booking.id}">
                        <i class="bi bi-check2-all me-1"></i> Confirm & Mark Checked-In
                    </button>
                `;

                document.getElementById('confirmAttendanceBtn').addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    confirmCheckin(id, booking.booking_id);
                });

            } catch (err) {
                console.error(err);
                resultBody.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Failed to communicate with server.
                    </div>
                `;
            }
        }

        async function confirmCheckin(bookingId, bookingCode) {
            // Wait for Clerk token
            let token = '';
            if (window.Clerk && window.Clerk.session) {
                token = await window.Clerk.session.getToken();
            }

            try {
                const res = await fetch('/api/owner/mark-attended', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ booking_id: bookingId })
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    alert('Error marking attendance: ' + (data.message || 'Check-in failed.'));
                    return;
                }

                alert(`Check-in complete for booking #${bookingCode}!`);
                
                // Hide results card if displayed
                document.getElementById('verificationResultCard').classList.add('d-none');
                
                // Reload list
                loadLiveBookings(currentBookingPage);

            } catch (err) {
                console.error(err);
                alert('Something went wrong. Please check your internet connection.');
            }
        }

        // Bind lookup buttons
        document.getElementById('lookupBookingBtn').addEventListener('click', function() {
            const code = document.getElementById('manualBookingId').value.trim();
            if (code.length !== 10) {
                alert('Please enter a valid 10-character Booking ID.');
                return;
            }
            verifyTicketCode(code);
        });

        document.getElementById('manualBookingId').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const code = this.value.trim();
                if (code.length === 10) verifyTicketCode(code);
            }
        });

        // Initialize QR Scanner on demand
        document.getElementById('startScannerBtn').addEventListener('click', function() {
            const btn = this;
            const readerContainer = document.getElementById('qrReader');
            const overlay = document.getElementById('scannerOverlay');

            if (isScannerRunning) {
                // Stop scanner
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().then(() => {
                        isScannerRunning = false;
                        btn.innerHTML = '<i class="bi bi-camera-fill me-1"></i> Start Camera';
                        overlay.classList.remove('d-none');
                    });
                }
                return;
            }

            overlay.classList.add('d-none');
            btn.innerHTML = '<i class="bi bi-camera-video-off me-1"></i> Stop Camera';
            isScannerRunning = true;

            html5QrcodeScanner = new Html5Qrcode("qrReader");
            html5QrcodeScanner.start(
                { facingMode: "environment" }, // Rear camera
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                qrCodeMessage => {
                    // Success callback
                    if (qrCodeMessage && qrCodeMessage.length === 10) {
                        // Stop scanner on success
                        html5QrcodeScanner.stop().then(() => {
                            isScannerRunning = false;
                            btn.innerHTML = '<i class="bi bi-camera-fill me-1"></i> Start Camera';
                            overlay.classList.remove('d-none');
                            verifyTicketCode(qrCodeMessage);
                        });
                    }
                },
                errorMessage => {
                    // Verbose error, ignore
                }
            ).catch(err => {
                console.error(err);
                alert("Failed to access camera. Please check camera permissions in your browser.");
                overlay.classList.remove('d-none');
                btn.innerHTML = '<i class="bi bi-camera-fill me-1"></i> Start Camera';
                isScannerRunning = false;
            });
        });

        // Trigger loading when tab is clicked
        const attendanceTabEl = document.getElementById('attendance-tab');
        if (attendanceTabEl) {
            attendanceTabEl.addEventListener('shown.bs.tab', function () {
                loadLiveBookings(1);
            });
        }
    });
</script>
@endpush
