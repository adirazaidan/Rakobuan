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
        const closeModalBtn = document.getElementById('closeModalBtn');
        const addToCartForm = document.getElementById('addToCartForm');
        const quantityInput = document.getElementById('quantity');
        const plusBtn = document.getElementById('increaseQty');
        const minusBtn = document.getElementById('decreaseQty');
        const modalPriceElement = document.getElementById('modalProductPrice');

        let currentBasePrice = 0; // Variabel untuk menyimpan harga satuan produk

        // --- FUNGSI BARU UNTUK MEMPERBARUI HARGA ---
        const updateModalPrice = (quantity) => {
            if (modalPriceElement) {
                const totalPrice = currentBasePrice * quantity;
                modalPriceElement.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
            }
        };

        // Fungsi untuk membuka modal dan mengisi data
        const openAddToCartModal = (button) => {
            // Simpan harga satuan saat modal dibuka
            currentBasePrice = parseInt(button.dataset.price);

            document.getElementById('modalProductId').value = button.dataset.id;
            document.getElementById('modalProductName').textContent = button.dataset.name;
            document.getElementById('modalProductDescription').textContent = button.dataset.description;
            document.getElementById('modalProductImage').src = button.dataset.image;
            document.getElementById('modalProductImage').alt = button.dataset.name;
            
            // Reset form dan harga ke nilai awal
            quantityInput.value = 1;
            document.getElementById('notes').value = '';
            updateModalPrice(1); // Tampilkan harga awal untuk 1 item

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

        // Event listener untuk menutup modal
        closeModalBtn.addEventListener('click', closeAddToCartModal);
        addToCartModal.addEventListener('click', (e) => {
            if (e.target === addToCartModal) closeAddToCartModal();
        });

        // Event listener untuk tombol kuantitas
        plusBtn.addEventListener('click', () => {
            quantityInput.value = parseInt(quantityInput.value) + 1;
            updateModalPrice(quantityInput.value); // Panggil fungsi update harga
        });
        minusBtn.addEventListener('click', () => {
            const currentQty = parseInt(quantityInput.value);
            if (currentQty > 1) {
                quantityInput.value = currentQty - 1;
                updateModalPrice(quantityInput.value); // Panggil fungsi update harga
            }
        });

        // Event listener untuk submit form via AJAX (tidak ada perubahan di sini)
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(appConfig.routes.cartAdd, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    document.getElementById('sidebar-cart-count').textContent = data.cartCount;
                    alert(data.message);
                    closeAddToCartModal();
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
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

    // Filter Kategori
    const categoryButtons = document.querySelectorAll('.btn-category');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const selectedCategoryId = this.dataset.categoryId;

            productCards.forEach(card => {
                card.style.display = (selectedCategoryId === 'all' || card.dataset.categoryId === selectedCategoryId) ? 'flex' : 'none';
            });
        });
    });

    // Filter Pencarian
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            productCards.forEach(card => {
                const productName = card.dataset.productName || '';
                card.style.display = productName.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    }
});