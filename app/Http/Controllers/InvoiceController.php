<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['apartment.user', 'user', 'payments']);

        return view('invoices.show', compact('booking'));
    }

    public function download(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['apartment.user', 'user', 'payments']);

        $pdf = Pdf::loadView('invoices.pdf', compact('booking'));
        
        return $pdf->download("Invoice-{$booking->booking_code}.pdf");
    }
}
