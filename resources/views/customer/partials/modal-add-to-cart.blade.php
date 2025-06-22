<div class="modal-overlay" id="addToCartModal">
    <div class="modal-content">
        <button class="modal-close" id="closeModalBtn">&times;</button>
        <div class="modal-body">
            <img id="modalProductImage" src="" alt="Gambar Produk">
            <div class="modal-product-details">
                <h3 id="modalProductName">Nama Produk</h3>
                <p class="price" id="modalProductPrice">Rp 0</p>
                <p class="description" id="modalProductDescription">Deskripsi produk...</p>
            </div>
        </div>
        <form id="addToCartForm" class="modal-form">
            <input type="hidden" id="modalProductId" name="product_id">
            <div class="form-group">
                <label>Jumlah</label>
                <div class="quantity-selector">
                    <button type="button" id="decreaseQty">-</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" readonly>
                    <button type="button" id="increaseQty">+</button>
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Catatan (Opsional)</label>
                <textarea id="notes" name="notes" rows="3" class="form-control" placeholder="Contoh: Jangan pakai bawang"></textarea>
            </div>
            <button type="submit" class="btn-submit">Tambah ke Keranjang</button>
        </form>
    </div>
</div>