<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redirect;
use App\Models\Transaction; // <-- Import
use Midtrans\Config; // <-- Import
use Midtrans\Snap; // <-- Import

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Ticket $ticket)
    {
        // Validasi input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->input('quantity');

        // Cek apakah stok tiket mencukupi
        if ($ticket->quantity < $quantity) {
            return back()->with('error', 'Stok tiket tidak mencukupi.');
        }

        // Gunakan database transaction untuk memastikan konsistensi data
        DB::transaction(function () use ($ticket, $quantity) {
            // 1. Kurangi stok tiket
            $ticket->decrement('quantity', $quantity);

            // 2. Hitung total harga
            $totalPrice = $ticket->price * $quantity;

            // 3. Buat booking baru
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'event_id' => $ticket->event_id,
                'total_price' => $totalPrice,
                'status' => 'pending', // Status awal
            ]);

            // 4. Lampirkan tiket ke booking di pivot table
            $booking->tickets()->attach($ticket->id, [
                'quantity' => $quantity,
                'price_per_ticket' => $ticket->price,
            ]);
        });

        // Redirect dengan pesan sukses
        return back()->with('success', 'Tiket berhasil ditambahkan ke keranjang!');
    }

    public function index(Request $request)
    {
        // 1. Ambil query dasar untuk booking milik user yang sedang login
        $query = Auth::user()->bookings()
                       ->whereIn('status', ['paid', 'checked-in', 'cancelled'])
                       ->with('event', 'tickets')
                       ->latest();

        // 2. Terapkan filter jika ada input

        // Filter berdasarkan Pencarian (pada nama event)
        if ($request->filled('search')) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan Status Booking
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan Kategori Event
        if ($request->filled('category')) {
            $query->whereHas('event', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Filter berdasarkan Tanggal Transaksi (Date Range)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // 3. Eksekusi query dengan paginasi
        $bookings = $query->paginate(5)->withQueryString();

        // 4. Ambil daftar kategori unik dari event yang pernah dibooking user
        $categories = Auth::user()
                        ->bookings()
                        ->whereHas('event', function ($q) {
                            $q->whereNotNull('category');
                        })
                        ->with('event:id,category')
                        ->get()
                        ->pluck('event.category')
                        ->unique()
                        ->values();

        return view('bookings.index', compact('bookings', 'categories'));
    }

    public function show(Booking $booking)
    {
        // Otorisasi menggunakan BookingPolicy
        $this->authorize('view', $booking);

        return view('bookings.show', compact('booking'));
    }

    public function cart()
    {
        $pendingBookings = Auth::user()->bookings()
                                    ->where('status', 'pending')
                                    ->with('event', 'tickets')
                                    ->latest()
                                    ->get(); // Kita tidak butuh paginasi di keranjang

        return view('bookings.cart', compact('pendingBookings'));
    }

    public function deleteSelected(Request $request)
    {
        $request->validate(['booking_ids' => 'required|array']);

        $bookings = auth()->user()->bookings()->whereIn('id', $request->booking_ids)->get();

        foreach ($bookings as $booking) {
            // Logika untuk mengembalikan stok tiket
            foreach ($booking->tickets as $ticket) {
                Ticket::find($ticket->id)->increment('quantity', $ticket->pivot->quantity);
            }
            $booking->delete();
        }

        return back()->with('success', 'Pesanan yang dipilih berhasil dihapus.');
    }

    public function proceedToPayment(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id',
        ]);

        $user = auth()->user();
        // Ambil semua booking yang diceklis oleh pengguna
        $selectedBookings = $user->bookings()->whereIn('id', $request->booking_ids)->where('status', 'pending')->get();

        if($selectedBookings->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu pesanan untuk dibayar.');
        }

        // 1. Hitung total harga dari SEMUA item yang dipilih
        $grandTotal = $selectedBookings->sum('total_price');

        // 2. Buat satu record transaksi baru sebagai "induk"
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'total_amount' => $grandTotal,
            'status' => 'pending',
        ]);

        // 3. Hubungkan semua booking yang dipilih ke transaksi ini
        // Ini akan mengisi kolom 'transaction_id' di setiap booking
        $transaction->bookings()->saveMany($selectedBookings);

        // 4. Arahkan ke halaman pembayaran dengan membawa OBJEK TRANSAKSI, bukan booking
        return redirect()->route('transactions.pay', $transaction);
    }
}
