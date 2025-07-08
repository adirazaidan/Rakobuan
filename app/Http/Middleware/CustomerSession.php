<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('dining_table_id')) {
            return redirect()->route('customer.login.form')->with('error', 'Anda harus login terlebih dahulu.');
        }

        return $next($request);
    }
}