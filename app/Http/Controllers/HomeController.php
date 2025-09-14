<?php

namespace App\Http\Controllers;

use App\Models\Event; // Import model Event
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Mulai query builder
        $query = Event::where('is_published', true)
                    ->where('start_time', '>', now())
                    ->withMin('tickets', 'price');

        // 1. Filter berdasarkan Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // 2. Filter berdasarkan Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Eksekusi query
        $events = $query->latest()->paginate(6)->withQueryString();

        // Ambil daftar kategori unik untuk ditampilkan di menu
        $categories = Event::where('is_published', true)
                            ->where('start_time', '>', now())
                            ->distinct()
                            ->pluck('category');

        return view('welcome', compact('events', 'categories'));
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
