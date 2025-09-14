<?php
namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Jobs\SendBookingConfirmationEmail;
use Illuminate\Support\Str;
use App\Events\TicketSold;

class PaymentController extends Controller
{
    public function pay(Booking $booking)
    {
        // Otorisasi: pastikan user hanya bisa membayar booking miliknya
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            abort(403);
        }

        // Set a-up konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Buat parameter transaksi untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $booking->id . '-' . time(), // Order ID unik
                'gross_amount' => $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
            ],
        ];

        // Dapatkan Snap Token
        $snapToken = Snap::getSnapToken($params);

        // Tampilkan halaman pembayaran
        return view('payments.pay', compact('snapToken', 'booking'));
    }

    public function notificationHandler(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        $notification = new Notification();

        $orderId = explode('-', $notification->order_id)[0];
        $transaction = Transaction::find($orderId); // <-- Cari Transaksi, bukan Booking

        if (!$transaction) return;

        if ($notification->transaction_status == 'settlement' || $notification->transaction_status == 'capture') {
            if ($transaction->status == 'pending') {
                // Update status transaksi utama
                $transaction->update(['status' => 'paid']);

                // Update status SEMUA booking yang terhubung
                foreach ($transaction->bookings as $booking) {
                    $booking->update(['status' => 'paid', 'unique_code' => Str::uuid()]);
                    // Kirim email untuk SETIAP booking
                    SendBookingConfirmationEmail::dispatch($booking);
                    TicketSold::dispatch($booking);
                }
            }
        }
    }

    public function paymentSuccess(Booking $booking)
    {
        // Otorisasi: pastikan user hanya bisa mengakses halaman sukses miliknya
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Set pesan sukses di session dan langsung redirect ke halaman E-Tiket
        return redirect()->route('bookings.show', $booking)
                        ->with('success', 'Pembayaran Berhasil! E-Tiket Anda telah diterbitkan.');
    }

    public function payTransaction(Transaction $transaction)
    {
        // Otorisasi
        if ($transaction->user_id !== auth()->id() || $transaction->status !== 'pending') {
            abort(403);
        }

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Siapkan detail item untuk Midtrans
        $item_details = [];
        foreach ($transaction->bookings as $booking) {
            foreach ($booking->tickets as $ticket) {
                $item_details[] = [
                    'id' => $ticket->id . '-' . $booking->id,
                    'price' => $ticket->pivot->price_per_ticket,
                    'quantity' => $ticket->pivot->quantity,
                    'name' => 'Tiket ' . $ticket->name . ' - ' . $booking->event->name,
                ];
            }
        }

        // Buat parameter transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->id . '-' . time(),
                'gross_amount' => $transaction->total_amount,
            ],
            'item_details' => $item_details,
            'customer_details' => [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
        ];

        // Dapatkan Snap Token dari Midtrans
        $snapToken = Snap::getSnapToken($params);

        // Simpan token ke database
        $transaction->update(['snap_token' => $snapToken]);

        // Tampilkan halaman pembayaran
        return view('payments.pay', compact('snapToken', 'transaction'));
    }

    public function paymentPending()
    {
        // Redirect ke keranjang dengan pesan peringatan
        return redirect()->route('cart.index')->with('warning', 'Pembayaran Anda tertunda. Silakan periksa statusnya di halaman Riwayat.');
    }

    public function paymentError()
    {
        // Redirect ke keranjang dengan pesan error
        return redirect()->route('cart.index')->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }
}
