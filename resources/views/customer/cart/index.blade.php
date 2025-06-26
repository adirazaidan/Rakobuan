@extends('layouts.customer')

@section('title', 'Keranjang Anda')

@section('content')
{{-- Kontainer utama ini sekarang hanya bertugas sebagai pemegang halaman --}}
<div class="cart-page-container">

    {{-- Wrapper baru ini yang akan mengatur padding dan lebar konten --}}
    <div class="cart-content-wrapper">
        <div class="cart-header">
            <a href="{{ route('customer.menu.index') }}" class="back-button" title="Kembali ke Menu"><i class="fas fa-arrow-left"></i></a>
            <h1>Keranjang Pesanan</h1>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="customer-details-box">
            Pesanan untuk <strong>{{ session('customer_name') }}</strong> di Meja <strong>{{ session('table_number') }}</strong>
        </div>

        <div id="cart-items-list" class="cart-items-list">
            @forelse ($cart as $id => $details)
                <div class="cart-item-card-new" data-id="{{ $id }}" data-price="{{ $details['price'] }}">
                    <div class="cart-item-image-wrapper">
                        <img src="{{ $details['image'] ? Storage::url('products/' . $details['image']) : 'https://via.placeholder.com/150' }}" alt="{{ $details['name'] }}" class="cart-item-image">
                        <button class="zoom-btn card-zoom-btn" title="Perbesar gambar" data-image-url="{{ $details['image'] ? Storage::url('products/' . $details['image']) : 'https://via.placeholder.com/400' }}">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                    <div class="cart-item-details">
                        <div class="item-info-header">
                            <h4 class="item-name">{{ $details['name'] }}</h4>
                            <strong class="item-subtotal" id="subtotal-{{ $id }}">Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</strong>
                        </div>
                        <form class="item-update-form">
                            @csrf
                            <div class="form-group">
                                <label>Catatan</label>
                                <textarea name="notes" class="form-control item-notes-input" placeholder="Contoh: Tidak pedas">{{ $details['notes'] }}</textarea>
                            </div>
                            <div class="item-controls">
                                <div class="quantity-selector-cart">
                                    <button type="button" class="btn-quantity btn-decrease">-</button>
                                    <input type="number" name="quantity" value="{{ $details['quantity'] }}" min="1" class="quantity-input" readonly>
                                    <button type="button" class="btn-quantity btn-increase">+</button>
                                </div>
                                <div class="item-actions">
                                    <button type="submit" class="btn-update-cart"><i class="fas fa-sync-alt"></i> Update</button>
                                    <button type="button" class="btn-remove" onclick="document.getElementById('remove-form-{{ $id }}').submit();" title="Hapus item">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form id="remove-form-{{ $id }}" action="{{ route('cart.remove', $id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart fa-3x"></i>
                    <p>Keranjang Anda masih kosong.</p>
                    <a href="{{ route('customer.menu.index') }}" class="btn-primary">Lihat Menu</a>
                </div>
            @endforelse
        </div>
    </div>
    
    {{-- Footer sticky tetap berada di luar wrapper konten --}}
    @if (count($cart) > 0)
    <div class="cart-summary-sticky">
        <div class="total-price">
            <span>Total Harga</span>
            <strong id="grand-total">Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
        </div>
        <div class="checkout-action">
            <form action="{{ route('checkout.store') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim pesanan ini?');" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-checkout">Kirim Orderan ke Dapur</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection