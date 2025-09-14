<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organizer = auth()->user();
        $events = $organizer->events()->latest()->paginate(10);

        // Hitung statistik
        $totalEvents = $organizer->events()->count();
        // Ambil semua ID event milik organizer ini
        $eventIds = $organizer->events()->pluck('id');
        // Cari semua booking yang sudah lunas untuk event-event tersebut
        $paidBookings = Booking::whereIn('event_id', $eventIds)->where('status', 'paid')->get();

        $totalRevenue = $paidBookings->sum('total_price');
        $ticketsSold = 0;
        foreach($paidBookings as $booking) {
            $ticketsSold += $booking->tickets->sum('pivot.quantity');
        }

        return view('events.index', compact('events', 'totalEvents', 'ticketsSold', 'totalRevenue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Tampilkan halaman form tambah event
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|in:Musik,Seminar,Olahraga,Festival',
        ]);

        if ($request->hasFile('image')) {
            // Simpan gambar di folder 'storage/app/public/event-images'
            $path = $request->file('image')->store('event-images', 'public');
            $validated['image'] = $path;
        }

        // 2. Tambahkan user_id dari user yang sedang login
        $validated['user_id'] = Auth::id();

        // 3. Simpan data ke database
        Event::create($validated);

        // 4. Redirect ke halaman index dengan pesan sukses
        return redirect()->route('events.index')
                        ->with('success', 'Event berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Otorisasi: Cek apakah user yang login boleh meng-update event ini
        $this->authorize('update', $event);

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Otorisasi: Cek lagi sebelum benar-benar meng-update
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|in:Musik,Seminar,Olahraga,Festival',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            // Simpan gambar baru
            $path = $request->file('image')->store('event-images', 'public');
            $validated['image'] = $path;
        }

        $event->update($validated);

        return redirect()->route('events.index')
                        ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Otorisasi menggunakan policy
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')
                        ->with('success', 'Event berhasil dihapus!');
    }

    // app/Http/Controllers/EventController.php
    public function togglePublish(Event $event)
    {
        // Otorisasi, hanya pemilik yang boleh mengubah
        $this->authorize('update', $event);

        // Ubah status is_published
        $event->is_published = !$event->is_published;
        $event->save();

        $message = $event->is_published ? 'Event berhasil di-publish.' : 'Event berhasil di-unpublish.';

        return back()->with('success', $message);
    }

    public function attendees(Event $event)
    {
        // 1. Otorisasi: Pastikan hanya pemilik event yang bisa melihat pesertanya
        $this->authorize('update', $event);

        // 2. Ambil semua booking untuk event ini yang statusnya sudah 'paid'
        $bookings = $event->bookings()
                          ->where('status', 'paid')
                          ->with('user', 'tickets') // Eager load untuk optimasi
                          ->latest()
                          ->paginate(10);

        // 3. Tampilkan view dengan data yang sudah diambil
        return view('events.attendees', compact('event', 'bookings'));
    }

    public function scanner(Event $event)
    {
        // Otorisasi: Pastikan hanya pemilik event yang bisa mengakses scanner
        $this->authorize('update', $event);

        return view('events.scanner', compact('event'));
    }

    public function scanTicket(Request $request)
    {
        $validated = $request->validate([
            'unique_code' => 'required|string|exists:bookings,unique_code',
        ]);

        $booking = Booking::where('unique_code', $validated['unique_code'])->first();

        // Cek apakah tiket sudah pernah di-check-in
        if ($booking->status == 'checked-in') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket ini sudah di-check-in.',
                'data' => [
                    'name' => $booking->user->name,
                    'checked_in_at' => $booking->updated_at->format('d M Y, H:i'),
                ]
            ], 409); // 409 Conflict
        }

        // Jika valid, update statusnya
        $booking->update(['status' => 'checked-in']);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in Berhasil!',
            'data' => [
                'name' => $booking->user->name,
            ]
        ]);
    }
}
