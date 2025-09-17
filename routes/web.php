<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/midtrans/notification', [PaymentController::class, 'notificationHandler'])->name('midtrans.notification');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/events/{event}/toggle-publish', [EventController::class, 'togglePublish'])->name('events.toggle-publish');
    Route::resource('events', EventController::class)->except(['show'])->middleware('role:organizer,admin');
    Route::get('/events/{event}/attendees', [EventController::class, 'attendees'])->name('events.attendees');
    Route::get('/events/{event}/scan', [EventController::class, 'scanner'])->name('events.scanner');

    Route::resource('events.tickets', TicketController::class)
        ->except(['show'])
        ->middleware('role:organizer,admin');

    Route::post('/bookings/{ticket}', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/cart', [BookingController::class, 'cart'])->name('cart.index');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/my-bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::delete('/cart/delete-selected', [BookingController::class, 'deleteSelected'])->name('cart.delete-selected');
    Route::post('/cart/proceed-to-payment', [BookingController::class, 'proceedToPayment'])->name('cart.proceed-to-payment');

    Route::get('/bookings/{booking}/pay', [PaymentController::class, 'pay'])->name('bookings.pay');
    Route::get('/bookings/{booking}/payment-success', [PaymentController::class, 'paymentSuccess'])->name('bookings.payment-success');
    Route::get('/transactions/{transaction}/pay', [PaymentController::class, 'payTransaction'])->name('transactions.pay');
    Route::get('/payment/pending', [PaymentController::class, 'paymentPending'])->name('payment.pending');
    Route::get('/payment/error', [PaymentController::class, 'paymentError'])->name('payment.error');

    Route::get('/organizer/sales-chart', [EventController::class, 'salesChartData'])->name('sales-chart');

    Route::get('/events/{event}/attendees/export', [EventController::class, 'exportAttendees'])->name('events.attendees.export');
});

Route::get('/admin/dashboard', function () {
    return 'Selamat datang, Admin!';
})->middleware(['auth', 'role:admin']);

Route::get('/events/{event}', [HomeController::class, 'show'])->name('events.show');

require __DIR__.'/auth.php';
