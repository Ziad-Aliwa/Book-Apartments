@extends('layouts')

@section('title', 'List a New Apartment')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
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

                        <!-- Capacity & Rooms -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="max_guests" class="form-label fw-bold">Max Guests</label>
                                <input type="number" min="1" max="50" name="max_guests" id="max_guests" class="form-control" value="{{ old('max_guests', 2) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="bedrooms" class="form-label fw-bold">Bedrooms</label>
                                <input type="number" min="0" max="20" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', 1) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="bathrooms" class="form-label fw-bold">Bathrooms</label>
                                <input type="number" min="0" max="20" name="bathrooms" id="bathrooms" class="form-control" value="{{ old('bathrooms', 1) }}" required>
                            </div>
                        </div>

                        <!-- Pricing & Discounts -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label fw-bold">Price per Night ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="1" name="price_per_night" id="price_per_night" class="form-control" placeholder="50.00" value="{{ old('price_per_night') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="weekly_discount" class="form-label fw-bold">Weekly Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="90" name="weekly_discount" id="weekly_discount" class="form-control" placeholder="10" value="{{ old('weekly_discount', 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">Discount for 7+ nights</small>
                            </div>
                            <div class="col-md-4">
                                <label for="monthly_discount" class="form-label fw-bold">Monthly Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="90" name="monthly_discount" id="monthly_discount" class="form-control" placeholder="20" value="{{ old('monthly_discount', 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">Discount for 28+ nights</small>
                            </div>
                        </div>

                        <!-- Availability Window -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="available_from" class="form-label fw-bold">Available From <span class="text-danger">*</span></label>
                                <input type="date" name="available_from" id="available_from" class="form-control" value="{{ old('available_from', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="available_to" class="form-label fw-bold">Available To <span class="text-danger">*</span></label>
                                <input type="date" name="available_to" id="available_to" class="form-control" value="{{ old('available_to') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control" placeholder="Describe the apartment amenities, view, bedrooms, nearby attractions...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Amenities Selection -->
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold d-block mb-2">
                                <i class="bi bi-stars text-warning me-1"></i>Amenities Provided
                            </label>
                            @php
                                $amenitiesList = [
                                    'wifi' => 'Fast Wi-Fi',
                                    'ac' => 'Air Conditioning',
                                    'kitchen' => 'Equipped Kitchen',
                                    'parking' => 'Free Parking',
                                    'pool' => 'Swimming Pool',
                                    'tv' => 'Smart TV',
                                    'elevator' => 'Elevator',
                                    'workspace' => 'Dedicated Workspace',
                                    'washing_machine' => 'Washing Machine',
                                ];
                            @endphp
                            <div class="row row-cols-2 row-cols-md-3 g-2">
                                @foreach($amenitiesList as $key => $name)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $key }}" id="amenity_{{ $key }}">
                                            <label class="form-check-label small" for="amenity_{{ $key }}">{{ $name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Location Map Pin Picker -->
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold d-block mb-1">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>Pin Exact Location on Map
                            </label>
                            <small class="text-muted d-block mb-2">Click anywhere on the map to set the exact coordinates of your property.</small>
                            <div id="pickerMap" class="map-container mb-2"></div>
                            <input type="hidden" name="latitude" id="input_latitude" value="30.0444">
                            <input type="hidden" name="longitude" id="input_longitude" value="31.2357">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = 30.0444;
        const defaultLng = 31.2357;

        const map = L.map('pickerMap').setView([defaultLat, defaultLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('input_latitude').value = lat.toFixed(6);
            document.getElementById('input_longitude').value = lng.toFixed(6);
        }

        marker.on('dragend', function (e) {
            const position = marker.getLatLng();
            updateCoords(position.lat, position.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
    });
</script>
@endpush
