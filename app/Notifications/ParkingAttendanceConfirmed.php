<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class ParkingAttendanceConfirmed extends Notification
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
            ->subject('Parking Attendance Confirmed - ParkEase')
            ->greeting('Hello, ' . ($this->booking->customer_name ?? 'Valued Customer') . '!')
            ->line('Your parking session attendance has been verified and confirmed at ' . $parkingName . '.')
            ->line('Booking Details:')
            ->line('• Booking ID: #' . $this->booking->booking_id)
            ->line('• Selected Slot: Slot ' . $slotName)
            ->line('• Vehicle Type: ' . strtoupper($this->booking->vehicle_type ?? 'N/A'))
            ->line('• Vehicle Number: ' . ($this->booking->vehicle_number ?? 'N/A'))
            ->line('• Duration window: ' . $this->booking->time_slot_id)
            ->line('Thank you for parking with ParkEase! Have a safe and happy day ahead.')
            ->salutation('Best Regards, ' . PHP_EOL . 'The ParkEase Support Team');
    }
}
