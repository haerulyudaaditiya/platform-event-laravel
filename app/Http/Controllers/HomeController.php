<?php

namespace App\Http\Controllers;

use App\Models\Event; // Import model Event
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $events = Event::where('is_published', true) // Hanya ambil event yang sudah di-publish
                         ->where('start_time', '>', now()) // Hanya ambil event yang akan datang
                         ->orderBy('start_time', 'asc') // Urutkan dari yang paling dekat tanggalnya
                         ->paginate(9); // Ambil 9 event per halaman

        return view('welcome', compact('events'));
    }

    public function show(Event $event)
    {
        // Pastikan event yang diakses sudah di-publish
        if (! $event->is_published) {
            abort(404); // Tampilkan halaman Not Found jika event belum di-publish
        }

        return view('events.show', compact('event'));
    }
}
