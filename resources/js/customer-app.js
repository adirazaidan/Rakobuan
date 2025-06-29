import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;

    /**
     * =================================
     * BAGIAN SIDEBAR & LIGHTBOX (GLOBAL)
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
        if (overlay) overlay.classList.add('active');
    };
    const closeSidebar = () => {
        if (sidebarCustomer) sidebarCustomer.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
    };
    if (customerMenuToggle) customerMenuToggle.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

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
        if (lightboxCloseBtn) lightboxCloseBtn.addEventListener('click', closeLightbox);
        imageLightbox.addEventListener('click', (e) => {
            if (e.target === imageLightbox) closeLightbox();
        });
    }

    /**
     * ==========================================================
     * FUNGSI-FUNGSI PEMBANTU (HELPERS) GLOBAL
     * ==========================================================
     */
    const updateMiniCartBar = (count, total) => {
        const miniCartBar = document.getElementById('mini-cart-bar');
        const countElement = document.getElementById('mini-cart-item-count');
        const priceElement = document.getElementById('mini-cart-total-price');
        const cartPage = document.getElementById('cart-items-list');

        if (!miniCartBar || !countElement || !priceElement) return;

        if (cartPage) {
            miniCartBar.classList.remove('show');
            return;
        }

        const menuPageContainer = document.querySelector('.menu-page-container');
        if (count > 0) {
            countElement.textContent = `${count} Item`;
            priceElement.textContent = 'Rp ' + total.toLocaleString('id-ID');
            miniCartBar.classList.add('show');
            if (menuPageContainer) {
                const barHeight = miniCartBar.offsetHeight;
                menuPageContainer.style.paddingBottom = `${barHeight + 20}px`;
            }
        } else {
            miniCartBar.classList.remove('show');
            if (menuPageContainer) {
                menuPageContainer.style.paddingBottom = '2rem';
            }
        }
    };

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

    const showStockFeedback = (productCard, message) => {
        const feedbackElement = productCard.querySelector('.inline-stock-feedback');
        if (!feedbackElement) return;
        feedbackElement.textContent = message;
        feedbackElement.classList.add('show');
        setTimeout(() => {
            feedbackElement.classList.remove('show');
        }, 2500);
    };

    /**
     * ==========================================================
     * BAGIAN MODAL CATATAN (SEKARANG GLOBAL)
     * ==========================================================
     */
    const notesModal = document.getElementById('notesModal');
    const notesForm = document.getElementById('notesForm');
    const closeNotesModalBtn = document.getElementById('closeNotesModalBtn');

    const openNotesModal = (productId, productName, currentNotes) => {
        if (!notesModal) return;
        document.getElementById('notesModalProductId').value = productId;
        document.getElementById('notesModalProductName').textContent = productName;
        document.getElementById('notesModalTextArea').value = currentNotes || '';
        notesModal.style.display = 'flex';
    };

    const closeNotes = () => {
        if (notesModal) notesModal.style.display = 'none';
    };

    if (closeNotesModalBtn) closeNotesModalBtn.addEventListener('click', closeNotes);
    if (notesModal) notesModal.addEventListener('click', (e) => {
        if (e.target === notesModal) closeNotes();
    });

    if (notesForm) notesForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const productId = document.getElementById('notesModalProductId').value;
        const notes = document.getElementById('notesModalTextArea').value;

        // Handles both menu page and cart page
        let productCard = document.getElementById(`product-card-${productId}`) || document.getElementById(`cart-item-${productId}`);
        if (!productCard) return;

        const quantityDisplay = productCard.querySelector('.quantity-inline-display');
        if (!quantityDisplay) return;

        const quantity = quantityDisplay.textContent;
        const saveButton = notesForm.querySelector('button[type="submit"]');
        saveButton.textContent = 'Menyimpan...';
        saveButton.disabled = true;

        const response = await handleCartAction(productId, quantity, notes);
        if (response.success) {
            const notesButton = productCard.querySelector('.btn-edit-notes');
            if (notesButton) {
                notesButton.dataset.notes = notes;
                if (notes && notes.trim() !== '') {
                    notesButton.classList.add('has-notes');
                } else {
                    notesButton.classList.remove('has-notes');
                }
            }

            const notesTextOnCartPage = productCard.querySelector('.item-notes-text');
            if (notesTextOnCartPage) notesTextOnCartPage.textContent = notes || 'Tidak ada catatan';

            closeNotes();
        } else {
            alert('Gagal menyimpan catatan.');
        }
        saveButton.textContent = 'Simpan Catatan';
        saveButton.disabled = false;
    });

    /**
     * ==========================================================
     * BAGIAN INTERAKSI SPESIFIK HALAMAN MENU
     * ==========================================================
     */
    const menuGrid = document.getElementById('menu-list');
    if (menuGrid) {

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
                const isGenerallyAvailable = productData.isAvailable === 'true';
                const isDisabled = !isGenerallyAvailable || maxStock <= 0;
                actionWrapper.innerHTML = `<button class="btn-add-cart-initial" data-product-id="${productId}" ${isDisabled ? 'disabled' : ''} title="Tambah ke Keranjang"><i class="fas fa-shopping-cart"></i></button>`;
                if (notesButton) notesButton.style.display = 'none';
            }
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
                        updateMiniCartBar(response.cartCount, response.grandTotal);
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
                    updateMiniCartBar(response.cartCount, response.grandTotal);
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
                if (feedbackElement) feedbackElement.classList.remove('show');
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
                        updateMiniCartBar(response.cartCount, response.grandTotal);
                    } else {
                        display.textContent = quantity + 1;
                        alert(response.message || 'Gagal memperbarui item.');
                    }
                } else {
                    const response = await handleRemoveAction(productId);
                    if (response.success) {
                        updateProductCardUI(productId, 0);
                        updateMiniCartBar(response.cartCount, response.grandTotal);
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
                openNotesModal(productId, productName, notesBtn.dataset.notes);
            }

            if (zoomBtn) {
                e.stopPropagation();
                openLightbox(zoomBtn.dataset.imageUrl);
            }
        });

        const initialItemCount = menuGrid.dataset.cartItemCount || 0;
        const initialTotalPrice = menuGrid.dataset.cartTotalPrice || 0;
        updateMiniCartBar(parseInt(initialItemCount), parseInt(initialTotalPrice));

        document.querySelectorAll('.quantity-selector-inline').forEach(selector => {
            const productCard = selector.closest('.product-card');
            const display = selector.querySelector('.quantity-inline-display');
            if (productCard && display) {
                const currentQty = parseInt(display.textContent);
                const maxStock = parseInt(productCard.dataset.stock);
                if (currentQty >= maxStock) {
                    showStockFeedback(productCard, `Stok tersisa ${maxStock}`);
                }
            }
        });
    }

    /**
     * ==========================================================
     * BAGIAN INTERAKTIVITAS HALAMAN KERANJANG
     * ==========================================================
     */
    const cartPage = document.getElementById('cart-items-list');
    if (cartPage) {
        const updateCartPageTotals = () => {
            const grandTotalElement = document.getElementById('grand-total');
            let grandTotal = 0;
            const cartItems = document.querySelectorAll('.cart-item-card-new');
            cartItems.forEach(item => {
                const price = parseFloat(item.dataset.price);
                const quantityDisplay = item.querySelector('.quantity-inline-display');
                if (!quantityDisplay) return;
                const quantity = parseInt(quantityDisplay.textContent);
                const subtotal = price * quantity;
                grandTotal += subtotal;
                item.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            });
            if (grandTotalElement) {
                grandTotalElement.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            }
            document.getElementById('sidebar-cart-count').textContent = cartItems.length;
            const emptyCartMessage = document.getElementById('empty-cart-message');
            const cartSummarySticky = document.querySelector('.cart-summary-sticky');
            if (cartItems.length === 0 && emptyCartMessage) {
                if (emptyCartMessage) emptyCartMessage.style.display = 'flex';
                if (cartSummarySticky) cartSummarySticky.style.display = 'none';
            }
        };

        cartPage.addEventListener('click', async (e) => {
            const target = e.target;
            const increaseBtn = target.closest('.btn-increase-inline');
            const decreaseBtn = target.closest('.btn-decrease-inline');
            const notesBtn = target.closest('.btn-edit-notes');
            const zoomBtn = target.closest('.card-zoom-btn');

            if (increaseBtn) {
                const itemCard = increaseBtn.closest('.cart-item-card-new');
                const maxStock = parseInt(itemCard.dataset.stock);
                const display = itemCard.querySelector('.quantity-inline-display');
                let currentQty = parseInt(display.textContent);
                if (currentQty >= maxStock) {
                    const selector = increaseBtn.closest('.quantity-selector-inline');
                    selector.classList.add('shake');
                    setTimeout(() => selector.classList.remove('shake'), 500);
                    showStockFeedback(itemCard, `Stok tersisa ${maxStock}`);
                    increaseBtn.disabled = true;
                    return;
                }
                display.textContent = currentQty + 1;
                updateCartPageTotals();
                const notes = itemCard.querySelector('.item-notes-text')?.textContent || '';
                const response = await handleCartAction(itemCard.dataset.id, display.textContent, notes);
                if (!response.success) {
                    display.textContent = currentQty;
                    updateCartPageTotals();
                    alert('Gagal memperbarui keranjang.');
                }
            }

            if (decreaseBtn) {
                const itemCard = decreaseBtn.closest('.cart-item-card-new');
                const feedbackElement = itemCard.querySelector('.inline-stock-feedback');
                if (feedbackElement) feedbackElement.classList.remove('show');
                const display = itemCard.querySelector('.quantity-inline-display');
                let quantity = parseInt(display.textContent) - 1;
                const increaseBtnReference = itemCard.querySelector('.btn-increase-inline');
                if (increaseBtnReference) increaseBtnReference.disabled = false;
                if (quantity > 0) {
                    display.textContent = quantity;
                    updateCartPageTotals();
                    const notes = itemCard.querySelector('.item-notes-text')?.textContent || '';
                    await handleCartAction(itemCard.dataset.id, quantity, notes);
                } else {
                    itemCard.style.transition = 'opacity 0.3s ease';
                    itemCard.style.opacity = '0';
                    const response = await handleRemoveAction(itemCard.dataset.id);
                    if (response.success) {
                        setTimeout(() => {
                            itemCard.remove();
                            updateCartPageTotals();
                            updateMiniCartBar(response.cartCount, response.grandTotal);
                        }, 300);
                    } else {
                        itemCard.style.opacity = '1';
                        alert('Gagal menghapus item.');
                    }
                }
            }

            if (notesBtn) {
                const productId = notesBtn.dataset.productId;
                const itemCard = document.getElementById(`cart-item-${productId}`);
                const productName = itemCard.querySelector('.item-name').textContent;
                openNotesModal(productId, productName, notesBtn.dataset.notes);
            }

            if (zoomBtn) {
                e.stopPropagation();
                openLightbox(zoomBtn.dataset.imageUrl);
            }
        });

        document.querySelectorAll('.cart-item-card-new').forEach(itemCard => {
            const display = itemCard.querySelector('.quantity-inline-display');
            if (!display) return;
            const maxStock = parseInt(itemCard.dataset.stock);
            const currentQty = parseInt(display.textContent);
            if (currentQty >= maxStock) {
                const increaseBtn = itemCard.querySelector('.btn-increase-inline');
                if (increaseBtn) increaseBtn.disabled = true;
                showStockFeedback(itemCard, `Stok tersisa ${maxStock}`);
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
    const searchInput = document.getElementById('searchInput');

    if (categoryDropdown) {
        categoryDropdown.addEventListener('change', function() {
            const selectedCategoryId = this.value;
            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            filterAndSearchProducts(selectedCategoryId, searchTerm);
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const selectedCategoryId = categoryDropdown ? categoryDropdown.value : 'all';
            filterAndSearchProducts(selectedCategoryId, searchTerm);
        });
    }

    function filterAndSearchProducts(categoryId, searchTerm) {
        productCards.forEach(card => {
            const productName = (card.dataset.productName || '').toLowerCase();
            const cardCategoryId = card.dataset.categoryId;

            const categoryMatch = (categoryId === 'all' || cardCategoryId === categoryId);
            const searchMatch = productName.includes(searchTerm);

            if (categoryMatch && searchMatch) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
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
     * LOGIKA UNTUK PROSES CHECKOUT VIA AJAX
     * =================================
     */
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('.btn-checkout');
            button.textContent = 'Memproses Pesanan...';
            button.disabled = true;

            const formData = new FormData(this);

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
                    window.location.href = data.redirect_url;
                } else {
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
     * REAL-TIME LISTENERS (ECHO - GLOBAL)
     * =================================
     */
    if (typeof window.Echo !== 'undefined' && typeof appConfig !== 'undefined') {
        // Listener untuk force logout
        if (appConfig.sessionId) {
            const logoutChannel = `customer-logout.${appConfig.sessionId}`;
            console.log(`Listening for logout signal on channel: ${logoutChannel}`);
            window.Echo.channel(logoutChannel)
                .listen('.SessionCleared', (e) => {
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

        // Listener untuk update stok produk
        console.log("Listening for product stock updates...");
        window.Echo.channel('products')
            .listen('.StockUpdated', (e) => {
                console.log('StockUpdated event received!', e);
                const productCard = document.querySelector(`.product-card[data-product-id="${e.productId}"]`);

                if (productCard) {
                    console.log(`Updating UI for product ID: ${e.productId}`);
                    productCard.dataset.stock = e.newStock; // Update stok di dataset

                    const isAvailable = e.newStock > 0;
                    const outOfStockOverlay = productCard.querySelector('.out-of-stock-overlay');
                    const actionWrapper = productCard.querySelector('.cart-action-wrapper');
                    const quantityDisplay = productCard.querySelector('.quantity-inline-display');

                    if (!isAvailable) {
                        if (outOfStockOverlay) outOfStockOverlay.style.display = 'flex';
                        // Jika produk sedang ada di keranjang, kembalikan ke tombol add
                        updateProductCardUI(e.productId, 0);
                    } else {
                        if (outOfStockOverlay) outOfStockOverlay.style.display = 'none';
                        // Jika ada quantity selector, periksa apakah perlu di-enable/disable tombolnya
                        if (quantityDisplay) {
                           const increaseBtn = productCard.querySelector('.btn-increase-inline');
                           if(increaseBtn) increaseBtn.disabled = false;
                        } else {
                           const initialAddBtn = actionWrapper.querySelector('.btn-add-cart-initial');
                           if(initialAddBtn) initialAddBtn.disabled = false;
                        }
                    }
                }
            });

        // Listener untuk update meja yang tersedia (di halaman login)
        const tableDropdown = document.getElementById('dining_table_id');
        if (tableDropdown) {
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
    }
});