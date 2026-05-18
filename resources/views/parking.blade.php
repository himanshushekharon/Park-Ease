@extends('layouts.app')

@section('title', 'Book Parking Slot')

@push('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.2);
        --primary-gradient: linear-gradient(135deg, #000000 0%, #333333 100%);
        --navy-blue: #000080;
        --light-blue: #E6F0FF;
    }

    body {
        background: #f4f7f6;
    }

    .booking-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .parking-header {
        background: var(--primary-gradient);
        color: white;
        padding: 40px;
        border-radius: 24px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    /* Tabs Styling */
    .nav-pills-custom .nav-link {
        color: var(--navy-blue) !important;
        background: var(--light-blue);
        border-radius: 12px;
        padding: 10px 25px;
        font-weight: 700;
        margin: 0 5px;
        transition: all 0.3s ease;
        border: 2px solid var(--navy-blue);
    }

    .grid-message {
        animation: fadeIn 0.3s ease;
    }

    /* Skeleton Loader */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 12px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    .nav-pills-custom .nav-link.active {
        background: var(--navy-blue) !important;
        color: #fff !important;
        border-color: var(--navy-blue) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 128, 0.2);
    }

    .nav-pills-custom .nav-link:hover:not(.active) {
        background: #d0e4ff;
        color: var(--navy-blue);
    }

    /* Slot Styling */
    .slot-box {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        position: relative;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid #edf2f7;
    }

    .slot-available {
        border-color: var(--navy-blue) !important;
        background-color: var(--light-blue) !important;
        color: var(--navy-blue) !important;
    }

    .slot-available:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 128, 0.2);
        opacity: 0.9;
    }

    .slot-selected {
        background: #000 !important;
        color: #fff !important;
        border-color: #000 !important;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
        transform: scale(1.05);
    }

    .slot-booked {
        background: #f1f5f9 !important;
        color: #cbd5e0 !important;
        border-color: #e2e8f0 !important;
        cursor: not-allowed !important;
        opacity: 0.6;
        pointer-events: none;
    }

    .slot-booked:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    .slot-watermark {
        font-size: 0.5rem;
        text-transform: uppercase;
        margin-top: 2px;
        opacity: 0.6;
        font-weight: 800;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .legend-box {
        width: 18px;
        height: 18px;
        border-radius: 4px;
    }

    /* Sidebar Summary */
    .summary-item {
        padding: 12px;
        background: #fff;
        border-radius: 12px;
        margin-bottom: 10px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .btn-primary-custom {
        background: #000;
        color: white;
        border-radius: 16px;
        padding: 15px;
        font-weight: 700;
        border: none;
    }

    .entrance-label {
        font-size: 0.7rem;
        letter-spacing: 3px;
        color: #94a3b8;
        font-weight: 800;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="container py-4 booking-container">
    <div class="parking-header glass-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-white text-dark mb-2 px-3 py-2 rounded-pill fw-bold">Parking Area</span>
                <h1 class="fw-bold mb-1" id="parkingName">Loading...</h1>
                <p class="mb-0 opacity-75" id="parkingAddress">Loading address...</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-inline-block bg-white bg-opacity-10 p-3 rounded-4 backdrop-blur">
                    <div class="small opacity-75">Starting from</div>
                    <div class="h4 fw-bold mb-0">₹{{ $lot->car_price ?? 0 }}/hr</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-4">1. Booking Details</h5>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Select Date</label>
                    <input type="date" id="bookingDate" class="form-control rounded-3" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Time Slot</label>
                    <select id="timeSlot" class="form-select rounded-3">
                        <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                        <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                        <option value="12:00-13:00">12:00 PM - 01:00 PM</option>
                        <option value="13:00-14:00">01:00 PM - 02:00 PM</option>
                        <option value="14:00-15:00">02:00 PM - 03:00 PM</option>
                    </select>
                </div>

                <button id="checkAvailabilityBtn" class="btn btn-dark w-100 py-3 rounded-4 fw-bold mb-4 shadow-sm">
                    Check Availability
                </button>

                <div id="bookingSummary" class="d-none">
                    <div class="p-4 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important; color: white;">
                        <h6 class="fw-bold mb-3 text-white"><i class="bi bi-receipt me-2"></i>Summary</h6>
                        <div id="selectedSlotsList" class="mb-3">
                            <!-- Slots will be listed here -->
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-25">
                            <span class="fw-bold text-white opacity-75">Total Amount</span>
                            <span class="h4 fw-bold text-white mb-0" id="summaryPrice">₹0</span>
                        </div>
                    </div>
                    
                    <button id="confirmBookingBtn" class="btn btn-primary-custom w-100 shadow" disabled>
                        Confirm & Pay
                    </button>
                    
                    <button class="btn btn-link btn-sm w-100 mt-2 text-muted" onclick="resetSelection()">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="glass-card p-4 h-100">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    <h5 class="fw-bold mb-0">2. Select Your Spot</h5>
                    
                    <ul class="nav nav-pills nav-pills-custom">
                        <li class="nav-item"><a class="nav-link active cursor-pointer" onclick="filterSlots('car')" id="tab-car">Cars</a></li>
                        <li class="nav-item"><a class="nav-link cursor-pointer" onclick="filterSlots('bike')" id="tab-bike">Bikes</a></li>
                        <li class="nav-item"><a class="nav-link cursor-pointer" onclick="filterSlots('bus')" id="tab-bus">Buses</a></li>
                    </ul>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-4 mb-4 p-3 bg-light rounded-4">
                    <div class="legend-item"><div class="legend-box" style="background: var(--light-blue); border: 2px solid var(--navy-blue);"></div> Available</div>
                    <div class="legend-item"><div class="legend-box" style="background: #000;"></div> Selected</div>
                    <div class="legend-item"><div class="legend-box" style="background: #f1f5f9; opacity: 0.7;"></div> Booked</div>
                </div>

                <div id="gridLoading" class="d-none text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>

                <div id="slotGridContainer" class="d-flex flex-wrap justify-content-center gap-3">
                    <p class="text-muted py-5">Please check availability to view slots.</p>
                </div>

                <div class="mt-5 text-center">
                    <div class="d-inline-block px-5 py-2 border-top border-2 border-dashed">
                        <span class="entrance-label">Parking Entrance / Exit</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const parkingId = '{{ $id }}';
    let selectedSlots = []; 
    let currentFilter = 'car';
    let allSlots = [];
    const isLoggedIn = @json(Auth::check());
    
    const prices = {
        car: {{ $lot->car_price ?? 0 }},
        bike: {{ $lot->bike_price ?? 0 }},
        bus: {{ $lot->bus_price ?? 0 }}
    };

    document.getElementById('parkingName').innerText = "{{ $lot->name }}";
    document.getElementById('parkingAddress').innerText = "{{ $lot->address }}, {{ $lot->city }}";
    
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('checkAvailabilityBtn').addEventListener('click', loadSlots);
    });

    async function loadSlots() {
        const date = document.getElementById('bookingDate').value;
        const timeSlotId = document.getElementById('timeSlot').value;
        if (!date) return alert("Please select a date");

        const btn = document.getElementById('checkAvailabilityBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Checking...';

        const container = document.getElementById('slotGridContainer');
        container.innerHTML = Array(12).fill(0).map(() => `<div class="skeleton" style="width: 55px; height: 55px;"></div>`).join('');

        try {
            const response = await fetch(`/api/parking-lots/${parkingId}/slots?date=${date}&time_slot_id=${timeSlotId}`);
            if (!response.ok) throw new Error('Failed to fetch slots');
            
            const data = await response.json();
            allSlots = data.slots || [];
            filterSlots(currentFilter);
            updateSummary();
        } catch (err) {
            console.error(err);
            container.innerHTML = `
                <div class="text-center py-5 w-100 grid-message">
                    <i class="bi bi-wifi-off fs-1 text-danger opacity-50 mb-3"></i>
                    <h6 class="fw-bold">Unable to Load Slots</h6>
                    <p class="small text-muted">Please check your connection and try again.</p>
                    <button class="btn btn-sm btn-outline-dark rounded-pill px-4 mt-2" onclick="loadSlots()">Retry</button>
                </div>
            `;
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Check Availability';
        }
    }

    window.filterSlots = function(type) {
        currentFilter = type;
        document.querySelectorAll('.nav-link').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + type).classList.add('active');

        if (allSlots.length === 0) return;

        const filtered = allSlots.filter(s => s.vehicle_type === type);
        const container = document.getElementById('slotGridContainer');
        
        if(filtered.length === 0) {
            container.innerHTML = `<p class="text-muted py-5 w-100 text-center">No ${type} slots available.</p>`;
            return;
        }

        container.innerHTML = filtered.map(slot => {
            const slotId = slot.id || slot._id;
            const isSelected = selectedSlots.some(s => s.id === slotId);
            const statusClass = slot.is_booked ? 'slot-booked' : (isSelected ? 'slot-selected' : 'slot-available');
            const onClick = slot.is_booked ? '' : `onclick="toggleSlot('${slotId}', '${slot.slot_number}', '${slot.vehicle_type}', ${prices[slot.vehicle_type]})"`;
            
            return `
                <div class="slot-box ${statusClass}" id="slot-${slotId}" ${onClick}>
                    ${slot.slot_number}
                    <div class="slot-watermark">${slot.vehicle_type}</div>
                </div>
            `;
        }).join('');
    };

    window.toggleSlot = function(id, number, type, price) {
        const index = selectedSlots.findIndex(s => s.id === id);
        const el = document.getElementById('slot-' + id);

        if (index > -1) {
            selectedSlots.splice(index, 1);
            if (el) {
                el.classList.remove('slot-selected');
                el.classList.add('slot-available');
            }
        } else {
            selectedSlots.push({id, number, type, price});
            if (el) {
                el.classList.add('slot-selected');
                el.classList.remove('slot-available');
            }
        }
        
        updateSummary();
    }

    function updateSummary() {
        const summaryDiv = document.getElementById('bookingSummary');
        const listContainer = document.getElementById('selectedSlotsList');
        
        if (selectedSlots.length === 0) {
            summaryDiv.classList.add('d-none');
            document.getElementById('confirmBookingBtn').disabled = true;
        } else {
            summaryDiv.classList.remove('d-none');
            listContainer.innerHTML = selectedSlots.map(s => `
                <div class="summary-item d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 10px; border-radius: 12px; margin-bottom: 8px;">
                    <div>
                        <div class="fw-bold">Slot ${s.number}</div>
                        <div class="small opacity-75 text-uppercase" style="font-size: 0.6rem;">${s.type}</div>
                    </div>
                    <div class="fw-bold">₹${s.price}</div>
                </div>
            `).join('');
            
            const total = selectedSlots.reduce((sum, s) => sum + s.price, 0);
            document.getElementById('summaryPrice').innerText = '₹' + total;
            document.getElementById('confirmBookingBtn').disabled = false;
        }
    }

    function resetSelection() {
        selectedSlots = [];
        document.querySelectorAll('.slot-selected').forEach(el => {
            el.classList.remove('slot-selected');
            el.classList.add('slot-available');
        });
        updateSummary();
    }

    document.getElementById('confirmBookingBtn').addEventListener('click', function() {
        if (!isLoggedIn) {
            window.location.href = '/login';
            return;
        }
        const bookingData = {
            lot_id: parkingId,
            slots: selectedSlots,
            time_slot_id: document.getElementById('timeSlot').value,
            date: document.getElementById('bookingDate').value
        };
        sessionStorage.setItem('pending_booking', JSON.stringify(bookingData));
        window.location.href = `/checkout?lot_id=${parkingId}`;
    });
</script>
@endpush
