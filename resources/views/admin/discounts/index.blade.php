@extends('layouts.admin')
@section('title', 'Manajemen Diskon')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Diskon</h1>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">Tambah Diskon</a>
    </div>

    @include('admin.partials.outlet-filter')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding:1.5rem">
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
    </div>
</div>
@endsection