<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function unpaid()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->where('payment_status', 'unpaid')
            ->whereNotIn('status', ['canceled', 'expired'])
            ->with('apartment')
            ->get();

        return view('payments.unpaid', compact('bookings'));
    }

    public function pay($booking_id)
    {
        $booking = Booking::where('id', $booking_id)
            ->where('user_id', Auth::id())
            ->where('payment_status', 'unpaid')
            ->whereNotIn('status', ['canceled', 'expired'])
            ->firstOrFail();

        DB::transaction(function () use ($booking) {
            $booking->update([
                'payment_status' => 'paid',
                'paid_amount' => $booking->total_price,
            ]);

            Payments::create([
                'booking_id' => $booking->id,
                'amount_paid' => $booking->total_price,
                'payment_date' => now(),
            ]);
        });

        return redirect()->route('bookings.index')->with('success', 'Payment successful! Payment record created.');
    }
}
