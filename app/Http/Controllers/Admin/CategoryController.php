<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Outlet; 
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::all();
        $selectedOutletId = $request->input('outlet_id');
        $query = Category::query();
        if ($selectedOutletId) {
            $query->where('outlet_id', $selectedOutletId);
        }

        $categories = $query->with('outlet')->latest()->paginate(10);
        return view('admin.categories.index', compact('categories', 'outlets', 'selectedOutletId'));
}

    public function create()
    {
        $outlets = Outlet::all();
        return view('admin.categories.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'outlet_id' => 'required|exists:outlets,id',
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
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}