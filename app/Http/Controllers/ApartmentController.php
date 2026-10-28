<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Apartment::where('admin_approve', true)
            ->where('status', 'available')
            ->with(['images', 'reviews', 'user']);

        // Search by location / title
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('address', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        // Price range filtering
        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        // Guests & Bedrooms filtering
        if ($request->filled('guests')) {
            $query->where('max_guests', '>=', (int) $request->guests);
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', (int) $request->bedrooms);
        }

        // Amenities filtering
        if ($request->filled('amenities') && is_array($request->amenities)) {
            foreach ($request->amenities as $amenity) {
                $query->whereJsonContains('amenities', $amenity);
            }
        }

        // Availability dates filtering
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;

            // Must fit within available_from and available_to
            $query->where('available_from', '<=', $checkIn)
                  ->where('available_to', '>=', $checkOut);

            // Must NOT have conflicting confirmed bookings
            $query->whereDoesntHave('bookings', function ($bQuery) use ($checkIn, $checkOut) {
                $bQuery->where('status', 'confirmed')
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('start_date', [$checkIn, $checkOut])
                          ->orWhereBetween('end_date', [$checkIn, $checkOut])
                          ->orWhere(function ($sub) use ($checkIn, $checkOut) {
                              $sub->where('start_date', '<=', $checkIn)
                                  ->where('end_date', '>=', $checkOut);
                          });
                    });
            });
        }

        // Sort order
        $sortBy = $request->input('sort', 'latest');
        if ($sortBy === 'price_asc') {
            $query->orderBy('price_per_night', 'asc');
        } elseif ($sortBy === 'price_desc') {
            $query->orderBy('price_per_night', 'desc');
        } else {
            $query->latest();
        }

        $apartments = $query->paginate(9)->withQueryString();

        return view('apartments.index', compact('apartments'));
    }

    public function all_apartment(Request $request)
    {
        $query = Apartment::where('admin_approve', true)
            ->with(['images', 'reviews', 'user']);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('address', 'like', $term);
            });
        }

        $apartments = $query->latest()->paginate(9)->withQueryString();

        return view('apartments.all_apartment', compact('apartments'));
    }

    public function show($id)
    {
        $apartment = Apartment::with(['images', 'reviews.user', 'user'])->findOrFail($id);

        $this->authorize('view', $apartment);

        // Fetch confirmed bookings date ranges for calendar disabling
        $bookedRanges = Booking::where('apartment_id', $apartment->id)
            ->where('status', 'confirmed')
            ->get(['start_date', 'end_date'])
            ->map(fn($b) => ['from' => $b->start_date, 'to' => $b->end_date]);

        // Check if current user has a completed booking that can be reviewed
        $userCanReview = false;
        $reviewableBookingId = null;
        if (Auth::check()) {
            $bookingToReview = Booking::where('apartment_id', $apartment->id)
                ->where('user_id', Auth::id())
                ->where('status', 'confirmed')
                ->whereDoesntHave('review')
                ->first();

            if ($bookingToReview) {
                $userCanReview = true;
                $reviewableBookingId = $bookingToReview->id;
            }
        }

        return view('apartments.show', compact('apartment', 'bookedRanges', 'userCanReview', 'reviewableBookingId'));
    }

    public function create()
    {
        $this->authorize('create', Apartment::class);
        return view('apartments.create');
    }

    public function store(StoreApartmentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $apartment = Apartment::create($validated);

        // Handle uploaded images
        if ($request->hasFile('images')) {
            $coverIndex = (int) $request->input('cover_index', 0);
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('apartments/' . $apartment->id, 'public');
                $apartment->images()->create([
                    'image_path' => $path,
                    'is_cover' => ($index === $coverIndex),
                ]);
            }
        }

        return redirect()->route('apartments.index')
            ->with('success', 'Apartment submitted successfully! It is now pending admin approval.');
    }

    public function edit($id)
    {
        $apartment = Apartment::with('images')->findOrFail($id);
        $this->authorize('update', $apartment);

        return view('apartments.edit', compact('apartment'));
    }

    public function update(UpdateApartmentRequest $request, $id)
    {
        $apartment = Apartment::findOrFail($id);
        $this->authorize('update', $apartment);

        $apartment->update($request->validated());

        // Handle additional uploaded images
        if ($request->hasFile('images')) {
            $hasCover = $apartment->images()->where('is_cover', true)->exists();
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('apartments/' . $apartment->id, 'public');
                $apartment->images()->create([
                    'image_path' => $path,
                    'is_cover' => (!$hasCover && $index === 0),
                ]);
            }
        }

        return redirect()->route('apartments.show', $apartment->id)
            ->with('success', 'Apartment successfully modified');
    }

    public function destroy($id)
    {
        $apartment = Apartment::with('images')->findOrFail($id);
        $this->authorize('delete', $apartment);

        // Delete associated image files
        foreach ($apartment->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $apartment->delete();

        return redirect()->route('apartments.index')->with('success', 'Apartment deleted successfully');
    }

    public function destroyImage($apartmentId, $imageId)
    {
        $apartment = Apartment::findOrFail($apartmentId);
        $this->authorize('update', $apartment);

        $image = ApartmentImage::where('apartment_id', $apartment->id)->findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        // If cover was deleted, make first remaining image cover
        if ($image->is_cover) {
            $next = $apartment->images()->first();
            if ($next) {
                $next->update(['is_cover' => true]);
            }
        }

        return back()->with('success', 'Image removed successfully.');
    }

    public function myApartmentsWithBookings()
    {
        $user = Auth::user();
        $apartments = $user->apartments()->with(['bookings.user', 'images'])->latest()->get();

        return view('apartments.my_bookings', compact('apartments'));
    }
}
