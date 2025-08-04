@extends('layouts.admin')
@section('title', 'Manajemen Diskon')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Daftar Diskon</h1>
        <div class="mt-3">
            <a href="{{ route('admin.discounts.create') }}" class="custom-action-btn-tambah custom-action-btn-tambah-primary">
                <i class="fas fa-plus"></i> <span>Tambah Diskon</span>
            </a>
        </div>
    </div>

    {{-- Filter Outlet dan Pencarian Diskon (Hardcoded) --}}
    <div class="card mb-4 filter-card card-padding">
        <form action="{{ route('admin.discounts.index') }}" method="GET" class="filter-form">
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

                {{-- Kolom Pencarian Diskon --}}
                <div class="form-group form-group-flex">
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

    <div class="card card-padding">
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
                                <span class="color-green">Aktif</span>
                            @else
                                <span class="color-red">Tidak Aktif</span>
                            @endif
                        </td>
                        <td data-label="Aksi">
                            <div class="d-flex justify-content-end align-items-center gap-2 actions-wrapper">
                                <a href="{{ route('admin.discounts.edit', $discount) }}" class="custom-action-btn custom-action-btn-warning" title="Sunting Diskon">
                                    <i class="fas fa-edit"></i> <span>Sunting</span>
                                </a>
                                <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="custom-action-btn custom-action-btn-danger" title="Hapus Diskon">
                                        <i class="fas fa-trash"></i> <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
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
            <ul class="pagination" role="navigation">
                {{-- Previous Button --}}
                @if ($discounts->onFirstPage())
                    <li class="page-item disabled">
                        <button class="page-link" disabled><i class="fas fa-chevron-left"></i></button>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $discounts->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
                    </li>
                @endif

                {{-- Numbered Links (Limited) --}}
                @foreach ($discounts->getUrlRange(1, $discounts->lastPage()) as $page => $url)
                    @if ($page == $discounts->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @elseif ($page == 1 || $page == $discounts->lastPage() || ($page >= $discounts->currentPage() - 1 && $page <= $discounts->currentPage() + 1))
                        <li class="page-item">
                            <a class="page-link" href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $page }}</a>
                        </li>
                    @elseif ($page == $discounts->currentPage() - 2 || $page == $discounts->currentPage() + 2)
                        <li class="page-item disabled d-none d-md-block">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if ($discounts->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $discounts->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next"><i class="fas fa-chevron-right"></i></a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <button class="page-link" disabled><i class="fas fa-chevron-right"></i></button>
                    </li>
                @endif
            </ul>
        </div>
        {{-- End Pagination --}}
    </div>
</div>
@endsection
