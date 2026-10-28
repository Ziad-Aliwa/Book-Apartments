@extends('layouts')

@section('title', 'All Apartments Directory')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">All Apartments Directory</h2>
            <span class="text-muted small">Complete catalog of approved apartments</span>
        </div>
        <div>
            <a href="{{ route('apartments.index') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-grid-fill me-1"></i>Card View
            </a>
            @auth
                <a href="{{ route('apartments.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg me-1"></i>Add Apartment
                </a>
            @endauth
        </div>
    </div>

    @if ($apartments->isEmpty())
        <div class="alert alert-info py-4 text-center">No apartments listed yet.</div>
    @else
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Cover</th>
                            <th>Title & Location</th>
                            <th>Host</th>
                            <th>Price / Night</th>
                            <th>Rating</th>
                            <th>Availability</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($apartments as $apartment)
                            <tr>
                                <td style="width: 80px;">
                                    <img src="{{ $apartment->cover_url }}" class="rounded" style="width: 70px; height: 50px; object-fit: cover;" alt="{{ $apartment->title }}">
                                </td>
                                <td>
                                    <strong class="d-block">{{ $apartment->title }}</strong>
                                    <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $apartment->address }}</small>
                                </td>
                                <td>{{ $apartment->user->name ?? 'Host' }}</td>
                                <td><strong>${{ number_format($apartment->price_per_night, 2) }}</strong></td>
                                <td>
                                    <i class="bi bi-star-fill star-rating"></i>
                                    <strong>{{ $apartment->average_rating > 0 ? $apartment->average_rating : '-' }}</strong>
                                </td>
                                <td>
                                    <small class="d-block text-muted">From: {{ $apartment->available_from }}</small>
                                    <small class="d-block text-muted">To: {{ $apartment->available_to }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $apartment->status === 'available' ? 'success' : ($apartment->status === 'booked' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($apartment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('apartments.show', $apartment->id) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $apartment)
                                            <a href="{{ route('apartments.edit', $apartment->id) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $apartment)
                                            <form action="{{ route('apartments.destroy', $apartment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this apartment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $apartments->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection
