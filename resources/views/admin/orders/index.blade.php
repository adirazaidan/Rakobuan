@extends('layouts.admin')
@section('title', 'Daftar Orderan Masuk')

@section('content')
<div class="container">
    <h1>@yield('title')</h1>
    <p>Daftar pesanan yang perlu ditangani atau sedang diproses.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="order-container">
        @forelse ($orders as $order)
            <div class="order-card status-{{ $order->status }}">
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
                        @if ($order->status == 'pending')
                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="btn btn-primary">Tangani</button>
                            </form>
                        @elseif ($order->status == 'processing')
                             <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success">Selesai</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="padding: 2rem; text-align:center;">
                <p>Tidak ada pesanan masuk saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

<!-- @push('styles')
<style> /* Style sementara, bisa dipindah ke admin.css */
.order-container { display: flex; flex-direction: column; gap: 1.5rem; }
.order-card { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 5px solid; }
.order-card.status-pending { border-color: #fd7e14; /* Oranye */ }
.order-card.status-processing { border-color: #0d6efd; /* Biru */ }
.order-header, .order-footer { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; }
.order-header { border-bottom: 1px solid #eee; }
.order-footer { border-top: 1px solid #eee; }
.order-header h3 { margin: 0 0 5px 0; }
.order-status { text-align: right; }
.order-status strong { display: block; }
.order-body { padding: 1rem 1.5rem; }
.order-body ul { list-style: none; padding: 0; margin: 0; }
.order-body ul li { display: flex; justify-content: space-between; padding: 0.5rem 0; }
.order-actions { display: flex; gap: 0.5rem; }
.btn-success { background-color: #198754; color: white; }
</style>
@endpush -->