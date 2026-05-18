<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->table('bookings', function (Blueprint $collection) {
            $collection->index('booking_id');
            $collection->index('parking_lot_id');
            $collection->index('parking_id');
            $collection->index('status');
            $collection->index('date');
            $collection->index('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->table('bookings', function (Blueprint $collection) {
            $collection->dropIndex(['booking_id']);
            $collection->dropIndex(['parking_lot_id']);
            $collection->dropIndex(['parking_id']);
            $collection->dropIndex(['status']);
            $collection->dropIndex(['date']);
            $collection->dropIndex(['booking_date']);
        });
    }
};
