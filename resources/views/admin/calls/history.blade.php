@extends('layouts.admin')
@section('title', 'Riwayat Panggilan') {{-- Judul halaman --}}

@section('content')
<div class="container-fluid"> {{-- Menggunakan container-fluid untuk layout lebar penuh --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>@yield('title')</h1>
            <p class="mb-0">Daftar semua panggilan dari pelanggan yang telah selesai ditangani.</p> 
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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
                                        {{ ucfirst($call->status) }}
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
        </div>
    </div>
</div>
@endsection