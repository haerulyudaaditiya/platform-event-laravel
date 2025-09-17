<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmationNotification extends Notification
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Siapkan rincian tiket untuk ditampilkan
        $ticketDetails = [];
        foreach($this->booking->tickets as $ticket){
            $ticketDetails[] = '- ' . $ticket->pivot->quantity . 'x ' . $ticket->name . ' (@ Rp ' . number_format($ticket->pivot->price_per_ticket) . ')';
        }

        return (new MailMessage)
                    ->subject('Konfirmasi Pembayaran: ' . $this->booking->event->name)
                    ->greeting('Pembayaran Berhasil, ' . $this->booking->user->name . '!')
                    ->line('Terima kasih, pembayaran Anda telah kami terima. Berikut adalah rincian pesanan Anda:')
                    ->line('**Event:** ' . $this->booking->event->name)
                    ->line('**Total Pembayaran:** Rp ' . number_format($this->booking->total_price))
                    ->line('**Tiket yang Dipesan:**')
                    ->lines($ticketDetails)
                    ->action('Lihat E-Tiket Anda', route('bookings.show', $this->booking))
                    ->line('Sampai jumpa di event!');
    }
}
