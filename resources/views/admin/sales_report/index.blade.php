@extends('layouts.admin')
@section('title', 'Laporan Hasil Penjualan')

@section('content')
<div class="container">
    <h1>@yield('title')</h1>
    <p>Analisis penjualan berdasarkan rentang tanggal yang dipilih.</p>

    {{-- Filter Form --}}
    <div class="card mb-4 card-padding">
        <form action="{{ route('admin.sales.report.index') }}" method="GET" class="sales-filter-form" >
            <div class="display-flex gap-1rem align-items-end">
                {{-- Form Tanggal Mulai (tidak berubah) --}}
                <div class="form-group form-group-flex">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                {{-- Form Tanggal Selesai (tidak berubah) --}}
                <div class="form-group form-group-flex">
                    <label for="end_date">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                </div>

                {{-- TAMBAHKAN DROPDOWN OUTLET INI --}}
                <div class="form-group form-group-flex">
                    <label for="outlet_id">Pilih Outlet</label>
                    <select name="outlet_id" id="outlet_id" class="form-control">
                        <option value="">Semua Outlet</option>
                        @foreach ($outlets as $outlet)
                            <option value="{{ $outlet->id }}" {{ $selectedOutletId == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Ringkasan Penjualan --}}
    <div class="grid-2-columns mb-4">
        <div class="card card-padding">
            <h4>Total Pendapatan</h4>
            <h2>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
        </div>
        <div class="card card-padding">
            <h4>Total Transaksi</h4>
            <h2>{{ $totalOrders }} Pesanan</h2>
        </div>
    </div>

        {{-- Analisis Tambahan --}}
        <div class="analytics-grid mb-4">
            {{-- Kartu Menu Best Seller --}}
            <div class="card">
                <div class="card-body">
                    <h4>Menu Best Seller</h4>
                    <ol class="ranked-list">
                        @forelse ($bestSellingProducts as $item)
                            <li class="ranked-list-item">
                                <span>{{ $item->product->name ?? 'Menu Dihapus' }}</span>
                                <strong class="item-count">{{ $item->total_sold }} terjual</strong>
                            </li>
                        @empty
                            <li class="ranked-list-item-empty">Tidak ada data.</li>
                        @endforelse
                    </ol>
                </div>
            </div>

            {{-- Kartu Pelanggan Teratas --}}
            <div class="card">
                <div class="card-body">
                    <h4>Pelanggan Teratas</h4>
                    <ol class="ranked-list">
                        @forelse ($topCustomers as $customer)
                            <li class="ranked-list-item">
                                <span>{{ $customer->customer_name }}</span>
                                <strong class="item-count">{{ $customer->total_orders }} pesanan</strong>
                            </li>
                        @empty
                            <li class="ranked-list-item-empty">Tidak ada data.</li>
                        @endforelse
                    </ol>
                </div>
            </div>
        </div>  
    </div>


    {{-- Tabel Rincian Pesanan --}}
    <div class="card card-padding">
        <h3>Rincian Transaksi ({{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }})</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Rincian Item</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td data-label="Waktu">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        <td data-label="Pelanggan">{{ $order->customer_name }}</td>
                        <td data-label="Meja">{{ $order->table_number }}</td>
                        <td data-label="Rincian Item">
                            <ul>
                                @foreach($order->orderItems as $item)
                                    <li>{{ $item->quantity }}x {{ $item->product->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td data-label="Total"><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data penjualan pada rentang tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection