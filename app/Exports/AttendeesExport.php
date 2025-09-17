<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendeesExport implements FromCollection, WithHeadings, WithMapping
{
    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Ambil semua data booking yang 'paid' atau 'checked-in' untuk event ini
        return $this->event->bookings()
                    ->whereIn('status', ['paid', 'checked-in'])
                    ->with('user', 'tickets')
                    ->get();
    }

    /**
     * Tentukan judul untuk setiap kolom.
     */
    public function headings(): array
    {
        return [
            'Nama Peserta',
            'No. Telepon',
            'Status Check-in',
            'Tiket',
            'Tanggal Pesan',
        ];
    }

    /**
     * Petakan data yang ingin ditampilkan di setiap baris.
     * @param Booking $booking
     */
    public function map($booking): array
    {
        // Gabungkan detail tiket menjadi satu string
        $ticketsDetails = $booking->tickets->map(function ($ticket) {
            return $ticket->pivot->quantity . 'x ' . $ticket->name;
        })->implode(', ');

        return [
            $booking->user->name,
            $booking->user->phone_number,
            $booking->status,
            $ticketsDetails,
            $booking->created_at->format('d-m-Y H:i'),
        ];
    }
}
