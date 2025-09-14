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
    <body class="font-sans antialiased bg-gray-100">

        @if (session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if (session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif
        @if (session('warning'))
            <x-alert type="warning" :message="session('warning')" />
        @endif

        <div class="min-h-screen">
            <nav class="bg-gray-800 border-b border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="{{ (auth()->check() && auth()->user()->role === 'organizer') ? route('events.index') : route('home') }}">
                                    <x-application-logo class="block h-9 w-auto fill-current text-white" />
                                </a>
                            </div>

                            @auth
                                @if(auth()->user()->role === 'user')
                                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                                            {{ __('Event') }}
                                        </x-nav-link>
                                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.index')">
                                            {{ __('Keranjang') }}
                                        </x-nav-link>
                                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.index')">
                                            {{ __('Riwayat') }}
                                        </x-nav-link>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <div class="flex items-center">
                            @auth
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                            <div>{{ Auth::user()->name }}</div>
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            </div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            {{ __('Profil') }}
                                        </x-dropdown-link>
                                        @if(auth()->user()->role === 'organizer')
                                            <x-dropdown-link :href="route('events.index')">
                                                {{ __('Manajemen Event') }}
                                            </x-dropdown-link>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                                {{ __('Log Out') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                {{ $slot }}
            </main>
        </div>
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="col-span-2 md:col-span-1">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2">
                           <x-application-logo class="block h-9 w-auto fill-current text-white" />
                           <span class="font-bold text-xl">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                        <p class="mt-4 text-gray-400 text-sm">Platform terpercaya untuk menemukan dan membuat event tak terlupakan.</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold tracking-wider uppercase">Navigasi</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-base text-gray-400 hover:text-white">Tentang Kami</a></li>
                            <li><a href="#" class="text-base text-gray-400 hover:text-white">Kontak</a></li>
                            <li><a href="#" class="text-base text-gray-400 hover:text-white">Bantuan</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold tracking-wider uppercase">Legal</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-base text-gray-400 hover:text-white">Kebijakan Privasi</a></li>
                            <li><a href="#" class="text-base text-gray-400 hover:text-white">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-base text-gray-400 md:order-1">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                    <div class="flex space-x-6 md:order-2 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808s-.012 2.784-.06 3.808c-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808s.012-2.784.06-3.808c.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.05 4.51c.636-.247 1.363-.416 2.427-.465C9.53 4.013 9.884 4 12.315 4h.001zM12 6.845a5.155 5.155 0 100 10.31 5.155 5.155 0 000-10.31zm0 8.465a3.31 3.31 0 110-6.62 3.31 3.31 0 010 6.62zm5.83-8.815a1.24 1.24 0 100-2.48 1.24 1.24 0 000 2.48z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
        @stack('scripts')
    </body>
</html>
