@extends('layouts')

@section('title', 'Booking Details')

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
    <h1 class="mb-4">Booking Details</h1>

    <div class="card mt-4">
        <div class="card-body">
            <h4>Apartment : {{ $booking->apartment->title }}</h4>
            <p><strong>Address:</strong> {{ $booking->apartment->address }}</p>
            <p><strong>Start Date:</strong> {{ $booking->start_date }}</p>
            <p><strong>End Date:</strong> {{ $booking->end_date }}</p>
            <p><strong>Total Price:</strong> {{ number_format($booking->total_price, 2) }} EGP</p>
            <p><strong>Booking Status:</strong> <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($booking->status) }}</span></p>
            <p><strong>Payment Status:</strong> <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : 'danger' }}">{{ ucfirst($booking->payment_status) }}</span></p>
        </div>
    </div>

    @if($booking->payments && $booking->payments->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Payment Records</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($booking->payments as $payment)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Amount: <strong>{{ number_format($payment->amount_paid, 2) }} EGP</strong></span>
                            <span class="text-muted">{{ $payment->payment_date ?? $payment->created_at }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="mt-4">
        @if (Auth::id() === $booking->apartment->user_id && $booking->status === 'pending')
            <a href="{{ route('apartments.my_bookings') }}" class="btn btn-success me-2">Bookings confirmation</a>
        @endif

        @if (Auth::id() === $booking->user_id && $booking->status === 'pending')
            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-warning me-2">Edit Booking</a>
        @endif

        @if (Auth::id() === $booking->user_id && $booking->payment_status === 'unpaid' && !in_array($booking->status, ['canceled', 'expired']))
            <a href="{{ route('payments.unpaid') }}" class="btn btn-success me-2">Pay Now</a>
        @endif

        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Back to Bookings</a>
    </div>

@endsection
