@extends('layouts')

@section('title', 'Find & Book Apartments')

@section('content')

    <!-- Search & Filter Banner -->
    <div class="card shadow-sm border-0 mb-4 bg-white rounded-3">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Find your next stay</h4>
            <form action="{{ route('apartments.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">Destination or Title</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="e.g. Cairo, Zamalek, Luxury..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted">Check-in</label>
                    <input type="date" name="check_in" id="filter_check_in" class="form-control" value="{{ request('check_in') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted">Check-out</label>
                    <input type="date" name="check_out" id="filter_check_out" class="form-control" value="{{ request('check_out') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted">Max Price / Night ($)</label>
                    <input type="number" name="max_price" class="form-control" placeholder="Any" value="{{ request('max_price') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted">Sort By</label>
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                    @if(request()->hasAny(['search', 'check_in', 'check_out', 'min_price', 'max_price', 'sort']))
                        <a href="{{ route('apartments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Reset Filters
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-funnel-fill me-1"></i>Search Apartments
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Header & Results Count -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-0">Available Apartments</h3>
            <span class="text-muted small">Showing {{ $apartments->total() }} properties available for booking</span>
        </div>
        <div>
            <a href="{{ route('apartments.all_apartment') }}" class="btn btn-sm btn-outline-dark me-2">
                <i class="bi bi-grid-3x3-gap-fill me-1"></i>View All Directory
            </a>
            @auth
                <a href="{{ route('apartments.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle me-1"></i>Add New Listing
                </a>
            @endauth
        </div>
    </div>

    <!-- Apartments Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        @forelse($apartments as $apartment)
            <div class="col">
                <div class="card h-100 shadow-sm border-0 apartment-card">
                    <!-- Apartment Image -->
                    <div class="position-relative">
                        <img src="{{ $apartment->cover_url }}" class="card-img-top apartment-thumb" alt="{{ $apartment->title }}" onerror="this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80'">
                        <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 fs-6">
                            ${{ number_format($apartment->price_per_night, 2) }} <small class="text-light fw-normal">/ night</small>
                        </span>
                        @if($apartment->images->count() > 1)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-secondary bg-opacity-75">
                                <i class="bi bi-images me-1"></i>{{ $apartment->images->count() }} photos
                            </span>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted text-truncate" style="max-width: 65%;">
                                <i class="bi bi-geo-alt me-1 text-danger"></i>{{ $apartment->address }}
                            </small>
                            <small class="text-nowrap">
                                <i class="bi bi-star-fill star-rating"></i>
                                <strong>{{ $apartment->average_rating > 0 ? $apartment->average_rating : 'New' }}</strong>
                                @if($apartment->reviews_count > 0)
                                    <span class="text-muted">({{ $apartment->reviews_count }})</span>
                                @endif
                            </small>
                        </div>

                        <h5 class="card-title fw-bold text-truncate mb-2" title="{{ $apartment->title }}">
                            <a href="{{ route('apartments.show', $apartment->id) }}" class="text-decoration-none text-dark">
                                {{ $apartment->title }}
                            </a>
                        </h5>

                        <p class="card-text text-muted small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $apartment->description ?? 'No detailed description provided.' }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="small text-muted">
                                By <strong>{{ $apartment->user->name ?? 'Host' }}</strong>
                            </span>
                            <a href="{{ route('apartments.show', $apartment->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                View Details & Book <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5 shadow-sm">
                    <i class="bi bi-search fs-1 text-primary d-block mb-3"></i>
                    <h5 class="fw-bold">No apartments match your search criteria.</h5>
                    <p class="text-muted mb-3">Try adjusting your filters, destination keyword, or dates.</p>
                    <a href="{{ route('apartments.index') }}" class="btn btn-outline-primary">View All Available Apartments</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $apartments->links('pagination::bootstrap-5') }}
    </div>

@endsection
