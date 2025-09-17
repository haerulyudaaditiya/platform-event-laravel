<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Event: ') . $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <x-input-error :messages="$errors->all()" class="mb-4"/>

                    <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Detail Utama Event</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <x-input-label for="name" :value="__('Nama Event')" />
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $event->name)" required autofocus />
                                    </div>

                                    <div>
                                        <x-input-label for="category" :value="__('Kategori Event')" />
                                        <select name="category" id="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="Musik" {{ old('category', $event->category) == 'Musik' ? 'selected' : '' }}>Musik</option>
                                            <option value="Seminar" {{ old('category', $event->category) == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                                            <option value="Olahraga" {{ old('category', $event->category) == 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                                            <option value="Festival" {{ old('category', $event->category) == 'Festival' ? 'selected' : '' }}>Festival</option>
                                        </select>
                                    </div>

                                    <div>
                                        <x-input-label for="description" :value="__('Deskripsi')" />
                                        <textarea id="description" name="description" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description', $event->description) }}</textarea>
                                    </div>

                                    <div>
                                        <x-input-label for="image_file" :value="__('Ganti Gambar/Poster (Opsional)')" />
                                        <input id="image_file" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="image_file" />
                                        @if($event->image)
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500 mb-1">Gambar saat ini:</p>
                                                <img src="{{ Str::startsWith($event->image, 'http') ? $event->image : asset('storage/' . $event->image) }}" alt="Gambar event" class="w-48 h-auto rounded">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Waktu & Lokasi</h3>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="start_time" :value="__('Waktu Mulai')" />
                                        <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time" :value="old('start_time', \Carbon\Carbon::parse($event->start_time)->format('Y-m-d\TH:i'))" required />
                                    </div>
                                    <div>
                                        <x-input-label for="end_time" :value="__('Waktu Selesai')" />
                                        <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local" name="end_time" :value="old('end_time', \Carbon\Carbon::parse($event->end_time)->format('Y-m-d\TH:i'))" required />
                                    </div>
                                    <div>
                                        <x-input-label for="venue" :value="__('Nama Tempat (Venue)')" />
                                        <x-text-input id="venue" class="block mt-1 w-full" type="text" name="venue" :value="old('venue', $event->venue)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="location" :value="__('Alamat / Kota')" />
                                        <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $event->location)" required />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Pengaturan Lanjutan</h3>
                                 <div class="block mt-4">
                                    <label for="is_published" class="inline-flex items-center">
                                        <input id="is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_published" value="1" @checked(old('is_published', $event->is_published))>
                                        <span class="ms-2 text-sm text-gray-600">{{ __('Publikasikan Event Ini') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 pt-6 border-t">
                            <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Update Event') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
