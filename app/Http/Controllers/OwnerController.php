<?php

namespace App\Http\Controllers;

use App\Models\ParkingLot;
use App\Models\Slot;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    public function storeParkingLot(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'pincode' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'car_price' => 'required|numeric|min:0',
            'bike_price' => 'required|numeric|min:0',
            'bus_price' => 'required|numeric|min:0',
            'opening_time' => 'required|string',
            'closing_time' => 'required|string',
            'car_slots' => 'required|integer|min:0',
            'bike_slots' => 'required|integer|min:0',
            'bus_slots' => 'required|integer|min:0',
        ]);

        $parkingLot = ParkingLot::create(array_merge($validated, [
            'owner_id' => Auth::id(),
            'layout_type' => 'dynamic',
        ]));

        $this->generateVehicleSlots($parkingLot, $validated['car_slots'], $validated['bike_slots'], $validated['bus_slots']);

        return response()->json([
            'message' => 'Parking lot created successfully',
            'parking_lot' => $parkingLot
        ]);
    }

    public function manageLot($id)
    {
        $parkingLot = ParkingLot::where('_id', $id)->where('owner_id', Auth::id())->firstOrFail();
        return view('owner.manage', compact('parkingLot'));
    }

    public function storeManualBooking(Request $request)
    {
        $validated = $request->validate([
            'parking_lot_id' => 'required|string',
            'slot_ids' => 'required|array',
            'slot_ids.*' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'date' => 'required|date',
            'time_slot_id' => 'required|string',
        ]);
    
        $parkingLot = ParkingLot::findOrFail($validated['parking_lot_id']);
        $bookings = [];
    
        foreach ($validated['slot_ids'] as $slotId) {
            $slot = Slot::where('_id', $slotId)->where('parking_lot_id', $parkingLot->_id)->firstOrFail();
    
            // Check if already booked
            $exists = Booking::where('slot_id', $slot->_id)
                ->where('date', $validated['date'])
                ->where('time_slot_id', $validated['time_slot_id'])
                ->whereIn('status', ['confirmed', 'pending'])
                ->exists();
    
            if ($exists) {
                return response()->json(['message' => "Slot {$slot->slot_number} already booked for this time."], 409);
            }
    
            $price = match ($slot->vehicle_type) {
                'car' => $parkingLot->car_price,
                'bike' => $parkingLot->bike_price,
                'bus' => $parkingLot->bus_price,
                default => 0,
            };
    
            $booking = Booking::create([
                'user_id' => Auth::id(), // Booked by the owner
                'parking_lot_id' => $parkingLot->_id,
                'slot_id' => $slot->_id,
                'time_slot_id' => $validated['time_slot_id'],
                'date' => $validated['date'],
                'price' => $price,
                'status' => 'confirmed',
                'booking_id' => strtoupper(\Illuminate\Support\Str::random(10)),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'is_manual' => true,
                'payment_method' => 'cash' // Record that it was a manual cash booking
            ]);

            // Generate Invoice
            \App\Http\Controllers\InvoiceController::generateInvoice($booking);

            // Record transaction for owner dashboard
            Transaction::create([
                'user_id' => Auth::id(), // The owner who created it
                'owner_id' => Auth::id(), // The owner themselves
                'booking_id' => $booking->_id,
                'amount' => $price,
                'type' => 'earning',
                'status' => 'completed',
                'payment_method' => 'cash',
                'description' => "Manual Booking (Cash): Slot {$slot->slot_number} - {$validated['customer_name']}",
                'metadata' => [
                    'date' => $validated['date'],
                    'time_slot' => $validated['time_slot_id'],
                    'customer' => $validated['customer_name']
                ]
            ]);
            
            $bookings[] = $booking;
        }
    
        return response()->json([
            'message' => 'Manual bookings successful',
            'bookings' => $bookings
        ], 201);
    }

    private function generateVehicleSlots(ParkingLot $parkingLot, $carSlots, $bikeSlots, $busSlots)
    {
        $slots = [];
        
        for ($i = 1; $i <= $carSlots; $i++) {
            $slots[] = [
                'parking_lot_id' => $parkingLot->_id,
                'slot_number' => 'C' . $i,
                'vehicle_type' => 'car',
                'slot_type' => 'standard',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        for ($i = 1; $i <= $bikeSlots; $i++) {
            $slots[] = [
                'parking_lot_id' => $parkingLot->_id,
                'slot_number' => 'B' . $i,
                'vehicle_type' => 'bike',
                'slot_type' => 'standard',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        for ($i = 1; $i <= $busSlots; $i++) {
            $slots[] = [
                'parking_lot_id' => $parkingLot->_id,
                'slot_number' => 'BS' . $i,
                'vehicle_type' => 'bus',
                'slot_type' => 'standard',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($slots)) {
            Slot::insert($slots);
        }
    }
}
