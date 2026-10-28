@extends('layouts')

@section('title', 'List a New Apartment')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-dark text-white p-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-house-add-fill me-2"></i>List a New Apartment</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">
                        Fill in the details below. Once submitted, your listing will be reviewed and approved by administrators before going live.
                    </p>

                    <form action="{{ route('apartments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Apartment Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Modern 2-Bedroom Flat in Zamalek" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Full Address / Location <span class="text-danger">*</span></label>
                            <input type="text" name="address" id="address" class="form-control" placeholder="Street, Neighborhood, City" value="{{ old('address') }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label fw-bold">Price per Night ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="1" name="price_per_night" id="price_per_night" class="form-control" placeholder="50.00" value="{{ old('price_per_night') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="available_from" class="form-label fw-bold">Available From <span class="text-danger">*</span></label>
                                <input type="date" name="available_from" id="available_from" class="form-control" value="{{ old('available_from', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="available_to" class="form-label fw-bold">Available To <span class="text-danger">*</span></label>
                                <input type="date" name="available_to" id="available_to" class="form-control" value="{{ old('available_to') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control" placeholder="Describe the apartment amenities, view, bedrooms, nearby attractions...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label for="images" class="form-label fw-bold">
                                <i class="bi bi-images me-1 text-primary"></i>Apartment Photos
                            </label>
                            <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted d-block mt-1">
                                You can select multiple images (up to 10 photos). The first photo will be used as the cover. Allowed formats: JPG, PNG, WEBP (Max 5MB each).
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <a href="{{ route('apartments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Submit Listing for Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
