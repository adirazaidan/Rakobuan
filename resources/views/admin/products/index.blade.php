@extends('layouts.admin')
@section('title', 'Manajemen Menu')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Daftar Menu</h1>
        <div class="mt-3">
            <a href="{{ route('admin.products.create') }}" class="custom-action-btn-tambah custom-action-btn-tambah-primary">
                <i class="fas fa-plus"></i> <span>Tambah Menu</span>
            </a>
        </div>
    </div>

    {{-- Filter Outlet, Kategori, Status, dan Pencarian --}}
    <div class="card mb-4 filter-card card-padding">
            <form action="{{ route('admin.products.index') }}" method="GET" class="filter-form">
                <div class="filter-container">
                    {{-- Filter Outlet --}}
                    <div class="form-group form-group-flex">
                        <label for="outlet_id">Filter Outlet</label>
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
                    <div class="form-group form-group-flex">
                        <label for="category_id">Filter Kategori</label>
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
                    <div class="form-group form-group-flex">
                        <label for="status">Filter Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="available" {{ $selectedStatus == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="unavailable" {{ $selectedStatus == 'unavailable' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>
                    {{-- Kolom Pencarian --}}
                    <div class="form-group form-group-flex">
                        <label for="search">Cari Menu</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Masukkan nama menu..." value="{{ request('search') }}">
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="form-group filter-buttons">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mt-2 w-100">Reset</a>
                    </div>
                </div>
            </form>
    </div>
    {{-- End Filter --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-padding">
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
                            <img src="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/80' }}" alt="{{ $product->name }}" width="80" class="border-radius-8">
                        </td>
                        <td data-label="Nama">{{ $product->name }}</td>
                        <td data-label="Kategori">{{ $product->category->name }}</td>
                        <td data-label="Outlet">{{ $product->category->outlet->name }}</td>
                        <td data-label="Harga">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td data-label="Stok" class="product-stock">{{ $product->stock }}</td>
                        <td data-label="Status" class="product-status">
                            @if($product->is_available)
                                <span class="color-green">Tersedia</span>
                            @else
                                <span class="color-red">Habis</span>
                            @endif
                        </td>
                        <td data-label="Aksi">
                            <div class="d-flex justify-content-end align-items-center gap-2 actions-wrapper">
                                <a href="{{ route('admin.products.edit', $product) }}" class="custom-action-btn custom-action-btn-warning" title="Sunting Menu">
                                    <i class="fas fa-edit"></i> <span>Sunting</span>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="custom-action-btn custom-action-btn-danger" title="Hapus Menu">
                                        <i class="fas fa-trash"></i> <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
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
            <ul class="pagination" role="navigation">
                {{-- Previous Button --}}
                @if ($products->onFirstPage())
                    <li class="page-item disabled">
                        <button class="page-link" disabled><i class="fas fa-chevron-left"></i></button>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $products->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
                    </li>
                @endif

                {{-- Numbered Links (Limited) --}}
                @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if ($page == $products->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @elseif ($page == 1 || $page == $products->lastPage() || ($page >= $products->currentPage() - 1 && $page <= $products->currentPage() + 1))
                        <li class="page-item">
                            <a class="page-link" href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $page }}</a>
                        </li>
                    @elseif ($page == $products->currentPage() - 2 || $page == $products->currentPage() + 2)
                        <li class="page-item disabled d-none d-md-block">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if ($products->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $products->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next"><i class="fas fa-chevron-right"></i></a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <button class="page-link" disabled><i class="fas fa-chevron-right"></i></button>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection
