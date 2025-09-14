<x-public-layout>
   <div class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Temukan Pengalaman Tak Terlupakan</h1>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-300">Jelajahi berbagai event menarik, mulai dari konser musik, seminar, hingga festival budaya. Tiket Anda menanti.</p>

            <form action="{{ route('home') }}" method="GET" class="mt-8 max-w-xl mx-auto">
                <div class="flex rounded-md shadow-sm">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input block w-full rounded-none rounded-l-md text-gray-900 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari nama event...">
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800">Kenapa Memilih Platform Kami?</h2>
                <p class="mt-2 text-gray-500">Pengalaman terbaik dalam mencari dan membeli tiket event.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="feature-item">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-medium text-gray-900">Pembelian Mudah & Aman</h3>
                    <p class="mt-2 text-base text-gray-500">Dengan gateway pembayaran terpercaya, transaksi Anda dijamin aman.</p>
                </div>
                <div class="feature-item">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-medium text-gray-900">E-Tiket Instan</h3>
                    <p class="mt-2 text-base text-gray-500">Dapatkan E-Tiket dengan QR Code langsung setelah pembayaran berhasil.</p>
                </div>
                <div class="feature-item">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-medium text-gray-900">Temukan Beragam Event</h3>
                    <p class="mt-2 text-base text-gray-500">Dari konser musik hingga seminar, temukan event yang sesuai minat Anda.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="py-16 sm:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="mb-12">
                <h3 class="text-xl font-bold mb-4 text-gray-800 text-center">Telusuri Berdasarkan Kategori</h3>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-full text-sm font-semibold transition {{ !request('category') ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-indigo-50' }}">
                        Semua
                    </a>
                    @foreach($categories as $category)
                        @if($category)
                            <a href="{{ route('home', ['category' => $category]) }}" class="px-4 py-2 rounded-full text-sm font-semibold transition {{ request('category') == $category ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-indigo-50' }}">
                                {{ $category }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <h2 class="text-3xl font-bold mb-8 text-center text-gray-900 tracking-tight">
                @if(request('category'))
                    Menampilkan Kategori {{ request('category') }}
                @else
                    Event Terbaru
                @endif
            </h2>

            @if($events->isEmpty())
                <div class="text-center py-12 border-dashed border-2 border-gray-200 rounded-lg">
                    <p class="text-gray-500">Tidak ada event yang cocok dengan pencarian Anda.</p>
                    <a href="{{ route('home') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Lihat Semua Event
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($events as $event)
                        @include('components.event-card', ['event' => $event])
                    @endforeach
                </div>
                <div class="mt-10">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Apa Kata Mereka?</h2>
                <p class="mt-4 text-lg text-gray-500">Lihat pengalaman pengguna dan organizer yang puas dengan platform kami.</p>
            </div>
            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('images/testimonial-1.jpg') }}" alt="Pengguna 1">
                        <div class="ml-4">
                            <p class="font-semibold text-gray-900">Sarah Wulandari</p>
                            <p class="text-sm text-gray-500">Pengunjung Event</p>
                        </div>
                    </div>
                    <p class="text-gray-600">"Proses pembelian tiketnya sangat cepat dan mudah. E-Tiket dengan QR code sangat praktis, tidak perlu cetak lagi. Pasti akan pakai platform ini lagi!"</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('images/testimonial-2.jpg') }}" alt="Pengguna 2">
                        <div class="ml-4">
                            <p class="font-semibold text-gray-900">Budi Santoso</p>
                            <p class="text-sm text-gray-500">Event Organizer</p>
                        </div>
                    </div>
                    <p class="text-gray-600">"Sebagai organizer, fitur manajemen pesertanya luar biasa. Saya bisa melihat siapa saja yang sudah bayar secara real-time. Sangat membantu di hari-H."</p>
                </div>
                <div class="bg-gray-50 p-8 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('images/testimonial-3.jpg') }}" alt="Pengguna 3">
                        <div class="ml-4">
                            <p class="font-semibold text-gray-900">Rina Setiawati</p>
                            <p class="text-sm text-gray-500">Pengunjung Event</p>
                        </div>
                    </div>
                    <p class="text-gray-600">"Suka sekali dengan desainnya yang bersih dan pilihan event yang beragam. Menemukan konser musik favorit jadi lebih mudah di sini."</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-gray-900">Pertanyaan yang Sering Diajukan</h2>
                <p class="mt-4 text-lg text-gray-500">Tidak menemukan jawaban Anda? Hubungi kami.</p>
            </div>
            <dl class="mt-12 space-y-10">
                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">Bagaimana saya menerima tiket setelah pembayaran?</dt>
                    <dd class="text-base text-gray-500">Setelah pembayaran berhasil, E-Tiket dengan QR Code unik akan otomatis dikirim ke email Anda dan juga akan tersedia di halaman "Pesanan Saya" di akun Anda.</dd>
                </div>
                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">Apakah saya bisa membatalkan tiket dan meminta refund?</dt>
                    <dd class="text-base text-gray-500">Kebijakan pembatalan dan refund bervariasi tergantung pada setiap event. Silakan periksa detail dan syarat ketentuan di halaman event masing-masing.</dd>
                </div>
                <div class="space-y-2">
                    <dt class="text-lg leading-6 font-medium text-gray-900">Bagaimana cara saya menjadi seorang event organizer di platform ini?</dt>
                    <dd class="text-base text-gray-500">Sangat mudah! Cukup klik tombol "Mulai Sekarang" di bawah ini, daftar sebagai pengguna, dan Anda akan mendapatkan akses ke dasbor organizer untuk mulai membuat event pertama Anda.</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bg-gray-800">
        <div class="max-w-4xl mx-auto text-center py-16 px-4 sm:px-6 sm:py-20 lg:px-8">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                <span class="block">Jadikan Event Anda Sukses</span>
                <span class="block">Daftar Sebagai Organizer Sekarang.</span>
            </h2>
            <p class="mt-4 text-lg leading-6 text-gray-300">Jangkau audiens yang lebih luas dan kelola tiket Anda dengan mudah bersama kami.</p>
            <a href="{{ route('register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 sm:w-auto">
                Mulai Sekarang
            </a>
        </div>
    </div>
</x-public-layout>
