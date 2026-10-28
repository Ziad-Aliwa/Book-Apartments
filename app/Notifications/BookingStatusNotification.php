<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public Booking $booking;
    public string $status;

    public function __construct(Booking $booking, string $status)
    {
        $this->booking = $booking;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_status',
            'booking_id' => $this->booking->id,
            'title' => 'Booking Status Updated',
            'message' => "Your booking for '{$this->booking->apartment->title}' has been {$this->status}.",
            'url' => route('bookings.show', $this->booking->id),
        ];
    }
}
