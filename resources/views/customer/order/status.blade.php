@extends('layouts.customer')
@section('title', 'Status Aktivitas Anda')

@section('content')
<div class="order-status-page-container">
    <div class="cart-header">
        <a href="{{ route('customer.menu.index') }}" class="back-button" title="Kembali ke Menu"><i class="fas fa-arrow-left"></i></a>
        <h1>Aktivitas Anda</h1>
    </div>

    <div class="customer-details-box">
        Semua pesanan dan panggilan <strong>{{ session('customer_name') }}</strong> di Meja <strong>{{ session('table_number') }}</strong>
    </div>

    @if($activities->isEmpty())
        <div class="empty-status">
            <i class="fas fa-receipt"></i>
            <p>Anda belum memiliki aktivitas.</p>
            <a href="{{ route('customer.menu.index') }}" class="btn-primary">Pesan Sekarang</a>
        </div>
    @else
        <div class="order-list">
            @if($activities->isEmpty())
                <div class="empty-status">
                    <p>Anda belum memiliki aktivitas.</p>
                    <a href="{{ route('customer.menu.index') }}" class="btn-primary">Pesan Sekarang</a>
                </div>
            @else
                @foreach($activities as $activity)
                    @if($activity instanceof \App\Models\Order)
                        @include('customer.order.partials._receipt_card', ['order' => $activity])
                    @elseif($activity instanceof \App\Models\Call)
                        @include('customer.order.partials._call_card', ['call' => $activity])
                    @endif
                @endforeach
            @endif
        </div>
    @endif
</div>

    <div class="status-page-actions status-actions-sticky">
        <a href="{{ route('customer.menu.index') }}" class="btn-secondary"><i class="fas fa-plus"></i> Pesan Lagi</a>
        <button class="btn-primary call-waiter-btn"><i class="fas fa-bell"></i> Panggil Pelayan</button>
    </div>
@endsection
