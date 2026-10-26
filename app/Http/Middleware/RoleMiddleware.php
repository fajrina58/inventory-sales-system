<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Memastikan user yang sedang login memiliki salah satu role yang diizinkan.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Akses Ditolak: Peran akun Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
