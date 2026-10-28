@extends('layouts')

@section('title', 'Book Apartment - ' . $apartment->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-dark text-white p-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-calendar2-check-fill me-2"></i>Book Apartment</h4>
                </div>
                <div class="card-body p-4">
                    <!-- Apartment Summary -->
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4">
                        <img src="{{ $apartment->cover_url }}" class="rounded" style="width: 120px; height: 80px; object-fit: cover;" alt="{{ $apartment->title }}">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $apartment->title }}</h5>
                            <p class="text-muted small mb-1"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $apartment->address }}</p>
                            <span class="badge bg-primary fs-6">${{ number_format($apartment->price_per_night, 2) }} / night</span>
                        </div>
                    </div>

                    <form action="{{ route('bookings.store', $apartment->id) }}" method="POST" id="bookingForm">
                        @csrf
                        <input type="hidden" name="apartment_id" value="{{ $apartment->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Stay Dates <span class="text-danger">*</span></label>
                            <input type="text" id="date_range_picker" class="form-control form-control-lg" placeholder="Click to choose Check-in and Check-out dates" required>
                            <input type="hidden" name="start_date" id="start_date">
                            <input type="hidden" name="end_date" id="end_date">
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-shield-lock me-1"></i>Dates already confirmed for other guests are automatically blocked.
                            </small>
                        </div>

                        <!-- Price calculation card -->
                        <div id="costSummary" class="p-3 bg-light rounded-3 mb-4" style="display: none;">
                            <div class="d-flex justify-content-between mb-2">
                                <span>${{ number_format($apartment->price_per_night, 2) }} &times; <span id="numDays">0</span> nights</span>
                                <span class="fw-bold">$<span id="subTotal">0.00</span></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-5 fw-bold text-dark">
                                <span>Total Price</span>
                                <span>$<span id="finalTotal">0.00</span></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <a href="{{ route('apartments.show', $apartment->id) }}" class="btn btn-outline-secondary">Back to Apartment</a>
                            <button type="submit" id="reserveBtn" class="btn btn-primary px-4 py-2 fw-bold" disabled>
                                Confirm & Request Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookedRanges = @json($bookedRanges ?? []);
        const pricePerNight = {{ $apartment->price_per_night }};
        const minDate = "{{ $apartment->available_from }}" > new Date().toISOString().split('T')[0] ? "{{ $apartment->available_from }}" : "today";
        const maxDate = "{{ $apartment->available_to }}";

        const disabledDates = bookedRanges.map(function(range) {
            return {
                from: range.from,
                to: range.to
            };
        });

        const picker = document.getElementById('date_range_picker');
        if (picker) {
            flatpickr(picker, {
                mode: 'range',
                minDate: minDate,
                maxDate: maxDate,
                disable: disabledDates,
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startDate = instance.formatDate(selectedDates[0], "Y-m-d");
                        const endDate = instance.formatDate(selectedDates[1], "Y-m-d");

                        document.getElementById('start_date').value = startDate;
                        document.getElementById('end_date').value = endDate;

                        const diffTime = Math.abs(selectedDates[1] - selectedDates[0]);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        const total = (diffDays * pricePerNight).toFixed(2);

                        document.getElementById('numDays').textContent = diffDays;
                        document.getElementById('subTotal').textContent = total;
                        document.getElementById('finalTotal').textContent = total;

                        document.getElementById('costSummary').style.display = 'block';
                        document.getElementById('reserveBtn').disabled = false;
                    } else {
                        document.getElementById('costSummary').style.display = 'none';
                        document.getElementById('reserveBtn').disabled = true;
                    }
                }
            });
        }
    });
</script>
@endpush
