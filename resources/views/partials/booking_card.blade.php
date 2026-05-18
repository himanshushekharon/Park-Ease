<div class="col-md-6 col-lg-4 mb-4">
    <div class="surface-card p-4 h-100 d-flex flex-column hover-lift">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h5 class="text-h5 mb-1 text-truncate" style="max-width: 200px;">{{ $booking->parkingLot->name ?? 'Unknown Parking Hub' }}</h5>
                <p class="text-small text-muted mb-0"><i class="bi bi-geo-alt-fill text-primary"></i> {{ $booking->parkingLot->city ?? '' }}</p>
            </div>
            
            @php
                $statusClass = '';
                $statusLabel = ucfirst($type);
                if ($type === 'active') {
                    $statusClass = 'bg-info bg-opacity-10 text-info border-info border-opacity-25';
                    $statusLabel = '<span class="pulse-dot me-2"></span>Live Session';
                } elseif ($type === 'upcoming') {
                    $statusClass = 'bg-success bg-opacity-10 text-success border-success border-opacity-25';
                } elseif ($type === 'completed') {
                    $statusClass = 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25';
                } elseif ($type === 'cancelled') {
                    $statusClass = 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25';
                }
            @endphp
            <div class="badge rounded-pill {{ $statusClass }} border px-3 py-2 fw-bold text-uppercase d-flex align-items-center" style="letter-spacing: 0.5px; font-size: 0.65rem;">
                {!! $statusLabel !!}
            </div>
        </div>

        <div class="d-flex align-items-center mb-4 p-3 rounded-3" style="background: var(--bg-elevated); border: 1px solid var(--border-default);">
            <div class="flex-shrink-0 me-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px; background: var(--bg-surface); border-color: var(--border-strong) !important;">
                    <h4 class="text-h4 mb-0">{{ $booking->slot->slot_number ?? '?' }}</h4>
                </div>
            </div>
            <div>
                <div class="text-small text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">{{ strtoupper($booking->vehicle_type ?? 'CAR') }} Slot</div>
                <div class="fw-bold fs-5 text-primary">₹{{ $booking->price }}</div>
            </div>
            
            <div class="ms-auto text-end">
                @php
                    $startTimeStr = explode('-', $booking->time_slot_id)[0];
                    $endTimeStr = explode('-', $booking->time_slot_id)[1];
                    $start = \Carbon\Carbon::parse($booking->date . ' ' . $startTimeStr);
                    $end = \Carbon\Carbon::parse($booking->date . ' ' . $endTimeStr);
                @endphp

                @if($type === 'active')
                    <div class="text-small text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">Remaining</div>
                    <div class="text-h6 live-timer text-primary" data-endtime="{{ $end->toIso8601String() }}" style="font-family: 'Outfit', monospace; margin-bottom: 0;">--:--:--</div>
                @elseif($type === 'upcoming')
                    @php $minsDiff = now()->diffInMinutes($start, false); @endphp
                    <div class="text-small text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">Starts In</div>
                    <div class="text-h6 text-success" style="margin-bottom: 0;">
                        {{ $minsDiff > 60 ? floor($minsDiff/60).'h '.($minsDiff%60).'m' : $minsDiff.'m' }}
                    </div>
                @endif
            </div>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-6">
                <div class="p-2 rounded-2 text-center" style="background: var(--bg-elevated); border: 1px solid var(--border-default);">
                    <div class="text-small fw-bold text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.5px;">Date</div>
                    <div class="fw-bold text-small text-primary">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 rounded-2 text-center" style="background: var(--bg-elevated); border: 1px solid var(--border-default);">
                    <div class="text-small fw-bold text-uppercase text-muted" style="font-size: 0.6rem; letter-spacing: 0.5px;">Window</div>
                    <div class="fw-bold text-small text-primary text-nowrap">{{ $booking->time_slot_id }}</div>
                </div>
            </div>
        </div>
        
        @if($type === 'cancelled')
            <div class="mt-auto mb-3 p-3 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 d-flex justify-content-between align-items-center">
                <span class="text-danger fw-bold text-small text-uppercase">Refund</span>
                <span class="fw-bold text-danger">₹{{ $booking->refund_amount ?? 0 }} <span class="badge bg-danger ms-1">{{ $booking->refund_status ?? 'N/A' }}</span></span>
            </div>
        @endif

        <div class="mt-auto d-flex gap-2">
            @if($type === 'active')
                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $booking->parkingLot->latitude }},{{ $booking->parkingLot->longitude }}" target="_blank" class="btn btn-secondary flex-grow-1">
                    <i class="bi bi-geo-alt"></i> Map
                </a>
                <div class="dropdown flex-grow-1">
                    <button class="btn btn-brand w-100" data-bs-toggle="dropdown">
                        <i class="bi bi-ticket-perforated"></i> Ticket
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-premium">
                        <li><a class="dropdown-item dropdown-item-premium" href="javascript:void(0)" onclick="openExtendModal('{{ $booking->_id }}', '{{ $booking->parkingLot->name }}', '{{ $booking->slot->slot_number }}')"><i class="bi bi-clock-history"></i> Extend Session</a></li>
                        <li><a class="dropdown-item dropdown-item-premium" href="javascript:void(0)" onclick="openTicketModal('{{ $booking->_id }}', '{{ $booking->parkingLot->name }}', '{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}', '{{ $booking->time_slot_id }}', '{{ $booking->slot->slot_number }}', '{{ strtoupper($booking->vehicle_type ?? 'CAR') }}')"><i class="bi bi-qr-code-scan"></i> E-Ticket</a></li>
                        <li><div class="dropdown-divider dropdown-divider-premium"></div></li>
                        <li><a class="dropdown-item dropdown-item-premium" href="{{ route('invoice.view', $booking->_id) }}" target="_blank"><i class="bi bi-eye"></i> View PDF</a></li>
                        <li><a class="dropdown-item dropdown-item-premium" href="{{ route('invoice.download', $booking->_id) }}"><i class="bi bi-download"></i> Download PDF</a></li>
                    </ul>
                </div>
            @elseif($type === 'upcoming')
                @php
                    $minsDiff = now()->diffInMinutes($start, false);
                    $refundPreview = $minsDiff >= 120 ? $booking->price : ($minsDiff >= 30 ? $booking->price * 0.5 : 0);
                @endphp
                <button class="btn btn-danger flex-grow-1" onclick="openCancelModal('{{ $booking->_id }}', '{{ $booking->parkingLot->name }}', '{{ $booking->slot->slot_number }}', '{{ $booking->price }}', '{{ $refundPreview }}')">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <div class="dropdown flex-grow-1">
                    <button class="btn btn-secondary w-100" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i> Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-premium">
                        <li><a class="dropdown-item dropdown-item-premium" href="https://www.google.com/maps/dir/?api=1&destination={{ $booking->parkingLot->latitude }},{{ $booking->parkingLot->longitude }}" target="_blank"><i class="bi bi-geo-alt"></i> Navigate</a></li>
                        <li><a class="dropdown-item dropdown-item-premium" href="javascript:void(0)" onclick="openTicketModal('{{ $booking->_id }}', '{{ $booking->parkingLot->name }}', '{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}', '{{ $booking->time_slot_id }}', '{{ $booking->slot->slot_number }}', '{{ strtoupper($booking->vehicle_type ?? 'CAR') }}')"><i class="bi bi-qr-code-scan"></i> E-Ticket</a></li>
                        <li><div class="dropdown-divider dropdown-divider-premium"></div></li>
                        <li><a class="dropdown-item dropdown-item-premium" href="{{ route('invoice.view', $booking->_id) }}" target="_blank"><i class="bi bi-eye"></i> View PDF</a></li>
                        <li><a class="dropdown-item dropdown-item-premium" href="{{ route('invoice.download', $booking->_id) }}"><i class="bi bi-download"></i> Download PDF</a></li>
                    </ul>
                </div>
            @else
                <a href="/parking/{{ $booking->parking_lot_id }}" class="btn btn-brand flex-grow-1">
                    <i class="bi bi-arrow-repeat"></i> Rebook Spot
                </a>
                <div class="dropdown flex-grow-1">
                    <button class="btn btn-secondary w-100" data-bs-toggle="dropdown">
                        <i class="bi bi-receipt"></i> Invoice
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-premium">
                        <li><a class="dropdown-item dropdown-item-premium" href="{{ route('invoice.view', $booking->_id) }}" target="_blank"><i class="bi bi-eye"></i> View PDF</a></li>
                        <li><a class="dropdown-item dropdown-item-premium" href="{{ route('invoice.download', $booking->_id) }}"><i class="bi bi-download"></i> Download PDF</a></li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--color-info);
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        animation: pulse-ring 2s infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(59, 130, 246, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
</style>
