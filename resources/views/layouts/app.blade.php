<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Platform Event') }}</title>

        <link rel="icon" href="{{ asset('icon/icon.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @auth
            @if(auth()->user()->role === 'organizer')
                <div
                    x-data="{
                        show: false,
                        message: '',
                        eventName: '',
                        init() {
                            window.Echo.private('organizer.{{ auth()->id() }}')
                                .listen('TicketSold', (e) => {
                                    this.message = `${e.buyer_name} baru saja membeli tiket.`;
                                    this.eventName = e.event_name;
                                    this.show = true;
                                    setTimeout(() => this.show = false, 7000); // Sembunyikan setelah 7 detik
                                });
                        }
                    }"
                    x-show="show"
                    x-transition:enter="transform ease-out duration-300 transition"
                    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed top-5 right-5 w-full max-w-sm z-50 bg-white border border-gray-200 rounded-md p-4 shadow-lg"
                    style="display: none;"
                >
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900" x-text="`Tiket Terjual!`"></p>
                            <p class="text-sm text-gray-600" x-text="message"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="`Untuk event: ${eventName}`"></p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="show = false" type="button" class="-mx-1.5 -my-1.5 inline-flex bg-white text-gray-400 hover:text-gray-500 rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">Dismiss</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
