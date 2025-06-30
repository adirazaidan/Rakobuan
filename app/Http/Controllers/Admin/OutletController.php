<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::latest()->paginate(10);
        return view('admin.outlets.index', compact('outlets'));
    }

    public function create()
    {
        return view('admin.outlets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/outlets');
            $validated['image'] = basename($path);
        }

        Outlet::create($validated);

        return redirect()->route('admin.outlets.index')->with('success', 'Outlet berhasil ditambahkan.');
    }

    public function edit(Outlet $outlet)
    {
        return view('admin.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:outlets,name,' . $outlet->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($outlet->image) {
                Storage::delete('public/outlets/' . $outlet->image);
            }
            $path = $request->file('image')->store('public/outlets');
            $validated['image'] = basename($path);
        }

        $outlet->update($validated);

        return redirect()->route('admin.outlets.index')->with('success', 'Outlet berhasil diperbarui.');
    }

    public function destroy(Outlet $outlet)
    {
        if ($outlet->image) {
            Storage::delete('public/outlets/' . $outlet->image);
        }
        
        $outlet->delete();

        return redirect()->route('admin.outlets.index')->with('success', 'Outlet berhasil dihapus.');
    }
}