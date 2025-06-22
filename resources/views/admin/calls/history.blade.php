@extends('layouts.admin')
@section('title', 'Riwayat Panggilan') {{-- <-- UBAH JUDUL --}}

@section('content')
<div class="container">
    <h1>@yield('title')</h1>
    <p>Daftar semua panggilan dari pelanggan yang telah selesai ditangani.</p> {{-- <-- UBAH DESKRIPSI --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-container">
        @forelse ($calls as $call)
            <div class="order-card status-completed"> {{-- Status pasti completed --}}
                <div class="order-header">
                    <div>
                        <h3>Meja: {{ $call->table_number }}</h3>
                        <span>Pelanggan: {{ $call->customer_name }}</span>
                    </div>
                    <div class="order-status">
                        <strong>{{ ucfirst($call->status) }}</strong>
                        <span>{{ $call->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <div class="order-body">
                    <p><strong>Catatan:</strong> {{ $call->notes ?: '-' }}</p>
                </div>
                <div class="order-footer">
                    <div></div> {{-- Div kosong untuk alignment --}}
                    <div class="order-actions">
                        {{-- HANYA SISAKAN TOMBOL HAPUS --}}
                        <form action="{{ route('admin.calls.destroy', $call) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus riwayat panggilan ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="padding: 2rem; text-align:center;">
                <p>Tidak ada riwayat panggilan.</p> {{-- <-- UBAH PESAN KOSONG --}}
            </div>
        @endforelse
    </div>
</div>
@endsection