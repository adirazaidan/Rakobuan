@extends('layouts.customer')

@section('title', 'Daftar Menu')

@section('content')
<div class="menu-page-container">
    {{-- Beri ID pada input search --}}
    <div class="search-bar">
        <input type="search" id="searchInput" placeholder="Cari nama menu..." class="form-control">
    </div>

    <div class="category-filters">
        {{-- Beri data-category-id="all" untuk tombol Semua --}}
        <button class="btn-category active" data-category-id="all">Semua</button>
        @foreach($categories as $category)
            {{-- Beri data-category-id pada setiap tombol kategori --}}
            <button class="btn-category" data-category-id="{{ $category->id }}">{{ $category->name }}</button>
        @endforeach
    </div>

    <div class="menu-grid" id="menu-list">
        @forelse ($products as $product)
            {{-- Tambahkan data-category-id dan data-product-name pada setiap kartu --}}
            <div class="product-card" data-category-id="{{ $product->category_id }}" data-product-name="{{ strtolower($product->name) }}">
                <div style="position: relative;">
                    <img src="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}" class="product-image">

                    @if($product->is_bestseller)
                        <span class="bestseller-badge">Best Seller</span>
                    @endif

                    @if(!$product->is_available)
                        <div class="out-of-stock-overlay">
                            <span class="out-of-stock-text">Habis</span>
                        </div>
                    @endif
                </div>

                @if($product->activeDiscount)
                    <span class="discount-badge">{{ $product->activeDiscount->percentage }}% OFF</span>
                @endif

                <div class="product-info">
                    <h4 class="product-name">{{ $product->name }}</h4>
                    <p class="product-description">{{ Str::limit($product->description, 60) }}</p>
                    <div class="product-footer">
                        <div class="price-wrapper">
                            @if($product->activeDiscount)
                                @php
                                    $discountedPrice = $product->price - ($product->price * $product->activeDiscount->percentage / 100);
                                @endphp
                                <span class="product-price-discounted">Rp {{ number_format($discountedPrice, 0, ',', '.') }}</span>
                                <span class="product-price-original"><del>Rp {{ number_format($product->price, 0, ',', '.') }}</del></span>
                            @else
                                <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <button class="btn-add-cart add-to-cart-btn"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            {{-- DATA-PRICE SEKARANG MENGGUNAKAN HARGA SETELAH DISKON JIKA ADA --}}
                            data-price="{{ $product->activeDiscount ? $discountedPrice : $product->price }}"
                            data-description="{{ $product->description }}"
                            data-image="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/150' }}"
                            @if(!$product->is_available) disabled @endif >
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p>Tidak ada menu yang tersedia untuk outlet ini.</p>
        @endforelse
    </div>
</div>
@endsection