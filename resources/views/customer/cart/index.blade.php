@extends('layouts.customer')

@section('title', 'Keranjang Anda')

@section('content')
<div class="cart-page-container">
    <div class="cart-header">
        <a href="{{ route('customer.menu.index') }}" class="back-button" title="Kembali ke Menu"><i class="fas fa-arrow-left"></i></a>
        <h1>Keranjang Anda</h1>
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

    <div class="cart-items-list">
        @forelse ($cart as $id => $details)
            <div class="cart-item-card">
                <img src="{{ $details['image'] ? Storage::url('products/' . $details['image']) : 'https://via.placeholder.com/150' }}" alt="{{ $details['name'] }}">
                <div class="item-details">
                    <h4>{{ $details['name'] }}</h4>
                    <p class="item-price">Rp {{ number_format($details['price'], 0, ',', '.') }}</p>
                    <form action="{{ route('cart.remove', $id) }}" method="POST" class="remove-form" onsubmit="return confirm('Hapus item ini dari keranjang?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="remove-button"><i class="fas fa-trash-alt"></i> Hapus</button>
                    </form>
                </div>
                <div class="item-actions">
                    <form action="{{ route('cart.update', $id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number" name="quantity" value="{{ $details['quantity'] }}" min="1" class="form-control quantity-input">
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Contoh: Tidak pedas">{{ $details['notes'] }}</textarea>
                        </div>
                        <button type="submit" class="btn-update">Update</button>
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

    @if (count($cart) > 0)
    <div class="cart-summary">
        <div class="total-price">
            <span>Total Harga</span>
            <strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong>
        </div>
        <div class="checkout-action">
            <form action="{{ route('checkout.store') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim pesanan ini?');">
                @csrf
                <button type="submit" class="btn-checkout">Kirim Orderan ke Dapur</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .cart-page-container { padding: 1.5rem; max-width: 800px; margin: auto; }
    .cart-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
    .back-button { font-size: 1.5rem; color: var(--text-color); }
    .cart-header h1 { margin: 0; font-size: 1.8rem; }
    .customer-details-box { background: #e9ecef; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; font-size: 1.1rem;}
    .cart-item-card { display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 1rem; align-items: flex-start; }
    .cart-item-card img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; }
    .item-details h4 { margin: 0 0 0.5rem 0; font-size: 1.2rem; }
    .item-details .item-price { font-weight: 600; color: var(--primary-color); margin: 0; }
    .remove-form { margin-top: 1rem; }
    .remove-button { background: none; border: none; color: var(--danger-color, #dc3545); cursor: pointer; padding: 0; font-size: 0.9rem; }
    .item-actions .form-group { margin-bottom: 0.75rem; }
    .item-actions .form-control { padding: 0.5rem; }
    .quantity-input { max-width: 80px; }
    .btn-update { font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 6px; background-color: #e9ecef; color: var(--text-color); border: none; cursor: pointer; }
    .cart-summary { background: white; padding: 1.5rem; border-radius: 12px; margin-top: 2rem; box-shadow: 0 -4px 15px rgba(0,0,0,0.05); }
    .total-price { display: flex; justify-content: space-between; align-items: center; font-size: 1.2rem; margin-bottom: 1.5rem; }
    .btn-checkout { width: 100%; padding: 1rem; border: none; background-color: var(--accent-color); color: var(--primary-color); border-radius: 8px; font-size: 1.2rem; font-weight: bold; cursor: pointer; }
    .empty-cart { text-align: center; padding: 3rem; background: white; border-radius: 12px; }
    .empty-cart i { color: #ccc; margin-bottom: 1rem; }
    .btn-primary { background-color: var(--primary-color); color: white; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; }
    .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
    .alert-success { background-color: #d1e7dd; color: #0f5132; }
    .alert-danger { background-color: #f8d7da; color: #721c24; }

    @media (max-width: 600px) {
        .cart-item-card { grid-template-columns: 1fr; text-align: center; }
        .cart-item-card img { margin: 0 auto 1rem auto; }
        .item-actions { margin-top: 1rem; }
    }
</style>
@endpush