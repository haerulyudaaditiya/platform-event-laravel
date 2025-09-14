<x-public-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800">Riwayat Transaksi Anda</h2>

                    <form action="{{ route('bookings.index') }}" method="GET" class="mb-8 p-4 bg-gray-50 rounded-lg border">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari Event</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Nama event...">
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Semua</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="checked-in" {{ request('status') == 'checked-in' ? 'selected' : '' }}>Checked-in</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">Semua</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <a href="{{ route('bookings.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Reset</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Filter</button>
                        </div>
                    </form>

                    <div class="space-y-6">
                        @forelse ($bookings as $booking)
                            <div class="border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                <div class="bg-gray-50 p-4 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600">Pesanan Dibuat: {{ $booking->created_at->format('d F Y') }}</p>
                                        <p class="text-xs text-gray-500 font-mono">ID: {{ $booking->id }}-{{ $booking->unique_code ? Str::limit($booking->unique_code, 8, '') : '' }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                        @if($booking->status == 'paid') bg-green-100 text-green-800 @endif
                                        @if($booking->status == 'cancelled') bg-red-100 text-red-800 @endif
                                        @if($booking->status == 'checked-in') bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-xl text-gray-900">{{ $booking->event->name }}</h4>
                                    <p class="text-sm text-gray-600 mb-3">{{ $booking->event->venue }}</p>

                                    <p class="font-semibold text-sm mb-2 text-gray-800">Detail Tiket:</p>
                                    <ul class="text-gray-700 text-sm space-y-1">
                                        @foreach ($booking->tickets as $ticket)
                                            <li class="flex justify-between">
                                                <span>{{ $ticket->pivot->quantity }}x {{ $ticket->name }}</span>
                                                <span>Rp {{ number_format($ticket->pivot->price_per_ticket * $ticket->pivot->quantity) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <hr class="my-3">
                                    <div class="flex justify-between items-center">
                                        <p class="text-lg font-bold text-gray-900">Total</p>
                                        <p class="text-lg font-bold text-indigo-600">Rp {{ number_format($booking->total_price) }}</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 text-right">
                                    @if($booking->status == 'pending')
                                        <a href="{{ route('bookings.pay', $booking) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                            Bayar Sekarang
                                        </a>
                                    @elseif($booking->status == 'paid' || $booking->status == 'checked-in')
                                        <a href="{{ route('bookings.show', $booking) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                            Lihat E-Tiket
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty

                            <div class="text-center py-12 border-dashed border-2 border-gray-200 rounded-lg">
                                <p class="text-gray-500">Tidak ada pesanan yang cocok dengan filter Anda.</p>
                                <a href="{{ route('bookings.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Reset Filter
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
