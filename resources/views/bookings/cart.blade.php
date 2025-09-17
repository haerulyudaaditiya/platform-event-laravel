<x-public-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800">Keranjang Anda</h2>
                    @if($pendingBookings->isNotEmpty())
                        <form id="cart-form" action="" method="POST">
                            @csrf
                            <input type="hidden" name="_method" id="form-method-input" value="POST">

                            <div class="mb-4">
                                <label for="select-all" class="flex items-center text-sm">
                                    <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-gray-700">Pilih Semua</span>
                                </label>
                            </div>

                            <div class="space-y-6">
                                @foreach ($pendingBookings as $booking)
                                    <div class="border rounded-lg shadow-sm overflow-hidden flex items-start p-4 space-x-4 border-gray-200">
                                        <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}"
                                               class="booking-checkbox mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                                        <div class="flex-grow">
                                            <p class="text-sm text-yellow-800 font-semibold bg-yellow-50 border border-yellow-200 rounded-md p-2 mb-2">
                                                Pesanan ini akan otomatis dibatalkan jika tidak dibayar dalam 1 jam.
                                            </p>
                                            <h3 class="font-bold text-xl text-gray-900">{{ $booking->event->name }}</h3>

                                            <div class="text-sm text-gray-500 mb-3 space-y-1">
                                                <p>📅 {{ \Carbon\Carbon::parse($booking->event->start_time)->format('d M Y') }}</p>
                                                <p>📍 {{ $booking->event->venue }}</p>
                                            </div>

                                            <ul class="text-gray-700 text-sm space-y-1">
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
                                    <button type="button"
                                            onclick="confirmDelete()"
                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
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
        // Fungsi untuk select all
        document.getElementById('select-all').addEventListener('change', function(event) {
            document.querySelectorAll('.booking-checkbox').forEach(checkbox => {
                checkbox.checked = event.target.checked;
            });
        });

        // Fungsi untuk konfirmasi hapus dengan SweetAlert
        function confirmDelete() {
            const form = document.getElementById('cart-form');
            Swal.fire({
                title: 'Anda yakin?',
                text: "Pesanan yang dipilih akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.action = '{{ route("cart.delete-selected") }}';
                    form.querySelector('input[name=_method]').value = 'DELETE';
                    form.submit();
                }
            })
        }

        // Fungsi submit utama (disempurnakan)
        function submitCartAction(actionUrl, method) {
            event.preventDefault();
            const form = document.getElementById('cart-form');
            form.action = actionUrl;
            form.querySelector('input[name=_method]').value = method;
            form.submit();
        }
    </script>
    @endpush
</x-public-layout>
