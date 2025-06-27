<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsOperator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
        {
            // Jika user sudah login DAN rolenya adalah 'operator'
            if (auth()->check() && auth()->user()->role === 'operator') {
                return $next($request); // Lanjutkan request
            }

            // Jika tidak, tendang dengan error 403 (Akses Ditolak)
            abort(403, 'ANDA TIDAK PUNYA AKSES UNTUK HALAMAN INI.');
        }
}
