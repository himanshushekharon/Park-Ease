<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ParkingLot;
use App\Models\Slot;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestBackendFlow extends Command
{
    protected $signature = 'test:backend';
    protected $description = 'Test the ParkEase backend flow';

    public function handle()
    {
        $this->info("--- Starting Backend Logic Test ---");

        // Clean up previous test data if needed
        User::where('email', 'testowner@example.com')->delete();
        User::where('email', 'testuser@example.com')->delete();

        // 1. Create an Owner
        $owner = User::create([
            'name' => 'Test Owner',
            'email' => 'testowner@example.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);
        $this->info("1. Owner created successfully: {$owner->email}");

        // 2. Create a Parking Lot & Grid
        $parkingLot = ParkingLot::create([
            'owner_id' => $owner->_id,
            'name' => 'City Center Parking',
            'address' => '123 Main St',
            'pincode' => '10001',
            'city' => 'New York',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'car_price' => 50.00,
            'bike_price' => 20.00,
            'bus_price' => 100.00,
            'layout_type' => 'grid',
            'total_rows' => 3,
            'slots_per_row' => 4,
        ]);
        
        // Simulate grid generation logic from OwnerController
        $this->generateGridSlots($parkingLot, 3, 4);
        
        $slotsCount = Slot::where('parking_lot_id', $parkingLot->_id)->count();
        $this->info("2. Parking Lot created. Generated {$slotsCount} slots (Expected: 12).");

        // 3. Create a User
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
        $this->info("3. User created successfully.");

        // 4. Create a Booking
        $slotToBook = Slot::where('parking_lot_id', $parkingLot->_id)->first();
        $date = '2026-05-01';
        $timeSlotId = '10:00-11:00';

        $price = match ($slotToBook->vehicle_type ?? 'car') {
            'car' => $parkingLot->car_price,
            'bike' => $parkingLot->bike_price,
            'bus' => $parkingLot->bus_price,
            default => 0,
        };

        $booking = Booking::create([
            'user_id' => $user->_id,
            'parking_lot_id' => $parkingLot->_id,
            'slot_id' => $slotToBook->_id,
            'time_slot_id' => $timeSlotId,
            'date' => $date,
            'price' => $price,
            'status' => 'confirmed',
            'booking_id' => strtoupper(Str::random(10)),
        ]);
        $this->info("4. Booking created successfully. ID: {$booking->booking_id} for Slot: {$slotToBook->slot_number}");

        // 5. Test Double Booking Prevention
        $existingBooking = Booking::where('parking_lot_id', $parkingLot->_id)
            ->where('slot_id', $slotToBook->_id)
            ->where('date', $date)
            ->where('time_slot_id', $timeSlotId)
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();

        if ($existingBooking) {
            $this->info("5. SUCCESS: Double booking prevented! System correctly recognized slot {$slotToBook->slot_number} is already booked.");
        } else {
            $this->error("5. FAILURE: Double booking check failed.");
        }

        $this->info("--- Backend Logic Test Completed ---");
    }

    private function generateGridSlots(ParkingLot $parkingLot, $rows, $cols)
    {
        $rowLabels = range('A', 'Z');
        $slots = [];
        for ($r = 0; $r < $rows; $r++) {
            for ($c = 1; $c <= $cols; $c++) {
                $rowLabel = $rowLabels[$r % 26] . ($r >= 26 ? (int)($r/26) : '');
                $slotNumber = $rowLabel . $c;
                $slots[] = [
                    'parking_lot_id' => $parkingLot->_id,
                    'slot_number' => $slotNumber,
                    'row' => $r,
                    'column' => $c,
                    'slot_type' => 'standard',
                    'status' => 'active',
                ];
            }
        }
        Slot::insert($slots);
    }
}
