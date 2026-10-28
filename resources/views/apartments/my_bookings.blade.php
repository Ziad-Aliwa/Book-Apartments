@extends('layouts')

@section('title', 'Bookings on My Apartments')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-door-open-fill text-primary me-2"></i>Bookings on My Apartments</h2>
            <span class="text-muted small">Manage reservations requested by guests on your listed properties</span>
        </div>
        <a href="{{ route('apartments.create') }}" class="btn btn-warning fw-bold btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add New Listing
        </a>
    </div>

    @forelse ($apartments as $apartment)
        <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $apartment->cover_url }}" class="rounded" style="width: 60px; height: 40px; object-fit: cover;" alt="{{ $apartment->title }}">
                    <div>
                        <a href="{{ route('apartments.show', $apartment->id) }}" class="text-white text-decoration-none fw-bold fs-6">
                            {{ $apartment->title }}
                        </a>
                        <small class="d-block text-light opacity-75"><i class="bi bi-geo-alt me-1"></i>{{ $apartment->address }}</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-{{ $apartment->admin_approve ? 'success' : 'warning text-dark' }} me-2">
                        {{ $apartment->admin_approve ? 'Active' : 'Pending Approval' }}
                    </span>
                    <a href="{{ route('apartments.edit', $apartment->id) }}" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                @if ($apartment->bookings->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Guest</th>
                                    <th>Dates</th>
                                    <th>Total Price</th>
                                    <th>Booking Status</th>
                                    <th>Payment Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($apartment->bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->user->name }}</strong>
                                            <small class="d-block text-muted">{{ $booking->user->email }}</small>
                                        </td>
                                        <td>
                                            <span>{{ $booking->start_date }}</span>
                                            <span class="text-muted mx-1">&rarr;</span>
                                            <span>{{ $booking->end_date }}</span>
                                        </td>
                                        <td><strong>${{ number_format($booking->total_price, 2) }}</strong></td>
                                        <td>
                                            @php
                                                $bClass = match($booking->status) {
                                                    'confirmed' => 'success',
                                                    'pending' => 'warning text-dark',
                                                    'canceled' => 'danger',
                                                    'expired' => 'secondary',
                                                    default => 'info'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $bClass }}">{{ ucfirst($booking->status) }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('bookings.update_payment_status', $booking->id) }}" method="POST" class="d-inline-flex">
                                                @csrf
                                                <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="unpaid" {{ $booking->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                                    <option value="partial" {{ $booking->payment_status === 'partial' ? 'selected' : '' }}>Partial</option>
                                                    <option value="paid" {{ $booking->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            @if ($booking->status === 'pending')
                                                <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm fw-semibold"><i class="bi bi-check-lg"></i> Accept</button>
                                                </form>

                                                <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject this booking request?')">
                                                    @csrf
                                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i> Reject</button>
                                                </form>
                                            @else
                                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    Details
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-calendar-x fs-2 d-block mb-1"></i>
                        No bookings requested for this apartment yet.
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0 py-5 text-center rounded-3 bg-white">
            <div class="card-body">
                <i class="bi bi-building fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold">You haven't listed any apartments yet</h5>
                <p class="text-muted mb-3">Start renting out your space and earning revenue today.</p>
                <a href="{{ route('apartments.create') }}" class="btn btn-warning fw-bold">List Your First Apartment</a>
            </div>
        </div>
    @endforelse
@endsection
