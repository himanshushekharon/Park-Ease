@extends('layouts.app')

@section('title', 'Manage Parking - ' . $parkingLot->name)

@section('content')
<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold mb-1">{{ $parkingLot->name }}</h2>
            <p class="text-muted"><i class="bi bi-geo-alt"></i> {{ $parkingLot->address }}, {{ $parkingLot->city }}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="/owner/dashboard" class="btn btn-outline-dark btn-sm">Back to Dashboard</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card p-5 shadow-sm mb-4 border-0 rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Live Slot Status</h4>
                    <div class="d-flex gap-3">
                        <div class="mb-0">
                            <label class="small text-muted d-block">Select Date</label>
                            <input type="date" id="manageDate" class="form-control form-control-sm rounded-3" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted d-block">Time Slot</label>
                            <select id="manageTime" class="form-select form-select-sm rounded-3">
                                <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                                <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                                <option value="12:00-13:00">12:00 PM - 01:00 PM</option>
                                <option value="13:00-14:00">01:00 PM - 02:00 PM</option>
                                <option value="14:00-15:00">02:00 PM - 03:00 PM</option>
                            </select>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-pills mb-4 nav-pills-custom" id="vehicleTabs">
                    <li class="nav-item"><a class="nav-link active cursor-pointer" onclick="filterSlots('car')" id="tab-car">Cars</a></li>
                    <li class="nav-item"><a class="nav-link cursor-pointer" onclick="filterSlots('bike')" id="tab-bike">Bikes</a></li>
                    <li class="nav-item"><a class="nav-link cursor-pointer" onclick="filterSlots('bus')" id="tab-bus">Buses</a></li>
                </ul>

                <div id="slotGridContainer" class="d-flex flex-wrap gap-3 justify-content-center border p-4 rounded-4 bg-light min-vh-25 shadow-inner">
                    <!-- Slots will be loaded here -->
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div id="bookingCard" class="card p-4 shadow-lg border-0 rounded-4 d-none">
                <h5 class="fw-bold mb-3">Spot Booking</h5>
                
                <div class="bg-light p-3 rounded-4 mb-4">
                    <p class="text-muted small mb-1">Selected Slots:</p>
                    <div id="selectedSlotName" class="fw-bold text-dark"></div>
                </div>
                
                <form id="manualBookingForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Customer Name</label>
                        <input type="text" class="form-control rounded-3" name="customer_name" required placeholder="John Doe">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Customer Phone</label>
                        <input type="tel" class="form-control rounded-3" name="customer_phone" required placeholder="+91 0000000000">
                    </div>
                    
                    <div class="p-3 rounded-4 mb-4 border-0 bg-primary shadow-sm" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-white opacity-75 fw-bold">Total Slots:</span>
                            <span id="displayCount" class="small fw-bold text-white">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-white opacity-75 fw-bold">Total Amount:</span>
                            <span id="displayPrice" class="h4 fw-bold text-white mb-0">₹0</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-4 fw-bold shadow-sm" id="confirmBtn">Confirm Spot Booking</button>
                    <button type="button" class="btn btn-link btn-sm w-100 mt-2 text-muted text-decoration-none" onclick="deselectAllSlots()">Cancel Selection</button>
                </form>
            </div>

            <div id="instructionCard" class="card p-4 bg-light text-center border-0 rounded-4">
                <i class="bi bi-info-circle fs-2 text-muted mb-3"></i>
                <h6 class="fw-bold">Manual Entry</h6>
                <p class="small text-muted mb-0">Click on any available (blue) slot in the grid to start a manual booking for a walk-in customer.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function getISTDateTime() {
        const now = new Date();
        const formatter = new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Kolkata',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        const formatted = formatter.format(now);
        const cleaned = formatted.replace(',', '').trim();
        const parts = cleaned.split(' ');
        return {
            date: parts[0],
            time: parts[1],
            full: cleaned
        };
    }

    function isSlotExpired(dateStr, timeSlotId) {
        if (!dateStr || !timeSlotId) return false;
        const ist = getISTDateTime();
        
        if (dateStr < ist.date) {
            return true;
        }
        
        if (dateStr === ist.date) {
            const startTimeStr = timeSlotId.split('-')[0].trim();
            const slotStartTime = startTimeStr.includes(':') ? startTimeStr + ':00' : startTimeStr;
            if (slotStartTime < ist.time) {
                return true;
            }
        }
        return false;
    }

    const parkingId = '{{ $parkingLot->_id }}';
    const prices = {
        car: {{ $parkingLot->car_price }},
        bike: {{ $parkingLot->bike_price }},
        bus: {{ $parkingLot->bus_price }}
    };

    let allSlots = [];
    let currentFilter = 'car';
    let selectedSlots = []; 

    document.addEventListener('DOMContentLoaded', () => {
        loadSlots();

        document.getElementById('manageDate').addEventListener('change', loadSlots);
        document.getElementById('manageTime').addEventListener('change', loadSlots);

        document.getElementById('manualBookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (selectedSlots.length === 0) return alert('Please select at least one slot.');

            const btn = document.getElementById('confirmBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            data.parking_lot_id = parkingId;
            data.slot_ids = selectedSlots.map(s => s.id);
            data.date = document.getElementById('manageDate').value;
            data.time_slot_id = document.getElementById('manageTime').value;

            try {
                const response = await fetch('/api/owner/manual-booking', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert('Spot booking successful!');
                    deselectAllSlots();
                    loadSlots();
                } else {
                    const err = await response.json();
                    alert('Error: ' + err.message);
                }
            } catch (err) {
                console.error(err);
                alert('Something went wrong.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Confirm Spot Booking';
            }
        });
    });

    async function loadSlots() {
        const date = document.getElementById('manageDate').value;
        const time = document.getElementById('manageTime').value;
        const container = document.getElementById('slotGridContainer');
        
        container.innerHTML = '<div class="spinner-border text-dark my-4"></div>';

        try {
            const res = await fetch(`/api/parking-lots/${parkingId}/slots?date=${date}&time_slot_id=${time}`);
            const data = await res.json();
            allSlots = data.slots || [];
            filterSlots(currentFilter);
        } catch (err) {
            container.innerHTML = '<p class="text-danger my-4">Failed to load slots.</p>';
        }
    }

    window.filterSlots = function(type) {
        currentFilter = type;
        document.querySelectorAll('#vehicleTabs .nav-link').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + type).classList.add('active');

        const filtered = allSlots.filter(s => s.vehicle_type === type);
        const container = document.getElementById('slotGridContainer');
        
        if (filtered.length === 0) {
            container.innerHTML = `<p class="text-muted my-4 w-100 text-center">No ${type} slots configured.</p>`;
            return;
        }

        const date = document.getElementById('manageDate').value;
        const time = document.getElementById('manageTime').value;
        const isExpired = isSlotExpired(date, time);

        container.innerHTML = filtered.map(slot => {
            const slotId = slot.id || slot._id;
            const isBooked = slot.is_booked || isExpired;
            const isSelected = selectedSlots.some(s => s.id === slotId);
            const statusClass = isExpired ? 'slot-expired' : (slot.is_booked ? 'slot-booked' : (isSelected ? 'slot-selected' : 'slot-available'));
            const attr = isBooked ? '' : `onclick="toggleSlot('${slotId}', '${slot.slot_number}', '${slot.vehicle_type}')"`;
            const tooltipAttr = isExpired ? 'title="Booking time expired"' : '';
            
            const watermark = `<span class="slot-watermark">${isExpired ? 'EXPIRED' : slot.vehicle_type}</span>`;
            return `<div class="slot-box ${statusClass} position-relative" id="slot-${slotId}" ${attr} ${tooltipAttr}>
                        ${slot.slot_number}
                        ${watermark}
                    </div>`;
        }).join('');
    }

    window.toggleSlot = function(id, name, type) {
        // EXACT ID COMPARISON
        const index = selectedSlots.findIndex(s => s.id === id);
        const el = document.getElementById('slot-' + id);

        if (index > -1) {
            selectedSlots.splice(index, 1);
            if (el) {
                el.classList.remove('slot-selected');
                el.classList.add('slot-available');
            }
        } else {
            selectedSlots.push({id, name, type});
            if (el) {
                el.classList.add('slot-selected');
                el.classList.remove('slot-available');
            }
        }

        updateSidebar();
    }

    function updateSidebar() {
        const summaryCard = document.getElementById('bookingCard');
        const instructionCard = document.getElementById('instructionCard');

        if (selectedSlots.length === 0) {
            summaryCard.classList.add('d-none');
            instructionCard.classList.remove('d-none');
            return;
        }

        summaryCard.classList.remove('d-none');
        instructionCard.classList.add('d-none');

        const names = selectedSlots.map(s => `<span class="badge bg-white text-dark border me-1 mb-1">${s.name}</span>`).join('');
        const totalPrice = selectedSlots.reduce((sum, s) => sum + prices[s.type], 0);

        document.getElementById('selectedSlotName').innerHTML = names;
        document.getElementById('displayCount').innerText = selectedSlots.length;
        document.getElementById('displayPrice').innerText = '₹' + totalPrice;
    }

    function deselectAllSlots() {
        selectedSlots = [];
        document.querySelectorAll('.slot-selected').forEach(el => {
            el.classList.remove('slot-selected');
            el.classList.add('slot-available');
        });
        document.getElementById('bookingCard').classList.add('d-none');
        document.getElementById('instructionCard').classList.remove('d-none');
        document.getElementById('manualBookingForm').reset();
    }
</script>
@endpush

@push('styles')
<style>
    :root {
        --navy-blue: #000080;
        --light-blue: #E6F0FF;
    }

    .nav-pills-custom .nav-link {
        color: var(--navy-blue) !important;
        background: var(--light-blue);
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 700;
        margin-right: 8px;
        border: 2px solid var(--navy-blue);
        transition: all 0.2s;
    }

    .nav-pills-custom .nav-link.active {
        background: var(--navy-blue) !important;
        color: white !important;
    }

    .slot-box {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .slot-available {
        border: 2px solid var(--navy-blue);
        background-color: var(--light-blue);
        color: var(--navy-blue);
    }

    .slot-available:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 128, 0.2);
    }

    .slot-selected {
        background-color: #000 !important;
        color: #fff !important;
        border: 2px solid #000 !important;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        transform: scale(1.05);
    }

    .slot-booked {
        background-color: #f1f5f9 !important;
        color: #cbd5e0 !important;
        border: 2px solid #e2e8f0 !important;
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }

    .slot-expired {
        background-color: #f8fafc !important;
        color: #cbd5e0 !important;
        border: 2px dashed #cbd5e0 !important;
        opacity: 0.45;
        cursor: not-allowed;
        pointer-events: none;
    }

    .slot-watermark {
        position: absolute;
        font-size: 0.5rem;
        bottom: 4px;
        right: 4px;
        opacity: 0.8;
        text-transform: uppercase;
        pointer-events: none;
        font-weight: 800;
        color: var(--navy-blue);
    }

    .slot-selected .slot-watermark {
        color: rgba(255,255,255,0.6);
    }

    .shadow-inner {
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
</style>
@endpush
