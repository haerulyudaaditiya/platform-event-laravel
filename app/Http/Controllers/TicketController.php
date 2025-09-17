<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Ticket;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Event $event)
    {
        // Otorisasi: Pastikan organizer hanya bisa mengelola tiket event miliknya
        $this->authorize('update', $event);

        return view('tickets.index', [
            'event' => $event,
            'tickets' => $event->tickets, // Ambil tiket melalui relasi
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        // Buat tiket baru yang langsung terhubung dengan event ini
        $event->tickets()->create($validated);

        return back()->with('success', 'Tiket berhasil ditambahkan.');
    }

    public function edit(Event $event, Ticket $ticket)
    {
        $this->authorize('update', $event);
        return view('tickets.edit', compact('event', 'ticket'));
    }

    public function update(Request $request, Event $event, Ticket $ticket)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0', // Izinkan 0 untuk tiket habis
        ]);

        $ticket->update($validated);

        return redirect()->route('events.tickets.index', $event)->with('success', 'Tiket berhasil diperbarui.');
    }

    public function destroy(Event $event, Ticket $ticket)
    {
        $this->authorize('update', $event);

        // Logika tambahan: Anda bisa mencegah penghapusan jika tiket sudah pernah dipesan.
        if ($ticket->bookings()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus tiket yang sudah memiliki riwayat pesanan.');
        }

        $ticket->delete();

        return back()->with('success', 'Tiket berhasil dihapus.');
    }
}
