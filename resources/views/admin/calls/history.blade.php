@extends('layouts.admin')
@section('title', 'Riwayat Panggilan') 

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>@yield('title')</h1>
            <p class="mb-0">Daftar semua panggilan dari pelanggan yang telah selesai ditangani.</p> 
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
    <div class="card mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('admin.calls.history') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Cari ID Panggilan</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari ID panggilan..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Filter Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Riwayat</option>
                            <option value="handled" {{ request('status') == 'handled' ? 'selected' : '' }}>Ditangani</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('admin.calls.history') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-orders align-middle"> 
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Meja</th>
                            <th>Pelanggan</th>
                            <th>Catatan Panggilan</th> 
                            <th class="text-center">Status</th>
                            <th>Waktu Selesai / Dibatalkan</th> 
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($calls as $call)
                            <tr>
                                <td data-label="ID"><strong>#{{ $call->call_number }}</strong></td>
                                <td data-label="Meja" class="text-nowrap">{{ $call->table_number }}</td>
                                <td data-label="Pelanggan" class="text-nowrap">{{ $call->customer_name }}</td>
                                <td data-label="Catatan Panggilan">
                                    @if($call->notes)
                                        <small class="item-note d-block" title="{{ $call->notes }}">
                                            <i class="fas fa-sticky-note"></i> {{ Str::limit($call->notes, 50) }}
                                        </small>
                                    @else
                                        - 
                                    @endif
                                </td>
                                <td data-label="Status" class="text-center">
                                    <span class="status-badge status-{{ $call->status }}">
                                        {{ $call->translated_status }}
                                    </span>
                                </td>
                                <td data-label="Waktu Selesai" class="text-nowrap" title="{{ $call->updated_at->format('d M Y, H:i:s') }}">
                                    {{ $call->updated_at->diffForHumans() }}
                                </td>
                                <td data-label="Aksi">
                                    <div class="d-flex justify-content-end align-items-center gap-2 actions-wrapper">
                                        <form action="{{ route('admin.calls.destroy', $call) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus riwayat panggilan ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="custom-action-btn custom-action-btn-danger" title="Hapus Permanen">
                                                <i class="fas fa-trash"></i> <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada riwayat panggilan yang tersedia.</td> 
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="pagination-container">
                {{-- Tombol Sebelumnya --}}
                @if ($calls->onFirstPage())
                    <li class="page-item disabled">
                        <button class="page-link" disabled>Sebelumnya</button>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $calls->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev">Sebelumnya</a>
                    </li>
                @endif

                {{-- Link Halaman --}}
                {{ $calls->appends(request()->query())->links('vendor.pagination.custom-numbered') }}

                {{-- Tombol Berikutnya --}}
                @if ($calls->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $calls->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next">Berikutnya</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <button class="page-link" disabled>Berikutnya</button>
                    </li>
                @endif
            </div>
            
        </div>
    </div>
</div>
@endsection
