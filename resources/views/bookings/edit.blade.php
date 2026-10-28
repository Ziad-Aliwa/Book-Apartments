@extends('layouts')

@section('title', 'Edit Booking - ' . $booking->apartment->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-dark text-white p-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Modify Booking Dates</h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4">
                        <img src="{{ $booking->apartment->cover_url }}" class="rounded" style="width: 100px; height: 70px; object-fit: cover;" alt="{{ $booking->apartment->title }}">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $booking->apartment->title }}</h5>
                            <span class="text-muted small"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $booking->apartment->address }}</span>
                            <div class="small fw-bold text-primary mt-1">${{ number_format($booking->apartment->price_per_night, 2) }} / night</div>
                        </div>
                    </div>

                    <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Select New Stay Dates</label>
                            <input type="text" id="date_range_picker" class="form-control form-control-lg"
                                   placeholder="{{ $booking->start_date }} to {{ $booking->end_date }}" required>
                            <input type="hidden" name="start_date" id="start_date" value="{{ $booking->start_date }}">
                            <input type="hidden" name="end_date" id="end_date" value="{{ $booking->end_date }}">
                        </div>

                        <div id="costSummary" class="p-3 bg-light rounded-3 mb-4">
                            <div class="d-flex justify-content-between mb-2 small text-muted">
                                <span>${{ number_format($booking->apartment->price_per_night, 2) }} &times; <span id="numDays">0</span> nights</span>
                                <span>$<span id="subTotal">0.00</span></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-5 fw-bold text-dark">
                                <span>Updated Total</span>
                                <span>$<span id="finalTotal">{{ number_format($booking->total_price, 2) }}</span></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" id="updateBtn" class="btn btn-primary px-4 fw-bold">Update Reservation</button>
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
        const pricePerNight = {{ $booking->apartment->price_per_night }};
        const minDate = "{{ $booking->apartment->available_from }}" > new Date().toISOString().split('T')[0] ? "{{ $booking->apartment->available_from }}" : "today";
        const maxDate = "{{ $booking->apartment->available_to }}";

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
                defaultDate: ["{{ $booking->start_date }}", "{{ $booking->end_date }}"],
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
                        document.getElementById('updateBtn').disabled = false;
                    }
                }
            });
        }
    });
</script>
@endpush
