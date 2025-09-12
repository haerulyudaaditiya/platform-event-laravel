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
                        <form action="{{ route('tickets.store', $event) }}" method="POST">
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
                         <x-auth-session-status class="mb-4" :status="session('success')" />
                        <h3 class="text-lg font-semibold mb-4">Daftar Tiket</h3>
                        <div class="space-y-4">
                            @forelse($tickets as $ticket)
                                <div class="border p-4 rounded-lg flex justify-between items-center">
                                    <div>
                                        <p class="font-bold">{{ $ticket->name }}</p>
                                        <p class="text-sm text-gray-600">Rp {{ number_format($ticket->price) }} - {{ $ticket->quantity }} tersedia</p>
                                    </div>
                                    <div>
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
</x-app-layout>
