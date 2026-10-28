@extends('layouts')

@section('title', 'Host Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i>Host Dashboard</h2>
            <span class="text-muted small">Welcome back, {{ Auth::user()->name }}! Here is an overview of your rental business.</span>
        </div>
        <a href="{{ route('apartments.create') }}" class="btn btn-warning fw-bold">
            <i class="bi bi-plus-lg me-1"></i>List Another Apartment
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">MY APARTMENTS</small>
                        <span class="fs-3 fw-bold text-dark">{{ $totalApartments }}</span>
                    </div>
                    <div class="fs-1 text-primary"><i class="bi bi-buildings"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">TOTAL BOOKINGS</small>
                        <span class="fs-3 fw-bold text-dark">{{ $totalBookings }}</span>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-calendar-check"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">PENDING REQUESTS</small>
                        <span class="fs-3 fw-bold text-warning">{{ $pendingBookingsCount }}</span>
                    </div>
                    <div class="fs-1 text-warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted fw-bold d-block">TOTAL EARNINGS</small>
                        <span class="fs-3 fw-bold text-success">${{ number_format($totalEarnings, 2) }}</span>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Booking Requests -->
    <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
        <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Recent Booking Requests on Your Apartments</h5>
            <a href="{{ route('apartments.my_bookings') }}" class="btn btn-sm btn-outline-light">View All Bookings</a>
        </div>
        <div class="card-body p-0">
            @if($recentBookings->isEmpty())
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No booking requests received yet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Guest</th>
                                <th>Apartment</th>
                                <th>Dates</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking->user->name }}</strong>
                                        <small class="d-block text-muted">{{ $booking->user->email }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('apartments.show', $booking->apartment_id) }}" class="text-decoration-none text-dark fw-bold">
                                            {{ $booking->apartment->title }}
                                        </a>
                                    </td>
                                    <td>{{ $booking->start_date }} &rarr; {{ $booking->end_date }}</td>
                                    <td><strong>${{ number_format($booking->total_price, 2) }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning text-dark' : 'danger') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : 'danger' }}">
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i> Accept</button>
                                            </form>
                                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reject booking?')">
                                                @csrf
                                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-lg"></i> Reject</button>
                                            </form>
                                        @else
                                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm">Details</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
