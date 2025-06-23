<div class="modal-overlay" id="addToCartModal">
    <div class="modal-content visual-cart">
        <div class="modal-header-visual">
            <img id="modalProductImage" src="https://via.placeholder.com/400" alt="Gambar Produk">
            <button id="zoomImageBtn" class="zoom-btn" title="Perbesar gambar">
                <i class="fas fa-expand"></i>
            </button>
            <span id="modalBestsellerBadge" class="bestseller-badge modal-badge" style="display: none;">Best Seller</span>
            <span id="modalDiscountBadge" class="discount-badge modal-badge" style="display: none;">Diskon 0%</span>
            
            <div class="product-info-overlay">
                <h3 id="modalProductName" class="product-title-overlay">Nama Produk</h3>
                <p class="product-price-overlay" id="modalProductPriceOverlay">Rp 0</p>
            </div>
            
            <button class="modal-close" id="closeModalBtn">&times;</button>
        </div>

        <div class="modal-body-compact">
            <p id="modalProductDescription" class="product-description-modal"></p>

            <form id="addToCartForm" class="modal-form">
                <input type="hidden" id="modalProductId" name="product_id">

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" id="bungkusCheckbox" class="form-check-input">
                        <label for="bungkusCheckbox" class="form-check-label">Bungkus (Takeaway)</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan (Opsional)</label>
                    <textarea id="notes" name="notes" rows="2" class="form-control" placeholder="Contoh: Tidak pedas"></textarea>
                </div>

                <div class="form-group quantity-group">
                    <label for="quantity">Jumlah</label>
                    <div class="quantity-selector">
                        <button type="button" class="btn-quantity" id="decreaseQty">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="quantity" name="quantity" class="quantity-input" value="1" min="1" readonly>
                        <button type="button" class="btn-quantity" id="increaseQty">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit-cart with-total">Tambah ke Keranjang (Rp 0)</button>
            </form>
        </div>
    </div>
</div>