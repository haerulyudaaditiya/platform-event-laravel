<?php

// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use App\Models\Event; // <-- Tambahkan ini
use App\Policies\EventPolicy; // <-- Tambahkan ini
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Event::class => EventPolicy::class, // <-- Tambahkan baris ini
    ];

    // ...
}
