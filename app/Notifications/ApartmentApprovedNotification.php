<?php

namespace App\Notifications;

use App\Models\Apartment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApartmentApprovedNotification extends Notification
{
    use Queueable;

    public Apartment $apartment;

    public function __construct(Apartment $apartment)
    {
        $this->apartment = $apartment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'apartment_approved',
            'apartment_id' => $this->apartment->id,
            'title' => 'Apartment Approved!',
            'message' => "Great news! Your apartment listing '{$this->apartment->title}' has been approved and is now live.",
            'url' => route('apartments.show', $this->apartment->id),
        ];
    }
}
