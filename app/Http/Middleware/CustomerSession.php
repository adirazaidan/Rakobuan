<?php

namespace App\Http\Middleware; 

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerSession // <-- PASTIKAN NAMA CLASS INI BENAR
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('customer_name') || !session()->has('table_number')) {
            // Jika sesi tidak ada, redirect ke halaman login
            return redirect()->route('customer.login.form')->with('error', 'Silakan masukkan nama dan nomor meja Anda terlebih dahulu.');
        }

        return $next($request);
    }
}