<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Event Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <x-input-error :messages="$errors->all()" class="mb-4"/>

                    <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Detail Utama Event</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <x-input-label for="name" :value="__('Nama Event')" />
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                    </div>

                                    <div>
                                        <x-input-label for="category" :value="__('Kategori Event')" />
                                        <select name="category" id="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="Musik">Musik</option>
                                            <option value="Seminar">Seminar</option>
                                            <option value="Olahraga">Olahraga</option>
                                            <option value="Festival">Festival</option>
                                        </select>
                                    </div>

                                    <div>
                                        <x-input-label for="description" :value="__('Deskripsi')" />
                                        <textarea id="description" name="description" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                                    </div>

                                    <div>
                                        <x-input-label for="image_file" :value="__('Gambar/Poster Event')" />
                                        <input id="image_file" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="file" name="image_file" />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Waktu & Lokasi</h3>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="start_time" :value="__('Waktu Mulai')" />
                                        <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time" :value="old('start_time')" required />
                                    </div>
                                    <div>
                                        <x-input-label for="end_time" :value="__('Waktu Selesai')" />
                                        <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local" name="end_time" :value="old('end_time')" required />
                                    </div>
                                    <div>
                                        <x-input-label for="venue" :value="__('Nama Tempat (Venue)')" />
                                        <x-text-input id="venue" class="block mt-1 w-full" type="text" name="venue" :value="old('venue')" required />
                                    </div>
                                    <div>
                                        <x-input-label for="location" :value="__('Alamat / Kota')" />
                                        <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" required />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Tiket & Publikasi</h3>
                                <div class="mt-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="ticket_price" :value="__('Harga Tiket Dasar (Rp)')" />
                                            <x-text-input id="ticket_price" class="block mt-1 w-full" type="number" name="ticket_price" :value="old('ticket_price')" placeholder="Contoh: 50000" />
                                            <p class="mt-1 text-xs text-gray-500">Kosongkan jika event gratis atau akan menambah jenis tiket lain nanti.</p>
                                        </div>
                                        <div>
                                            <x-input-label for="ticket_quantity" :value="__('Jumlah Tiket Tersedia')" />
                                            <x-text-input id="ticket_quantity" class="block mt-1 w-full" type="number" name="ticket_quantity" :value="old('ticket_quantity')" placeholder="Contoh: 100" />
                                        </div>
                                    </div>
                                    <div class="block mt-4">
                                        <label for="is_published" class="inline-flex items-center">
                                            <input id="is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_published" value="1">
                                            <span class="ms-2 text-sm text-gray-600">{{ __('Langsung Publikasikan Event') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-6 border-t">
                            <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Event') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
