<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Event: ') . $event->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-input-error :messages="$errors->all()" class="mb-4"/>

                    <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nama Event')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $event->name)" required autofocus />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="image" :value="__('Ganti Gambar Event (Opsional)')" />
                            <input id="image" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="file" name="image" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $event->description) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <x-input-label for="start_time" :value="__('Waktu Mulai')" />
                                <x-text-input id="start_time" class="block mt-1 w-full" type="datetime-local" name="start_time" :value="old('start_time', \Carbon\Carbon::parse($event->start_time)->format('Y-m-d\TH:i'))" required />
                            </div>
                            <div>
                                <x-input-label for="end_time" :value="__('Waktu Selesai')" />
                                <x-text-input id="end_time" class="block mt-1 w-full" type="datetime-local" name="end_time" :value="old('end_time', \Carbon\Carbon::parse($event->end_time)->format('Y-m-d\TH:i'))" required />
                            </div>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="venue" :value="__('Nama Tempat (Venue)')" />
                            <x-text-input id="venue" class="block mt-1 w-full" type="text" name="venue" :value="old('venue', $event->venue)" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="location" :value="__('Lokasi (Alamat / Link Gmaps)')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $event->location)" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="category" :value="__('Kategori Event')" />
                            <select name="category" id="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Musik" {{ (old('category', $event->category ?? '')) == 'Musik' ? 'selected' : '' }}>Musik</option>
                                <option value="Seminar" {{ (old('category', $event->category ?? '')) == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                                <option value="Olahraga" {{ (old('category', $event->category ?? '')) == 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                                <option value="Festival" {{ (old('category', $event->category ?? '')) == 'Festival' ? 'selected' : '' }}>Festival</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end mt-4">
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
