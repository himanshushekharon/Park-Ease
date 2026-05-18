@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="surface-card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="p-4 text-center border-bottom border-subtle" style="background: var(--bg-elevated);">
                    <h3 class="text-h3 mb-0">Order Summary</h3>
                    <p class="text-secondary small mb-0">Confirm your details and pay</p>
                </div>
                
                <div class="card-body p-4">
                    <!-- Booking Details -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Parking Details</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Parking Area:</span>
                            <span class="fw-bold text-primary">{{ $lot->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Location:</span>
                            <span class="small text-end">{{ $lot->address }}, {{ $lot->city }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Date & Time:</span>
                            <span class="fw-bold" id="displayDateTime">Loading...</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Slot Breakdown</h5>
                        <div id="slotList">
                            <!-- JS populated -->
                        </div>
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top border-2">
                            <span class="h5 fw-bold">Total Amount</span>
                            <span class="h5 fw-bold text-success" id="totalAmount">₹0</span>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Customer Information</h5>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Full Name</label>
                            <input type="text" id="cust_name" class="form-control-premium w-100" placeholder="Enter your name" value="{{ Auth::user()->name ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Email Address</label>
                            <input type="email" id="cust_email" class="form-control-premium w-100" placeholder="name@example.com" value="{{ Auth::user()->email ?? '' }}">
                            <div class="form-text text-secondary small">Access this booking anytime using the same email.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" id="cust_phone" class="form-control-premium w-100" placeholder="10-digit mobile number" required>
                            <div class="form-text text-secondary small">Required for seamless payment experience.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Vehicle Registration Number <span class="text-danger">*</span></label>
                            <input type="text" id="cust_vehicle" class="form-control-premium w-100" placeholder="e.g. MH12AB1234" required style="text-transform: uppercase;">
                            <div class="form-text text-secondary small">Required for check-in verification at the parking property.</div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-5">
                        <h5 class="fw-bold mb-3">Select Payment Method</h5>
                        <div class="d-grid gap-2">
                            <!-- Razorpay Options -->
                            <div class="form-check surface-card p-3 border border-subtle rounded-3 cursor-pointer payment-option active hover-lift" data-method="razorpay">
                                <input class="form-check-input d-none" type="radio" name="paymentMethod" id="razorpay_gateway" value="razorpay" checked>
                                <label class="form-check-label d-flex align-items-center w-100 cursor-pointer" for="razorpay_gateway">
                                    <i class="bi bi-credit-card fs-4 me-3 text-brand"></i>
                                    <div>
                                        <div class="fw-bold text-primary">Pay Online (Instant)</div>
                                        <div class="small text-muted text-nowrap">UPI, Cards, Net Banking</div>
                                    </div>
                                    <i class="bi bi-check-circle-fill ms-auto text-brand check-icon"></i>
                                </label>
                            </div>

                        </div>
                    </div>



                    <button id="payNowBtn" class="btn btn-brand w-100 py-3 fs-5 shadow">
                        Pay & Confirm Booking
                    </button>
                    
                    <p class="text-center mt-3 small text-muted">
                        <i class="bi bi-shield-check text-success"></i> Secure 256-bit SSL Encrypted Payment
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Success Modal -->
<div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-5 border-0 rounded-4">
            <div class="mb-4">
                <div class="success-checkmark mx-auto">
                    <div class="check-icon">
                        <span class="icon-line line-tip"></span>
                        <span class="icon-line line-long"></span>
                        <div class="icon-circle"></div>
                        <div class="icon-fix"></div>
                    </div>
                </div>
            </div>
            <h3 class="fw-bold text-success mb-2">Booking Reserved!</h3>
            <p class="text-muted mb-4" id="successModalMsg">Your parking slots have been booked. Check your dashboard for the ticket.</p>
            <a href="/dashboard" class="btn btn-primary-custom w-100 py-2">Go to Dashboard</a>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .payment-option { transition: all 0.2s; }
    .payment-option.active { border-color: var(--primary) !important; background-color: rgba(0,0,0,0.02); }
    .payment-option:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    
    /* Success Checkmark Animation */
    .success-checkmark {
        width: 80px;
        height: 115px;
        margin: 0 auto;
    }
    .success-checkmark .check-icon {
        width: 80px;
        height: 80px;
        position: relative;
        border-radius: 50%;
        box-sizing: content-box;
        border: 4px solid #4CAF50;
    }
    .success-checkmark .check-icon::before {
        top: 3px; left: -2px; width: 30px; transform-origin: 100% 50%; border-radius: 100px 0 0 100px;
    }
    .success-checkmark .check-icon::after {
        top: 0; left: 30px; width: 60px; transform-origin: 0 50%; border-radius: 0 100px 100px 0;
        animation: rotate-circle 4.25s ease-in;
    }
    .success-checkmark .icon-line {
        height: 5px; background-color: #4CAF50; display: block; border-radius: 2px; position: absolute; z-index: 10;
    }
    .success-checkmark .icon-line.line-tip {
        top: 46px; left: 14px; width: 25px; transform: rotate(45deg); animation: icon-line-tip 0.75s;
    }
    .success-checkmark .icon-line.line-long {
        top: 38px; right: 8px; width: 47px; transform: rotate(-45deg); animation: icon-line-long 0.75s;
    }
    .success-checkmark .icon-circle {
        top: -4px; left: -4px; z-index: 10; width: 80px; height: 80px; border-radius: 50%; border: 4px solid rgba(76, 175, 80, .5); position: absolute; box-sizing: content-box;
    }
    
    @keyframes icon-line-tip { 0% { width: 0; left: 1px; top: 19px; } 54% { width: 0; left: 1px; top: 19px; } 70% { width: 50px; left: -8px; top: 37px; } 84% { width: 17px; left: 21px; top: 48px; } 100% { width: 25px; left: 14px; top: 46px; } }
    @keyframes icon-line-long { 0% { width: 0; right: 46px; top: 54px; } 65% { width: 0; right: 46px; top: 54px; } 84% { width: 55px; right: 0px; top: 35px; } 100% { width: 47px; right: 8px; top: 38px; } }
</style>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Load data from session storage (saved from parking page)
        const bookingData = JSON.parse(sessionStorage.getItem('pending_booking'));
        if (!bookingData || bookingData.lot_id !== '{{ $lot->_id }}') {
            window.location.href = '/parking/{{ $lot->_id }}';
            return;
        }

        if (isSlotExpired(bookingData.date, bookingData.time_slot_id)) {
            alert("This parking slot time has already passed. Please select a future time slot.");
            window.location.href = '/parking/{{ $lot->_id }}';
            return;
        }

        const date = bookingData.date;
        const time = bookingData.time_slot_id;
        const slots = bookingData.slots; // Array of {id, number, type, price}
        
        let total = 0;

        document.getElementById('displayDateTime').innerText = `${date} | ${time}`;

        const slotList = document.getElementById('slotList');
        slots.forEach(slot => {
            total += slot.price;
            const div = document.createElement('div');
            div.className = 'd-flex justify-content-between mb-2 small';
            div.innerHTML = `<span class="text-muted">Slot ${slot.number} (${slot.type.toUpperCase()})</span> <span>₹${slot.price}</span>`;
            slotList.appendChild(div);
        });
        
        document.getElementById('totalAmount').innerText = '₹' + total;

        let selectedMethod = 'razorpay';
        const payNowBtn = document.getElementById('payNowBtn');



        // Final Payment Action with Razorpay Integration
        payNowBtn.addEventListener('click', async function() {
            const name = document.getElementById('cust_name').value.trim();
            const email = document.getElementById('cust_email').value.trim();
            const phone = document.getElementById('cust_phone').value.trim();
            const vehicle = document.getElementById('cust_vehicle').value.trim();

            if (!name || !email || !phone || !vehicle) {
                alert('Please fill in all the required customer fields (including phone and vehicle registration number).');
                return;
            }

            if (isSlotExpired(date, time)) {
                alert("This parking slot time has already passed. Please select a future time slot.");
                window.location.href = `/parking/${bookingData.lot_id}`;
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Initializing Payment...';

            try {
                // Step 1: Create an order on the server
                const orderResponse = await fetch('/api/create-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ amount: total })
                });

                const orderDataResp = await orderResponse.json();

                if (!orderResponse.ok || !orderDataResp.success) {
                    throw new Error(orderDataResp.message || 'Failed to create order');
                }

                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Opening Secure Gateway...';

                // Step 2: Initialize Premium Razorpay Checkout
                const options = {
                    "key": orderDataResp.key, // The Key ID generated from the Dashboard
                    "amount": orderDataResp.amount, // Amount is in currency subunits. Default currency is INR.
                    "currency": orderDataResp.currency,
                    "name": "ParkEase Premium",
                    "description": "Secure Parking Reservation",
                    "image": "/favicon.ico", // You can replace with your logo URL
                    "order_id": orderDataResp.order_id, // This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                    
                    // Premium UI Configuration: UPI First
                    "config": {
                        "display": {
                            "blocks": {
                                "upi": {
                                    "name": "Recommended: Pay via UPI",
                                    "instruments": [
                                        { "method": "upi" },
                                        { "method": "upi", "provider": "google_pay" },
                                        { "method": "upi", "provider": "phonepe" },
                                        { "method": "upi", "provider": "paytm" }
                                    ]
                                },
                                "other": {
                                    "name": "Other Payment Modes",
                                    "instruments": [
                                        { "method": "card" },
                                        { "method": "netbanking" },
                                        { "method": "wallet" }
                                    ]
                                }
                            },
                            "hide": [
                                { "method": "emi" },
                                { "method": "paylater" }
                            ],
                            "sequence": ["block.upi", "block.other"],
                            "preferences": {
                                "show_default_blocks": false
                            }
                        }
                    },
                    "retry": {
                        "enabled": false // Let us handle failure gracefully
                    },
                    "handler": async function (response){
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Confirming Booking...';
                        
                        // Step 3: Verify signature and create booking on server
                        try {
                            const verifyResponse = await fetch('/api/bookings', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    parking_lot_id: bookingData.lot_id,
                                    slot_ids: slots.map(s => s.id),
                                    time_slot_id: bookingData.time_slot_id,
                                    date: bookingData.date,
                                    vehicle_type: bookingData.vehicle_type,
                                    email: document.getElementById('cust_email').value,
                                    customer_name: document.getElementById('cust_name').value,
                                    customer_phone: document.getElementById('cust_phone').value,
                                    vehicle_number: document.getElementById('cust_vehicle').value,
                                    payment_method: 'razorpay',
                                    // Razorpay details
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature
                                })
                            });

                            if (verifyResponse.ok) {
                                sessionStorage.removeItem('pending_booking');
                                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                                successModal.show();
                            } else {
                                const err = await verifyResponse.json();
                                alert('Error confirming booking: ' + (err.message || 'Verification failed'));
                                btn.disabled = false;
                                btn.innerHTML = 'Pay & Confirm Booking';
                            }
                        } catch (err) {
                            console.error(err);
                            alert('Failed to communicate with server. Contact support if amount was deducted.');
                            btn.disabled = false;
                            btn.innerHTML = 'Pay & Confirm Booking';
                        }
                    },
                    "prefill": {
                        "name": document.getElementById('cust_name').value,
                        "email": document.getElementById('cust_email').value,
                        "contact": document.getElementById('cust_phone').value || ""
                    },
                    "theme": {
                        "color": "#000000" // Premium Dark Mode feel for gateway
                    },
                    "modal": {
                        "ondismiss": function(){
                            btn.disabled = false;
                            btn.innerHTML = 'Pay & Confirm Booking';
                        },
                        "animation": true,
                        "backdropclose": false
                    }
                };
                
                const rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response){
                    alert("Payment Failed: " + response.error.description);
                    btn.disabled = false;
                    btn.innerHTML = 'Pay & Confirm Booking';
                });
                rzp1.open();

            } catch (err) {
                console.error(err);
                alert(err.message || 'Payment initialization failed. Please try again.');
                btn.disabled = false;
                btn.innerHTML = 'Pay & Confirm Booking';
            }
        });
    });
</script>
@endpush
