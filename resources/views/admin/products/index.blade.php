@extends('layouts.admin')
@section('title', 'Manajemen Menu')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Menu</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Tambah Menu</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding:1.5rem">
        <table class="table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Outlet</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td data-label="Gambar">
                            <img src="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/80' }}" alt="{{ $product->name }}" width="80" style="border-radius: 8px;">
                        </td>
                        <td data-label="Nama">{{ $product->name }}</td>
                        <td data-label="Kategori">{{ $product->category->name }}</td>
                        <td data-label="Outlet">{{ $product->category->outlet->name }}</td>
                        <td data-label="Harga">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td data-label="Stok">{{ $product->stock }}</td>
                        <td data-label="Status">
                            @if($product->is_available)
                                <span style="color: green;">Tersedia</span>
                            @else
                                <span style="color: red;">Habis</span>
                            @endif
                        </td>
                        <td data-label="Aksi">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus menu ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data menu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection