<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Platform Event') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
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

            @auth
            @if(auth()->user()->role === 'organizer')
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        // Dengarkan di channel privat milik organizer yang sedang login
                        window.Echo.private('organizer.{{ auth()->id() }}')
                            .listen('TicketSold', (e) => {
                                // Tampilkan notifikasi sederhana
                                alert(`Tiket Terjual! ${e.buyer_name} baru saja membeli tiket untuk event "${e.event_name}"`);

                                // Di aplikasi nyata, Anda bisa menggunakan library notifikasi
                                // yang lebih cantik daripada alert().
                            });
                    });
                </script>
            @endif
        @endauth
    </body>
</html>
