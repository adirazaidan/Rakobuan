@extends('layouts.customer')

@section('title', 'Daftar Menu')

@section('content')
<div class="menu-page-container" style="padding: 2rem;">
    <div class="search-bar" style="margin-bottom: 2rem;">
        <input type="search" placeholder="Cari Menu..." class="form-control">
    </div>

    {{-- Nanti kita bisa fungsikan filter kategori ini dengan JS --}}
    <div class="category-filters" style="margin-bottom: 2rem; display:flex; gap: 0.5rem; flex-wrap:wrap;">
        <button class="btn-category active">Semua</button>
        @foreach($categories as $category)
            <button class="btn-category">{{ $category->name }}</button>
        @endforeach
    </div>

    <div class="menu-grid" id="menu-list">
        @forelse ($products as $product)
            <div class="product-card">
                <img src="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}" class="product-image">
                @if($product->is_bestseller)
                    <span class="bestseller-badge">Best Seller</span>
                @endif
                <div class="product-info">
                    <h4 class="product-name">{{ $product->name }}</h4>
                    <p class="product-description">{{ Str::limit($product->description, 60) }}</p>
                    <div class="product-footer">
                        <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <button class="btn-add-cart add-to-cart-btn"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}"
                            data-description="{{ $product->description }}"
                            data-image="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/150' }}">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p>Tidak ada menu yang tersedia saat ini.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Style sementara untuk halaman menu, bisa dipindah ke customer.css */
    .btn-category { padding: 0.5rem 1rem; border: 1px solid #ccc; border-radius: 20px; background: white; cursor: pointer; }
    .btn-category.active { background: var(--primary-color); color: white; border-color: var(--primary-color); }
    .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
    .product-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); overflow: hidden; position: relative; display: flex; flex-direction: column; }
    .product-image { width: 100%; height: 180px; object-fit: cover; }
    .bestseller-badge { position: absolute; top: 1rem; left: 1rem; background: var(--accent-color); color: var(--primary-color); padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
    .product-info { padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; }
    .product-name { margin: 0 0 0.5rem 0; font-size: 1.1rem; }
    .product-description { font-size: 0.9rem; color: #666; flex-grow: 1; }
    .product-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
    .product-price { font-size: 1.2rem; font-weight: 600; color: var(--primary-color); }
    .btn-add-cart { background: var(--primary-color); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; transition: background-color 0.2s; }
    .btn-add-cart:hover { background: var(--secondary-color); }
</style>
@endpush