@extends('layouts')

@section('title', 'Unpaid Bookings & Invoices')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-wallet2 text-danger me-2"></i>Unpaid Bookings & Invoices</h2>
            <span class="text-muted small">Complete payments before the deadline to secure your reservation</span>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-dark btn-sm">
            <i class="bi bi-arrow-left me-1"></i>All My Bookings
        </a>
    </div>

    @forelse($bookings as $booking)
        <div class="card shadow-sm border-0 rounded-3 mb-3 overflow-hidden bg-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-2 mb-3 mb-md-0">
                        <img src="{{ $booking->apartment->cover_url }}" class="rounded w-100" style="height: 100px; object-fit: cover;" alt="{{ $booking->apartment->title }}">
                    </div>
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h5 class="fw-bold mb-1">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="text-dark text-decoration-none">
                                {{ $booking->apartment->title }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $booking->apartment->address }}</p>
                        <div class="small">
                            <span class="fw-semibold">Dates:</span> {{ $booking->start_date }} &rarr; {{ $booking->end_date }}
                        </div>
                        @if($booking->payment_deadline)
                            <div class="small text-danger mt-1">
                                <i class="bi bi-alarm me-1"></i>Payment Deadline: <strong>{{ \Carbon\Carbon::parse($booking->payment_deadline)->format('M d, Y - H:i') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="fs-4 fw-bold text-dark mb-2">${{ number_format($booking->total_price, 2) }}</div>
                        <form action="{{ route('payments.pay', $booking->id) }}" method="POST" onsubmit="return confirm('Confirm payment of ${{ number_format($booking->total_price, 2) }}?')">
                            @csrf
                            <button type="submit" class="btn btn-success fw-bold px-4">
                                <i class="bi bi-credit-card me-1"></i>Pay Now
                            </button>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm ms-1">Details</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow-sm border-0 py-5 text-center rounded-3 bg-white">
            <div class="card-body">
                <i class="bi bi-check2-circle fs-1 text-success d-block mb-3"></i>
                <h5 class="fw-bold">No Pending Invoices!</h5>
                <p class="text-muted mb-3">You don't have any outstanding payments at the moment.</p>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-primary btn-sm">View Booking History</a>
            </div>
        </div>
    @endforelse
@endsection
