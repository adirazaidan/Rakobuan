<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Outlet;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan halaman daftar menu untuk pelanggan.
     */
    public function index(Outlet $outlet = null)
    {
        // Jika tidak ada outlet yang dipilih di URL (misal: akses /menu pertama kali),
        // ambil outlet pertama dan redirect ke URL dengan ID outlet tersebut.
        if (is_null($outlet)) {
            $firstOutlet = Outlet::first();
            // Jika tidak ada outlet sama sekali di database
            if (!$firstOutlet) {
                abort(404, 'Tidak ada outlet yang terdaftar.');
            }
            return redirect()->route('customer.menu.index', $firstOutlet);
        }

        // Jika outlet dipilih, filter kategori dan produk berdasarkan outlet tersebut
        $categories = Category::where('outlet_id', $outlet->id)->get();
        $products = Product::whereHas('category', function ($query) use ($outlet) {
            $query->where('outlet_id', $outlet->id);
        })
        ->with('activeDiscount')
        ->get();

        // Kirim data outlet yang sedang aktif ke view untuk menandai sidebar
        $currentOutlet = $outlet;

        return view('customer.menu.index', compact('categories', 'products', 'currentOutlet'));
    }
}