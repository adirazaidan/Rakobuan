@extends('layouts.customer')

@section('title', 'Pesanan Diterima')

@section('content')
<div class="success-page-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Pesanan Anda Telah Diterima!</h2>
        <p class="subtitle">Pesanan Anda sedang disiapkan oleh dapur. Silakan tunggu dan nikmati waktu Anda.</p>

        <div class="receipt-details">
            <h3>Rincian Pesanan #{{ $order->id }}</h3>
            <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
            <p><strong>Meja:</strong> {{ $order->table_number }}</p>
            <p><strong>Waktu:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Status:</strong> <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>

            <ul class="receipt-items">
                @foreach($order->orderItems as $item)
                    <li>
                        <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
                        <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="receipt-total">
                <span>Total</span>
                <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="success-actions">
            <a href="{{ route('customer.menu.index') }}" class="btn-secondary">Pesan Lagi</a>
            <button class="btn-primary call-waiter-btn">Panggil Pelayan</button> {{-- <-- TAMBAHKAN CLASS INI --}}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .success-page-container { padding: 2rem; display: flex; justify-content: center; align-items: flex-start; }
    .success-card { background: white; padding: 2rem 2.5rem; border-radius: 12px; width: 100%; max-width: 600px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    .success-icon i { font-size: 4rem; color: var(--success-color, #198754); }
    .success-card h2 { margin: 1.5rem 0 0.5rem 0; }
    .success-card .subtitle { color: #666; margin-bottom: 2rem; }
    .receipt-details { border: 1px solid #eee; background-color: #f8f9fa; border-radius: 8px; margin-top: 1.5rem; padding: 1.5rem; text-align: left; }
    .receipt-details h3 { text-align: center; margin-top: 0; margin-bottom: 1.5rem; }
    .receipt-details p { margin: 0.5rem 0; }
    .status-badge { padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; color: #664d03; background-color: #fff3cd; }
    .receipt-items { list-style: none; padding: 1rem 0; margin: 1rem 0; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; }
    .receipt-items li { display: flex; justify-content: space-between; padding: 0.6rem 0; }
    .receipt-total { display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; padding-top: 0.5rem; }
    .success-actions { margin-top: 2rem; display: flex; justify-content: center; gap: 1rem; }
    .btn-primary { background: var(--primary-color); color: white; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; font-weight: 500; }
    .btn-secondary { background: #6c757d; color: white; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; font-weight: 500; }
</style>
@endpush