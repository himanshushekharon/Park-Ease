<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id',
    'parking_lot_id',
    'slot_id',
    'time_slot_id',
    'date',
    'price',
    'status',
    'booking_id',
    'customer_name',
    'customer_phone',
    'booking_email',
    'vehicle_type',
    'payment_status',
    'is_manual',
    'razorpay_payment_id',
    'razorpay_order_id',
    'payment_method',
    'invoice_path',
    'invoice_number',
    'generated_at',
    'refund_amount',
    'refund_status',
    'cancelled_at'
]) ]
class Booking extends Model
{
    protected $collection = 'bookings';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class, 'parking_lot_id');
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'booking_id');
    }
}
