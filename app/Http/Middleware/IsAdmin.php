<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek 1: Apakah user sudah login?
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek 2: Apakah role-nya admin?
        // Pastikan di database kolomnya 'role' dan isinya 'admin' (huruf kecil)
        if (Auth::user()->role !== 'admin') {
            // Jika user biasa coba masuk admin, tendang ke home dengan pesan error
            return redirect('/home')->with('error', 'Anda tidak memiliki akses Admin!');
        }

        return $next($request);
    }
}