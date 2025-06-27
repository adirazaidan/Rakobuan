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

        const openLightbox = (imageUrl) => {
            if (imageUrl) {
                lightboxImage.src = imageUrl;
                imageLightbox.style.display = 'flex';
            }
        };

        const closeLightbox = () => {
            imageLightbox.style.display = 'none';
        };

        // Event listener untuk tombol zoom di MODAL
        if (modalImageContainer) {
            modalImageContainer.addEventListener('click', (e) => {
                if (e.target.closest('.zoom-btn')) {
                    e.stopPropagation();
                    const currentModalImageSrc = document.getElementById('modalProductImage').src;
                    openLightbox(currentModalImageSrc);
                }
            });
        }

        // Event listener untuk tombol zoom di KARTU MENU dan KERANJANG
        if (menuList || document.querySelector('.cart-items-list')) {
            const container = menuList || document.querySelector('.cart-items-list');
            container.addEventListener('click', (e) => {
                const zoomButton = e.target.closest('.card-zoom-btn');
                if (zoomButton) {
                    e.stopPropagation();
                    openLightbox(zoomButton.dataset.imageUrl);
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

    // LOGIKA TOMBOL SUNTING DI KERANJANG
    const cartItemList = document.querySelector('.cart-items-list');
    if (cartItemList) {
        cartItemList.addEventListener('click', (e) => {
            const editButton = e.target.closest('.btn-edit-cart');
            if (editButton) {
                const itemId = editButton.dataset.itemId;
                console.log(`Tombol Sunting diklik untuk item ID: ${itemId}`);
                // Di sini Anda bisa menambahkan logika sebenarnya untuk menampilkan form sunting
                // Misalnya, menampilkan modal atau mengubah tampilan item untuk diedit.
            }
        });
    }

    /**
     * =================================
     * BAGIAN INTERAKTIVITAS HALAMAN KERANJANG
     * =================================
     */
    const cartPage = document.getElementById('cart-items-list');

    if (cartPage) {
        const grandTotalElement = document.getElementById('grand-total');

        // Fungsi untuk menghitung ulang semua harga secara visual
        const updateAllPrices = () => {
            let grandTotal = 0;
            const cartItems = document.querySelectorAll('.cart-item-card-new');
            
            cartItems.forEach(item => {
                const basePrice = parseFloat(item.dataset.price);
                const quantity = parseInt(item.querySelector('.quantity-input').value);
                const subtotal = basePrice * quantity;
                grandTotal += subtotal;
                
                // Update sub-total per item
                item.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            });

            // Update grand total di bagian bawah
            grandTotalElement.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        };

        // Event listener untuk seluruh area item keranjang
        cartPage.addEventListener('click', (e) => {
            const target = e.target;
            const itemCard = target.closest('.cart-item-card-new');
            if (!itemCard) return;

            const quantityInput = itemCard.querySelector('.quantity-input');
            let currentQty = parseInt(quantityInput.value);

            // Logika untuk tombol +
            if (target.classList.contains('btn-increase')) {
                quantityInput.value = currentQty + 1;
                updateAllPrices(); // Update tampilan harga secara instan
            }
            // Logika untuk tombol -
            if (target.classList.contains('btn-decrease')) {
                if (currentQty > 1) {
                    quantityInput.value = currentQty - 1;
                    updateAllPrices(); // Update tampilan harga secara instan
                }
            }
        });

        // Event listener untuk form update (AJAX)
        cartPage.addEventListener('submit', function(e) {
            if (e.target.classList.contains('item-update-form')) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const productId = form.closest('.cart-item-card-new').dataset.id;
                const url = `/cart/update/${productId}`; // Bangun URL secara dinamis
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Tambahkan method PATCH secara manual untuk FormData
                formData.append('_method', 'PATCH');
                
                form.querySelector('.btn-update-cart').textContent = 'Menyimpan...';

                fetch(url, {
                    method: 'POST', // Form method spoofing
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Update total di keranjang sidebar
                        document.getElementById('sidebar-cart-count').textContent = data.cartCount;
                        // Kembalikan teks tombol ke semula
                        form.querySelector('.btn-update-cart').innerHTML = '<i class="fas fa-sync-alt"></i> Update';
                        // Optional: tampilkan notifikasi sukses kecil
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }

    /**
     * =================================
     * FIX STICKY FOOTER OVERLAP (HALAMAN KERANJANG)
     * =================================
     */
    // Cek apakah kita berada di halaman yang memiliki sticky footer dan kontainer keranjang
    const stickyCartSummary = document.querySelector('.cart-summary-sticky');
    const cartPageContainer = document.querySelector('.cart-page-container');

    if (stickyCartSummary && cartPageContainer) {
        // Ukur tinggi elemen sticky footer secara akurat
        const footerHeight = stickyCartSummary.offsetHeight; 

        // Terapkan tinggi tersebut sebagai padding-bottom pada kontainer utama halaman keranjang
        cartPageContainer.style.paddingBottom = footerHeight + 20 + 'px'; // Ditambah 20px sebagai spasi ekstra

        // Log ini untuk debugging, Anda bisa melihatnya di Console (F12)
        console.log(`Sticky footer height detected: ${footerHeight}px. Padding bottom applied.`);
    }

    /**
     * =================================
     * LISTENER UNTUK FORCE LOGOUT DARI ADMIN
     * =================================
     */
    if (typeof window.Echo !== 'undefined' && appConfig.sessionId) {
        const channelName = `customer-logout.${appConfig.sessionId}`;
        console.log(`Listening for logout signal on public channel: ${channelName}`);

        // Dengarkan di channel PUBLIK dengan nama yang unik
        window.Echo.channel(channelName)
            .listen('SessionCleared', (e) => {
                console.log('Logout signal received from admin!', e);
                alert('Sesi Anda untuk meja ini telah dihentikan oleh admin. Anda akan dikembalikan ke halaman login.');
                
                const logoutForm = document.getElementById('logout-form');
                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    window.location.href = '/'; 
                }
            });
    }

    /**
 * =================================
 * REAL-TIME UPDATE UNTUK DROPDOWN MEJA LOGIN
 * =================================
 */
const tableDropdown = document.getElementById('dining_table_id');

if (tableDropdown && typeof window.Echo !== 'undefined') {
    console.log("Login page detected. Listening for table status updates...");

    const updateTableDropdown = () => {
        fetch(appConfig.routes.getAvailableTables)
            .then(response => response.text())
            .then(html => {
                tableDropdown.innerHTML = html;
                console.log("Table dropdown updated via real-time.");
            });
    };

    window.Echo.channel('tables-status')
        .listen('.AvailableTablesUpdated', (e) => {
            console.log('AvailableTablesUpdated event received!', e);
            updateTableDropdown();
        });


    }

       /**
     * =================================
     * LOGIKA UNTUK PROSES CHECKOUT VIA AJAX
     * =================================
     */
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Hentikan submit form biasa

            const button = this.querySelector('.btn-checkout');
            button.textContent = 'Memproses Pesanan...';
            button.disabled = true;

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Jika sukses, arahkan ke URL resi dari server
                    window.location.href = data.redirect_url;
                } else {
                    // Jika gagal, tampilkan pesan error dan aktifkan kembali tombolnya
                    alert(data.message || 'Terjadi kesalahan saat memproses pesanan.');
                    button.textContent = 'Kirim Orderan ke Dapur';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Checkout Error:', error);
                alert('Terjadi kesalahan teknis. Silakan coba lagi.');
                button.textContent = 'Kirim Orderan ke Dapur';
                button.disabled = false;
            });
        });
    } 

});