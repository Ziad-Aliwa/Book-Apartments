@extends('layouts')

@section('title', 'Booking #' . $booking->booking_code)

@section('content')

    @if (session('success'))
        <div class="alert alert-success mt-2">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-2">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Booking Details</h2>
            <span class="badge bg-secondary fs-6">Ref: #{{ $booking->booking_code }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bookings.invoice', $booking->id) }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-receipt me-1"></i>View Invoice
            </a>
            <a href="{{ route('bookings.invoice.download', $booking->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                <img src="{{ $booking->apartment->cover_url }}" class="rounded" style="width: 100px; height: 70px; object-fit: cover;" alt="{{ $booking->apartment->title }}">
                <div>
                    <h4 class="fw-bold mb-1">
                        <a href="{{ route('apartments.show', $booking->apartment_id) }}" class="text-dark text-decoration-none">
                            {{ $booking->apartment->title }}
                        </a>
                    </h4>
                    <p class="text-muted small mb-0"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $booking->apartment->address }}</p>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Start Date</small>
                    <strong>{{ $booking->start_date->format('M d, Y') }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">End Date</small>
                    <strong>{{ $booking->end_date->format('M d, Y') }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Booking Status</small>
                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning text-dark' : 'danger') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Payment Status</small>
                    <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : 'danger' }}">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </div>
            </div>

            @if($booking->discount_amount > 0)
                <div class="alert alert-success py-2 small mb-3">
                    <i class="bi bi-tag-fill me-1"></i>Long-stay discount of <strong>${{ number_format($booking->discount_amount, 2) }}</strong> applied!
                </div>
            @endif

            <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                <span class="fs-5 fw-bold text-dark">Total Price:</span>
                <span class="fs-4 fw-bold text-primary">${{ number_format($booking->total_price, 2) }}</span>
            </div>
        </div>
    </div>

    @if($booking->payments && $booking->payments->isNotEmpty())
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white p-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card text-success me-2"></i>Payment History</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($booking->payments as $payment)
                        <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                            <div>
                                <span class="fw-bold">${{ number_format($payment->amount_paid, 2) }}</span>
                                <small class="text-success ms-2"><i class="bi bi-check-circle-fill"></i> Paid</small>
                            </div>
                            <span class="text-muted small">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y - H:i') : $payment->created_at->format('M d, Y - H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-4">
        @if (Auth::id() === $booking->apartment->user_id && $booking->status === 'pending')
            <a href="{{ route('apartments.my_bookings') }}" class="btn btn-success">Go to Booking Confirmations</a>
        @endif

        @if (Auth::id() === $booking->user_id && $booking->status === 'pending')
            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-warning">Edit Dates</a>
        @endif

        @if (Auth::id() === $booking->user_id && $booking->payment_status === 'unpaid' && !in_array($booking->status, ['canceled', 'expired']))
            <a href="{{ route('payments.unpaid') }}" class="btn btn-success">Pay Now</a>
        @endif

        <!-- Contact Host / Guest -->
        <form action="{{ route('messages.start', $booking->apartment_id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-chat-dots me-1"></i>Message {{ Auth::id() === $booking->user_id ? 'Host' : 'Guest' }}
            </button>
        </form>

        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Back to My Bookings</a>
    </div>

@endsection
