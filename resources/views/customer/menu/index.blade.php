@extends('layouts.customer')

@section('title', 'Daftar Menu')

@section('content')
<style>
    /* Tambahan CSS untuk menyesuaikan tampilan filter */
    .filter-wrapper {
        display: flex;
        justify-content: flex-start; /* Sejajarkan ke kiri */
        align-items: center;
        flex-wrap: wrap; /* Izinkan wrap jika layar kecil */
        gap: 1rem;
        margin-bottom: 2rem;
        margin-top: 1rem;
    }
    .custom-select-wrapper {
        flex-grow: 1; /* Dropdown kategori mengambil ruang yang tersedia */
        min-width: 200px; /* Minimal lebar untuk dropdown */
    }
    .filter-price-buttons {
        display: flex;
        gap: 8px;
    }
    .filter-price-buttons button {
        background: none;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-muted);
        padding: 6px 10px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .filter-price-buttons button:hover {
        background-color: var(--bg-hover);
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .filter-price-buttons button.active {
        background-color: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }
    .filter-price-buttons button.active i {
        color: #fff;
    }

    /* CSS baru untuk checkbox yang lebih impresif */
    .checkbox-filter-wrapper {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    .form-check {
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    .form-check-input {
        appearance: none;
        -webkit-appearance: none;
        width: 0;
        height: 0;
        position: absolute;
    }
    .form-check-label {
        position: relative;
        padding-left: 28px; /* Ruang untuk ikon kustom */
        margin-bottom: 0;
        font-weight: 500;
        color: var(--text-muted);
        transition: color 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    /* Style untuk ikon yang tidak dicentang */
    .form-check-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        border: 2px solid var(--border-color);
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    /* Style untuk ikon yang dicentang */
    .form-check-input:checked + .form-check-label::before {
        border-color: var(--primary-color);
        background-color: var(--primary-color);
    }
    /* Style untuk ikon centang (checkmark) */
    .form-check-label::after {
        content: '\f00c'; /* FontAwesome checkmark */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%) scale(0);
        color: var(--white-color);
        font-size: 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 18px;
        height: 18px;
        transition: transform 0.2s ease-in-out;
    }
    .form-check-input:checked + .form-check-label::after {
        transform: translateY(-50%) scale(1);
    }
    /* Style untuk Best Seller dan Diskon */
    #bestsellerFilter + .form-check-label::before {
        content: '\f005'; /* Ikon bintang */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        border: none;
        background: var(--border-color);
        border-radius: 50%;
        color: var(--text-muted);
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        left: -4px;
        transform: translateY(-50%) scale(1);
    }
    #bestsellerFilter:checked + .form-check-label::before {
        background-color: var(--accent-color);
        color: var(--primary-color);
    }
    #discountFilter + .form-check-label::before {
        content: '\f02b'; /* Ikon tag */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        border: none;
        background: var(--border-color);
        border-radius: 50%;
        color: var(--text-muted);
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        left: -4px;
        transform: translateY(-50%) scale(1);
    }
    #discountFilter:checked + .form-check-label::before {
        background-color: var(--danger-color);
        color: var(--white-color);
    }
    /* Hapus ikon centang default untuk Best Seller & Diskon */
    #bestsellerFilter + .form-check-label::after,
    #discountFilter + .form-check-label::after {
        content: none;
    }
</style>
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initialItemCount = {{ $cartItemCount ?? 0 }};
            const initialTotalPrice = {{ $cartTotalPrice ?? 0 }};

            if (typeof updateMiniCartBar === 'function') {
                updateMiniCartBar(initialItemCount, initialTotalPrice);
            }
            
            // Logika baru untuk filter
            const searchInput = document.getElementById('searchInput');
            const categoryFilterDropdown = document.getElementById('categoryFilterDropdown');
            const priceFilterButtons = document.querySelectorAll('.filter-price-buttons button');
            const bestsellerFilter = document.getElementById('bestsellerFilter');
            const discountFilter = document.getElementById('discountFilter');
            const menuGrid = document.getElementById('menu-list');
            const allProductCards = document.querySelectorAll('.product-card');

            let currentPriceSortOrder = null;

            function filterAndSortProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilterDropdown.value;
                const isBestsellerChecked = bestsellerFilter.checked;
                const isDiscountChecked = discountFilter.checked;
                
                const productCardsArray = Array.from(allProductCards);
                let filteredProducts = productCardsArray.filter(card => {
                    const productName = card.getAttribute('data-product-name');
                    const productCategoryId = card.getAttribute('data-category-id');
                    const isBestseller = card.getAttribute('data-is-bestseller') === 'true';
                    const hasDiscount = card.getAttribute('data-has-discount') === 'true';
                    
                    const matchesSearch = productName.includes(searchTerm);
                    const matchesCategory = selectedCategory === 'all' || productCategoryId === selectedCategory;
                    const matchesBestseller = !isBestsellerChecked || isBestseller;
                    const matchesDiscount = !isDiscountChecked || hasDiscount;
                    
                    return matchesSearch && matchesCategory && matchesBestseller && matchesDiscount;
                });
                
                if (currentPriceSortOrder) {
                    filteredProducts.sort((a, b) => {
                        const priceA = parseFloat(a.getAttribute('data-price'));
                        const priceB = parseFloat(b.getAttribute('data-price'));
                        if (currentPriceSortOrder === 'asc') {
                            return priceA - priceB;
                        } else {
                            return priceB - priceA;
                        }
                    });
                }
                
                menuGrid.innerHTML = '';
                
                if (filteredProducts.length > 0) {
                    filteredProducts.forEach(card => {
                        menuGrid.appendChild(card);
                        card.style.display = 'block';
                    });
                } else {
                    menuGrid.innerHTML = '<p style="padding: 0 1.5rem;">Tidak ada menu yang sesuai dengan filter yang dipilih.</p>';
                }
            }

            // Tambahkan event listener untuk setiap filter
            searchInput.addEventListener('input', filterAndSortProducts);
            categoryFilterDropdown.addEventListener('change', filterAndSortProducts);
            bestsellerFilter.addEventListener('change', filterAndSortProducts);
            discountFilter.addEventListener('change', filterAndSortProducts);
            
            // Event listener untuk tombol filter harga
            priceFilterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const sortOrder = this.getAttribute('data-sort-order');
                    
                    if (this.classList.contains('active')) {
                        this.classList.remove('active');
                        currentPriceSortOrder = null;
                    } else {
                        priceFilterButtons.forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        currentPriceSortOrder = sortOrder;
                    }
                    filterAndSortProducts();
                });
            });
        });
    </script>
    @endpush

@endsection