<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Notifications\BookingConfirmationNotification; // <-- Import notifikasi baru
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function handle(): void
    {
        // Ganti Mail::send() dengan ->notify()
        $this->booking->user->notify(new BookingConfirmationNotification($this->booking));
    }
}
