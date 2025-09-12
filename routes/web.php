<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events/{event}', [HomeController::class, 'show'])->name('events.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/events/{event}/toggle-publish', [EventController::class, 'togglePublish'])->name('events.toggle-publish');
    Route::resource('events', EventController::class)->except(['show'])->middleware('role:organizer,admin');
    Route::get('/events/{event}/tickets', [TicketController::class, 'index'])->name('tickets.index')->middleware('role:organizer,admin');
    Route::post('/events/{event}/tickets', [TicketController::class, 'store'])->name('tickets.store')->middleware('role:organizer,admin');
});

Route::get('/admin/dashboard', function () {
    return 'Selamat datang, Admin!';
})->middleware(['auth', 'role:admin']);

require __DIR__.'/auth.php';
