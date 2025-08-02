@extends('layouts.admin')
@section('title', 'Manajemen Diskon')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Diskon</h1>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">Tambah Diskon</a>
    </div>

    {{-- Filter Outlet dan Pencarian Diskon (Hardcoded) --}}
    <div class="card mb-4 filter-card" style="padding: 1.5rem;">
        <form action="{{ route('admin.discounts.index') }}" method="GET" class="filter-form">
            <div class="filter-container">
                {{-- Filter Outlet --}}
                <div class="form-group" style="flex: 1;">
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

                {{-- Kolom Pencarian Diskon --}}
                <div class="form-group" style="flex: 1;">
                    <label for="search">Cari Diskon</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Masukkan nama diskon..." value="{{ request('search') }}">
                </div>

                {{-- Tombol Filter --}}
                <div class="form-group filter-buttons">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary w-100 mt-2">Reset</a>
                </div>
            </div>
        </form>
    </div>
    {{-- End Filter --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding:1.5rem">
        {{-- Indikator Jumlah Data --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="text-muted mb-0">
                Menampilkan {{ $discounts->firstItem() }} sampai {{ $discounts->lastItem() }} dari total {{ $discounts->total() }} data.
                @if(request('outlet_id'))
                    (Filter Outlet: {{ $outlets->find(request('outlet_id'))->name ?? 'Tidak Dikenal' }})
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
                    <th>Nama Diskon</th>
                    <th>Menu Terkait</th>
                    <th>Persentase</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($discounts as $discount)
                    <tr>
                        <td data-label="Nama Diskon">{{ $discount->name }}</td>
                        {{-- Tampilkan nama produk, jika produknya sudah dihapus, tampilkan pesan --}}
                        <td data-label="Nama Menu">{{ $discount->product->name ?? 'Produk Dihapus' }}</td>
                        <td data-label="Diskon">{{ $discount->percentage }}%</td>
                        <td data-label="Status">
                            @if($discount->is_active)
                                <span style="color: green;">Aktif</span>
                            @else
                                <span style="color: red;">Tidak Aktif</span>
                            @endif
                        </td>
                        <td data-label="Aksi">
                            <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus diskon ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data diskon.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="pagination-container">
            {{-- Tombol Sebelumnya --}}
            @if ($discounts->onFirstPage())
                <li class="page-item disabled">
                    <button class="page-link" disabled>Sebelumnya</button>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $discounts->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev">Sebelumnya</a>
                </li>
            @endif

            {{ $discounts->appends(request()->query())->links('vendor.pagination.custom-numbered') }}

            {{-- Tombol Berikutnya --}}
            @if ($discounts->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $discounts->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next">Berikutnya</a>
                </li>
            @else
                <li class="page-item disabled">
                    <button class="page-link" disabled>Berikutnya</button>
                </li>
            @endif
        </div>
        {{-- End Pagination --}}
    </div>
</div>
@endsection
