@extends('layouts.admin')
@section('title', 'Manajemen Kategori')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Kategori</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
    </div>
    
    @include('admin.partials.outlet-filter')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card" style="padding:1.5rem">
        <div class="table-responsive-wrapper"></div>
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
                            <td data-label="Nomor">{{ $index + 1 }}</td>
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
    </div>
</div>
@endsection
