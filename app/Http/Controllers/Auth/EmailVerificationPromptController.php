<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Import Auth
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Ganti logika if-then-else yang lama
        if ($request->user()->hasVerifiedEmail()) {
            // Jika sudah terverifikasi, arahkan berdasarkan peran
            if (Auth::user()->role === 'organizer') {
                return redirect()->intended(route('events.index'));
            }
            return redirect()->intended(route('home'));
        }

        // Jika belum terverifikasi, tampilkan halaman verifikasi
        return view('auth.verify-email');
    }
}
