@extends('layouts.admin')
@section('title', 'Manajemen Outlet')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Daftar Outlet</h1>
        <div class="mt-3">
            <a href="{{ route('admin.outlets.create') }}" class="custom-action-btn-tambah custom-action-btn-tambah-primary">
                <i class="fas fa-plus"></i> <span>Tambah Outlet</span>
            </a>
        </div>
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
                        <div class="d-flex justify-content-end align-items-center gap-2 actions-wrapper">
                            <a href="{{ route('admin.outlets.edit', $outlet) }}" class="custom-action-btn custom-action-btn-warning" title="Sunting Outlet">
                                <i class="fas fa-edit"></i> <span>Sunting</span>
                            </a>
                            <form action="{{ route('admin.outlets.destroy', $outlet) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="custom-action-btn custom-action-btn-danger" title="Hapus Outlet">
                                    <i class="fas fa-trash"></i> <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>Belum ada data outlet.</p>
        @endforelse
    </div>
</div>
@endsection

