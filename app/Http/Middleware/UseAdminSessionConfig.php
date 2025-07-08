<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UseAdminSessionConfig
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah permintaan ini untuk area admin
        if ($request->is('admin') || $request->is('admin/*') || $request->is('broadcasting/auth')) {
            
            // 1. Ubah konfigurasi sesi untuk menggunakan cookie admin
            config(['session.cookie' => config('session.admin.cookie')]);

            // 2. (KUNCI UTAMA) Ubah guard autentikasi default menjadi 'admin'
            // Ini akan memberitahu StartSession untuk menggunakan aturan admin.
            Auth::setDefaultDriver('admin');
        }
        
        // Lanjutkan permintaan setelah semua konfigurasi diatur
        return $next($request);
    }
}