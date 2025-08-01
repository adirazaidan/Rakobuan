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
        $query = Product::query()->with('category.outlet');

        // Filter berdasarkan outlet_id
        if ($request->filled('outlet_id')) {
            $query->whereHas('category.outlet', function ($q) use ($request) {
                $q->where('outlets.id', $request->outlet_id);
            });
        }
        
        // Filter berdasarkan category_id
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter berdasarkan status ketersediaan (is_available)
        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('is_available', true);
            } elseif ($request->status === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // Filter berdasarkan nama menu (search)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Ambil semua outlet dan kategori untuk filter dropdown
        $outlets = Outlet::all();
        $categories = Category::all();

        // Simpan ID yang dipilih untuk dropdown
        $selectedOutletId = $request->outlet_id;
        $selectedCategoryId = $request->category_id;
        $selectedStatus = $request->status;

        // Paginate hasilnya
        $products = $query->latest()->paginate(10);

        return view('admin.products.index', compact('products', 'outlets', 'categories', 'selectedOutletId', 'selectedCategoryId', 'selectedStatus'));
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