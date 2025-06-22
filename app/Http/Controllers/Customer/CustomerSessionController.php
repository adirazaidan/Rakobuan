<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerSessionController extends Controller
{
    /**
     * Menampilkan halaman/form login untuk pelanggan.
     */
    public function create()
    {
        // Cek jika pelanggan sudah memiliki sesi, langsung arahkan ke menu
        if (session()->has('customer_name')) {
            return redirect()->route('customer.menu.index');
        }
        return view('customer.login');
    }

    /**
     * Memproses form login, memvalidasi, dan menyimpan data ke session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'table_number' => 'required|string|max:20',
        ]);

        session([
            'customer_name' => $validated['customer_name'],
            'table_number' => $validated['table_number'],
        ]);

        return redirect()->route('customer.menu.index');
    }

    /**
     * Menghapus sesi pelanggan (logout).
     */
    public function destroy(Request $request)
    {
        $request->session()->flush(); // Menghapus semua data dari sesi

        return redirect()->route('customer.login.form');
    }
}