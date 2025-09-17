<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Exports\AttendeesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class EventController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // <-- Jangan lupa tambahkan Request
    {
        $organizer = auth()->user();

        // 1. Mulai query dasar
        $query = $organizer->events()
                        ->withCount(['bookings' => function ($query) {
                            $query->where('status', 'paid');
                        }]);

        // 2. Terapkan filter status
        if ($request->filled('status')) {
            if ($request->status == 'published') {
                $query->where('is_published', true);
            } elseif ($request->status == 'draft') {
                $query->where('is_published', false);
            }
        }

        // 3. Terapkan filter pencarian
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 4. Eksekusi query
        $events = $query->latest()->paginate(10)->withQueryString();

        // Hitung statistik (tetap sama)
        $totalEvents = $organizer->events()->count();
        $eventIds = $organizer->events()->pluck('id');
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
        // 1. Validasi semua data yang masuk, termasuk input baru
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Musik,Seminar,Olahraga,Festival',
            'description' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Menggunakan image_file
            'start_time' => 'required|date',
            'end_time' => 'required|date|after_or_equal:start_time',
            'venue' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'ticket_price' => ['nullable', 'integer', 'min:0', 'required_with:ticket_quantity'],
            'ticket_quantity' => ['nullable', 'integer', 'min:1', 'required_with:ticket_price'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        // 2. Proses upload gambar jika ada
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('event-images', 'public');
            // Simpan path ke kolom 'image' di database
            $validated['image'] = $path;
        }

        // 3. Proses status publikasi dari checkbox
        $validated['is_published'] = $request->boolean('is_published');

        // 4. Tambahkan user_id dari user yang sedang login
        $validated['user_id'] = Auth::id();

        // 5. Buat event baru dengan data yang sudah divalidasi
        $event = Event::create($validated);

        // 6. Buat tiket dasar "Regular" jika harga & kuantitas diisi
        if ($request->filled('ticket_price') && $request->filled('ticket_quantity')) {
            $event->tickets()->create([
                'name' => 'Regular',
                'price' => $request->ticket_price,
                'quantity' => $request->ticket_quantity,
            ]);
        }

        // 7. Redirect ke halaman index dengan pesan sukses
        return redirect()->route('events.index')->with('success', 'Event berhasil dibuat!');
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
        $this->authorize('update', $event);

        $bookingsQuery = $event->bookings()->whereIn('status', ['paid', 'checked-in']);

        // Hitung statistik
        $attendeesCheckedIn = $event->bookings()->where('status', 'checked-in')->count();
        $totalAttendees = $bookingsQuery->count();

        $bookings = $bookingsQuery->with('user', 'tickets')->latest()->paginate(10);

        return view('events.attendees', compact('event', 'bookings', 'attendeesCheckedIn', 'totalAttendees'));
    }

    public function scanner(Event $event)
    {
        // Otorisasi
        $this->authorize('update', $event);

        // Ambil 10 booking terakhir yang sudah check-in untuk event ini
        $recentCheckIns = $event->bookings()
                                ->where('status', 'checked-in')
                                ->with('user')
                                ->latest('updated_at') // Urutkan berdasarkan waktu check-in terbaru
                                ->take(10)
                                ->get();

        return view('events.scanner', compact('event', 'recentCheckIns'));
    }

    public function scanTicket(Request $request)
    {
        $validated = $request->validate([
            'unique_code' => 'required|string|exists:bookings,unique_code',
        ]);

        // Tambahkan 'with('event')' untuk mengambil data event terkait
        $booking = Booking::where('unique_code', $validated['unique_code'])->with('event')->first();

        // =======================================================
        // PENGECEKAN BARU: Apakah event sudah berakhir?
        // =======================================================
        if (now()->gt($booking->event->end_time)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event ini sudah berakhir.',
                'data' => [
                    'name' => $booking->user->name,
                    'event_name' => $booking->event->name,
                ]
            ], 400); // 400 Bad Request
        }

        // Pengecekan lama: Apakah sudah pernah check-in?
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

    public function salesChartData()
    {
        $user = auth()->user();
        $eventIds = $user->events()->pluck('id');

        // Ambil data total pendapatan per hari selama 7 hari terakhir
        $salesData = Booking::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total')
            )
            ->whereIn('event_id', $eventIds)
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Format data untuk Chart.js
        $labels = $salesData->pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('d M');
        });
        $data = $salesData->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function exportAttendees(Event $event)
    {
        // Otorisasi
        $this->authorize('update', $event);

        // Buat nama file yang unik
        $fileName = 'peserta-' . Str::slug($event->name) . '.xlsx';

        // Panggil class export dan unduh filenya
        return Excel::download(new AttendeesExport($event), $fileName);
    }
}
