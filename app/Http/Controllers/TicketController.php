<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
}
