<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Outlet; // <-- Import model Outlet
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Ambil kategori beserta relasi outlet-nya untuk ditampilkan
        $categories = Category::with('outlet')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        // Ambil semua outlet untuk ditampilkan di form dropdown
        $outlets = Outlet::all();
        return view('admin.categories.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'outlet_id' => 'required|exists:outlets,id', // Validasi bahwa outlet_id ada di tabel outlets
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $outlets = Outlet::all();
        return view('admin.categories.edit', compact('category', 'outlets'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'outlet_id' => 'required|exists:outlets,id',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Karena kita sudah set 'onDelete('cascade')' pada migrasi tabel products,
        // semua menu yang terkait dengan kategori ini akan ikut terhapus otomatis.
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}