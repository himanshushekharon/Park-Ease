<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'start_time',
    'end_time'
])]
class TimeSlot extends Model
{
    protected $collection = 'time_slots';
}
