import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event fired');
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;

    // Global DOM elements
    const searchInput = document.getElementById('searchInput');
    const categoryFilterDropdown = document.getElementById('categoryFilterDropdown');
    const priceFilterButtons = document.querySelectorAll('.filter-price-buttons button');
    const bestsellerFilter = document.getElementById('bestsellerFilter');
    const discountFilter = document.getElementById('discountFilter');
    const menuGrid = document.getElementById('menu-list');
    const allProductCards = document.querySelectorAll('.product-card');

    /**
     * =================================
     * CUSTOM ALERT DIALOG FUNCTIONALITY
     * =================================
     */
    const customAlertModal = document.getElementById('customAlertModal');
    const alertModalTitle = document.getElementById('alertModalTitle');
    const alertModalMessage = document.getElementById('alertModalMessage');
    const alertModalIcon = document.getElementById('alertModalIcon');
    const closeAlertModalBtn = document.getElementById('closeAlertModalBtn');
    const alertModalConfirmBtn = document.getElementById('alertModalConfirmBtn');

    // Custom alert function to replace all alert() calls
    window.customAlert = function(message, type = 'info', title = 'Pesan') {
        if (!customAlertModal) return;

        // Set modal content
        alertModalTitle.textContent = title;
        alertModalMessage.textContent = message;

        // Set icon based on type
        alertModalIcon.className = 'fas';
        switch (type) {
            case 'success':
                alertModalIcon.classList.add('fa-check-circle', 'alert-success');
                break;
            case 'warning':
                alertModalIcon.classList.add('fa-exclamation-triangle', 'alert-warning');
                break;
            case 'danger':
            case 'error':
                alertModalIcon.classList.add('fa-times-circle', 'alert-danger');
                break;
            case 'info':
            default:
                alertModalIcon.classList.add('fa-info-circle', 'alert-info');
                break;
        }

        // Set modal class for styling
        customAlertModal.className = 'modal-overlay';
        customAlertModal.classList.add(`alert-${type}`);

        // Show modal
        customAlertModal.style.display = 'flex';
    };

    // Close alert modal
    const closeAlertModal = () => {
        if (customAlertModal) {
            customAlertModal.style.display = 'none';
            // Remove type classes
            customAlertModal.classList.remove('alert-success', 'alert-warning', 'alert-danger', 'alert-info');
        }
    };

    // Event listeners for alert modal
    if (closeAlertModalBtn) {
        closeAlertModalBtn.addEventListener('click', closeAlertModal);
    }
    if (alertModalConfirmBtn) {
        alertModalConfirmBtn.addEventListener('click', closeAlertModal);
    }
    if (customAlertModal) {
        customAlertModal.addEventListener('click', (e) => {
            if (e.target === customAlertModal) {
                closeAlertModal();
            }
        });
    }

    // Override the default alert function
    window.alert = function(message) {
        customAlert(message, 'info', 'Pesan');
    };

    // Override native confirm
    window.confirm = function(message) {
        return new Promise((resolve) => {
            const confirmModal = document.createElement('div');
            confirmModal.className = 'modal-overlay';
            confirmModal.style.display = 'flex';
            confirmModal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Konfirmasi</h4>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert-icon-wrapper">
                            <i class="fas fa-question-circle" style="color: var(--warning-color);"></i>
                        </div>
                        <p class="alert-message">${message}</p>
                        <div class="alert-actions">
                            <button class="btn btn-secondary" id="cancelBtn">Batal</button>
                            <button class="btn btn-primary" id="confirmBtn">Ya</button>
                        </div>
                    </div>
                </div>
            `;

            const closeModal = (result) => {
                document.body.removeChild(confirmModal);
                resolve(result);
            };

            document.body.appendChild(confirmModal);

            confirmModal.querySelector('#cancelBtn').addEventListener('click', () => closeModal(false));
            confirmModal.querySelector('#confirmBtn').addEventListener('click', () => closeModal(true));
            confirmModal.querySelector('.modal-close').addEventListener('click', () => closeModal(false));
            confirmModal.addEventListener('click', (e) => {
                if (e.target === confirmModal) {
                    closeModal(false);
                }
            });
        });
    };

    // Logout confirmation function
    window.showLogoutConfirm = function() {
        confirm('Apakah Anda yakin ingin keluar dari meja ini?').then((confirmed) => {
            if (confirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    };

    /**
     * ==========================================================
     * MENU FILTERING AND SORTING FUNCTIONALITY
     * ==========================================================
     */
    // Check if we're on the menu page
    if (searchInput && menuGrid) {
        console.log('Menu filtering elements found, initializing filters');
        console.log('searchInput:', searchInput);
        console.log('menuGrid:', menuGrid);
        console.log('categoryFilterDropdown:', categoryFilterDropdown);
        console.log('priceFilterButtons:', priceFilterButtons);
        console.log('bestsellerFilter:', bestsellerFilter);
        console.log('discountFilter:', discountFilter);
        console.log('allProductCards:', allProductCards);
        
        let currentPriceSortOrder = null;

        function filterAndSortProducts() {
            console.log('filterAndSortProducts called');
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilterDropdown ? categoryFilterDropdown.value : 'all';
            const isBestsellerChecked = bestsellerFilter ? bestsellerFilter.checked : false;
            const isDiscountChecked = discountFilter ? discountFilter.checked : false;
            
            console.log('Filter values:', {
                searchTerm,
                selectedCategory,
                isBestsellerChecked,
                isDiscountChecked,
                currentPriceSortOrder
            });
            
            const productCardsArray = Array.from(allProductCards);
            let filteredProducts = productCardsArray.filter(card => {
                const productName = card.getAttribute('data-product-name') || '';
                const productCategoryId = card.getAttribute('data-category-id') || '';
                const isBestseller = card.getAttribute('data-is-bestseller') === 'true';
                const hasDiscount = card.getAttribute('data-has-discount') === 'true';
                
                const matchesSearch = productName.includes(searchTerm);
                const matchesCategory = selectedCategory === 'all' || productCategoryId === selectedCategory;
                const matchesBestseller = !isBestsellerChecked || isBestseller;
                const matchesDiscount = !isDiscountChecked || hasDiscount;
                
                return matchesSearch && matchesCategory && matchesBestseller && matchesDiscount;
            });
            
            // Enhanced sorting logic
            if (currentPriceSortOrder) {
                filteredProducts.sort((a, b) => {
                    const priceA = parseFloat(a.getAttribute('data-price')) || 0;
                    const priceB = parseFloat(b.getAttribute('data-price')) || 0;
                    if (currentPriceSortOrder === 'asc') {
                        return priceA - priceB;
                    } else {
                        return priceB - priceA;
                    }
                });
            }
            
            // Check for bestseller and discount availability
            const hasBestsellerItems = productCardsArray.some(card => card.getAttribute('data-is-bestseller') === 'true');
            const hasDiscountItems = productCardsArray.some(card => card.getAttribute('data-has-discount') === 'true');
            
            // Update filter button states and show messages
            updateFilterButtonStates(hasBestsellerItems, hasDiscountItems);
            
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
        
        function updateFilterButtonStates(hasBestsellerItems, hasDiscountItems) {
            if (!bestsellerFilter || !discountFilter) return;
            
            const bestsellerLabel = bestsellerFilter.nextElementSibling;
            const discountLabel = discountFilter.nextElementSibling;
            
            // Update bestseller filter
            if (!hasBestsellerItems) {
                bestsellerFilter.disabled = true;
                bestsellerFilter.checked = false;
                if (bestsellerLabel) {
                    bestsellerLabel.textContent = 'Terlaris (belum ada)';
                    bestsellerLabel.style.color = 'var(--gray-500)';
                }
            } else {
                bestsellerFilter.disabled = false;
                if (bestsellerLabel) {
                    bestsellerLabel.textContent = 'Terlaris';
                    bestsellerLabel.style.color = '';
                }
            }
            
            // Update discount filter
            if (!hasDiscountItems) {
                discountFilter.disabled = true;
                discountFilter.checked = false;
                if (discountLabel) {
                    discountLabel.textContent = 'Diskon (belum ada)';
                    discountLabel.style.color = 'var(--gray-500)';
                }
            } else {
                discountFilter.disabled = false;
                if (discountLabel) {
                    discountLabel.textContent = 'Diskon';
                    discountLabel.style.color = '';
                }
            }
        }

        // Add event listeners for filters
        if (searchInput) {
            searchInput.addEventListener('input', filterAndSortProducts);
            console.log('Search input listener added');
        }
        
        if (categoryFilterDropdown) {
            categoryFilterDropdown.addEventListener('change', filterAndSortProducts);
            console.log('Category dropdown listener added');
        }
        
        if (bestsellerFilter) {
            bestsellerFilter.addEventListener('change', filterAndSortProducts);
            console.log('Bestseller filter listener added');
        }
        
        if (discountFilter) {
            discountFilter.addEventListener('change', filterAndSortProducts);
            console.log('Discount filter listener added');
        }
        
        if (priceFilterButtons.length > 0) {
            priceFilterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const sortOrder = this.getAttribute('data-sort-order');
                    
                    // Remove active class from all price buttons
                    priceFilterButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.style.transform = '';
                    });
                    
                    // Toggle active state
                    if (currentPriceSortOrder === sortOrder) {
                        currentPriceSortOrder = null;
                    } else {
                        currentPriceSortOrder = sortOrder;
                        this.classList.add('active');
                        this.style.transform = 'scale(1.1)';
                    }
                    
                    filterAndSortProducts();
                });
            });
            console.log('Price filter buttons listeners added');
        }

        // Initialize filter states on page load
        filterAndSortProducts();
    } else {
        console.log('Menu filtering elements not found, skipping filter initialization');
    }

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

    const descriptionModal = document.getElementById('descriptionModal');
    const descriptionModalTitle = document.getElementById('descriptionModalTitle');
    const descriptionModalText = document.getElementById('descriptionModalText');
    const closeDescriptionModalBtn = document.getElementById('closeDescriptionModalBtn');

    const openDescriptionModal = (productName, fullDescription) => {
        if (!descriptionModal) return;
        descriptionModalTitle.textContent = productName;
        descriptionModalText.textContent = fullDescription;
        descriptionModal.style.display = 'flex';
    };

    const closeDescriptionModal = () => {
        const descriptionModal = document.getElementById('descriptionModal');
        if (descriptionModal) {
            descriptionModal.style.display = 'none';
        }
    };

    // Payment Modal Functionality
    const paymentModal = document.getElementById('paymentModal');
    const paymentBtn = document.getElementById('paymentBtn');
    const closePaymentModalBtn = document.getElementById('closePaymentModalBtn');
    const callWaiterFromPayment = document.getElementById('callWaiterFromPayment');

    const openPaymentModal = () => {
        if (paymentModal) {
            paymentModal.style.display = 'flex';
            // Add entrance animation
            paymentModal.style.opacity = '0';
            setTimeout(() => {
                paymentModal.style.opacity = '1';
            }, 10);
        }
    };

    const closePaymentModalFunc = () => {
        if (paymentModal) {
            paymentModal.style.opacity = '0';
            setTimeout(() => {
                paymentModal.style.display = 'none';
            }, 300);
        }
    };

    // Event listeners for payment modal
    if (paymentBtn) {
        paymentBtn.addEventListener('click', openPaymentModal);
    }

    if (closePaymentModalBtn) {
        closePaymentModalBtn.addEventListener('click', closePaymentModalFunc);
    }

    if (callWaiterFromPayment) {
        callWaiterFromPayment.addEventListener('click', () => {
            closePaymentModalFunc();
            // Trigger call waiter functionality
            const callWaiterBtn = document.querySelector('.call-waiter-btn');
            if (callWaiterBtn) {
                callWaiterBtn.click();
            }
        });
    }

    if (paymentModal) {
        paymentModal.addEventListener('click', (e) => {
            if (e.target === paymentModal) {
                closePaymentModalFunc();
            }
        });
    }

    if (closeDescriptionModalBtn) {
        closeDescriptionModalBtn.addEventListener('click', closeDescriptionModal);
    }
    if (descriptionModal) {
        descriptionModal.addEventListener('click', (e) => {
            if (e.target === descriptionModal) {
                closeDescriptionModal();
            }
        });
    }


    
    /**
     * ==========================================================
     * FUNGSI-FUNGSI PEMBANTU (HELPERS) GLOBAL
     * ==========================================================
     */

    const cancelOrderModal = document.getElementById('cancelOrderModal');
    const modalOrderNumber = document.getElementById('modalOrderNumber');
    document.querySelectorAll('.btn-cancel-top-right').forEach(button => {
        button.addEventListener('click', function() {
            const orderNumber = this.dataset.orderNumber;
            modalOrderNumber.textContent = orderNumber;
            cancelOrderModal.style.display = 'flex';
        });
    });

    document.querySelectorAll('.modal-close').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal-overlay');
            if (modal) {
                modal.style.display = 'none';
            }
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

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
        const finalNotes = (notes === null || notes.trim() === '') ? '-' : notes;

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        
        if (finalNotes !== null) {
            formData.append('notes', finalNotes);
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
            if (notesTextOnCartPage) notesTextOnCartPage.textContent = notes || '-';
            closeNotes();
        } else {
            customAlert('Gagal menyimpan catatan.', 'danger', 'Error');
        }
        saveButton.textContent = 'Simpan Catatan';
        saveButton.disabled = false;
    });

    /**
     * ==========================================================
     * BAGIAN INTERAKSI SPESIFIK HALAMAN MENU
     * ==========================================================
     */
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
                actionWrapper.innerHTML = `<div class="quantity-selector-inline" data-product-id="${productId}"><button class="btn-quantity-inline btn-decrease-inline">-</button><span class="quantity-inline-display">${newQuantity}</span><button class="btn-quantity-inline btn-increase-inline" ${isMax ? 'disabled' : ''}>+</button></div>`;
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
            const readMoreBtn = target.closest('.btn-read-more');
            const initialAddBtn = target.closest('.btn-add-cart-initial');
            const increaseBtn = target.closest('.btn-increase-inline');
            const decreaseBtn = target.closest('.btn-decrease-inline');
            const notesBtn = target.closest('.btn-edit-notes');
            const zoomBtn = target.closest('.card-zoom-btn');

            if (readMoreBtn) {
                const productId = readMoreBtn.dataset.productId;
                const productCard = document.getElementById(`product-card-${productId}`);
                if (productCard) {
                    const productName = productCard.querySelector('.product-name').textContent;
                    const fullDescription = productCard.querySelector('.product-description').dataset.fullDescription;
                    openDescriptionModal(productName, fullDescription);
                }
            } else if (initialAddBtn) {
                initialAddBtn.disabled = true;
                initialAddBtn.textContent = '...'; 
                
                const productId = initialAddBtn.dataset.productId;
                const productCard = initialAddBtn.closest('.product-card');
                const maxStock = parseInt(productCard.dataset.stock);
                
                try {
                    const response = await handleCartAction(productId, 1, '', true);
                    if (response.success) {
                        updateProductCardUI(productId, 1); 
                        updateMiniCartBar(response.cartCount, response.grandTotal);
                        if (maxStock <= 1) { 
                            showStockFeedback(productCard, `Stok tersisa ${maxStock}`);
                        }
                    } else {
                        customAlert(response.message || 'Gagal menambahkan item.', 'danger', 'Error');
                        updateProductCardUI(productId, 0); 
                    }
                } catch (error) {
                    console.error("Cart action failed:", error);
                    customAlert('Terjadi kesalahan jaringan. Gagal menambahkan item.', 'danger', 'Error Jaringan');
                    updateProductCardUI(productId, 0); 
                } finally {
                    initialAddBtn.disabled = false;
                    initialAddBtn.innerHTML = '<i class="fas fa-shopping-cart"></i>'; 
                }
            } else if (increaseBtn) {
                increaseBtn.disabled = true; 
                
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
                
                try {
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
                        customAlert(response.message || 'Gagal memperbarui item.', 'danger', 'Error');
                    }
                } catch (error) {
                    console.error("Cart action failed:", error);
                    display.textContent = currentQty; 
                    alert('Terjadi kesalahan jaringan. Gagal memperbarui item.');
                } finally {
                    if (parseInt(display.textContent) < maxStock) {
                        increaseBtn.disabled = false;
                    }
                }

            } else if (decreaseBtn) {
                decreaseBtn.disabled = true; 
                
                const productCard = decreaseBtn.closest('.product-card');
                const feedbackElement = productCard.querySelector('.inline-stock-feedback');
                if (feedbackElement) feedbackElement.classList.remove('show');
                const selector = decreaseBtn.closest('.quantity-selector-inline');
                const productId = selector.dataset.productId;
                const display = selector.querySelector('.quantity-inline-display');
                let currentQty = parseInt(display.textContent); 

                const increaseBtnReference = selector.querySelector('.btn-increase-inline');
                if (increaseBtnReference) increaseBtnReference.disabled = false; 

                let newQuantity = currentQty - 1;
                display.textContent = '...'; 

                try {
                    if (newQuantity > 0) {
                        const response = await handleCartAction(productId, newQuantity);
                        if (response.success) {
                            display.textContent = newQuantity; 
                            updateMiniCartBar(response.cartCount, response.grandTotal);
                        } else {
                            display.textContent = currentQty; 
                            customAlert(response.message || 'Gagal memperbarui item.', 'danger', 'Error');
                        }
                    } else { 
                        const response = await handleRemoveAction(productId);
                        if (response.success) {
                            updateProductCardUI(productId, 0); 
                            updateMiniCartBar(response.cartCount, response.grandTotal);
                        } else {
                            display.textContent = currentQty; 
                            customAlert(response.message || 'Gagal menghapus item.', 'danger', 'Error');
                        }
                    }
                } catch (error) {
                    console.error("Cart action failed:", error);
                    display.textContent = currentQty; 
                    alert('Terjadi kesalahan jaringan. Gagal memperbarui item.');
                } finally {
                    if (parseInt(display.textContent) > 0) {
                        decreaseBtn.disabled = false;
                    }
                }
            } else if (notesBtn) {
                const productId = notesBtn.dataset.productId;
                const productCard = document.getElementById(`product-card-${productId}`);
                const productName = productCard.querySelector('.product-name').textContent;
                openNotesModal(productId, productName, notesBtn.dataset.notes);
            } else if (zoomBtn) {
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
                    const increaseBtn = productCard.querySelector('.btn-increase-inline');
                    if (increaseBtn) increaseBtn.disabled = true;
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
                    customAlert('Gagal memperbarui keranjang.', 'danger', 'Error');
                }
            } else if (decreaseBtn) {
                const itemCard = decreaseBtn.closest('.cart-item-card-new');
                const feedbackElement = itemCard.querySelector('.inline-stock-feedback');
                if (feedbackElement) feedbackElement.classList.remove('show');
                const display = itemCard.querySelector('.quantity-inline-display');
                let quantity = parseInt(display.textContent) - 1;
                const increaseBtnReference = itemCard.querySelector('.btn-increase-inline');
                if (increaseBtnReference) increaseBtnReference.disabled = false;

                display.textContent = '...';
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
                        customAlert('Gagal menghapus item.', 'danger', 'Error');
                    }
                }
            } else if (notesBtn) {
                const productId = notesBtn.dataset.productId;
                const itemCard = document.getElementById(`cart-item-${productId}`);
                const productName = itemCard.querySelector('.item-name').textContent;
                openNotesModal(productId, productName, notesBtn.dataset.notes);
            } else if (zoomBtn) {
                e.stopPropagation();
                openLightbox(zoomBtn.dataset.imageUrl);
            }
        });

        cartPage.addEventListener('change', async (e) => {
            const target = e.target;
            if (target.classList.contains('bungkus-checkbox') || target.classList.contains('option-checkbox')) {
                const itemCard = target.closest('.cart-item-card-new');
                if (!itemCard) return;

                const productId = itemCard.dataset.id;
                const quantity = itemCard.querySelector('.quantity-inline-display').textContent;
                const notesButton = itemCard.querySelector('.btn-edit-notes');
                let currentNotes = notesButton ? notesButton.dataset.notes : '';
                const optionText = `(${target.dataset.option})`;
                let newNotes = '';

                // Remove the specific option from current notes
                currentNotes = currentNotes.replace(optionText, '').trim();

                if (target.checked) {
                    newNotes = currentNotes ? `${optionText} ${currentNotes}` : optionText;
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
                    customAlert('Gagal memperbarui opsi.', 'danger', 'Error');
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
                    customAlert(data.message, 'success', 'Berhasil');
                    closeCallModal();
                } else if (data.error) {
                    customAlert('Error: ' + data.error, 'danger', 'Error');
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
                    customAlert(data.message || 'Terjadi kesalahan saat memproses pesanan.', 'danger', 'Error');
                    button.textContent = 'Kirim Orderan';
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Checkout Error:', error);
                customAlert('Terjadi kesalahan teknis. Silakan coba lagi.', 'danger', 'Error Teknis');
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
                customAlert('Sesi Anda untuk meja ini telah dihentikan oleh admin. Anda akan dikembalikan ke halaman login.', 'warning', 'Sesi Dihentikan');
                
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
                customAlert('Sesi Anda telah berakhir. Anda akan di-logout.', 'warning', 'Sesi Berakhir');
                
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