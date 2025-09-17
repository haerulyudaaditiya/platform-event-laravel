<div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transform hover:scale-105 transition-transform duration-300">
    <a href="{{ route('events.show', $event) }}">
        <img class="w-full h-48 object-cover" src="{{ $event->image ? asset('storage/' . $event->image) : asset('images/placeholder.jpg') }}" alt="{{ $event->name }}">
    </a>

    <div class="p-6 flex flex-col flex-grow">
        <p class="text-sm font-semibold text-gray-600 mb-1">{{ $event->category }}</p>

        <p class="text-xs text-gray-500 font-semibold uppercase">{{ $event->organizer->name }}</p>
        <h3 class="font-bold text-xl mt-2 mb-3 text-gray-900 flex-grow">{{ $event->name }}</h3>

        <div class="space-y-2 text-sm text-gray-600 mb-4">
            <p class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @php
                    $startTime = \Carbon\Carbon::parse($event->start_time);
                    $endTime = \Carbon\Carbon::parse($event->end_time);
                @endphp

                {{ $startTime->format('d M Y') }}
                @if (!$startTime->isSameDay($endTime))
                    - {{ $endTime->format('d M Y') }}
                @endif
            </p>
            <p class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $event->venue }}
            </p>
        </div>

        <div class="mt-auto">
            @if($event->tickets_min_price !== null)
                <p class="text-lg font-bold text-gray-800">
                    Mulai dari <span class="text-indigo-600">Rp {{ number_format($event->tickets_min_price) }}</span>
                </p>
            @else
                <p class="text-lg font-semibold text-yellow-600">Tiket Segera Hadir</p>
            @endif
        </div>
    </div>

    <a href="{{ route('events.show', $event) }}" class="block bg-gray-800 text-white text-center font-bold py-3 uppercase tracking-wider hover:bg-gray-700">
        Lihat Detail
    </a>
</div>
