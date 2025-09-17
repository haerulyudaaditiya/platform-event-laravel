<x-public-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">

                    <div class="text-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">E-Tiket Anda</h2>
                        <p class="text-gray-500">Tunjukkan QR Code ini kepada panitia di lokasi event.</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-gray-50 p-4">
                            <h3 class="font-bold text-xl text-gray-900">{{ $booking->event->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $booking->event->category }}</p>
                        </div>
                        <div class="p-6 text-center">
                            <div class="inline-block p-4 bg-white border rounded-lg">
                                {!! QrCode::size(250)->generate($booking->unique_code) !!}
                            </div>
                            <p class="mt-2 text-sm text-gray-500 font-mono">{{ $booking->unique_code }}</p>
                        </div>
                        <div class="border-t p-4 space-y-4">
                            <div>
                                <p class="font-semibold text-sm mb-2 text-gray-800">Detail Event:</p>
                                <div class="text-sm text-gray-700">
                                    <p><strong>Tanggal:</strong>
                                        @php
                                            $startTime = \Carbon\Carbon::parse($booking->event->start_time);
                                            $endTime = \Carbon\Carbon::parse($booking->event->end_time);
                                        @endphp

                                        {{ $startTime->format('d F Y') }}
                                        @if (!$startTime->isSameDay($endTime))
                                            - {{ $endTime->format('d F Y') }}
                                        @endif
                                    </p>
                                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->event->end_time)->format('H:i') }} WIB</p>
                                    <p><strong>Tempat:</strong> {{ $booking->event->venue }}</p>
                                </div>
                            </div>

                            <hr>

                            <div>
                                <p class="font-semibold text-sm mb-2 text-gray-800">Detail Pemesan:</p>
                                <div class="text-sm text-gray-700">
                                    <p><strong>Nama:</strong> {{ $booking->user->name }}</p>
                                </div>
                            </div>

                            <hr>

                            <div>
                                <p class="font-semibold text-sm mb-2 text-gray-800">Detail Tiket:</p>
                                <ul class="text-gray-700 text-sm space-y-1">
                                    @foreach ($booking->tickets as $ticket)
                                        <li class="flex justify-between">
                                            <span>{{ $ticket->pivot->quantity }}x {{ $ticket->name }}</span>
                                            <span>Rp {{ number_format($ticket->pivot->price_per_ticket * $ticket->pivot->quantity) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                         <div class="bg-gray-50 p-4 flex justify-between items-center">
                            <p class="text-lg font-bold text-gray-900">Total</p>
                            <p class="text-lg font-bold text-indigo-600">Rp {{ number_format($booking->total_price) }}</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            &larr; Kembali ke Riwayat Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
