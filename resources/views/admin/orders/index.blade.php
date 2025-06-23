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

{{-- dalam resources/views/admin/orders/index.blade.php --}}

@push('scripts')

<script>
    // // Pastikan variabel Pusher di-load oleh bootstrap.js
    // if (typeof window.Pusher !== 'undefined') {
    //     console.log('Pusher JS & Laravel Echo loaded successfully.');

    //     // Inisialisasi audio player
    //     const notificationSound = new Audio('/sounds/notification.mp3');

    //     // Dengarkan siaran di channel privat 'orders'
    //     window.Echo.private('orders')
    //         .listen('NewOrderReceived', (e) => {
    //             console.log('Event NewOrderReceived Diterima:', e.order);

    //             // Mainkan suara notifikasi
    //             notificationSound.play().catch(error => console.log("Gagal memutar suara:", error));

    //             // Tampilkan notifikasi sederhana di browser
    //             alert('Ada pesanan baru masuk dari Meja ' + e.order.table_number + '!');

    //             // Refresh halaman untuk menampilkan pesanan baru
    //             window.location.reload();
    //         });

    //     console.log("Listening for events on 'private-orders' channel...");

    // } else {
    //     console.error('Laravel Echo atau Pusher JS gagal dimuat. Pastikan Anda sudah menjalankan "npm run dev".');
    // }
</script>
@endpush