@extends('layouts.admin')
@section('title', 'Manajemen Meja')

@section('content')

@include('admin.dining-tables._modal_history')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Layout Meja</h1>
    <a href="{{ route('admin.dining-tables.create') }}" class="btn btn-primary">Tambah Meja</a>
</div>

<div class="card mb-4 filter-card">
    <div class="card-body">
        <form action="{{ route('admin.dining-tables.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                {{-- Filter Lokasi --}}
                <div class="col-md-4">
                    <label for="location" class="form-label">Filter Lokasi</label>
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
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                    <a href="{{ route('admin.dining-tables.index') }}" class="btn btn-secondary mt-2 w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
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
        <div class="card" style="padding: 2rem; text-align: center;">
            <p>Belum ada data meja. Silakan tambahkan meja baru.</p>
        </div>
    @endforelse
</div>
@endsection
