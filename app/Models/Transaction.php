<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Transaction extends Model
{
    protected $collection = 'transactions';

    protected $fillable = [
        'user_id',
        'owner_id',
        'booking_id',
        'amount',
        'type', // 'earning', 'refund'
        'status', // 'completed', 'pending', 'failed'
        'payment_method',
        'description',
        'metadata'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
