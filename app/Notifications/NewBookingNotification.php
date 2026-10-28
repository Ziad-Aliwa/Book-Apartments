<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_booking',
            'booking_id' => $this->booking->id,
            'title' => 'New Booking Request',
            'message' => "{$this->booking->user->name} requested a booking for '{$this->booking->apartment->title}' ({$this->booking->start_date} to {$this->booking->end_date}).",
            'url' => route('apartments.my_bookings'),
        ];
    }
}
