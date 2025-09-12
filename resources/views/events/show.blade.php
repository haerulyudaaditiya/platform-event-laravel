<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $event->name }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen">
            <nav class="bg-white border-b border-gray-100 shadow-sm">
               <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('home') }}">
                                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h1 class="text-3xl font-bold mb-2">{{ $event->name }}</h1>
                            <p class="text-lg text-gray-500 mb-6">Diselenggarakan oleh: {{ $event->organizer->name }}</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-base text-gray-700 mb-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <strong class="block text-gray-800">📍 Lokasi & Tempat</strong>
                                    <p>{{ $event->venue }}</p>
                                    <p class="text-sm">{{ $event->location }}</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <strong class="block text-gray-800">📅 Waktu Pelaksanaan</strong>
                                    <p>{{ \Carbon\Carbon::parse($event->start_time)->format('d F Y') }}</p>
                                    <p class="text-sm">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }} WIB</p>
                                </div>
                            </div>

                            <hr class="my-6">

                            <h2 class="text-xl font-semibold mb-3">Deskripsi Event</h2>
                            <div class="prose max-w-none text-gray-600 mb-6">
                                {!! nl2br(e($event->description)) !!}
                            </div>

                            <hr class="my-6">

                            <h2 class="text-xl font-semibold mb-3">Tiket Tersedia</h2>
                            <div class="space-y-4">
                                @forelse ($event->tickets as $ticket)
                                    <div class="border border-gray-200 p-4 rounded-lg flex justify-between items-center shadow-sm">
                                        <div>
                                            <p class="font-bold text-lg text-gray-800">{{ $ticket->name }}</p>
                                            <p class="text-gray-600">Rp {{ number_format($ticket->price) }}</p>
                                        </div>
                                        <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                            Beli
                                        </a>
                                    </div>
                                @empty
                                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                                        <p class="text-yellow-800">Tiket untuk event ini belum tersedia.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-8 pt-6 border-t">
                               <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    &larr; Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
