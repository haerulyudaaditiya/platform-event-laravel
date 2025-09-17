<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Tiket untuk: {{ $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Tambah Tiket Baru</h3>
                        <x-input-error :messages="$errors->all()" class="mb-4"/>
                        <form action="{{ route('events.tickets.store', $event) }}" method="POST">
                            @csrf
                            <div>
                                <x-input-label for="name" value="Nama Tiket (e.g. VIP)" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="price" value="Harga (Rp)" />
                                <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" :value="old('price')" required />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="quantity" value="Jumlah Tersedia" />
                                <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity')" required />
                            </div>
                            <div class="mt-4">
                                <x-primary-button>Simpan</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        @if (session('success'))
                            <x-alert type="success" :message="session('success')" />
                        @endif
                        @if (session('error'))
                            <x-alert type="error" :message="session('error')" />
                        @endif
                        <h3 class="text-lg font-semibold mb-4">Daftar Tiket</h3>
                        <div class="space-y-4">
                            @forelse($tickets as $ticket)
                                <div class="border p-4 rounded-lg flex justify-between items-center">
                                    <div>
                                        <p class="font-bold">{{ $ticket->name }}</p>
                                        <p class="text-sm text-gray-600">Rp {{ number_format($ticket->price) }} - {{ $ticket->quantity }} tersedia</p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('events.tickets.edit', [$event, $ticket]) }}" class="text-sm text-indigo-600 hover:underline">Edit</a>

                                        <button type="button" onclick="confirmDelete({{ $ticket->id }})" class="text-sm text-red-600 hover:underline">
                                            Hapus
                                        </button>

                                        <form id="delete-form-{{ $ticket->id }}" action="{{ route('events.tickets.destroy', [$event, $ticket]) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p>Belum ada tiket untuk event ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(ticketId) {
            Swal.fire({
                title: 'Anda Yakin?',
                text: "Tindakan ini tidak bisa dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + ticketId).submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
