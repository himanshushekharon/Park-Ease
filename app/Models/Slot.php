<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'parking_lot_id',
    'slot_number',
    'row',
    'column',
    'x_position',
    'y_position',
    'slot_type',
    'vehicle_type',
    'status'
])]
class Slot extends Model
{
    protected $collection = 'slots';

    public function parkingLot()
    {
        return $this->belongsTo(ParkingLot::class, 'parking_lot_id');
    }
}
