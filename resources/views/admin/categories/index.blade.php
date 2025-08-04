@extends('layouts.admin')
@section('title', 'Manajemen Kategori')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Daftar Kategori</h1>
        <div class="mt-3">
            <a href="{{ route('admin.categories.create') }}" class="custom-action-btn-tambah custom-action-btn-tambah-primary">
                <i class="fas fa-plus"></i> <span>Tambah Kategori</span>
            </a>
        </div>
    </div>

    {{-- Form Filter --}}
    <div class="card mb-4 filter-card card-padding">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="filter-form">
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

                    {{-- Kolom Pencarian Kategori --}}
                    <div class="form-group form-group-flex">
                        <label for="search">Cari Kategori</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Masukkan nama kategori..." value="{{ request('search') }}">
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="form-group filter-buttons">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary w-100 mt-2">Reset</a>
                    </div>
                </div>
            </form>
    </div>  
    {{-- End Form Filter --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-padding">
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
                                <div class="d-flex justify-content-end align-items-center gap-2 actions-wrapper">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="custom-action-btn custom-action-btn-warning" title="Sunting Kategori">
                                        <i class="fas fa-edit"></i> <span>Sunting</span>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="custom-action-btn custom-action-btn-danger" title="Hapus Kategori">
                                            <i class="fas fa-trash"></i> <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
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
            <ul class="pagination" role="navigation">
                {{-- Previous Button --}}
                @if ($categories->onFirstPage())
                    <li class="page-item disabled">
                        <button class="page-link" disabled><i class="fas fa-chevron-left"></i></button>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $categories->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
                    </li>
                @endif

                {{-- Numbered Links (Limited) --}}
                @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                    @if ($page == $categories->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @elseif ($page == 1 || $page == $categories->lastPage() || ($page >= $categories->currentPage() - 1 && $page <= $categories->currentPage() + 1))
                        <li class="page-item">
                            <a class="page-link" href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $page }}</a>
                        </li>
                    @elseif ($page == $categories->currentPage() - 2 || $page == $categories->currentPage() + 2)
                        <li class="page-item disabled d-none d-md-block">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if ($categories->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $categories->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next"><i class="fas fa-chevron-right"></i></a>
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
