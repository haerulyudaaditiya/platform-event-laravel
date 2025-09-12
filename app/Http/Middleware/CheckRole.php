<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah pengguna sudah login DAN perannya termasuk dalam $roles yang diizinkan
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            // Jika tidak, kembalikan error 403 (Forbidden)
            abort(403, 'UNAUTHORIZED ACTION.');
        }

        return $next($request);
    }
}
