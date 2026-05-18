<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\ParkingLot;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $user = Auth::user();
        if (!$user) return redirect('/login');

        $bookings = Booking::where(function($query) use ($user) {
                $query->where('user_id', $user->_id)
                      ->orWhere('booking_email', $user->email);
            })
            ->with(['parkingLot:id,name,city,pincode,latitude,longitude', 'slot:id,slot_number,vehicle_type'])
            ->orderBy('date', 'desc')
            ->orderBy('time_slot_id', 'desc')
            ->get();

        $now = Carbon::now();
        
        $categorized = [
            'active' => [],
            'upcoming' => [],
            'completed' => [],
            'cancelled' => []
        ];

        foreach ($bookings as $booking) {
            if ($booking->status === 'cancelled') {
                $categorized['cancelled'][] = $booking;
                continue;
            }

            $times = explode('-', $booking->time_slot_id);
            $start = Carbon::parse($booking->date . ' ' . $times[0]);
            $end = Carbon::parse($booking->date . ' ' . $times[1]);

            if ($now->between($start, $end)) {
                $categorized['active'][] = $booking;
            } elseif ($now->lt($start)) {
                $categorized['upcoming'][] = $booking;
            } else {
                $categorized['completed'][] = $booking;
            }
        }

        $transactions = Transaction::where('user_id', $user->_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $stats = [
            'total_bookings' => $bookings->count(),
            'active_sessions' => count($categorized['active']),
            'total_spent' => $transactions->where('type', 'payment')->where('status', 'completed')->sum('amount'),
            'total_refunds' => $transactions->where('type', 'refund')->where('status', 'completed')->sum('amount'),
        ];
            
        return view('dashboard', compact('categorized', 'transactions', 'stats'));
    }

    public function ownerDashboard()
    {
        $owner = Auth::user();

        if ($owner->kyc_status !== 'verified') {
            return redirect('/owner/kyc');
        }
        
        $parkingLots = ParkingLot::where('owner_id', $owner->_id)->get();
        
        $totalParkingLots = $parkingLots->count();
        $totalSlots = \App\Models\Slot::whereIn('parking_lot_id', $parkingLots->pluck('_id'))->count();
        $activeBookingsCount = Booking::whereIn('parking_lot_id', $parkingLots->pluck('_id'))
            ->where('status', 'confirmed')
            ->where('date', '>=', date('Y-m-d'))
            ->count();

        $transactions = Transaction::where('owner_id', $owner->_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalEarnings = $transactions->where('status', 'completed')->sum('amount');
            
        return view('owner.dashboard', compact('parkingLots', 'totalParkingLots', 'totalSlots', 'activeBookingsCount', 'transactions', 'totalEarnings'));
    }

    public function settings()
    {
        return view('settings');
    }
}
