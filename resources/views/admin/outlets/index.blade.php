@extends('layouts.admin')
@section('title', 'Manajemen Outlet')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Daftar Outlet</h1>
        <a href="{{ route('admin.outlets.create') }}" class="btn btn-primary">Tambah Outlet</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card-container">
        @forelse ($outlets as $outlet)
            <div class="card">
                <img class="card-img" src="{{ $outlet->image ? Storage::url('outlets/' . $outlet->image) : 'https://via.placeholder.com/150' }}" alt="{{ $outlet->name }}">
                <div class="card-body">
                    <h3>{{ $outlet->name }}</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.outlets.edit', $outlet) }}" class="btn btn-sm btn-warning">Sunting</a>
                        <form action="{{ route('admin.outlets.destroy', $outlet) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus outlet ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>Belum ada data outlet.</p>
        @endforelse
    </div>
</div>
@endsection

