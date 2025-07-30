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
            if (notesTextOnCartPage) notesTextOnCartPage.textContent = notes || 'Tidak Ada Catatan';
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

        cartPage.addEventListener('change', async (e) => {
            const target = e.target;
            if (target.classList.contains('bungkus-checkbox')) {
                const itemCard = target.closest('.cart-item-card-new');
                if (!itemCard) return;

                const productId = itemCard.dataset.id;
                const quantity = itemCard.querySelector('.quantity-inline-display').textContent;
                const notesButton = itemCard.querySelector('.btn-edit-notes');
                let currentNotes = notesButton ? notesButton.dataset.notes : '';
                const takeawayText = '(Bungkus)';
                let newNotes = '';

                currentNotes = currentNotes.replace(takeawayText, '').trim();

                if (target.checked) {
                    newNotes = currentNotes ? `${takeawayText} ${currentNotes}` : takeawayText;
                } else {
                    newNotes = currentNotes;
                }

                const response = await handleCartAction(productId, quantity, newNotes);

                if (response.success) {
                    const notesTextElement = itemCard.querySelector('.item-notes-text');
                    if (notesTextElement) {
                        notesTextElement.textContent = newNotes || 'Tidak Ada Catatan';
                    }
                    if (notesButton) {
                        notesButton.dataset.notes = newNotes;
                    }
                } else {
                    target.checked = !target.checked;
                    alert('Gagal memperbarui opsi bungkus.');
                }
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

        window.addEventListener('pageshow', function (event) {
            // event.persisted bernilai true jika halaman dimuat dari cache
            if (event.persisted) {
                window.location.reload();
            }
        });
        
    }

    /**
     * =================================
     * BAGIAN MODAL PANGGIL PELAYAN (REVISI DENGAN EVENT DELEGATION)
     * =================================
     */
    const callWaiterModal = document.getElementById('callWaiterModal');
    if (callWaiterModal) {
        const closeCallModalBtn = callWaiterModal.querySelector('.modal-close');
        const callWaiterForm = document.getElementById('callWaiterForm');
        
        const openCallModal = () => {
            callWaiterModal.style.display = 'flex';
        };
        
        const closeCallModal = () => {
            if (callWaiterForm) callWaiterForm.reset();
            callWaiterModal.style.display = 'none';
        };

        document.body.addEventListener('click', function(e) {
            if (e.target.matches('.call-waiter-btn') || e.target.closest('.call-waiter-btn')) {
                e.preventDefault();
                openCallModal();
            }
        });

        if(closeCallModalBtn) closeCallModalBtn.addEventListener('click', closeCallModal);
        
        callWaiterModal.addEventListener('click', (e) => {
            if (e.target === callWaiterModal) closeCallModal();
        });

        if(callWaiterForm) callWaiterForm.addEventListener('submit', function(e) {
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
     * ========================================================
     * FIX STICKY ACTIONS OVERLAP (HALAMAN STATUS PESANAN)
     * ========================================================
     */
    const stickyStatusActions = document.querySelector('.status-page-actions');
    const statusPageContainer = document.querySelector('.order-status-page-container');

    if (stickyStatusActions && statusPageContainer) {
        const actionsHeight = stickyStatusActions.offsetHeight;
        statusPageContainer.style.paddingBottom = `${actionsHeight + 20}px`;
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
                    button.textContent = 'Kirim Orderan';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Checkout Error:', error);
                alert('Terjadi kesalahan teknis. Silakan coba lagi.');
                button.textContent = 'Kirim Orderan';
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

        console.log("Listening for product stock updates...");
        window.Echo.channel('products')
            .listen('ProductStockUpdated', (e) => {
                console.log('ProductStockUpdated event received!', e);
                
                const productCard = document.getElementById(`product-card-${e.productId}`);
                if (!productCard) return;

                productCard.dataset.stock = e.newStock;
                productCard.dataset.isAvailable = e.isAvailable;

                const outOfStockOverlay = productCard.querySelector('.out-of-stock-overlay');
                const quantityDisplay = productCard.querySelector('.quantity-inline-display');
                const increaseBtn = productCard.querySelector('.btn-increase-inline');
                const initialAddBtn = productCard.querySelector('.btn-add-cart-initial');

                if (!e.isAvailable) {
                    if (outOfStockOverlay) outOfStockOverlay.style.display = 'flex';

                    if (increaseBtn) increaseBtn.disabled = true;
                    if (initialAddBtn) initialAddBtn.disabled = true;

                } else {
                    if (outOfStockOverlay) outOfStockOverlay.style.display = 'none';

                    if (quantityDisplay) {
                        const currentQty = parseInt(quantityDisplay.textContent);
                        if (increaseBtn && currentQty < e.newStock) {
                            increaseBtn.disabled = false;
                        }
                    } else if (initialAddBtn) {
                        initialAddBtn.disabled = false;
                    }
                }
            });

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
                .listen('AvailableTablesUpdated', (e) => {
                    console.log('AvailableTablesUpdated event received!', e);
                    updateTableDropdown();
                });
        }
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
                    window.location.href = '/'; 
                }
            });
    }

    /**
     * ==========================================================
     * TIMER LOGOUT OTOMATIS (VERSI FINAL & ANDAL)
     * ==========================================================
     */
    if (typeof appConfig !== 'undefined' && appConfig.loginTimestamp !== null) {
        const sessionTimeoutInSeconds = appConfig.sessionLifetime;
        const loginTime = appConfig.loginTimestamp;
        const tableId = appConfig.tableId;

        const checkSession = () => {
            const nowInSeconds = Math.floor(Date.now() / 1000);
            const elapsedTimeInSeconds = nowInSeconds - loginTime;

            if (elapsedTimeInSeconds >= sessionTimeoutInSeconds) {
                clearInterval(sessionTimer);
                alert('Sesi Anda telah berakhir. Anda akan di-logout.');
                
                if(tableId) {
                    window.location.href = `/logout/${tableId}`;
                } else {
                    window.location.href = "{{ route('customer.logout') }}";
                }
            }
        };

        const sessionTimer = setInterval(checkSession, 30000);
    }


    /**
     * ==========================================================
     * BAGIAN REAL-TIME STATUS DI HALAMAN STATUS PESANAN (FINAL)
     * ==========================================================
     */
    const statusPage = document.querySelector('.order-status-page-container');
    if (statusPage && typeof window.Echo !== 'undefined') {

        const anyCardWithSession = document.querySelector('[data-order-session-id], [data-call-session-id]');

        if (anyCardWithSession) {
            const sessionId = anyCardWithSession.dataset.orderSessionId || anyCardWithSession.dataset.callSessionId;

            const orderChannelName = `order-status.${sessionId}`;
            console.log(`Listening for order events on: ${orderChannelName}`);
            window.Echo.channel(orderChannelName)
                .listen('OrderStatusUpdated', (e) => { 
                    console.log('Order status updated!', e.order);
                    const statusBadge = document.getElementById(`order-status-badge-${e.order.id}`);
                    if (statusBadge) {
                        statusBadge.textContent = e.order.translated_status;
                        statusBadge.className = `status-badge status-${e.order.status}`;
                    }
                });
            const callChannelName = `customer-session.${sessionId}`; 
            console.log(`Listening for call events on: ${callChannelName}`);
            window.Echo.channel(callChannelName)
                .listen('.call-received', (e) => { 
                    console.log('New call data received (CallReceived event)!', e.call);
                    const orderList = document.querySelector('.order-list');
                    const emptyStatus = document.querySelector('.empty-status');
                    if (orderList) {
                        if (emptyStatus) emptyStatus.style.display = 'none';

                        const callCard = document.createElement('div');
                        callCard.className = 'status-card call-card';
                        callCard.dataset.callSessionId = e.call.session_id;

                        const callTime = new Date(e.call.created_at).toLocaleString('id-ID', {
                            day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        });

                        const callStatus = e.call.translated_status || (e.call.status === 'pending' ? 'Menunggu' : (e.call.status === 'handled' ? 'Ditangani' : 'Selesai'));

                        callCard.innerHTML = `
                            <div class="receipt-details">
                                <div class="receipt-header">
                                    <h3>Rincian Panggilan #${e.call.call_number}</h3>
                                    <div class="receipt-customer-info">
                                        <span><strong>Waktu:</strong> ${callTime}</span>
                                        <p class="receipt-status">
                                            <strong>Status:</strong>
                                            <span id="call-status-badge-${e.call.call_number}" class="status-badge status-${e.call.status}">
                                                ${callStatus}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <table class="receipt-table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="item-name">
                                                    <i class="fas fa-comment-dots" style="margin-right: 0.5rem; color: var(--text-muted);"></i>
                                                    Catatan Panggilan
                                                </div>
                                                <div class="receipt-item-notes">
                                                    ${e.call.notes || 'Tidak ada catatan khusus.'}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        `;
                        orderList.prepend(callCard);
                    }
                })
                .listen('.call-status-updated', (e) => { 
                    console.log('Call status updated received (CallStatusUpdated event)!', e);
                    const callNumber = e.callNumber;
                    const newStatus = e.newStatus;
                    const translatedStatus = e.translatedStatus;
                    const statusBadge = document.getElementById(`call-status-badge-${callNumber}`);
                    if (statusBadge) {
                        statusBadge.textContent = translatedStatus;
                        statusBadge.classList.remove('status-pending', 'status-handled', 'status-completed');
                        statusBadge.classList.add(`status-${newStatus}`);
                        statusBadge.classList.add('status-updated-highlight');
                        setTimeout(() => {
                            statusBadge.classList.remove('status-updated-highlight');
                        }, 2000);
                    }
                });
        }
    }


});