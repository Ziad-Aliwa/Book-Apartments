<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $apartments = Auth::user()->wishlists()
            ->with(['images', 'reviews', 'user'])
            ->latest('wishlists.created_at')
            ->paginate(9);

        return view('wishlists.index', compact('apartments'));
    }

    public function toggle(Apartment $apartment)
    {
        $user = Auth::user();
        $isFavorited = $user->wishlists()->where('apartment_id', $apartment->id)->exists();

        if ($isFavorited) {
            $user->wishlists()->detach($apartment->id);
            $favorited = false;
            $message = 'Removed from your wishlist.';
        } else {
            $user->wishlists()->attach($apartment->id);
            $favorited = true;
            $message = 'Added to your wishlist!';
        }

        if (request()->wantsJson()) {
            return response()->json([
                'favorited' => $favorited,
                'message' => $message,
                'count' => $user->wishlists()->count(),
            ]);
        }

        return back()->with('success', $message);
    }
}
