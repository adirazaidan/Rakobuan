@extends('layouts.customer')

@section('title', 'Pesanan Diterima')

@section('content')
<div class="success-page-container">
    <div class="success-card" data-order-session-id="{{ $order->session_id }}">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Pesanan Anda Telah Diterima!</h2>
        <p class="subtitle">Pesanan Anda sedang disiapkan oleh dapur. Silakan tunggu dan nikmati waktu Anda.</p>

        <div class="receipt-details">
            <div class="receipt-header">
                <h3>Rincian Pesanan #{{ $order->id }}</h3>
                <div class="receipt-customer-info">
                    <span><strong>Nama:</strong> {{ $order->customer_name }}</span>
                    <span><strong>Meja:</strong> {{ $order->table_number }}</span>
                    <span><strong>Waktu:</strong> {{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <p class="receipt-status"><strong>Status:</strong> <span id="order-status-badge" class="status-badge status-{{ $order->status }}">{{ $order->translated_status }}</span></p>
            </div>

            <table class="receipt-table">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th>Item</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="item-name">{{ $item->product->name }}</div>
                                @if($item->notes)
                                    <div class="receipt-item-notes">
                                        <i class="fas fa-sticky-note"></i> {{ $item->notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Total</td>
                        <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="success-actions">
            <a href="{{ route('customer.menu.index') }}" class="btn-secondary">Pesan Lagi</a>
            <button class="btn-primary call-waiter-btn">Panggil Pelayan</button>
        </div>
    </div>
</div>
@endsection