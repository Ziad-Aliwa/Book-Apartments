@extends('layouts')

@section('title', 'Invoice #' . $booking->booking_code)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to Booking
                </a>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-printer me-1"></i>Print Receipt
                    </button>
                    <a href="{{ route('bookings.invoice.download', $booking->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                    </a>
                </div>
            </div>

            <!-- Invoice Card -->
            <div class="card shadow-sm border-0 rounded-3 p-4 bg-white" id="invoiceArea">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <h3 class="fw-bold text-primary mb-1">
                            <i class="bi bi-buildings-fill me-2"></i>BookApartments
                        </h3>
                        <span class="text-muted small">Apartment Reservation & Hospitality Services</span>
                    </div>
                    <div class="text-end">
                        <h4 class="fw-bold mb-0">INVOICE</h4>
                        <span class="badge bg-secondary fs-6">#{{ $booking->booking_code }}</span>
                        <div class="small text-muted mt-1">Date: {{ $booking->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                <!-- Billed To & Host Info -->
                <div class="row mb-4">
                    <div class="col-6">
                        <h6 class="text-muted fw-bold small text-uppercase mb-2">Guest (Billed To):</h6>
                        <strong class="d-block fs-6">{{ $booking->user->name }}</strong>
                        <span class="text-muted small">{{ $booking->user->email }}</span>
                    </div>
                    <div class="col-6 text-end">
                        <h6 class="text-muted fw-bold small text-uppercase mb-2">Property Host:</h6>
                        <strong class="d-block fs-6">{{ $booking->apartment->user->name }}</strong>
                        <span class="text-muted small">{{ $booking->apartment->user->email }}</span>
                    </div>
                </div>

                <!-- Reservation Details Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Property Description</th>
                                <th>Stay Dates</th>
                                <th>Rate / Night</th>
                                <th>Nights</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $nights = \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date)) + 1;
                                $subtotal = $nights * $booking->apartment->price_per_night;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $booking->apartment->title }}</strong>
                                    <small class="d-block text-muted">{{ $booking->apartment->address }}</small>
                                </td>
                                <td>{{ $booking->start_date }} &rarr; {{ $booking->end_date }}</td>
                                <td>${{ number_format($booking->apartment->price_per_night, 2) }}</td>
                                <td>{{ $nights }}</td>
                                <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Subtotal:</th>
                                <th class="text-end">${{ number_format($subtotal, 2) }}</th>
                            </tr>
                            @if($booking->discount_amount > 0)
                                <tr>
                                    <th colspan="4" class="text-end text-success">Long-stay Discount:</th>
                                    <th class="text-end text-success">-${{ number_format($booking->discount_amount, 2) }}</th>
                                </tr>
                            @endif
                            <tr class="table-dark fs-5">
                                <th colspan="4" class="text-end">Total Amount:</th>
                                <th class="text-end">${{ number_format($booking->total_price, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Payment Status Notice -->
                <div class="p-3 rounded-3 mb-4 {{ $booking->payment_status === 'paid' ? 'bg-success bg-opacity-10 border border-success' : 'bg-warning bg-opacity-10 border border-warning' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-{{ $booking->payment_status === 'paid' ? 'success' : 'warning text-dark' }} fs-6 mb-1">
                                PAYMENT STATUS: {{ strtoupper($booking->payment_status) }}
                            </span>
                            @if($booking->payment_status === 'paid')
                                <small class="d-block text-success">Total Amount Paid: ${{ number_format($booking->total_price, 2) }}</small>
                            @else
                                <small class="d-block text-danger">Payment required before: {{ $booking->payment_deadline ? \Carbon\Carbon::parse($booking->payment_deadline)->format('M d, Y') : 'N/A' }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'primary' : 'secondary' }} fs-6">
                                Booking: {{ strtoupper($booking->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-center text-muted small border-top pt-3">
                    Thank you for booking with BookApartments! For questions regarding your stay, contact the host or customer support.
                </div>
            </div>
        </div>
    </div>
@endsection
