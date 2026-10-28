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

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="price_per_night" class="form-label fw-bold">Price per Night ($) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="1" name="price_per_night" id="price_per_night" class="form-control" value="{{ old('price_per_night', $apartment->price_per_night) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="available_from" class="form-label fw-bold">Available From <span class="text-danger">*</span></label>
                                <input type="date" name="available_from" id="available_from" class="form-control" value="{{ old('available_from', $apartment->available_from) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="available_to" class="form-label fw-bold">Available To <span class="text-danger">*</span></label>
                                <input type="date" name="available_to" id="available_to" class="form-control" value="{{ old('available_to', $apartment->available_to) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $apartment->description) }}</textarea>
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
