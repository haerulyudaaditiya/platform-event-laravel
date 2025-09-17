<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
         // Menambahkan alias middleware untuk 'role'
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'NoCaptcha' => \Anhskohbo\NoCaptcha\Facades\NoCaptcha::class,
        ]);
         $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);
    })
    ->withProviders([
        App\Providers\AuthServiceProvider::class,
        Anhskohbo\NoCaptcha\NoCaptchaServiceProvider::class,
        App\Providers\ViewServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
