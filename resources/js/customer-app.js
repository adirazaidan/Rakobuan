import './bootstrap';

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
     * BAGIAN LIGHTBOX GAMBAR (GLOBAL)
     * =================================
     */
    const imageLightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const openLightbox = (imageUrl) => {
        if (imageLightbox && lightboxImage && imageUrl) {
            lightboxImage.src = imageUrl;
            imageLightbox.style.display = 'flex';
        }
    };
    const closeLightbox = () => {
        if (imageLightbox) imageLightbox.style.display = 'none';
    };
    if (imageLightbox) {
        const lightboxCloseBtn = document.querySelector('.lightbox-close');
        if(lightboxCloseBtn) lightboxCloseBtn.addEventListener('click', closeLightbox);
        imageLightbox.addEventListener('click', (e) => {
            if (e.target === imageLightbox) closeLightbox();
        });
    }

    /**
     * =================================
     * BAGIAN INTERAKSI MENU
     * =================================
     */
    const menuGrid = document.getElementById('menu-list');
    if (menuGrid) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const notesModal = document.getElementById('notesModal');
        const notesForm = document.getElementById('notesForm');
        const closeNotesModalBtn = document.getElementById('closeNotesModalBtn');
        
        const handleCartAction = (productId, quantity, notes = null, isAdding = false) => {
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            if (notes !== null) {
                formData.append('notes', notes);
            }
            let url = `/cart/update/${productId}`;
            let method = 'POST';
            formData.append('_method', 'PATCH');
            if (isAdding) {
                url = '/cart/add';
                formData.delete('_method');
            }
            return fetch(url, {
                method: method,
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            }).then(response => response.json());
        };
        const handleRemoveAction = (productId) => {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            return fetch(`/cart/remove/${productId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            }).then(response => response.json());
        };

        const updateProductCardUI = (productId, newQuantity) => {
            const productCard = document.getElementById(`product-card-${productId}`);
            if (!productCard) return;
            const actionWrapper = productCard.querySelector('.cart-action-wrapper');
            const notesButton = productCard.querySelector('.btn-edit-notes');
            const productData = productCard.dataset;
            const maxStock = parseInt(productData.stock);
            if (newQuantity > 0) {
                const isMax = newQuantity >= maxStock;
                actionWrapper.innerHTML = `
                    <div class="quantity-selector-inline" data-product-id="${productId}">
                        <button class="btn-quantity-inline btn-decrease-inline">-</button>
                        <span class="quantity-inline-display">${newQuantity}</span>
                        <button class="btn-quantity-inline btn-increase-inline" ${isMax ? 'disabled' : ''}>+</button>
                    </div>`;
                if (notesButton) notesButton.style.display = 'block';
            } else {
                const isDisabled = productData.available === 'false' || maxStock <= 0;
                actionWrapper.innerHTML = `<button class="btn-add-cart-initial" data-product-id="${productId}" ${isDisabled ? 'disabled' : ''} title="Tambah ke Keranjang"><i class="fas fa-shopping-cart"></i></button>`;
                if (notesButton) notesButton.style.display = 'none';
            }
        };
        const showStockFeedback = (productCard, message) => {
            const feedbackElement = productCard.querySelector('.inline-stock-feedback');
            if (!feedbackElement) return;
            feedbackElement.textContent = message;
            feedbackElement.classList.add('show');
            setTimeout(() => {
                feedbackElement.classList.remove('show');
            }, 2500);
        };
        
        menuGrid.addEventListener('click', async (e) => {
            const target = e.target;
            const initialAddBtn = target.closest('.btn-add-cart-initial');
            const increaseBtn = target.closest('.btn-increase-inline');
            const decreaseBtn = target.closest('.btn-decrease-inline');
            const notesBtn = target.closest('.btn-edit-notes');
            const zoomBtn = target.closest('.card-zoom-btn');

            if (initialAddBtn) {
                const productId = initialAddBtn.dataset.productId;
                const productCard = initialAddBtn.closest('.product-card');
                const maxStock = parseInt(productCard.dataset.stock);

                updateProductCardUI(productId, 1);

                if (maxStock <= 1) {
                    showStockFeedback(productCard, `Stok tersisa ${maxStock}`);
                }

                try {
                    const response = await handleCartAction(productId, 1, '', true);
                    if (response.success) {
                        document.getElementById('sidebar-cart-count').textContent = response.cartCount;
                    } else {
                        alert(response.message || 'Gagal menambahkan item.');
                        updateProductCardUI(productId, 0);
                    }
                } catch (error) {
                    console.error("Cart action failed:", error);
                    alert('Terjadi kesalahan jaringan. Gagal menambahkan item.');
                    updateProductCardUI(productId, 0);
                }
            }

            if (increaseBtn) {
                const productCard = increaseBtn.closest('.product-card');
                const maxStock = parseInt(productCard.dataset.stock);
                const selector = increaseBtn.closest('.quantity-selector-inline');
                const display = selector.querySelector('.quantity-inline-display');
                let currentQty = parseInt(display.textContent);

                if (currentQty >= maxStock) {
                    selector.classList.add('shake');
                    setTimeout(() => selector.classList.remove('shake'), 500);
                    showStockFeedback(productCard, `Stok tersisa ${maxStock}`);
                    increaseBtn.disabled = true;
                    return;
                }

                let newQuantity = currentQty + 1;
                display.textContent = '...';
                const response = await handleCartAction(selector.dataset.productId, newQuantity);
                if (response.success) {
                    display.textContent = newQuantity;
                    document.getElementById('sidebar-cart-count').textContent = response.cartCount;
                    if (newQuantity >= maxStock) {
                        increaseBtn.disabled = true;
                        showStockFeedback(productCard, `Stok tersisa ${maxStock}`);
                    }
                } else {
                    display.textContent = currentQty;
                    alert(response.message || 'Gagal memperbarui item.');
                }
            }
            
            if (decreaseBtn) {
                const productCard = decreaseBtn.closest('.product-card');
                const feedbackElement = productCard.querySelector('.inline-stock-feedback');
                if (feedbackElement) {
                    feedbackElement.classList.remove('show');
                }
                const selector = decreaseBtn.closest('.quantity-selector-inline');
                const productId = selector.dataset.productId;
                const display = selector.querySelector('.quantity-inline-display');
                let quantity = parseInt(display.textContent) - 1;
                const increaseBtnReference = selector.querySelector('.btn-increase-inline');
                if (increaseBtnReference) increaseBtnReference.disabled = false;
                
                display.textContent = '...';
                if (quantity > 0) {
                    const response = await handleCartAction(productId, quantity);
                    if (response.success) {
                        display.textContent = quantity;
                        document.getElementById('sidebar-cart-count').textContent = response.cartCount;
                    } else {
                        display.textContent = quantity + 1;
                        alert(response.message || 'Gagal memperbarui item.');
                    }
                } else {
                    const response = await handleRemoveAction(productId);
                    if (response.success) {
                        updateProductCardUI(productId, 0);
                        document.getElementById('sidebar-cart-count').textContent = response.cartCount;
                    } else {
                        display.textContent = 1;
                        alert(response.message || 'Gagal menghapus item.');
                    }
                }
            }
            
            if (notesBtn) {
                const productId = notesBtn.dataset.productId;
                const productCard = document.getElementById(`product-card-${productId}`);
                const productName = productCard.querySelector('.product-name').textContent;
                document.getElementById('notesModalProductId').value = productId;
                document.getElementById('notesModalProductName').textContent = productName;
                document.getElementById('notesModalTextArea').value = notesBtn.dataset.notes || '';
                notesModal.style.display = 'flex';
            }

            if (zoomBtn) {
                e.stopPropagation();
                openLightbox(zoomBtn.dataset.imageUrl);
            }
        });

        const closeNotes = () => {
            if(notesModal) notesModal.style.display = 'none';
        };
        if(closeNotesModalBtn) closeNotesModalBtn.addEventListener('click', closeNotes);
        if(notesModal) notesModal.addEventListener('click', (e) => {
            if(e.target === notesModal) closeNotes();
        });

        if(notesForm) notesForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const productId = document.getElementById('notesModalProductId').value;
            const notes = document.getElementById('notesModalTextArea').value;
            const productCard = document.getElementById(`product-card-${productId}`);
            const quantityDisplay = productCard.querySelector('.quantity-inline-display');
            if (!quantityDisplay) return;

            const quantity = quantityDisplay.textContent;
            const saveButton = notesForm.querySelector('button[type="submit"]');
            saveButton.textContent = 'Menyimpan...';
            saveButton.disabled = true;

            const response = await handleCartAction(productId, quantity, notes);
            if (response.success) {
                const notesButton = productCard.querySelector('.btn-edit-notes');
                notesButton.dataset.notes = notes;
                if (notes && notes.trim() !== '') {
                    notesButton.classList.add('has-notes');
                } else {
                    notesButton.classList.remove('has-notes');
                }
                closeNotes();
            } else {
                alert('Gagal menyimpan catatan.');
            }
            saveButton.textContent = 'Simpan Catatan';
            saveButton.disabled = false;
        });

        document.querySelectorAll('.quantity-selector-inline').forEach(selector => {
            const productCard = selector.closest('.product-card');
            const display = selector.querySelector('.quantity-inline-display');
            if(productCard && display) {
                const currentQty = parseInt(display.textContent);
                const maxStock = parseInt(productCard.dataset.stock);
                if (currentQty >= maxStock) {
                    showStockFeedback(productCard, `Stok tersisa ${maxStock}`);
                }
            }
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
    const categoryDropdown = document.getElementById('categoryFilterDropdown');
    if (categoryDropdown) {
        categoryDropdown.addEventListener('change', function() {
            const selectedCategoryId = this.value;

            productCards.forEach(card => {
                if (selectedCategoryId === 'all' || card.dataset.categoryId === selectedCategoryId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            productCards.forEach(card => {
                const productName = card.dataset.productName || '';
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
     * BAGIAN INTERAKTIVITAS HALAMAN KERANJANG 
     * =================================
     */
    const cartPage = document.getElementById('cart-items-list');
    if (cartPage) {
        const grandTotalElement = document.getElementById('grand-total');
        const checkoutButton = document.querySelector('.btn-checkout');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const debounce = (func, delay = 500) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        };

        const updateCartOnServer = (itemCard) => {
            const productId = itemCard.dataset.id;
            const quantityInput = itemCard.querySelector('.quantity-input');
            const notesInput = itemCard.querySelector('.item-notes-input');
            const updateButton = itemCard.querySelector('.btn-update-cart');
            
            if (updateButton) {
                updateButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                updateButton.disabled = true;
            }

            const formData = new FormData();
            formData.append('quantity', quantityInput.value);
            formData.append('notes', notesInput.value);
            formData.append('_method', 'PATCH'); 

            fetch(`/cart/update/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Session updated successfully for product:", productId);
                    const sidebarCartCount = document.getElementById('sidebar-cart-count');
                    if (sidebarCartCount) {
                        sidebarCartCount.textContent = data.cartCount;
                    }
                    if (updateButton) {
                        updateButton.innerHTML = '<i class="fas fa-sync-alt"></i> Update';
                        updateButton.disabled = false;
                    }
                } else {
                    alert(data.message || 'Gagal memperbarui keranjang.');
                }
            })
            .catch(error => console.error('Error updating cart:', error));
        };

        const debouncedUpdate = debounce(updateCartOnServer);

        const validateItem = (itemCard) => {
            const quantityInput = itemCard.querySelector('.quantity-input');
            const warningElement = itemCard.querySelector('.stock-warning');
            const maxStock = parseInt(itemCard.dataset.stock);
            const currentQty = parseInt(quantityInput.value);
            let isValid = true;

            if (currentQty > maxStock) {
                warningElement.textContent = `Stok hanya tersedia ${maxStock}`;
                warningElement.style.display = 'block';
                quantityInput.classList.add('is-invalid');
                isValid = false;
            } else {
                warningElement.textContent = '';
                warningElement.style.display = 'none';
                quantityInput.classList.remove('is-invalid');
            }
            return isValid;
        };

        const validateAllItems = () => {
            let isCartValid = true;
            const cartItems = document.querySelectorAll('.cart-item-card-new');
            cartItems.forEach(item => {
                if (!validateItem(item)) {
                    isCartValid = false;
                }
            });

            if (checkoutButton) {
                checkoutButton.disabled = !isCartValid;
            }
        };

        const updateAllPrices = () => {
            let grandTotal = 0;
            const cartItems = document.querySelectorAll('.cart-item-card-new');
            
            cartItems.forEach(item => {
                const basePrice = parseFloat(item.dataset.price);
                const quantity = parseInt(item.querySelector('.quantity-input').value);
                const subtotal = basePrice * quantity;
                grandTotal += subtotal;
                item.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            });
            
            if (grandTotalElement) {
                grandTotalElement.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            }
        };

        cartPage.addEventListener('click', (e) => {
            const target = e.target;
            const itemCard = target.closest('.cart-item-card-new');
            if (!itemCard) return;

            const quantityInput = itemCard.querySelector('.quantity-input');
            let currentQty = parseInt(quantityInput.value);
            let quantityChanged = false;

            if (target.classList.contains('btn-increase')) {
                quantityInput.value = currentQty + 1;
                quantityChanged = true;
            }
            
            if (target.classList.contains('btn-decrease')) {
                if (currentQty > 1) {
                    quantityInput.value = currentQty - 1;
                    quantityChanged = true;
                }
            }

            if (quantityChanged) {
                updateAllPrices();         
                validateAllItems();        
                debouncedUpdate(itemCard); 
            }
        });

        cartPage.addEventListener('submit', function(e) {
            if (e.target.classList.contains('item-update-form')) {
                e.preventDefault();
                const form = e.target;
                const itemCard = form.closest('.cart-item-card-new');
                updateCartOnServer(itemCard);
            }
        });
        
        validateAllItems();
    }

    /**
     * =================================
     * FIX STICKY FOOTER OVERLAP (HALAMAN KERANJANG)
     * =================================
     */
    const stickyCartSummary = document.querySelector('.cart-summary-sticky');
    const cartPageContainer = document.querySelector('.cart-page-container');
    if (stickyCartSummary && cartPageContainer) {
        const footerHeight = stickyCartSummary.offsetHeight; 
        cartPageContainer.style.paddingBottom = footerHeight + 20 + 'px'; 
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
        
        window.Echo.channel(channelName)
            .listen('SessionCleared', (e) => {
                console.log('Logout signal received from admin!', e);
                alert('Sesi Anda untuk meja ini telah dihentikan oleh admin. Anda akan dikembalikan ke halaman login.');
                
                const logoutForm = document.getElementById('logout-form');
                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    // Fallback jika form tidak ditemukan
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
    
    /**
     * =================================
     * REAL-TIME UPDATE UNTUK STOK PRODUK
     * =================================
     */
    if (typeof window.Echo !== 'undefined') {
        console.log("Listening for product stock updates...");

        window.Echo.channel('products')
            .listen('.StockUpdated', (e) => {
                console.log('StockUpdated event received!', e);
                const productCard = document.querySelector(`.product-card[data-product-id="${e.productId}"]`);

                if (productCard) {
                    console.log(`Updating UI for product ID: ${e.productId}`);

                    const addButton = productCard.querySelector('.add-to-cart-btn');
                    const outOfStockOverlay = productCard.querySelector('.out-of-stock-overlay');
                    if (!e.isAvailable) {
                        if (addButton) {
                            addButton.disabled = true;
                        }
                        if (outOfStockOverlay) {
                            outOfStockOverlay.style.display = 'flex'; 
                        }
                    } else {
                        if (addButton) {
                            addButton.disabled = false;
                        }
                        if (outOfStockOverlay) {
                            // Sembunyikan overlay "Habis"
                            outOfStockOverlay.style.display = 'none';
                        }
                    }
                }
            });
    }



});