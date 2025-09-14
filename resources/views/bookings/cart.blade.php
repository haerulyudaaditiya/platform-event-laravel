<x-public-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800">Keranjang Anda</h2>

                    <x-auth-session-status class="mb-4" :status="session('success')" />
                    <x-input-error :messages="$errors->all()" class="mb-4"/>

                    @if($pendingBookings->isNotEmpty())
                        <form id="cart-form" action="" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="form-method-input" value="POST">

                            <div class="space-y-6">
                                @foreach ($pendingBookings as $booking)
                                    <div class="border rounded-lg shadow-sm overflow-hidden flex items-start p-4 space-x-4 border-gray-200">
                                        <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}"
                                               class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                                        <div class="flex-grow">
                                            <p class="text-sm text-yellow-800 font-semibold bg-yellow-50 border border-yellow-200 rounded-md p-2 mb-2">
                                                Pesanan ini akan otomatis dibatalkan jika tidak dibayar dalam 1 jam.
                                            </p>
                                            <h3 class="font-bold text-xl text-gray-900">{{ $booking->event->name }}</h3>
                                            <p class="text-sm text-gray-600 mb-3">{{ $booking->event->venue }}</p>

                                            <ul class="text-gray-700 text-sm space-y-1 my-2">
                                                @foreach ($booking->tickets as $ticket)
                                                    <li class="flex justify-between">
                                                        <span>{{ $ticket->pivot->quantity }}x {{ $ticket->name }}</span>
                                                        <span>Rp {{ number_format($ticket->pivot->price_per_ticket * $ticket->pivot->quantity) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <hr class="my-2">
                                            <div class="flex justify-between items-center">
                                                <p class="text-base font-bold text-gray-900">Total</p>
                                                <p class="text-base font-bold text-indigo-600">Rp {{ number_format($booking->total_price) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 flex justify-between items-center border-t pt-6">
                                <div>
                                    <button type="submit"
                                            onclick="submitCartAction('{{ route('cart.delete-selected') }}', 'DELETE')"
                                            class="text-sm text-red-600 hover:underline">
                                        Hapus yang Dipilih
                                    </button>
                                </div>
                                <button type="submit"
                                        onclick="submitCartAction('{{ route('cart.proceed-to-payment') }}', 'POST')"
                                        class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Lanjutkan Pembayaran
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500">Keranjang Anda kosong.</p>
                            <a href="{{ route('home') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Cari Event</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function submitCartAction(actionUrl, method) {
            event.preventDefault(); // Mencegah submit default
            const form = document.getElementById('cart-form');
            const methodInput = document.getElementById('form-method-input');

            if (method === 'DELETE') {
                if (!confirm('Anda yakin ingin menghapus item yang dipilih?')) {
                    return; // Batalkan jika user klik 'Cancel'
                }
                methodInput.value = 'DELETE';
            } else {
                methodInput.value = 'POST';
            }

            form.action = actionUrl;
            form.submit();
        }
    </script>
    @endpush
</x-public-layout>
