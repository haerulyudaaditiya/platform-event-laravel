<?php
namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending bookings that have passed the payment time limit and restore ticket stock.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Tentukan batas waktu (misal: 1 jam yang lalu)
        $timeLimit = now()->subHour();

        // Cari semua booking 'pending' yang dibuat sebelum batas waktu
        $expiredBookings = Booking::where('status', 'pending')
                                  ->where('created_at', '<=', $timeLimit)
                                  ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired pending bookings found.');
            return;
        }

        $this->info("Found {$expiredBookings->count()} expired bookings. Cancelling...");

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                // Kembalikan stok untuk setiap tiket dalam booking ini
                foreach ($booking->tickets as $bookedTicket) {
                    Ticket::find($bookedTicket->id)->increment('quantity', $bookedTicket->pivot->quantity);
                }

                // Ubah status booking menjadi 'cancelled'
                $booking->update(['status' => 'cancelled']);
            });
        }

        $this->info('All expired pending bookings have been cancelled and stock restored.');
    }
}
