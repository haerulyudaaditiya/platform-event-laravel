@if(auth()->user()->role === 'organizer')

    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profil') }}
            </h2>
        </x-slot>

        @include('profile.partials.profile-content')
    </x-app-layout>

@else

    <x-public-layout>
        @include('profile.partials.profile-content')
    </x-public-layout>

@endif
