<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Illuminate\Http\Request;

class CallController extends Controller
{
    /**
     * Menyimpan panggilan baru dari pelanggan ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // Pastikan sesi pelanggan ada
        if (!session()->has('customer_name')) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 401);
        }

        Call::create([
            'customer_name' => session('customer_name'),
            'table_number' => session('table_number'),
            'notes' => $request->notes,
            'status' => 'pending', // Status awal selalu pending
        ]);

        return response()->json(['message' => 'Panggilan telah terkirim! Pelayan akan segera datang.']);
    }
}