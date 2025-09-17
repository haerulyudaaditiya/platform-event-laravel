<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Total Event</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalEvents }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Total Tiket Terjual</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $ticketsSold }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h4 class="text-sm font-medium text-gray-500">Total Pendapatan</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">Rp {{ number_format($totalRevenue) }}</p>
                </div>
            </div>

            <div class="mb-6 bg-white p-6 rounded-lg shadow-sm">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Grafik Pendapatan (7 Hari Terakhir)</h4>
                <div class="relative h-80">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                        <h3 class="text-xl font-bold mb-4 md:mb-0">Daftar Event Anda</h3>
                        <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Buat Event Baru
                        </a>
                    </div>

                    <form action="{{ route('events.index') }}" method="GET" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search" class="sr-only">Cari</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="Cari nama event...">
                            </div>
                            <div>
                                <label for="status" class="sr-only">Status</label>
                                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                </select>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Filter</button>
                            </div>
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="mb-4">
                            <x-alert type="success" :message="session('success')" />
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4">
                            <x-alert type="error" :message="session('error')" />
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Event</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta Terbayar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($events as $event)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $event->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $event->venue }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 text-center">
                                            {{ $event->bookings_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($event->start_time)->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                           <form action="{{ route('events.toggle-publish', $event) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $event->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ $event->is_published ? 'Published' : 'Draft' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('events.attendees', $event) }}" class="text-green-600 hover:text-green-900">Peserta</a>
                                            <a href="{{ route('events.tickets.index', $event) }}" class="text-blue-600 hover:text-blue-900 ml-4">Tiket</a>
                                            <a href="{{ route('events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>

                                            <form id="delete-form-{{ $event->id }}" action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $event->id }})" class="text-red-600 hover:text-red-900 ml-4">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada event yang cocok dengan filter Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Panggil endpoint dari route web
            fetch('{{ route("sales-chart") }}') // <-- Ubah route di sini
            .then(response => response.json())
            .then(apiData => {
                const ctx = document.getElementById('salesChart').getContext('2d');
                const salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: apiData.labels,
                        datasets: [{
                            label: 'Pendapatan',
                            data: apiData.data,
                            backgroundColor: 'rgba(79, 70, 229, 0.2)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true, // <-- Tambahkan ini
                        maintainAspectRatio: false, // <-- Tambahkan ini
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    // Format angka menjadi 'Rp xxx'
                                    callback: function(value, index, values) {
                                        return 'Rp ' + new Intl.NumberFormat().format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
        });
    </script>
    <script>
        function confirmDelete(eventId) {
            Swal.fire({
                title: 'Anda Yakin?',
                text: "Menghapus event ini juga akan menghapus semua data pesanan terkait secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Saja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, temukan form yang sesuai dan submit
                    document.getElementById('delete-form-' + eventId).submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
