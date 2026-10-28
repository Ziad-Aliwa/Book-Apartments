<?php

namespace App\Policies;

use App\Models\Apartment;
use App\Models\User;

class ApartmentPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Apartment $apartment): bool
    {
        if ($apartment->admin_approve) {
            return true;
        }

        return $user !== null && ($user->id === $apartment->user_id || $user->role === 'admin');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Apartment $apartment): bool
    {
        return $user->id === $apartment->user_id || $user->role === 'admin';
    }

    public function delete(User $user, Apartment $apartment): bool
    {
        return $user->id === $apartment->user_id || $user->role === 'admin';
    }

    public function book(User $user, Apartment $apartment): bool
    {
        // Prevent owners from booking their own apartments
        return $user->id !== $apartment->user_id;
    }
}
