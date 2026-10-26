<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Memastikan hanya user ber-role 'admin' yang dapat mengakses rute terkait.
     * Mengembalikan status HTTP 403 jika user adalah staff atau tidak berwenang.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Akses Ditolak: Fitur ini hanya dapat diakses oleh Administrator.');
        }

        return $next($request);
    }
}
