<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UpdateBookingStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update booking statuses based on current IST time (completed or expired)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('Asia/Kolkata');
        $this->info("Checking booking statuses at: " . $now->toDateTimeString());

        // Find active, upcoming, or attended bookings that have reached or passed their end times
        $bookings = Booking::whereNotIn('status', ['completed', 'expired', 'cancelled'])->get();

        $updatedCount = 0;

        foreach ($bookings as $booking) {
            $end = $booking->getEndCarbon();
            if (!$end) continue;

            if ($now->greaterThan($end)) {
                $oldStatus = $booking->status;

                if ($oldStatus === 'attended' || $oldStatus === 'active') {
                    // Attended or active -> completed
                    $booking->update([
                        'status' => 'completed',
                        'completed_at' => $now->toDateTimeString(),
                    ]);
                    $this->info("Booking #{$booking->booking_id} marked as COMPLETED.");
                    Log::info("Booking #{$booking->booking_id} auto-completed.", ['booking_id' => $booking->_id]);

                    // Dispatch notification
                    try {
                        if ($booking->user) {
                            $booking->user->notify(new \App\Notifications\ParkingSessionEnded($booking));
                        } else if ($booking->booking_email) {
                            Notification::route('mail', $booking->booking_email)
                                ->notify(new \App\Notifications\ParkingSessionEnded($booking));
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to send session ended notification for booking #{$booking->booking_id}: " . $e->getMessage());
                    }

                    $updatedCount++;
                } else if ($oldStatus === 'upcoming' || $oldStatus === 'confirmed') {
                    // Upcoming -> expired (never checked in/attended)
                    $booking->update([
                        'status' => 'expired',
                        'expired_at' => $now->toDateTimeString(),
                    ]);
                    $this->info("Booking #{$booking->booking_id} marked as EXPIRED.");
                    Log::info("Booking #{$booking->booking_id} auto-expired (no check-in).", ['booking_id' => $booking->_id]);

                    $updatedCount++;
                }
            }
        }

        $this->info("Status update run complete. Total updated: {$updatedCount}");
    }
}
