<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category; // <-- Import model Category
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Import Storage

class ProductController extends Controller
{
    public function index()
    {
        // Ambil produk beserta relasi kategori dan outlet-nya
        $products = Product::with('category.outlet')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        // Ambil semua kategori untuk ditampilkan di form dropdown
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

        // Handle boolean values from switches/checkboxes
        $validated['is_bestseller'] = $request->has('is_bestseller');
        $validated['is_available'] = $request->has('is_available');

        // Handle file upload
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
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }
            $path = $request->file('image')->store('public/products');
            $validated['image'] = basename($path);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Hapus gambar dari storage jika ada
        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Menu berhasil dihapus.');
    }
}