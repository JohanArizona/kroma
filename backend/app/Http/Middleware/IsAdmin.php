<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan rolenya admin
        if (auth('api')->check() && auth('api')->user()->role === 'admin') {
            return $next($request); 
        }

        // Kalau bukan admin error 403 (Forbidden)
        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Hanya Admin yang diizinkan melakukan aksi ini.'
        ], 403);
    }
}