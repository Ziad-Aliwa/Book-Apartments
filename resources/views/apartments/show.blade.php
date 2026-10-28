@extends('layouts')

@section('title', $apartment->title)

@section('content')

    <!-- Breadcrumb & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('apartments.index') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $apartment->title }}</li>
            </ol>
        </nav>
        <div>
            @can('update', $apartment)
                <a href="{{ route('apartments.edit', $apartment->id) }}" class="btn btn-sm btn-outline-warning me-1">
                    <i class="bi bi-pencil-square me-1"></i>Edit Listing
                </a>
            @endcan
            @can('delete', $apartment)
                <form action="{{ route('apartments.destroy', $apartment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this apartment listing?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Title & Rating Header -->
    <div class="mb-3">
        <h2 class="fw-bold mb-1">{{ $apartment->title }}</h2>
        <div class="d-flex align-items-center gap-3 text-muted">
            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $apartment->address }}</span>
            <span>&bull;</span>
            <span>
                <i class="bi bi-star-fill star-rating"></i>
                <strong>{{ $apartment->average_rating > 0 ? $apartment->average_rating : 'New' }}</strong>
                ({{ $apartment->reviews_count }} {{ Str::plural('review', $apartment->reviews_count) }})
            </span>
            <span>&bull;</span>
            <span>Hosted by <strong>{{ $apartment->user->name }}</strong></span>
        </div>
    </div>

    <!-- Photo Gallery / Carousel -->
    <div class="card shadow-sm border-0 mb-4 rounded-3 overflow-hidden">
        @if($apartment->images->count() > 1)
            <div id="apartmentCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($apartment->images as $key => $image)
                        <button type="button" data-bs-target="#apartmentCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner" style="max-height: 480px;">
                    @foreach($apartment->images as $key => $image)
                        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                            <img src="{{ $image->url }}" class="d-block w-100" style="height: 480px; object-fit: cover;" alt="Apartment Photo">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#apartmentCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#apartmentCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        @else
            <img src="{{ $apartment->cover_url }}" class="w-100" style="max-height: 480px; object-fit: cover;" alt="{{ $apartment->title }}">
        @endif
    </div>

    <div class="row g-4">
        <!-- Main Details & Reviews (Col-8) -->
        <div class="col-lg-8">
            <!-- Details Card -->
            <div class="card shadow-sm border-0 mb-4 p-4 rounded-3 bg-white">
                <h4 class="fw-bold mb-3">About this apartment</h4>
                <p class="lead fs-6 text-secondary" style="white-space: pre-line;">{{ $apartment->description ?? 'No description provided.' }}</p>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-light rounded-circle text-primary fs-4">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Available From</small>
                                <strong>{{ $apartment->available_from }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-light rounded-circle text-primary fs-4">
                                <i class="bi bi-calendar-x"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Available Until</small>
                                <strong>{{ $apartment->available_to }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="card shadow-sm border-0 p-4 rounded-3 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-star-fill star-rating me-1"></i>
                        {{ $apartment->average_rating > 0 ? $apartment->average_rating : 'No reviews yet' }}
                        <span class="text-muted fs-6 fw-normal">({{ $apartment->reviews_count }} {{ Str::plural('review', $apartment->reviews_count) }})</span>
                    </h4>
                </div>

                <!-- Review Form for eligible guests -->
                @if($userCanReview)
                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <h6 class="fw-bold"><i class="bi bi-pencil-fill me-1"></i>Leave a Review for your recent stay</h6>
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ $reviewableBookingId }}">
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-auto">
                                    <label class="form-label mb-0 small fw-bold">Your Rating:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="rating" class="form-select form-select-sm" required>
                                        <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                                        <option value="4">⭐⭐⭐⭐ 4 - Good</option>
                                        <option value="3">⭐⭐⭐ 3 - Average</option>
                                        <option value="2">⭐⭐ 2 - Below Average</option>
                                        <option value="1">⭐ 1 - Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-2">
                                <textarea name="comment" class="form-control" rows="3" placeholder="Share details about your experience, cleanliness, communication..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Submit Review</button>
                        </form>
                    </div>
                @endif

                <!-- Reviews List -->
                @forelse($apartment->reviews as $review)
                    <div class="border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <strong class="me-2">{{ $review->user->name }}</strong>
                                <span class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                        </div>
                        <p class="text-secondary small mb-0">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-muted mb-0">There are no reviews for this apartment yet. Be the first to stay and leave a review!</p>
                @endforelse
            </div>
        </div>

        <!-- Sticky Booking Widget (Col-4) -->
        <div class="col-lg-4">
            <div class="card shadow border-0 p-4 rounded-3 sticky-top" style="top: 90px;">
                <div class="d-flex justify-content-between align-items-baseline mb-3">
                    <div>
                        <span class="fs-3 fw-bold text-dark">${{ number_format($apartment->price_per_night, 2) }}</span>
                        <span class="text-muted">/ night</span>
                    </div>
                    <div class="small">
                        <i class="bi bi-star-fill star-rating"></i>
                        <strong>{{ $apartment->average_rating > 0 ? $apartment->average_rating : 'New' }}</strong>
                    </div>
                </div>

                @auth
                    @if(Auth::id() === $apartment->user_id)
                        <div class="alert alert-warning mb-0 text-center">
                            <i class="bi bi-info-circle me-1"></i>You are the host of this apartment.
                            <a href="{{ route('apartments.my_bookings') }}" class="d-block mt-2 btn btn-sm btn-outline-dark">
                                View Booking Requests
                            </a>
                        </div>
                    @else
                        <!-- Booking Form -->
                        <form action="{{ route('bookings.store', $apartment->id) }}" method="POST" id="bookingForm">
                            @csrf
                            <input type="hidden" name="apartment_id" value="{{ $apartment->id }}">

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Select Dates</label>
                                <input type="text" id="date_range_picker" class="form-control" placeholder="Choose Check-in to Check-out dates" required>
                                <input type="hidden" name="start_date" id="start_date">
                                <input type="hidden" name="end_date" id="end_date">
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-shield-lock me-1"></i>Disabled dates are already booked
                                </small>
                            </div>

                            <!-- Cost Breakdown -->
                            <div id="costSummary" class="bg-light p-3 rounded mb-3" style="display: none;">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>${{ number_format($apartment->price_per_night, 2) }} &times; <span id="numDays">0</span> nights</span>
                                    <span>$<span id="subTotal">0.00</span></span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                                    <span>Total Price</span>
                                    <span>$<span id="finalTotal">0.00</span></span>
                                </div>
                            </div>

                            <button type="submit" id="reserveBtn" class="btn btn-primary w-100 py-2 fw-bold" disabled>
                                Reserve Apartment
                            </button>
                        </form>
                    @endif
                @else
                    <div class="text-center py-3">
                        <p class="text-muted small mb-3">Sign in or register to book this apartment</p>
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 fw-bold mb-2">Login to Reserve</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100 btn-sm">Create an Account</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookedRanges = @json($bookedRanges);
        const pricePerNight = {{ $apartment->price_per_night }};
        const minAvailableDate = "{{ $apartment->available_from }}";
        const maxAvailableDate = "{{ $apartment->available_to }}";

        const disabledDates = bookedRanges.map(function(range) {
            return {
                from: range.from,
                to: range.to
            };
        });

        const datePickerInput = document.getElementById('date_range_picker');
        if (datePickerInput) {
            flatpickr(datePickerInput, {
                mode: 'range',
                minDate: minAvailableDate > new Date().toISOString().split('T')[0] ? minAvailableDate : "today",
                maxDate: maxAvailableDate,
                disable: disabledDates,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startDate = instance.formatDate(selectedDates[0], "Y-m-d");
                        const endDate = instance.formatDate(selectedDates[1], "Y-m-d");

                        document.getElementById('start_date').value = startDate;
                        document.getElementById('end_date').value = endDate;

                        // Calculate days & total
                        const diffTime = Math.abs(selectedDates[1] - selectedDates[0]);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        const total = (diffDays * pricePerNight).toFixed(2);

                        document.getElementById('numDays').textContent = diffDays;
                        document.getElementById('subTotal').textContent = total;
                        document.getElementById('finalTotal').textContent = total;

                        document.getElementById('costSummary').style.display = 'block';
                        document.getElementById('reserveBtn').disabled = false;
                    } else {
                        document.getElementById('costSummary').style.display = 'none';
                        document.getElementById('reserveBtn').disabled = true;
                    }
                }
            });
        }
    });
</script>
@endpush
