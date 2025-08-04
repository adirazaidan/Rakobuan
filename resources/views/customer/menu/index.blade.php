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
        <div class="filter-price-buttons">
            <button id="priceFilterAsc" data-sort-order="asc" title="Harga Termurah"><i class="fas fa-sort-amount-down-alt"></i></button>
            <button id="priceFilterDesc" data-sort-order="desc" title="Harga Termahal"><i class="fas fa-sort-amount-up-alt"></i></button>
        </div>
        <div class="checkbox-filter-wrapper">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="bestsellerFilter">
                <label class="form-check-label" for="bestsellerFilter">
                    Terlaris
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="discountFilter">
                <label class="form-check-label" for="discountFilter">
                    Diskon
                </label>
            </div>
        </div>
    </div>

    <div class="menu-grid" 
        id="menu-list"
        data-cart-item-count="{{ $cartItemCount ?? 0 }}"
        data-cart-total-price="{{ $cartTotalPrice ?? 0 }}">
        @forelse ($products as $product)
            @php
                $final_price = $product->activeDiscount 
                                ? $product->price - ($product->price * $product->activeDiscount->percentage / 100) 
                                : $product->price;
                $discount_percent = optional($product->activeDiscount)->percentage ?? 0;
                $full_image_url = $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/400';
            @endphp

            <div id="product-card-{{ $product->id }}" 
                 class="product-card" 
                 data-product-id="{{ $product->id }}" 
                 data-category-id="{{ $product->category_id }}" 
                 data-product-name="{{ strtolower($product->name) }}" 
                 data-stock="{{ $product->stock }}" 
                 data-is-available="{{ $product->is_available ? 'true' : 'false' }}"
                 data-price="{{ $final_price }}"
                 data-is-bestseller="{{ $product->is_bestseller ? 'true' : 'false' }}"
                 data-has-discount="{{ $product->activeDiscount ? 'true' : 'false' }}">
                <div class="product-image-container zoom-trigger" data-image-url="{{ $full_image_url }}">
                    <img src="{{ $full_image_url }}" alt="{{ $product->name }}" class="product-image">
                    <button class="zoom-btn card-zoom-btn" title="Perbesar gambar" data-image-url="{{ $full_image_url }}">
                        <i class="fas fa-expand"></i>
                    </button>
                    @if($product->is_bestseller)
                        <span class="bestseller-badge">
                            <i class="fas fa-star"></i> Terlaris
                        </span>
                    @endif
                    @if($product->activeDiscount)
                        <span class="discount-badge">{{ $discount_percent }}% Diskon</span>
                    @endif
                    @if(!$product->is_available)
                        <div class="out-of-stock-overlay"><span class="out-of-stock-text">Habis</span></div>
                    @endif
                </div>

                <div class="product-info">
                    <h4 class="product-name">{{ $product->name }}</h4>
                    <p class="product-description" data-full-description="{{ $product->description }}">
                        {{ Str::limit($product->description, 60) }}
                    </p>
                    @if (strlen($product->description) > 60) 
                        <button class="btn-read-more" data-product-id="{{ $product->id }}">Lihat Selanjutnya</button>
                    @endif

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
                                    class="{{ isset($cart[$product->id]) ? 'display-flex' : 'display-none' }}">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                        </div>
                        <div class="inline-stock-feedback"></div>
                    </div>
                </div>
            </div>
        @empty
            <p class="padding-0-1-5rem">Tidak ada menu yang tersedia untuk outlet ini.</p>
        @endforelse
    </div>
</div>
    <div id="mini-cart-bar" class="mini-cart-bar">
        <a href="{{ route('cart.index') }}" class="mini-cart-link">
            <div class="mini-cart-info">
                <i class="fas fa-shopping-basket"></i>
                <span id="mini-cart-item-count">0 Item</span>
            </div>
            <div class="mini-cart-total">
                <span id="mini-cart-total-price">Rp 0</span>
                <span>Lihat Keranjang <i class="fas fa-arrow-right"></i></span>
            </div>
        </a>
    </div>

    <div id="descriptionModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="descriptionModalTitle"></h4>
                <button class="modal-close" id="closeDescriptionModalBtn">&times;</button>
            </div>
            <div class="modal-body">
                <p id="descriptionModalText"></p>
            </div>
        </div>
    </div>

@endsection