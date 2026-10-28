<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request)
    {
        $booking = Booking::with('apartment')->findOrFail($request->booking_id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        if ($booking->status === 'canceled' || $booking->status === 'expired') {
            return back()->with('error', 'Cannot review a canceled or expired booking.');
        }

        if ($booking->review()->exists()) {
            return back()->with('error', 'You have already submitted a review for this booking.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'apartment_id' => $booking->apartment_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Thank you! Your review has been published.');
    }
}
