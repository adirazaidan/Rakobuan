@extends('layouts.admin')
@section('title', 'Manajemen Kategori')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Kategori</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
    </div>

    {{-- Form Filter --}}
    <div class="card mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('admin.categories.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    
                    {{-- Filter Outlet --}}
                    <div class="col-md-4">
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

                    {{-- Kolom Pencarian Kategori --}}
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Kategori</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Masukkan nama kategori..." value="{{ request('search') }}">
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Reset</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
    {{-- End Form Filter --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding:1.5rem">
        {{-- Indikator Jumlah Data --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted mb-0">
                Menampilkan {{ $categories->firstItem() }} sampai {{ $categories->lastItem() }} dari total {{ $categories->total() }} data.
                @if(request('outlet_id'))
                    (Filter Outlet: {{ $outlets->find(request('outlet_id'))->name ?? 'Tidak Dikenal' }})
                @endif
                @if(request('search'))
                    (Pencarian: "{{ request('search') }}")
                @endif
            </p>
        </div>
        {{-- End Indikator Jumlah Data --}}

        <div class="table-responsive-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kategori</th>
                        <th>Outlet</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $index => $category)
                        <tr>
                            <td data-label="Nomor">{{ $index + $categories->firstItem() }}</td>
                            <td data-label="Nama Kategori">{{ $category->name }}</td>
                            <td data-label="Nama Outlet">{{ $category->outlet->name }}</td>
                            <td data-label="Aksi">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">Sunting</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua menu di dalamnya juga akan terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination-container">
            {{-- Tombol Sebelumnya --}}
            @if ($categories->onFirstPage())
                <li class="page-item disabled">
                    <button class="page-link" disabled>Sebelumnya</button>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $categories->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev">Sebelumnya</a>
                </li>
            @endif

            {{ $categories->appends(request()->query())->links('vendor.pagination.custom-numbered') }}

            {{-- Tombol Berikutnya --}}
            @if ($categories->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $categories->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next">Berikutnya</a>
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
