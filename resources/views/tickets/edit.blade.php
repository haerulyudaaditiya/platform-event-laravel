<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Tiket: {{ $ticket->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('events.tickets.update', [$event, $ticket]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" value="Nama Tiket (e.g. VIP)" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $ticket->name)" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="price" value="Harga (Rp)" />
                            <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" :value="old('price', $ticket->price)" required />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="quantity" value="Jumlah Tersedia" />
                            <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity', $ticket->quantity)" required />
                        </div>
                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('events.tickets.index', $event) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <x-primary-button>Update Tiket</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
