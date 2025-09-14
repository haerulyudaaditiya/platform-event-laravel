<x-public-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <img class="w-full h-64 md:h-80 object-cover" src="{{ $event->image ? asset('storage/' . $event->image) : asset('images/placeholder.jpg') }}" alt="{{ $event->name }}">

                <div class="p-6 md:p-8">
                    <div class="md:grid md:grid-cols-3 md:gap-12 relative">

                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold text-gray-600 mb-1">{{ $event->category }}</p>
                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">{{ $event->name }}</h1>
                            <p class="text-lg text-gray-500 mb-6">Diselenggarakan oleh: {{ $event->organizer->name }}</p>
                            <hr class="my-6">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Deskripsi Event</h2>
                            <div class="prose max-w-none text-gray-600">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        </div>

                        <div class="md:col-span-1 mt-8 md:mt-0 md:sticky md:top-24">
                            <div class="bg-gray-50 p-6 rounded-lg shadow-inner mb-6">
                                <div class="space-y-4 text-gray-700">
                                    <p><strong class="block text-gray-800">📅 Tanggal & Waktu</strong>{{ \Carbon\Carbon::parse($event->start_time)->format('d F Y') }} <br><span class="text-sm">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }} WIB</span></p>
                                    <p><strong class="block text-gray-800">📍 Lokasi</strong>{{ $event->venue }} <br><span class="text-sm">{{ $event->location }}</span></p>
                                </div>
                            </div>

                            @if (auth()->guest() || auth()->id() !== $event->user_id)
                                <div>
                                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Pilih Tiket</h3>
                                    <div class="space-y-4">
                                        @forelse ($event->tickets as $ticket)
                                            <form action="{{ route('bookings.store', $ticket) }}" method="POST">
                                                @csrf
                                                <div class="border border-gray-200 p-4 rounded-lg flex justify-between items-center shadow-sm">
                                                    <div>
                                                        <p class="font-bold text-lg text-gray-800">{{ $ticket->name }}</p>
                                                        <p class="text-indigo-600 font-semibold">Rp {{ number_format($ticket->price) }}</p>
                                                        <p class="text-xs text-gray-500 mt-1">{{ $ticket->quantity }} tiket tersedia</p>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" name="quantity" class="w-16 border-gray-300 rounded-md shadow-sm" value="1" min="1" max="{{ $ticket->quantity }}">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Beli</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @empty
                                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                                                <p class="text-yellow-800">Tiket untuk event ini belum tersedia.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @else
                                <div class="bg-blue-50 p-6 rounded-lg shadow-inner">
                                    <h3 class="text-xl font-semibold mb-4 text-blue-800">Mode Organizer</h3>
                                    <p class="text-blue-700">Ini adalah halaman pratinjau untuk event Anda. Form pembelian tiket disembunyikan.</p>
                                    <a href="{{ route('events.attendees', $event) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        Lihat Daftar Peserta
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-12 border-t pt-6">
                       <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            &larr; Kembali ke Semua Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
