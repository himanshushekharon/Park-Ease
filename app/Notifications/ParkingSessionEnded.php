<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class ParkingSessionEnded extends Notification
{
    use Queueable;

    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function toArray($notifiable): array
    {
        return ['mail'];
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $slotName = $this->booking->slot ? $this->booking->slot->slot_number : 'N/A';
        $parkingName = $this->booking->parkingLot ? $this->booking->parkingLot->name : 'ParkEase Hub';

        return (new MailMessage)
            ->subject('Parking Session Ended - ParkEase')
            ->greeting('Hello, ' . ($this->booking->customer_name ?? 'Valued Customer') . '!')
            ->line('Your parking session at ' . $parkingName . ' has officially ended.')
            ->line('Session Summary:')
            ->line('• Booking ID: #' . $this->booking->booking_id)
            ->line('• Occupied Slot: Slot ' . $slotName)
            ->line('• Vehicle registration: ' . ($this->booking->vehicle_number ?? 'N/A'))
            ->line('• Cost: ₹' . $this->booking->price)
            ->line('• Scheduled Window: ' . $this->booking->time_slot_id)
            ->line('Your slot has been automatically released back to the pool for other commuters.')
            ->line('We hope you had a hassle-free parking experience. If you have any feedback, reply to this email!')
            ->salutation('Best Regards, ' . PHP_EOL . 'The ParkEase Support Team');
    }
}
