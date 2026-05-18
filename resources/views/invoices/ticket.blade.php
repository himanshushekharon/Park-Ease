<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkEase - Parking Ticket #{{ $booking->booking_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            /* dompdf friendly gradient and shadow simulation */
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 30px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
        }
        .brand {
            font-size: 28px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 5px 0 0 0;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .invoice-meta {
            font-size: 13px;
            color: #475569;
        }
        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-section table {
            width: 100%;
        }
        .info-box {
            background-color: #f1f5f9;
            border-radius: 6px;
            padding: 15px;
            width: 48%;
            vertical-align: top;
        }
        .info-box h3 {
            font-size: 16px;
            color: #1e293b;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 5px;
            font-size: 13px;
        }
        .info-label {
            font-weight: bold;
            color: #64748b;
            display: inline-block;
            width: 120px;
        }
        .info-value {
            color: #0f172a;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-pending { background-color: #fef08a; color: #854d0e; }
        
        .ticket-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .ticket-details th {
            background-color: #1e3a8a;
            color: #ffffff;
            text-align: left;
            padding: 12px 15px;
            font-size: 14px;
        }
        .ticket-details td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            color: #334155;
        }
        .ticket-details tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .amount-row {
            background-color: #eff6ff !important;
            font-weight: bold;
        }
        .amount-row td {
            font-size: 16px;
            color: #1d4ed8;
            border-top: 2px solid #93c5fd;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }
        
        /* Simulating Glassmorphism */
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        
        .payment-info {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 13px;
        }
        
        .qr-placeholder {
            width: 100px;
            height: 100px;
            background-color: #e2e8f0;
            border: 2px dashed #94a3b8;
            text-align: center;
            line-height: 100px;
            font-size: 10px;
            color: #64748b;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="invoice-container glass-panel">
    
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 50%;">
                    <h1 class="brand"><span style="color: #1e3a8a;">Park</span><span style="color: #3b82f6;">Ease</span></h1>
                    <p class="brand-subtitle">Smart & Cost-Effective Parking</p>

                </td>
                <td class="invoice-details">
                    <h2 class="invoice-title">PARKING TICKET</h2>
                    <div class="invoice-meta">
                        <strong>Invoice No:</strong> {{ $booking->invoice_number ?? 'N/A' }}<br>
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, h:i A') }}<br>
                        <strong>Booking ID:</strong> {{ $booking->booking_id }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Customer & Location Info -->
    <div class="info-section">
        <table>
            <tr>
                <td class="info-box" style="margin-right: 4%;">
                    <h3>Customer Details</h3>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $booking->customer_name ?? $user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $booking->booking_email ?? $user->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $booking->customer_phone ?? 'N/A' }}</span>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td class="info-box">
                    <h3>Parking Location</h3>
                    <div class="info-row">
                        <span class="info-label">Lot Name:</span>
                        <span class="info-value">{{ $parkingLot->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ $parkingLot->address }}, {{ $parkingLot->city }} - {{ $parkingLot->pincode }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Booking Status:</span>
                        <span class="status-badge {{ $booking->payment_status == 'paid' ? 'status-paid' : 'status-pending' }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Ticket Details -->
    <table class="ticket-details">
        <thead>
            <tr>
                <th>Description</th>
                <th>Vehicle Type</th>
                <th>Slot No.</th>
                <th>Date & Time</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Parking Reservation</td>
                <td style="text-transform: capitalize;">{{ $booking->vehicle_type }}</td>
                <td><strong>{{ $slot->slot_number ?? 'N/A' }}</strong></td>
                <td>
                    {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}<br>
                    <small style="color:#64748b;">
                        <?php 
                            $times = explode('-', $booking->time_slot_id);
                            if (count($times) == 2) {
                                echo \Carbon\Carbon::parse($times[0])->format('h:i A') . ' - ' . \Carbon\Carbon::parse($times[1])->format('h:i A');
                            } else {
                                echo $booking->time_slot_id;
                            }
                        ?>
                    </small>
                </td>
                <td style="text-align: right;">₹{{ number_format($booking->price, 2) }}</td>
            </tr>
            <tr class="amount-row">
                <td colspan="4" style="text-align: right;">Total Amount Paid</td>
                <td style="text-align: right;">₹{{ number_format($booking->price, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Information -->
    <div class="payment-info">
        <strong>Payment Information:</strong><br>
        <table style="width:100%; margin-top:5px;">
            <tr>
                <td style="width:50%;">
                    Payment Method: <strong>{{ $booking->payment_method == 'razorpay' ? 'Razorpay' : 'QR Scan/Manual' }}</strong><br>
                    Status: <strong>{{ ucfirst($booking->payment_status) }}</strong>
                </td>
                <td style="width:50%;">
                    @if($booking->payment_method == 'razorpay' && $booking->razorpay_payment_id)
                        Transaction ID: <strong>{{ $booking->razorpay_payment_id }}</strong><br>
                        Order ID: <strong>{{ $booking->razorpay_order_id }}</strong>
                    @elseif($booking->payment_method == 'manual_qr')
                        Reference: <strong>{{ $booking->booking_id }}</strong>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing ParkEase. Please present this ticket upon entry and exit.</p>
        <p>If you have any questions concerning this invoice, contact our support team at support@parkease.com</p>
        <p style="margin-top: 15px; font-size: 10px;">This is a computer-generated document. No signature is required.</p>
    </div>

</div>

</body>
</html>
