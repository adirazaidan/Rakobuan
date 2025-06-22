@extends('layouts.admin')
@section('title', 'Riwayat Orderan') {{-- <-- UBAH JUDUL --}}

@section('content')
<div class="container">
    <h1>@yield('title')</h1>
    <p>Daftar semua pesanan yang telah selesai.</p> {{-- <-- UBAH DESKRIPSI --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-container">
        @forelse ($orders as $order)
            {{-- Bagian card ini tidak perlu diubah sama sekali --}}
            <div class="order-card status-completed"> {{-- Statusnya pasti completed --}}
                <div class="order-header">
                    <div>
                        <h3>Meja: {{ $order->table_number }}</h3>
                        <span>Pelanggan: {{ $order->customer_name }}</span>
                    </div>
                    <div class="order-status">
                        <strong>{{ ucfirst($order->status) }}</strong>
                        <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <div class="order-body">
                    <h4>Item Pesanan:</h4>
                    <ul>
                        @foreach ($order->orderItems as $item)
                            <li>
                                <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
                                <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="order-footer">
                    <div class="total-price">
                        <strong>Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                    </div>
                    <div class="order-actions">
                        {{-- HAPUS SEMUA TOMBOL KECUALI HAPUS --}}
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus riwayat pesanan ini secara permanen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="padding: 2rem; text-align:center;">
                <p>Tidak ada riwayat pesanan.</p> {{-- <-- UBAH PESAN KOSONG --}}
            </div>
        @endforelse
    </div>
</div>
@endsection

{{-- Kita tidak perlu push style lagi karena sudah ada di index dan bisa kita pindah ke admin.css nanti --}}