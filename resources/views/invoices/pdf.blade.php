<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $booking->booking_code }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header table {
            width: 100%;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #777;
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        .items-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-paid {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .badge-unpaid {
            background-color: #fff3cd;
            color: #664d03;
        }
        .footer {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="company-name">BookApartments</div>
                    <div style="color: #777; font-size: 12px;">Official Booking Invoice & Receipt</div>
                </td>
                <td class="text-right">
                    <div class="invoice-title">INVOICE #{{ $booking->booking_code }}</div>
                    <div style="color: #777; font-size: 12px;">Date: {{ $booking->created_at->format('M d, Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-col">
                <div class="section-title">Guest Details:</div>
                <strong>{{ $booking->user->name }}</strong><br>
                {{ $booking->user->email }}
            </td>
            <td class="info-col text-right">
                <div class="section-title">Property Host:</div>
                <strong>{{ $booking->apartment->user->name }}</strong><br>
                {{ $booking->apartment->user->email }}
            </td>
        </tr>
    </table>

    @php
        $nights = \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date)) + 1;
        $subtotal = $nights * $booking->apartment->price_per_night;
    @endphp

    <table class="items-table">
        <thead>
            <tr>
                <th>Property</th>
                <th>Stay Period</th>
                <th>Rate / Night</th>
                <th>Nights</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $booking->apartment->title }}</strong><br>
                    <small style="color: #777;">{{ $booking->apartment->address }}</small>
                </td>
                <td>{{ $booking->start_date }} to {{ $booking->end_date }}</td>
                <td>${{ number_format($booking->apartment->price_per_night, 2) }}</td>
                <td>{{ $nights }}</td>
                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($booking->discount_amount > 0)
                <tr>
                    <td colspan="4" class="text-right" style="color: #198754;"><strong>Special Stay Discount:</strong></td>
                    <td class="text-right" style="color: #198754;">-${{ number_format($booking->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr style="background-color: #f1f3f5; font-size: 16px;">
                <td colspan="4" class="text-right"><strong>Total Amount:</strong></td>
                <td class="text-right"><strong>${{ number_format($booking->total_price, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 6px;">
        <table style="width: 100%;">
            <tr>
                <td>
                    <strong>Payment Status:</strong>
                    <span class="badge {{ $booking->payment_status === 'paid' ? 'badge-paid' : 'badge-unpaid' }}">
                        {{ strtoupper($booking->payment_status) }}
                    </span>
                </td>
                <td class="text-right">
                    <strong>Booking Status:</strong> {{ strtoupper($booking->status) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Thank you for reserving with BookApartments! We wish you a wonderful stay.
    </div>
</body>
</html>
