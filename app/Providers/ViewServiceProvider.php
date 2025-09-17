<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan data $cartCount ke semua view yang menggunakan layout 'layouts.public'
        View::composer('layouts.public', function ($view) {
            $cartCount = 0;
            if (Auth::check() && Auth::user()->role === 'user') {
                $cartCount = Auth::user()->bookings()->where('status', 'pending')->count();
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
