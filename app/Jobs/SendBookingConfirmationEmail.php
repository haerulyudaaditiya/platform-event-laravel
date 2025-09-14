<?php
namespace App\Jobs;

use App\Mail\BookingConfirmed; // <-- Import
use App\Models\Booking; // <-- Import
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail; // <-- Import

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
        // Kirim email menggunakan Mailable yang sudah dibuat
        Mail::to($this->booking->user->email)->send(new BookingConfirmed($this->booking));
    }
}
