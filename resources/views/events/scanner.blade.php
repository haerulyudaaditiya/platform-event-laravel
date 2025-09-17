<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Scan Tiket untuk: {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Arahkan QR Code ke Kamera</h3>
                    <div id="qr-reader" style="width:100%"></div>
                    <div id="qr-reader-results" class="mt-4"></div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Riwayat Check-in Terakhir</h3>
                    <div class="space-y-3">
                        @forelse($recentCheckIns as $booking)
                            <div class="flex items-center space-x-3 bg-gray-50 p-2 rounded-md">
                                <span class="text-green-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $booking->user->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Check-in pada: {{ $booking->updated_at->format('H:i:s') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada peserta yang melakukan check-in.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            function onScanSuccess(decodedText, decodedResult) {
                // `decodedText` adalah unique_code dari tiket
                console.log(`Code matched = ${decodedText}`, decodedResult);

                // Hentikan scanner agar tidak scan berulang kali
                html5QrcodeScanner.clear();

                // Kirim data ke API kita
                fetch('{{ route("api.scan-ticket") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Penting untuk API web
                    },
                    body: JSON.stringify({ unique_code: decodedText })
                })
                .then(response => response.json())
                .then(data => {
                    let resultDiv = document.getElementById('qr-reader-results');
                    if (data.status === 'success') {
                        resultDiv.innerHTML = `<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                                <strong class="font-bold">Berhasil!</strong>
                                                <span class="block sm:inline">Selamat datang, ${data.data.name}!</span>
                                            </div>`;
                    } else {
                        resultDiv.innerHTML = `<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                                <strong class="font-bold">Gagal!</strong>
                                                <span class="block sm:inline">${data.message} - Atas nama ${data.data.name}</span>
                                            </div>`;
                    }
                    // Tambahkan tombol untuk scan lagi
                    resultDiv.innerHTML += '<button onclick="location.reload()" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Scan Lagi</button>';
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
                // console.warn(`Code scan error = ${error}`);
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                /* verbose= */ false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        });
    </script>
    @endpush
</x-app-layout>
