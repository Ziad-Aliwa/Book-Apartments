<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpireUnpaidBookings extends Command
{
    protected $signature = 'bookings:expire-unpaid';

    protected $description = 'Expire bookings that exceeded payment deadline without payment';

    public function handle(): int
    {
        $affected = Booking::where('payment_status', 'unpaid')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->update([
                'status' => 'expired',
            ]);

        $this->info("Successfully expired {$affected} unpaid booking(s).");

        return Command::SUCCESS;
    }
}
