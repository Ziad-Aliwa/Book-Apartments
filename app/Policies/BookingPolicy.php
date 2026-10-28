<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            || $user->id === $booking->apartment->user_id
            || $user->role === 'admin';
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id && $booking->status === 'pending';
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id || $user->role === 'admin';
    }

    public function confirm(User $user, Booking $booking): bool
    {
        return $user->id === $booking->apartment->user_id && $booking->status === 'pending';
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return ($user->id === $booking->apartment->user_id || $user->id === $booking->user_id)
            && !in_array($booking->status, ['canceled', 'expired']);
    }

    public function updatePaymentStatus(User $user, Booking $booking): bool
    {
        return $user->id === $booking->apartment->user_id || $user->role === 'admin';
    }

    public function pay(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            && $booking->payment_status === 'unpaid'
            && !in_array($booking->status, ['canceled', 'expired']);
    }
}
