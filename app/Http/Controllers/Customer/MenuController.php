<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan halaman daftar menu untuk pelanggan.
     */
    public function index()
    {
        // Ambil semua kategori untuk ditampilkan sebagai filter
        $categories = Category::with('outlet')->get();

        // Ambil semua produk yang statusnya 'tersedia'
        $products = Product::where('is_available', true)->with('category.outlet')->latest()->get();
        
        // Kirim data ke view
        return view('customer.menu.index', compact('categories', 'products'));
    }
}