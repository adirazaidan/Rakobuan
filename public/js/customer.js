document.addEventListener('DOMContentLoaded', function() {
    /**
     * =================================
     * BAGIAN SIDEBAR RESPONSIVE
     * =================================
     */
    const customerMenuToggle = document.getElementById('customerMenuToggle');
    const sidebarCustomer = document.querySelector('.sidebar-customer');
    let overlay = document.querySelector('.sidebar-overlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    const openSidebar = () => {
        if (sidebarCustomer) sidebarCustomer.classList.add('open');
        overlay.classList.add('active');
    };

    const closeSidebar = () => {
        if (sidebarCustomer) sidebarCustomer.classList.remove('open');
        overlay.classList.remove('active');
    };

    if (customerMenuToggle) customerMenuToggle.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);


    /**
     * =================================
     * BAGIAN MODAL TAMBAH KE KERANJANG
     * =================================
     */
    const addToCartModal = document.getElementById('addToCartModal');
    const menuList = document.getElementById('menu-list');

    if (addToCartModal && menuList) {
        // Ambil semua elemen modal sekali saja untuk efisiensi
        const closeModalBtn = document.getElementById('closeModalBtn');
        const addToCartForm = document.getElementById('addToCartForm');
        const quantityInput = document.getElementById('quantity');
        const plusBtn = document.getElementById('increaseQty');
        const minusBtn = document.getElementById('decreaseQty');
        const notesTextarea = document.getElementById('notes');
        const bungkusCheckbox = document.getElementById('bungkusCheckbox');
        
        // Ambil elemen harga dan tombol
        const modalPriceOverlayElement = document.getElementById('modalProductPriceOverlay'); // Harga di gambar
        const addToCartButton = document.querySelector('.btn-submit-cart.with-total');      // Tombol simpan

        let currentBasePrice = 0; // Variabel untuk menyimpan harga satuan produk

        // --- FUNGSI DIPERBARUI: SEKARANG HANYA MENGUBAH HARGA DI TOMBOL ---
        const updateButtonPrice = (quantity) => {
            if (addToCartButton) {
                const totalPrice = currentBasePrice * quantity;
                const formattedPrice = 'Rp ' + totalPrice.toLocaleString('id-ID');
                addToCartButton.textContent = `Tambah ke Keranjang (${formattedPrice})`;
            }
        };

        const openAddToCartModal = (button) => {
            const isBestseller = button.dataset.bestseller === '1';
            const discountPercent = button.dataset.discountPercent;
            const bestsellerBadge = document.getElementById('modalBestsellerBadge');
            const discountBadge = document.getElementById('modalDiscountBadge');
            bestsellerBadge.style.display = 'none';
            discountBadge.style.display = 'none';

            if (isBestseller) {
                bestsellerBadge.style.display = 'inline-block';
            }
            if (discountPercent && parseFloat(discountPercent) > 0) {
                discountBadge.textContent = `${discountPercent}% OFF`;
                discountBadge.style.display = 'inline-block';
            }
            currentBasePrice = parseInt(button.dataset.price);

            document.getElementById('modalProductId').value = button.dataset.id;
            document.getElementById('modalProductName').textContent = button.dataset.name;
            document.getElementById('modalProductDescription').textContent = button.dataset.description;
            document.getElementById('modalProductImage').src = button.dataset.image;
            document.getElementById('modalProductImage').alt = button.dataset.name;
            
            if (modalPriceOverlayElement) {
                modalPriceOverlayElement.textContent = 'Rp ' + currentBasePrice.toLocaleString('id-ID');
            }
        
            quantityInput.value = 1;
            notesTextarea.value = '';
            bungkusCheckbox.checked = false;
            updateButtonPrice(1); 

            addToCartModal.style.display = 'flex';

            
        };

        // Fungsi untuk menutup modal
        const closeAddToCartModal = () => addToCartModal.style.display = 'none';

        // Event listener utama pada daftar menu
        menuList.addEventListener('click', (e) => {
            const button = e.target.closest('.add-to-cart-btn');
            if (button && !button.disabled) {
                openAddToCartModal(button);
            }
        });

        closeModalBtn.addEventListener('click', closeAddToCartModal);
        addToCartModal.addEventListener('click', (e) => {
            if (e.target === addToCartModal) closeAddToCartModal();
        });

        // Event listener untuk tombol kuantitas
        plusBtn.addEventListener('click', () => {
            quantityInput.value = parseInt(quantityInput.value) + 1;
            updateButtonPrice(quantityInput.value); // Panggil fungsi update harga tombol
        });
        minusBtn.addEventListener('click', () => {
            const currentQty = parseInt(quantityInput.value);
            if (currentQty > 1) {
                quantityInput.value = currentQty - 1;
                updateButtonPrice(quantityInput.value); // Panggil fungsi update harga tombol
            }
        });

        // Event listener untuk submit form via AJAX
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const bungkusText = "(Bungkus)";
            let originalNotes = notesTextarea.value.trim();
            if (bungkusCheckbox.checked) {
                notesTextarea.value = originalNotes ? `${bungkusText} ${originalNotes}` : bungkusText;
            }
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(appConfig.routes.cartAdd, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.message) {
                    document.getElementById('sidebar-cart-count').textContent = data.cartCount;
                    alert(data.message);
                    closeAddToCartModal();
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            }).catch(error => console.error('Error:', error));
        });
    }

    /**
     * =================================
     * BAGIAN MODAL PANGGIL PELAYAN
     * =================================
     */
    const callWaiterModal = document.getElementById('callWaiterModal');
    if (callWaiterModal) {
        const closeCallModalBtn = document.getElementById('closeCallModalBtn');
        const callWaiterForm = document.getElementById('callWaiterForm');
        const callWaiterButtons = document.querySelectorAll('.call-waiter-btn');

        const openCallModal = () => callWaiterModal.style.display = 'flex';
        const closeCallModal = () => {
            callWaiterForm.reset();
            callWaiterModal.style.display = 'none';
        };

        callWaiterButtons.forEach(button => button.addEventListener('click', openCallModal));
        closeCallModalBtn.addEventListener('click', closeCallModal);
        callWaiterModal.addEventListener('click', (e) => {
            if (e.target === callWaiterModal) closeCallModal();
        });

        callWaiterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(appConfig.routes.callWaiterStore, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    closeCallModal();
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            }).catch(error => console.error('Error:', error));
        });
    }


    /**
     * =================================
     * BAGIAN FILTER MENU & PENCARIAN
     * =================================
     */
    const productCards = document.querySelectorAll('.product-card');

    // --- LOGIKA BARU UNTUK FILTER KATEGORI DROPDOWN ---
    const categoryDropdown = document.getElementById('categoryFilterDropdown');

    if (categoryDropdown) {
        categoryDropdown.addEventListener('change', function() {
            const selectedCategoryId = this.value;

            productCards.forEach(card => {
                // Tampilkan kartu jika 'Semua' dipilih atau jika ID kategori kartu cocok
                if (selectedCategoryId === 'all' || card.dataset.categoryId === selectedCategoryId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }


    // --- Logika Pencarian (tidak berubah) ---
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            productCards.forEach(card => {
                const productName = card.dataset.productName || '';
                // Tampilkan kartu jika nama produk mengandung teks pencarian
                if (productName.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    /**
     * =================================
     * BAGIAN LIGHTBOX GAMBAR
     * =================================
     */
    const imageLightbox = document.getElementById('imageLightbox');

    if (imageLightbox) {
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxCloseBtn = document.querySelector('.lightbox-close');
        const modalImageContainer = document.querySelector('.modal-header-visual');

        // Fungsi untuk membuka lightbox, menerima URL gambar sebagai parameter
        const openLightbox = (imageUrl) => {
            if (imageUrl) {
                lightboxImage.src = imageUrl;
                imageLightbox.style.display = 'flex';
            }
        };

        const closeLightbox = () => {
            imageLightbox.style.display = 'none';
        };

        // Event listener untuk tombol zoom di dalam MODAL
        if (modalImageContainer) {
            modalImageContainer.addEventListener('click', (e) => {
                if (e.target.closest('.zoom-btn')) {
                    e.stopPropagation();
                    const currentModalImageSrc = document.getElementById('modalProductImage').src;
                    openLightbox(currentModalImageSrc);
                }
            });
        }

        // Event listener untuk tombol zoom di KARTU MENU UTAMA
        if (menuList) {
            menuList.addEventListener('click', (e) => {
                const zoomButton = e.target.closest('.card-zoom-btn'); // Cari tombol zoom spesifik
                if (zoomButton) {
                    e.stopPropagation(); // Hentikan event agar tidak memicu hal lain
                    openLightbox(zoomButton.dataset.imageUrl); // Buka lightbox dengan URL dari data-attribute
                }
            });
        }

        // Event listener untuk menutup lightbox
        lightboxCloseBtn.addEventListener('click', closeLightbox);
        imageLightbox.addEventListener('click', (e) => {
            if (e.target === imageLightbox) {
                closeLightbox();
            }
        });
    }
});