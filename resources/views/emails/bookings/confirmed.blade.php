<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Pemesanan</title>
</head>
<body>
    <h1>Terima kasih atas pesanan Anda!</h1>
    <p>Halo {{ $booking->user->name }},</p>
    <p>Pembayaran Anda untuk event <strong>{{ $booking->event->name }}</strong> telah kami terima. Berikut adalah detail pesanan Anda:</p>

    <ul>
        @foreach($booking->tickets as $ticket)
            <li>{{ $ticket->pivot->quantity }}x {{ $ticket->name }}</li>
        @endforeach
    </ul>

    <p><strong>Total Pembayaran: Rp {{ number_format($booking->total_price) }}</strong></p>
    <p>Status: {{ ucfirst($booking->status) }}</p>

    <p>Sampai jumpa di event!</p>
</body>
</html>
