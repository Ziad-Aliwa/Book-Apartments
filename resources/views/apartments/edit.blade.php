@extends('layouts')

@section('title', 'Edit Apartment - ' . $apartment->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-dark text-white p-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Apartment Listing</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('apartments.update', $apartment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Apartment Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $apartment->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Full Address / Location <span class="text-danger">*</span></label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $apartment->address) }}" required>
                        </div>

                        <!-- Capacity & Rooms -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="max_guests" class="form-label fw-bold">Max Guests</label>
                                <input type="number" min="1" max="50" name="max_guests" id="max_guests" class="form-control" value="{{ old('max_guests', $apartment->max_guests ?? 2) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="bedrooms" class="form-label fw-bold">Bedrooms</label>
                                <input type="number" min="0" max="20" name="bedrooms" id="bedrooms" class="form-control" value="{{ old('bedrooms', $apartment->bedrooms ?? 1) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="bathrooms" class="form-label fw-bold">Bathrooms</label>
                                <input type="number" min="0" max="20" name="bathrooms" id="bathrooms" class="form-control" value="{{ old('bathrooms', $apartment->bathrooms ?? 1) }}" required>
                            </div>
                        </div>

                        <!-- Pricing & Discounts -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label fw-bold">Price per Night ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="1" name="price_per_night" id="price_per_night" class="form-control" value="{{ old('price_per_night', $apartment->price_per_night) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="weekly_discount" class="form-label fw-bold">Weekly Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="90" name="weekly_discount" id="weekly_discount" class="form-control" value="{{ old('weekly_discount', $apartment->weekly_discount ?? 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">7+ nights discount</small>
                            </div>
                            <div class="col-md-4">
                                <label for="monthly_discount" class="form-label fw-bold">Monthly Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" min="0" max="90" name="monthly_discount" id="monthly_discount" class="form-control" value="{{ old('monthly_discount', $apartment->monthly_discount ?? 0) }}">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">28+ nights discount</small>
                            </div>
                        </div>

                        <!-- Availability Window -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="available_from" class="form-label fw-bold">Available From <span class="text-danger">*</span></label>
                                <input type="date" name="available_from" id="available_from" class="form-control" value="{{ old('available_from', $apartment->available_from) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="available_to" class="form-label fw-bold">Available To <span class="text-danger">*</span></label>
                                <input type="date" name="available_to" id="available_to" class="form-control" value="{{ old('available_to', $apartment->available_to) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $apartment->description) }}</textarea>
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
                                $currentAmenities = (array) ($apartment->amenities ?? []);
                            @endphp
                            <div class="row row-cols-2 row-cols-md-3 g-2">
                                @foreach($amenitiesList as $key => $name)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $key }}" id="amenity_{{ $key }}" {{ in_array($key, $currentAmenities) ? 'checked' : '' }}>
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
                            <small class="text-muted d-block mb-2">Click anywhere on the map or drag the marker to adjust coordinates.</small>
                            <div id="pickerMap" class="map-container mb-2"></div>
                            <input type="hidden" name="latitude" id="input_latitude" value="{{ $apartment->latitude ?? 30.0444 }}">
                            <input type="hidden" name="longitude" id="input_longitude" value="{{ $apartment->longitude ?? 31.2357 }}">
                        </div>

                        <!-- Current Photos Gallery -->
                        @if($apartment->images->isNotEmpty())
                            <div class="mb-4">
                                <label class="form-label fw-bold">Existing Photos</label>
                                <div class="row row-cols-2 row-cols-md-4 g-3">
                                    @foreach($apartment->images as $img)
                                        <div class="col">
                                            <div class="card position-relative border">
                                                <img src="{{ $img->url }}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="Photo">
                                                @if($img->is_cover)
                                                    <span class="position-absolute top-0 start-0 m-1 badge bg-primary">Cover</span>
                                                @endif
                                                <div class="card-body p-1 text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 py-0"
                                                            onclick="if(confirm('Delete this photo?')) document.getElementById('delete-img-{{ $img->id }}').submit();">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Upload Additional Photos -->
                        <div class="mb-4 p-3 bg-light rounded-3 border">
                            <label for="images" class="form-label fw-bold">
                                <i class="bi bi-plus-circle me-1 text-primary"></i>Upload Additional Photos
                            </label>
                            <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted d-block mt-1">Select one or more photos to append to this listing.</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <a href="{{ route('apartments.show', $apartment->id) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                        </div>
                    </form>

                    <!-- Separate Hidden Forms for image deletion -->
                    @foreach($apartment->images as $img)
                        <form id="delete-img-{{ $img->id }}" action="{{ route('apartments.destroy_image', [$apartment->id, $img->id]) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = {{ $apartment->latitude ?? 30.0444 }};
        const defaultLng = {{ $apartment->longitude ?? 31.2357 }};

        const map = L.map('pickerMap').setView([defaultLat, defaultLng], 13);
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
