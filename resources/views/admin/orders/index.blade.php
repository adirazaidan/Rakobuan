@extends('layouts.admin')
@section('title', 'Daftar Orderan Masuk')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1>@yield('title')</h1>
            <p class="mb-0">Daftar pesanan yang perlu ditangani atau sedang diproses.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
    <div class="card mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Cari ID Pesanan</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari ID pesanan..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Filter Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="current" {{ request('status') == 'current' || !request('status') ? 'selected' : '' }}>Belum Diproses & Diproses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Diproses</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        </select>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
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
                            <th>Waktu Masuk</th>
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
                                <td data-label="Waktu Masuk" class="text-nowrap" title="{{ $order->created_at->format('d M Y, H:i:s') }}">{{ $order->created_at->diffForHumans() }}</td>
                                <td data-label="Aksi">
                                    <div class="d-flex justify-content-end align-items-center gap-2 actions-wrapper">
                                        @if ($order->status == 'pending')
                                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="custom-action-btn custom-action-btn-primary" title="Tangani Pesanan"><i class="fas fa-hand-paper"></i> <span>Tangani</span></button>
                                            </form>
                                        @elseif ($order->status == 'processing')
                                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="custom-action-btn custom-action-btn-success" title="Tandai Selesai"><i class="fas fa-check-circle"></i> <span>Selesai</span></button>
                                            </form>
                                        @endif

                                        @if ($order->status == 'pending' || $order->status == 'processing')
                                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="custom-action-btn custom-action-btn-warning" title="Batalkan Pesanan"><i class="fas fa-times-circle"></i> <span>Batal</span></button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="custom-action-btn custom-action-btn-info" title="Cetak Struk"><i class="fas fa-print"></i> <span>Cetak</span></a>

                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini secara permanen?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="custom-action-btn custom-action-btn-danger" title="Hapus Permanen"><i class="fas fa-trash"></i> <span>Hapus</span></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Tidak ada pesanan masuk saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Replikasi struktur pagination dari halaman produk --}}
            <div class="pagination-container">
                {{-- Tombol Sebelumnya --}}
                @if ($orders->onFirstPage())
                    <li class="page-item disabled">
                        <button class="page-link" disabled>Sebelumnya</button>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="prev">Sebelumnya</a>
                    </li>
                @endif

                {{-- Link Halaman --}}
                {{ $orders->appends(request()->query())->links('vendor.pagination.custom-numbered') }}

                {{-- Tombol Berikutnya --}}
                @if ($orders->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $orders->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}" rel="next">Berikutnya</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <button class="page-link" disabled>Berikutnya</button>
                    </li>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection