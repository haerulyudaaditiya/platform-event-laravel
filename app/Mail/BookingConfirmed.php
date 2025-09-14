<?php
namespace App\Mail;

use App\Models\Booking; // <-- Import
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking; // <-- Buat properti publik

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking) // <-- Terima booking
    {
        $this->booking = $booking; // <-- Simpan booking
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Pemesanan Event: ' . $this->booking->event->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.confirmed', // <-- Arahkan ke view email
        );
    }
}
