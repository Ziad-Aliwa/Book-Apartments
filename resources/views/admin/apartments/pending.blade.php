@extends('layouts')

@section('title', 'Pending Apartments Approval')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-hourglass-split text-warning me-2"></i>Pending Apartments for Moderation</h2>
            <span class="text-muted small">Review newly submitted apartment listings before making them publicly bookable.</span>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Admin Dashboard
        </a>
    </div>

    @if ($apartments->isEmpty())
        <div class="card shadow-sm border-0 py-5 text-center rounded-3 bg-white">
            <div class="card-body">
                <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-3"></i>
                <h5 class="fw-bold">No Pending Listings</h5>
                <p class="text-muted mb-0">All submitted apartments have been reviewed and approved.</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Cover</th>
                            <th>Apartment Details</th>
                            <th>Host / Owner</th>
                            <th>Price / Night</th>
                            <th>Availability Period</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($apartments as $apartment)
                            <tr>
                                <td style="width: 90px;">
                                    <img src="{{ $apartment->cover_url }}" class="rounded" style="width: 80px; height: 55px; object-fit: cover;" alt="{{ $apartment->title }}">
                                </td>
                                <td>
                                    <strong class="d-block">{{ $apartment->title }}</strong>
                                    <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $apartment->address }}</small>
                                </td>
                                <td>
                                    <strong>{{ $apartment->user->name }}</strong>
                                    <small class="d-block text-muted">{{ $apartment->user->email }}</small>
                                </td>
                                <td><strong>${{ number_format($apartment->price_per_night, 2) }}</strong></td>
                                <td>
                                    <small class="d-block text-muted">From: {{ $apartment->available_from }}</small>
                                    <small class="d-block text-muted">To: {{ $apartment->available_to }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('apartments.show', $apartment->id) }}" class="btn btn-outline-info" target="_blank" title="Preview Listing">
                                            <i class="bi bi-box-arrow-up-right"></i> Preview
                                        </a>

                                        <form action="{{ route('admin.apartments.approve', $apartment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-success fw-bold" onclick="return confirm('Approve and publish this apartment?');">
                                                <i class="bi bi-check-lg"></i> Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.apartments.destroy', $apartment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject and delete this apartment listing permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger">
                                                <i class="bi bi-trash"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
