@extends('layouts.admin')
@section('title', 'Riwayat Orderan') 

@section('content')
<div class="container-fluid"> 
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>@yield('title')</h1>
            <p class="mb-0">Daftar semua pesanan yang telah selesai atau dibatalkan.</p> 
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-orders align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Meja</th>
                            <th>Pelanggan</th>
                            <th>Item Pesanan</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Status</th>
                            <th>Waktu Selesai / Dibatalkan</th> 
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td data-label="ID"><strong>#{{ $order->order_number }}</strong></td>
                                <td data-label="Meja" class="text-nowrap">{{ $order->table_number }}</td>
                                <td data-label="Pelanggan" class="text-nowrap">{{ $order->customer_name }}</td>
                                <td data-label="Item Pesanan">
                                    <ul class="order-item-list-condensed">
                                        @foreach ($order->orderItems as $item)
                                            <li>
                                                {{ $item->quantity }}x {{ $item->product->name }}
                                                @if($item->notes)
                                                    <small class="item-note d-block" title="{{ $item->notes }}">
                                                        <i class="fas fa-sticky-note"></i> {{ Str::limit($item->notes, 30) }}
                                                    </small>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td data-label="Total" class="text-right text-nowrap">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td data-label="Status" class="text-center"><span class="status-badge status-{{ $order->status }}">{{ $order->translated_status }}</span></td>
                                <td data-label="Waktu Selesai / Dibatalkan" class="text-nowrap" title="{{ $order->updated_at->format('d M Y, H:i:s') }}">{{ $order->updated_at->diffForHumans() }}</td> 
                                <td data-label="Aksi">
                                    <div class="d-flex justify-content-end align-items-center gap-2 actions-wrapper">
                                        <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="custom-action-btn custom-action-btn-info" title="Cetak Struk"><i class="fas fa-print"></i> <span>Cetak</span></a>

                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus riwayat pesanan ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="custom-action-btn custom-action-btn-danger" title="Hapus Permanen"><i class="fas fa-trash"></i> <span>Hapus</span></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Tidak ada riwayat pesanan yang tersedia.</td> 
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection