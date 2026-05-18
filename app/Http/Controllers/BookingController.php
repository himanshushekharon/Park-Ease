<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ParkingLot;
use App\Models\Slot;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use App\Models\Transaction;
use Exception;

class BookingController extends Controller
{
    public function getSlots(Request $request, $parkingLotId)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'required|string',
        ]);

        $slots = Slot::where('parking_lot_id', $parkingLotId)
            ->select(['_id', 'slot_number', 'vehicle_type', 'row', 'column'])
            ->get();
        
        $bookedSlotIds = Booking::where('parking_lot_id', $parkingLotId)
            ->where('date', $validated['date'])
            ->where('time_slot_id', $validated['time_slot_id'])
            ->whereIn('status', ['confirmed', 'pending'])
            ->pluck('slot_id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $slotsData = $slots->map(function ($slot) use ($bookedSlotIds) {
            $id = (string)$slot->_id;
            return [
                'id' => $id,
                '_id' => $id,
                'slot_number' => $slot->slot_number,
                'vehicle_type' => $slot->vehicle_type,
                'is_booked' => in_array($id, $bookedSlotIds)
            ];
        });

        return response()->json(['slots' => $slotsData]);
    }

    public function createBooking(Request $request)
    {
        $validated = $request->validate([
            'parking_lot_id' => 'required|string',
            'slot_ids' => 'required|array',
            'slot_ids.*' => 'required|string',
            'time_slot_id' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'vehicle_type' => 'nullable|string',
            'email' => 'required|email',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'payment_method' => 'required|string|in:razorpay,manual_qr',
            'razorpay_payment_id' => 'required_if:payment_method,razorpay|string|nullable',
            'razorpay_order_id' => 'required_if:payment_method,razorpay|string|nullable',
            'razorpay_signature' => 'required_if:payment_method,razorpay|string|nullable'
        ]);

        // Verify Razorpay Signature only if method is razorpay
        if ($validated['payment_method'] === 'razorpay') {
            try {
                $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
                $attributes = array(
                    'razorpay_order_id' => $validated['razorpay_order_id'],
                    'razorpay_payment_id' => $validated['razorpay_payment_id'],
                    'razorpay_signature' => $validated['razorpay_signature']
                );
                $api->utility->verifyPaymentSignature($attributes);
            } catch (Exception $e) {
                return response()->json(['message' => 'Payment verification failed: ' . $e->getMessage()], 400);
            }
        }

        $parkingLot = ParkingLot::findOrFail($validated['parking_lot_id']);
        $bookings = [];

        foreach ($validated['slot_ids'] as $slotId) {
            $slot = Slot::where('_id', $slotId)
                ->where('parking_lot_id', $parkingLot->_id)
                ->first();

            if (!$slot) {
                return response()->json(['message' => 'Invalid slot selection.'], 400);
            }

            // Double-Booking Prevention
            $existingBooking = Booking::where('parking_lot_id', $parkingLot->_id)
                ->where('slot_id', $slot->_id)
                ->where('date', $validated['date'])
                ->where('time_slot_id', $validated['time_slot_id'])
                ->whereIn('status', ['confirmed', 'pending'])
                ->exists();

            if ($existingBooking) {
                return response()->json([
                    'message' => "Slot {$slot->slot_number} is already booked.",
                ], 409);
            }

            $price = match ($slot->vehicle_type) {
                'car' => $parkingLot->car_price,
                'bike' => $parkingLot->bike_price,
                'bus' => $parkingLot->bus_price,
                default => 0,
            };

            $userId = Auth::id();
            $userEmail = $request->input('email');
            
            if (!$userId && $userEmail) {
                $existingUser = \App\Models\User::where('email', $userEmail)->first();
                if ($existingUser) {
                    $userId = $existingUser->_id;
                }
            }

            $booking = Booking::create([
                'user_id' => $userId,
                'booking_email' => $userEmail,
                'parking_lot_id' => $parkingLot->_id,
                'slot_id' => $slot->_id,
                'time_slot_id' => $validated['time_slot_id'],
                'date' => $validated['date'],
                'price' => $price,
                'status' => 'confirmed',
                'payment_status' => $validated['payment_method'] === 'manual_qr' ? 'pending_verification' : 'paid',
                'booking_id' => strtoupper(Str::random(10)),
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'vehicle_type' => $slot->vehicle_type,
                'payment_method' => $validated['payment_method'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'] ?? null,
                'razorpay_order_id' => $validated['razorpay_order_id'] ?? null,
            ]);

            // Automatically generate the PDF invoice/ticket
            \App\Http\Controllers\InvoiceController::generateInvoice($booking);

            // Create Transaction Record
            Transaction::create([
                'user_id' => $userId,
                'owner_id' => $parkingLot->owner_id,
                'booking_id' => $booking->_id,
                'amount' => $price,
                'type' => 'earning',
                'status' => $validated['payment_method'] === 'manual_qr' ? 'pending' : 'completed',
                'payment_method' => $validated['payment_method'],
                'description' => "Booking for slot {$slot->slot_number} at {$parkingLot->name}",
                'metadata' => [
                    'date' => $validated['date'],
                    'time_slot' => $validated['time_slot_id']
                ]
            ]);

            $bookings[] = $booking;
        }

        return response()->json([
            'message' => 'Bookings confirmed successfully',
            'bookings' => $bookings
        ], 201);
    }

    public function cancelBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // Security Check: Only the owner of the booking can cancel it
        $user = Auth::user();
        if ($booking->user_id !== $user->_id && $booking->booking_email !== $user->email) {
            return response()->json(['message' => 'Unauthorized. This is not your booking.'], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Booking is already cancelled.'], 400);
        }

        // Calculate refund
        // Assume time_slot_id is "HH:mm-HH:mm"
        $startTimeStr = explode('-', $booking->time_slot_id)[0];
        $bookingStart = \Carbon\Carbon::parse($booking->date . ' ' . $startTimeStr);
        $now = now();
        
        $hoursDiff = $now->diffInHours($bookingStart, false);
        $minsDiff = $now->diffInMinutes($bookingStart, false);

        $refundPercentage = 0;
        if ($minsDiff >= 120) {
            $refundPercentage = 100;
        } elseif ($minsDiff >= 30) {
            $refundPercentage = 50;
        } else {
            $refundPercentage = 0;
        }

        $refundAmount = ($booking->price * $refundPercentage) / 100;

        $booking->status = 'cancelled';
        $booking->refund_amount = $refundAmount;
        $booking->refund_status = $refundAmount > 0 ? 'processing' : 'none';
        $booking->cancelled_at = now();
        $booking->save();

        // Create Transaction Record for Refund
        if ($refundAmount > 0) {
            Transaction::create([
                'user_id' => $user->_id,
                'owner_id' => $booking->parkingLot->owner_id ?? null,
                'booking_id' => $booking->_id,
                'amount' => $refundAmount,
                'type' => 'refund',
                'status' => 'completed',
                'payment_method' => $booking->payment_method ?? 'original',
                'description' => "Refund for cancelled booking {$booking->booking_id}",
                'metadata' => [
                    'original_amount' => $booking->price,
                    'refund_percentage' => $refundPercentage
                ]
            ]);
        }

        return response()->json([
            'message' => 'Booking cancelled successfully',
            'refund_amount' => $refundAmount,
            'refund_percentage' => $refundPercentage
        ]);
    }

    public function extendBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'minutes' => 'required|integer|min:15|max:120',
        ]);

        $booking = Booking::findOrFail($id);
        $user = Auth::user();

        if ($booking->user_id !== $user->_id && $booking->booking_email !== $user->email) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Only active bookings can be extended.'], 400);
        }

        // Parse current end time
        $times = explode('-', $booking->time_slot_id);
        $currentEnd = \Carbon\Carbon::parse($booking->date . ' ' . $times[1]);
        $newEnd = $currentEnd->copy()->addMinutes($validated['minutes']);
        
        // Availability Check
        // Check if another booking starts before our new end time and ends after our old end time
        $collision = Booking::where('parking_lot_id', $booking->parking_lot_id)
            ->where('slot_id', $booking->slot_id)
            ->where('date', $booking->date)
            ->where('_id', '!=', $booking->_id)
            ->where('status', 'confirmed')
            ->get()
            ->filter(function ($b) use ($currentEnd, $newEnd) {
                $bTimes = explode('-', $b->time_slot_id);
                $bStart = \Carbon\Carbon::parse($b->date . ' ' . $bTimes[0]);
                // If the existing booking starts before our new end time AND after our current end time
                return $bStart->lt($newEnd) && $bStart->gte($currentEnd);
            })->first();

        if ($collision) {
            return response()->json(['message' => 'Cannot extend: The slot is reserved by another user starting at ' . explode('-', $collision->time_slot_id)[0]], 409);
        }

        // Calculate Cost (Pro-rated)
        // For simplicity: (original_price / 60) * minutes * 1.2 (premium for extension)
        $extensionCost = round(($booking->price / 60) * $validated['minutes'] * 1.2);

        // Update Booking
        $newTimeSlotId = $times[0] . '-' . $newEnd->format('H:i');
        $booking->time_slot_id = $newTimeSlotId;
        $booking->price += $extensionCost;
        $booking->save();

        // Create Transaction
        Transaction::create([
            'user_id' => $user->_id,
            'owner_id' => $booking->parkingLot->owner_id ?? null,
            'booking_id' => $booking->_id,
            'amount' => $extensionCost,
            'type' => 'payment',
            'status' => 'completed',
            'payment_method' => $booking->payment_method ?? 'original',
            'description' => "Extension ({$validated['minutes']} mins) for booking {$booking->booking_id}",
            'metadata' => [
                'additional_minutes' => $validated['minutes'],
                'new_end_time' => $newEnd->format('H:i')
            ]
        ]);

        return response()->json([
            'message' => 'Session extended successfully',
            'new_end_time' => $newEnd->format('H:i'),
            'cost' => $extensionCost
        ]);
    }
}
