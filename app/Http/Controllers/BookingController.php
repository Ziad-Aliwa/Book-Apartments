<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Apartment;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use App\Notifications\NewBookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['apartment.images'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create($apartment_id)
    {
        $apartment = Apartment::with('images')->findOrFail($apartment_id);

        if ($apartment->user_id === Auth::id()) {
            return redirect()->route('apartments.show', $apartment->id)
                ->withErrors(['error' => 'You cannot book your own apartment.']);
        }

        // Confirmed booking dates for calendar disabling
        $bookedRanges = Booking::where('apartment_id', $apartment->id)
            ->where('status', 'confirmed')
            ->get(['start_date', 'end_date'])
            ->map(fn($b) => ['from' => $b->start_date, 'to' => $b->end_date]);

        return view('bookings.create', compact('apartment', 'bookedRanges'));
    }

    public function store(StoreBookingRequest $request)
    {
        $apartment = Apartment::findOrFail($request->apartment_id);

        if ($apartment->user_id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot book your own apartment!']);
        }

        $booking = DB::transaction(function () use ($request, $apartment) {
            $hasConfirmedBooking = Booking::where('apartment_id', $apartment->id)
                ->where('status', 'confirmed')
                ->lockForUpdate()
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                        ->orWhere(function ($query) use ($request) {
                            $query->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                        });
                })
                ->exists();

            if ($hasConfirmedBooking) {
                return null;
            }

            $days = now()->parse($request->start_date)->diffInDays(now()->parse($request->end_date)) + 1;
            $total = $days * $apartment->price_per_night;

            return Booking::create([
                'user_id' => Auth::id(),
                'apartment_id' => $apartment->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_price' => $total,
                'payment_status' => 'unpaid',
                'status' => 'pending',
                'payment_deadline' => now()->addDays(2),
            ]);
        });

        if (!$booking) {
            return redirect()->back()->withErrors([
                'error' => 'This apartment is already confirmed for the selected period. Please choose other dates.'
            ]);
        }

        // Send notification to apartment owner
        if ($apartment->user) {
            $apartment->user->notify(new NewBookingNotification($booking));
        }

        return redirect()->route('bookings.index')->with('success', 'Booking request created successfully. Awaiting owner approval.');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['apartment.images', 'payments', 'user', 'review']);

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);

        $bookedRanges = Booking::where('apartment_id', $booking->apartment_id)
            ->where('status', 'confirmed')
            ->where('id', '!=', $booking->id)
            ->get(['start_date', 'end_date'])
            ->map(fn($b) => ['from' => $b->start_date, 'to' => $b->end_date]);

        return view('bookings.edit', compact('booking', 'bookedRanges'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $apartment = $booking->apartment;

        $conflict = DB::transaction(function () use ($request, $apartment, $booking) {
            return Booking::where('apartment_id', $apartment->id)
                ->where('status', 'confirmed')
                ->where('id', '!=', $booking->id)
                ->lockForUpdate()
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                        ->orWhere(function ($query) use ($request) {
                            $query->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                        });
                })
                ->exists();
        });

        if ($conflict) {
            return redirect()->back()->withErrors([
                'error' => 'This apartment is already confirmed for the selected period. Please choose another date.'
            ]);
        }

        $days = now()->parse($request->start_date)->diffInDays(now()->parse($request->end_date)) + 1;
        $total = $days * $apartment->price_per_night;

        $booking->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully');
    }

    public function confirm($id)
    {
        $booking = Booking::with('apartment.user', 'user')->findOrFail($id);
        $this->authorize('confirm', $booking);

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'confirmed']);

            // Auto-cancel overlapping pending bookings
            Booking::where('id', '!=', $booking->id)
                ->where('apartment_id', $booking->apartment_id)
                ->where('status', 'pending')
                ->where(function ($query) use ($booking) {
                    $query->whereBetween('start_date', [$booking->start_date, $booking->end_date])
                        ->orWhereBetween('end_date', [$booking->start_date, $booking->end_date])
                        ->orWhere(function ($q) use ($booking) {
                            $q->where('start_date', '<=', $booking->start_date)
                                ->where('end_date', '>=', $booking->end_date);
                        });
                })
                ->update(['status' => 'canceled']);
        });

        // Notify guest
        $booking->user->notify(new BookingStatusNotification($booking, 'confirmed'));

        return redirect()->back()->with('success', 'Booking confirmed successfully. Overlapping pending requests have been canceled.');
    }

    public function cancel($id)
    {
        $booking = Booking::with('apartment', 'user')->findOrFail($id);
        $this->authorize('cancel', $booking);

        $booking->update(['status' => 'canceled']);

        // Notify guest if canceled by owner
        if (Auth::id() === $booking->apartment->user_id) {
            $booking->user->notify(new BookingStatusNotification($booking, 'canceled'));
        }

        return back()->with('success', 'Reservation has been canceled.');
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,partial,paid',
        ]);

        $booking = Booking::findOrFail($id);
        $this->authorize('updatePaymentStatus', $booking);

        $booking->update([
            'payment_status' => $request->payment_status,
            'paid_amount' => $request->payment_status === 'paid' ? $booking->total_price : $booking->paid_amount,
        ]);

        return back()->with('success', 'Payment status updated successfully');
    }
}
