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

        $query = Discount::query();

        if ($selectedOutletId) {
            $query->whereHas('product.category', function ($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }

        $discounts = $query->with('product')->latest()->paginate(10);

        return view('admin.discounts.index', compact('discounts', 'outlets', 'selectedOutletId'));
    }

    public function create()
    {
        $products = Product::where('is_available', true)->get();
        return view('admin.discounts.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');
        Discount::create($validated);

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit(Discount $discount)
    {
        $products = Product::where('is_available', true)->get();
        return view('admin.discounts.edit', compact('discount', 'products'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
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
}