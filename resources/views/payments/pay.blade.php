<x-public-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-gray-800">Selesaikan Pembayaran</h2>
                        <p class="mt-2 text-gray-600">Anda akan membayar untuk Transaksi #{{ $transaction->id }}</p>
                    </div>

                    <div class="mt-6 border-t pt-6">
                        <div class="flex justify-between items-center">
                             <p class="text-lg font-bold text-gray-900">Total Tagihan</p>
                             <p class="text-lg font-bold text-indigo-600">Rp {{ number_format($transaction->total_amount) }}</p>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <button id="pay-button" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-gray-700">
                            Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
          document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $snapToken }}', {
              onSuccess: function(result){
                // Untuk multi-item, redirect ke Riwayat dengan pesan sukses
                window.location.href = '{{ route("bookings.index") }}?payment_success=1';
              },
              onPending: function(result){
                // Redirect ke route perantara untuk notifikasi 'pending'
                window.location.href = '{{ route("payment.pending") }}';
              },
              onError: function(result){
                // Redirect ke route perantara untuk notifikasi 'error'
                window.location.href = '{{ route("payment.error") }}';
              }
            })
          };
        </script>
    @endpush
</x-public-layout>
