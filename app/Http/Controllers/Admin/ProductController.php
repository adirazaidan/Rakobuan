<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\Category; 
use Illuminate\Http\Request;
use App\Events\ProductStockUpdated;
use Illuminate\Support\Facades\Storage; 

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::all();
        $selectedOutletId = $request->input('outlet_id');

        $query = Product::query();

        if ($selectedOutletId) {
            $query->whereHas('category', function ($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }

        $products = $query->with('category.outlet')->latest()->paginate(10);

        return view('admin.products.index', compact('products', 'outlets', 'selectedOutletId'));
    }

    public function create()
    {
        $categories = Category::with('outlet')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $validated['is_bestseller'] = $request->has('is_bestseller');
        $validated['is_available'] = $request->has('is_available');
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/products');
            $validated['image'] = basename($path);
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::with('outlet')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $validated['is_bestseller'] = $request->has('is_bestseller');
        $validated['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }
            $path = $request->file('image')->store('public/products');
            $validated['image'] = basename($path);
        }

        $product->update($validated);
        
        ProductStockUpdated::dispatch(
            $product->id,
            $product->stock,
            $product->is_available
        );

        return redirect()->route('admin.products.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Menu berhasil dihapus.');
    }
}