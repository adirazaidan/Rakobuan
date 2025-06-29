@extends('layouts.customer')

@section('title', 'Daftar Menu')

@section('content')
<div class="menu-page-container">
    <div class="search-bar-container">
        <i class="fas fa-search search-icon"></i>
        <input type="search" id="searchInput" class="form-control search-input-with-icon" placeholder="Cari menu favoritmu...">
    </div>

    <div class="filter-wrapper">
        <div class="custom-select-wrapper">
            <select id="categoryFilterDropdown" class="form-control custom-select">
                <option value="all">Tampilkan Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="menu-grid" id="menu-list">
        @forelse ($products as $product)
            @php
                $final_price = $product->activeDiscount 
                                ? $product->price - ($product->price * $product->activeDiscount->percentage / 100) 
                                : $product->price;
                $discount_percent = optional($product->activeDiscount)->percentage ?? 0;
                $full_image_url = $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/400';
            @endphp

            <div id="product-card-{{ $product->id }}" class="product-card" data-product-id="{{ $product->id }}" data-category-id="{{ $product->category_id }}" data-product-name="{{ strtolower($product->name) }}" data-stock="{{ $product->stock }}">
                <div class="product-image-container zoom-trigger" data-image-url="{{ $full_image_url }}">
                    <img src="{{ $full_image_url }}" alt="{{ $product->name }}" class="product-image">
                    <button class="zoom-btn card-zoom-btn" title="Perbesar gambar" data-image-url="{{ $full_image_url }}">
                        <i class="fas fa-expand"></i>
                    </button>
                    @if($product->is_bestseller)
                        <span class="bestseller-badge">Best Seller</span>
                    @endif
                    @if($product->activeDiscount)
                        <span class="discount-badge">{{ $discount_percent }}% OFF</span>
                    @endif
                    @if(!$product->is_available)
                        <div class="out-of-stock-overlay"><span class="out-of-stock-text">Habis</span></div>
                    @endif
                </div>

                <div class="product-info">
                    <h4 class="product-name">{{ $product->name }}</h4>
                    <p class="product-description">{{ Str::limit($product->description, 60) }}</p>
                    <div class="product-footer">
                        <div class="price-wrapper">
                            @if($product->activeDiscount)
                                <span class="product-price-discounted">Rp {{ number_format($final_price, 0, ',', '.') }}</span>
                                <span class="product-price-original"><del>Rp {{ number_format($product->price, 0, ',', '.') }}</del></span>
                            @else
                                <span class="product-price">Rp {{ number_format($final_price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <div class="product-controls">
                            <div class="cart-action-wrapper">
                                @if(isset($cart[$product->id]))
                                    <div class="quantity-selector-inline" data-product-id="{{ $product->id }}">
                                        <button class="btn-quantity-inline btn-decrease-inline">-</button>
                                        <span class="quantity-inline-display">{{ $cart[$product->id]['quantity'] }}</span>
                                        <button class="btn-quantity-inline btn-increase-inline" @if($cart[$product->id]['quantity'] >= $product->stock) disabled @endif>+</button>
                                    </div>
                                @else
                                    <button class="btn-add-cart-initial" data-product-id="{{ $product->id }}" @if(!$product->is_available || $product->stock <= 0) disabled @endif>
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                @endif
                            </div>
                                @php
                                    $hasNotes = isset($cart[$product->id]) && !empty($cart[$product->id]['notes']);
                                @endphp
                                <button class="btn-edit-notes {{ $hasNotes ? 'has-notes' : '' }}" 
                                        data-product-id="{{ $product->id }}" 
                                        data-notes="{{ $cart[$product->id]['notes'] ?? '' }}" 
                                        style="display: {{ isset($cart[$product->id]) ? 'flex' : 'none' }};">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                        </div>
                        <div class="inline-stock-feedback"></div>
                    </div>
                </div>
            </div>
        @empty
            <p style="padding: 0 1.5rem;">Tidak ada menu yang tersedia untuk outlet ini.</p>
        @endforelse
    </div>
</div>
@endsection