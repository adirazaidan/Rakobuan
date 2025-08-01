<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product; 
use Illuminate\Http\Request;
use App\Models\Outlet;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::all();
        $selectedOutletId = $request->input('outlet_id');
        $searchTerm = $request->input('search');

        $query = Discount::query();

        if ($selectedOutletId) {
            $query->whereHas('product.category', function ($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }
        
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        $discounts = $query->with('product')->latest()->paginate(10);

        return view('admin.discounts.index', compact('discounts', 'outlets', 'selectedOutletId', 'searchTerm'));
    }

    public function create()
    {
        $products = Product::where('is_available', true)->get(); // Masih diperlukan jika ada fallback atau untuk debugging
        return view('admin.discounts.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id', // product_id sekarang dari hidden input
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');
        Discount::create($validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit(Discount $discount)
    {
        $products = Product::where('is_available', true)->get(); // Masih diperlukan jika ada fallback atau untuk debugging
        return view('admin.discounts.edit', compact('discount', 'products'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id', // product_id sekarang dari hidden input
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $discount->update($validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil dihapus.');
    }

    /**
     * Handle AJAX request to search for products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('query');
        $products = Product::where('name', 'like', '%' . $query . '%')
                            ->where('is_available', true) // Hanya tampilkan produk yang tersedia
                            ->select('id', 'name') // Hanya ambil id dan nama
                            ->limit(10) // Batasi jumlah hasil
                            ->get();

        return response()->json($products);
    }
}
