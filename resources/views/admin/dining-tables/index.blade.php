@extends('layouts.admin')
@section('title', 'Manajemen Meja')

@section('content')

@include('admin.dining-tables._modal_history')

<div class="mb-4">
    <h1>Layout Meja</h1>
    <div class="mt-3">
        <a href="{{ route('admin.dining-tables.create') }}" class="custom-action-btn-tambah custom-action-btn-tambah-primary">
            <i class="fas fa-plus"></i> <span>Tambah Meja</span>
        </a>
    </div>
</div>

    <div class="card mb-4 filter-card card-padding">
    <form action="{{ route('admin.dining-tables.index') }}" method="GET" class="filter-form">
        <div class="filter-container">
            {{-- Filter Lokasi --}}
                            <div class="form-group form-group-flex">
                <label for="location">Filter Lokasi</label>
                <select name="location" id="location" class="form-control">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location }}" {{ $selectedLocation == $location ? 'selected' : '' }}>
                            {{ $location }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Tombol Filter --}}
            <div class="form-group filter-buttons">
                <button type="submit" class="btn btn-primary w-100">Cari</button>
                <a href="{{ route('admin.dining-tables.index') }}" class="btn btn-secondary w-100 mt-2">Reset</a>
            </div>
        </div>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Tombol Kunci/Buka Semua --}}
<div class="mb-4 d-flex align-items-center gap-2 action-buttons-group">
    <form action="{{ route('admin.dining-tables.lockAll') }}" method="POST" onsubmit="return confirm('Yakin ingin mengunci SEMUA meja?');">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm">Kunci Semua</button>
    </form>
    <form action="{{ route('admin.dining-tables.unlockAll') }}" method="POST" onsubmit="return confirm('Yakin ingin membuka SEMUA meja?');">
        @csrf
        <button type="submit" class="btn btn-success btn-sm">Buka Semua</button>
    </form>
</div>

<div class="table-visual-grid">
    @forelse ($tables as $table)
        @include('admin.dining-tables._card', ['table' => $table])
    @empty
        <div class="card card-padding-lg text-center">
            <p>Belum ada data meja. Silakan tambahkan meja baru.</p>
        </div>
    @endforelse
</div>
@endsection