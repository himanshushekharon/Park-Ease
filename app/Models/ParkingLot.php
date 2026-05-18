<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'owner_id',
    'name',
    'address',
    'pincode',
    'city',
    'latitude',
    'longitude',
    'car_price',
    'bike_price',
    'bus_price',
    'layout_type',
    'total_rows',
    'slots_per_row',
    'car_slots',
    'bike_slots',
    'bus_slots',
    'opening_time',
    'closing_time'
])]
class ParkingLot extends Model
{
    protected $collection = 'parking_lots';

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function slots()
    {
        return $this->hasMany(Slot::class, 'parking_lot_id');
    }
}
