<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EventController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil event yang dibuat oleh user yang sedang login saja
        // Urutkan berdasarkan yang terbaru, dan gunakan paginasi
        $events = Auth::user()->events()->latest()->paginate(10);

        return view('events.index', compact('events'));
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
        ]);

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
        ]);

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
}
