@extends('layouts.admin')
@section('title', 'Manajemen Menu')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Menu</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Tambah Menu</a>
    </div>

    {{-- Filter Outlet, Kategori, Status, dan Pencarian --}}
    <div class="card mb-4 filter-card" >
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    {{-- Filter Outlet --}}
                    <div class="col-md-3">
                        <label for="outlet_id" class="form-label">Filter Outlet</label>
                        <select name="outlet_id" id="outlet_id" class="form-control">
                            <option value="">Semua Outlet</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ $selectedOutletId == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Filter Kategori</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div class="col-md-3">
                        <label for="status" class="form-label">Filter Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="available" {{ $selectedStatus == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="unavailable" {{ $selectedStatus == 'unavailable' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>

                    {{-- Kolom Pencarian --}}
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Menu</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Masukkan nama menu..." value="{{ request('search') }}">
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mt-2 w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- End Filter --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding:1.5rem">
        {{-- Indikator Jumlah Data --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted mb-0">
                Menampilkan {{ $products->firstItem() }} sampai {{ $products->lastItem() }} dari total {{ $products->total() }} data.
                @if(request('outlet_id'))
                    (Filter Outlet: {{ $outlets->find(request('outlet_id'))->name ?? 'Tidak Dikenal' }})
                @endif
                @if(request('category_id'))
                    (Filter Kategori: {{ $categories->find(request('category_id'))->name ?? 'Tidak Dikenal' }})
                @endif
                @if(request('status'))
                    (Filter Status: {{ request('status') == 'available' ? 'Tersedia' : 'Habis' }})
                @endif
                @if(request('search'))
                    (Pencarian: "{{ request('search') }}")
                @endif
            </p>
        </div>
        {{-- End Indikator Jumlah Data --}}

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
                    <tr id="product-row-{{ $product->id }}">
                        <td data-label="Gambar">
                            <img src="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/80' }}" alt="{{ $product->name }}" width="80" style="border-radius: 8px;">
                        </td>
                        <td data-label="Nama">{{ $product->name }}</td>
                        <td data-label="Kategori">{{ $product->category->name }}</td>
                        <td data-label="Outlet">{{ $product->category->outlet->name }}</td>
                        <td data-label="Harga">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td data-label="Stok" class="product-stock">{{ $product->stock }}</td>
                        <td data-label="Status" class="product-status">
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
        <div class="pagination-container">
            {{-- Tombol Sebelumnya --}}
            @if ($products->onFirstPage())
                <li class="page-item disabled">
                    <button class="page-link" disabled>Sebelumnya</button>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $products->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev">Sebelumnya</a>
                </li>
            @endif

            {{ $products->appends(request()->query())->links('vendor.pagination.custom-numbered') }}

            {{-- Tombol Berikutnya --}}
            @if ($products->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $products->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next">Berikutnya</a>
                </li>
            @else
                <li class="page-item disabled">
                    <button class="page-link" disabled>Berikutnya</button>
                </li>
            @endif
        </div>
    </div>
</div>
@endsection
