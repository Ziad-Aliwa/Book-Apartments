<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Response;

class CalendarController extends Controller
{
    public function export(Apartment $apartment): Response
    {
        $bookings = $apartment->bookings()
            ->where('status', 'confirmed')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//BookApartments//Apartment Booking Platform//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:' . addcslashes($apartment->title, ",;"),
        ];

        foreach ($bookings as $booking) {
            $startDate = \Carbon\Carbon::parse($booking->start_date)->format('Ymd');
            // iCal DTEND for full days is exclusive, so add 1 day
            $endDate = \Carbon\Carbon::parse($booking->end_date)->addDay()->format('Ymd');
            $dtStamp = $booking->created_at->format('Ymd\THis\Z');

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . md5('booking-' . $booking->id . '-' . $booking->booking_code) . '@bookapartments.com';
            $lines[] = 'DTSTAMP:' . $dtStamp;
            $lines[] = 'DTSTART;VALUE=DATE:' . $startDate;
            $lines[] = 'DTEND;VALUE=DATE:' . $endDate;
            $lines[] = 'SUMMARY:Reserved (' . ($booking->booking_code ?? 'BK-' . $booking->id) . ')';
            $lines[] = 'DESCRIPTION:Apartment reservation on BookApartments.';
            $lines[] = 'STATUS:CONFIRMED';
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        $content = implode("\r\n", $lines);

        return response($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="apartment-' . $apartment->id . '-calendar.ics"',
        ]);
    }
}
