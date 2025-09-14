<?php
namespace App\Events;

use App\Models\Booking; // Import
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel; // Import
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Implement
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketSold implements ShouldBroadcast // Implement interface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    // Tentukan data yang akan disiarkan
    public function broadcastWith(): array
    {
        return [
            'buyer_name' => $this->booking->user->name,
            'event_name' => $this->booking->event->name,
        ];
    }

    // Tentukan channel siaran
    public function broadcastOn(): array
    {
        // Kirim ke channel privat milik si organizer event
        return [
            new PrivateChannel('organizer.' . $this->booking->event->user_id),
        ];
    }
}
