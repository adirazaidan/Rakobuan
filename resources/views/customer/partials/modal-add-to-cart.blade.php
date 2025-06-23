<div class="modal-overlay" id="addToCartModal">
    <div class="modal-content compact">
        {{-- Gambar akan menjadi header dari modal --}}
        <img id="modalProductImage" src="https://via.placeholder.com/400" alt="Gambar Produk" class="modal-header-image">
        
        <button class="modal-close" id="closeModalBtn">&times;</button>
        
        {{-- Semua konten teks dan form berada di dalam div ini --}}
        <div class="modal-product-info">
            <h3 id="modalProductName" class="product-title">Nama Produk</h3>
            <p id="modalProductDescription" class="product-description-modal">Deskripsi produk akan muncul di sini.</p>

            <form id="addToCartForm" class="modal-form">
                <input type="hidden" id="modalProductId" name="product_id">
                
                {{-- Grup untuk menyejajarkan label dan tombol kuantitas --}}
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
                
                <div class="form-group">
                    <label for="notes">Catatan (Opsional)</label>
                    <textarea id="notes" name="notes" rows="2" class="form-control" placeholder="Contoh: Jangan pakai bawang"></textarea>
                </div>
                
                <div class="modal-form-footer">
                    <span class="product-price-modal" id="modalProductPrice">Rp 0</span>
                    <button type="submit" class="btn-submit-cart">Tambah ke Keranjang</button>
                </div>
            </form>
        </div>
    </div>
</div>