@extends('layouts')

@section('title', 'My Wishlist')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-heart-fill text-danger me-2"></i>My Saved Wishlist</h2>
            <span class="text-muted small">Your favorite apartments saved for upcoming trips</span>
        </div>
        <a href="{{ route('apartments.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-search me-1"></i>Explore More
        </a>
    </div>

    @if($apartments->isEmpty())
        <div class="card shadow-sm border-0 py-5 text-center rounded-3 bg-white">
            <div class="card-body">
                <i class="bi bi-heartbreak fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold">Your wishlist is empty</h5>
                <p class="text-muted mb-3">Save apartments by clicking the heart icon while exploring.</p>
                <a href="{{ route('apartments.index') }}" class="btn btn-primary">Discover Apartments</a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            @foreach($apartments as $apartment)
                <div class="col" id="wishlist-col-{{ $apartment->id }}">
                    <div class="card h-100 shadow-sm border-0 apartment-card">
                        <div class="position-relative">
                            <img src="{{ $apartment->cover_url }}" class="card-img-top apartment-thumb" alt="{{ $apartment->title }}">
                            
                            <!-- Wishlist Toggle Button -->
                            <button type="button" class="wishlist-btn position-absolute top-0 end-0 m-2 active shadow-sm"
                                    onclick="toggleWishlist({{ $apartment->id }}, this); document.getElementById('wishlist-col-{{ $apartment->id }}').remove();">
                                <i class="bi bi-heart-fill text-danger fs-5"></i>
                            </button>

                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 fs-6">
                                ${{ number_format($apartment->price_per_night, 2) }} <small class="text-light fw-normal">/ night</small>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted text-truncate" style="max-width: 65%;">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $apartment->address }}
                                </small>
                                <small>
                                    <i class="bi bi-star-fill star-rating"></i>
                                    <strong>{{ $apartment->average_rating > 0 ? $apartment->average_rating : 'New' }}</strong>
                                </small>
                            </div>

                            <h5 class="card-title fw-bold text-truncate mb-2">
                                <a href="{{ route('apartments.show', $apartment->id) }}" class="text-decoration-none text-dark">
                                    {{ $apartment->title }}
                                </a>
                            </h5>

                            <div class="d-flex gap-3 text-muted small mb-3">
                                <span><i class="bi bi-people me-1"></i>{{ $apartment->max_guests }} guests</span>
                                <span><i class="bi bi-door-closed me-1"></i>{{ $apartment->bedrooms }} beds</span>
                                <span><i class="bi bi-droplet me-1"></i>{{ $apartment->bathrooms }} baths</span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                <span class="small text-muted">Host: <strong>{{ $apartment->user->name }}</strong></span>
                                <a href="{{ route('apartments.show', $apartment->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    Book Now &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $apartments->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection
