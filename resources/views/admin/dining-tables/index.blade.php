@extends('layouts.admin')
@section('title', 'Layout Meja')

@section('content')

@include('admin.dining-tables._modal_history')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Layout Meja</h1>
    <a href="{{ route('admin.dining-tables.create') }}" class="btn btn-primary">Tambah Meja</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.dining-tables.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <div class="form-group mb-0 flex-grow-1" style="max-width: 300px;">
                <label for="location" class="visually-hidden">Filter Lokasi</label>
                <select name="location" id="location" class="form-control">
                    <option value="">Semua Lokasi</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location }}" {{ $selectedLocation == $location ? 'selected' : '' }}>
                            {{ $location }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="mb-4 d-flex gap-2">
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