@extends('layouts')

@section('title', 'My Bookings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-calendar2-check-fill text-primary me-2"></i>My Bookings</h2>
            <span class="text-muted small">Manage your current, upcoming, and past reservations</span>
        </div>
        <a href="{{ route('apartments.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-search me-1"></i>Explore More Apartments
        </a>
    </div>

    @if($bookings->isEmpty())
        <div class="card shadow-sm border-0 py-5 text-center rounded-3 bg-white">
            <div class="card-body">
                <i class="bi bi-ticket-perforated fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold">No bookings found</h5>
                <p class="text-muted mb-3">You haven't made any apartment reservations yet.</p>
                <a href="{{ route('apartments.index') }}" class="btn btn-primary">Find an Apartment</a>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Apartment</th>
                            <th>Stay Period</th>
                            <th>Total Price</th>
                            <th>Booking Status</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $booking->apartment->cover_url }}" class="rounded" style="width: 70px; height: 50px; object-fit: cover;" alt="{{ $booking->apartment->title }}">
                                        <div>
                                            <a href="{{ route('apartments.show', $booking->apartment_id) }}" class="fw-bold text-decoration-none text-dark d-block">
                                                {{ $booking->apartment->title }}
                                            </a>
                                            <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $booking->apartment->address }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $booking->start_date }}</strong>
                                    <span class="text-muted mx-1">&rarr;</span>
                                    <strong>{{ $booking->end_date }}</strong>
                                </td>
                                <td>
                                    <span class="fw-bold">${{ number_format($booking->total_price, 2) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($booking->status) {
                                            'confirmed' => 'success',
                                            'pending' => 'warning text-dark',
                                            'canceled' => 'danger',
                                            'expired' => 'secondary',
                                            default => 'info'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : ($booking->payment_status === 'partial' ? 'warning text-dark' : 'danger') }}">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary" title="Details">
                                            <i class="bi bi-eye"></i> View
                                        </a>

                                        @if($booking->status === 'pending')
                                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-outline-warning" title="Edit Dates">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        @endif

                                        @if(!in_array($booking->status, ['canceled', 'expired']))
                                            <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this booking?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Cancel Booking">
                                                    <i class="bi bi-x-circle"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $bookings->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection
