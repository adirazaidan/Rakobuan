document.addEventListener('DOMContentLoaded', function() {
    // --- Bagian Sidebar Responsif ---
    const customerMenuToggle = document.getElementById('customerMenuToggle');
    const sidebarCustomer = document.querySelector('.sidebar-customer');

    // Buat overlay secara dinamis (hanya sekali)
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    const openSidebar = () => {
        if(sidebarCustomer) sidebarCustomer.classList.add('open');
        overlay.classList.add('active');
    };
    const closeSidebar = () => {
        if(sidebarCustomer) sidebarCustomer.classList.remove('open');
        overlay.classList.remove('active');
    };

    if (customerMenuToggle) {
        customerMenuToggle.addEventListener('click', openSidebar);
    }
    overlay.addEventListener('click', closeSidebar);


    // --- Bagian Modal Add to Cart (Kode yang sudah ada sebelumnya) ---
    const modal = document.getElementById('addToCartModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const menuList = document.getElementById('menu-list');

    if (modal && menuList) {
        // Buka Modal saat tombol di-klik
        menuList.addEventListener('click', function(e) {
            // Cari parent elemen dengan class 'add-to-cart-btn'
            const button = e.target.closest('.add-to-cart-btn');
            if (button) {
                // Isi data modal dari data-attributes
                document.getElementById('modalProductId').value = button.dataset.id;
                document.getElementById('modalProductName').textContent = button.dataset.name;
                document.getElementById('modalProductPrice').textContent = 'Rp ' + parseInt(button.dataset.price).toLocaleString('id-ID');
                document.getElementById('modalProductDescription').textContent = button.dataset.description;
                document.getElementById('modalProductImage').src = button.dataset.image;

                // Reset form
                document.getElementById('quantity').value = 1;
                document.getElementById('notes').value = '';

                modal.style.display = 'flex';
            }
        });

        // Tutup Modal
        const closeModalCart = () => modal.style.display = 'none';
        closeModalBtn.addEventListener('click', closeModalCart);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModalCart();
        });
    }

    // --- Bagian Quantity Selector ---
    const qtyInput = document.getElementById('quantity');
    if (qtyInput) {
        document.getElementById('decreaseQty').addEventListener('click', () => {
            if (qtyInput.value > 1) qtyInput.value--;
        });
        document.getElementById('increaseQty').addEventListener('click', () => {
            qtyInput.value++;
        });
    }

    // --- Bagian Form Submission (AJAX) ---
    const addToCartForm = document.getElementById('addToCartForm');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(appConfig.routes.cartAdd, { // Menggunakan URL dari global config
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
                    // Update cart count di sidebar
                    document.getElementById('sidebar-cart-count').textContent = data.cartCount;
                    alert(data.message); // Notifikasi sederhana
                    if(modal) modal.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    }

    // --- Bagian Panggil Pelayan ---
    const callWaiterModal = document.getElementById('callWaiterModal');
    const closeCallModalBtn = document.getElementById('closeCallModalBtn');
    const callWaiterForm = document.getElementById('callWaiterForm');
    const callWaiterButtons = document.querySelectorAll('.call-waiter-btn');

    if (callWaiterModal) {
        // Fungsi untuk membuka modal panggilan
        const openCallModal = () => callWaiterModal.style.display = 'flex';
        // Fungsi untuk menutup modal panggilan
        const closeCallModal = () => {
            callWaiterForm.reset(); // Reset form saat ditutup
            callWaiterModal.style.display = 'none';
        };

        // Tambahkan event listener ke semua tombol 'Panggil Pelayan'
        callWaiterButtons.forEach(button => {
            button.addEventListener('click', openCallModal);
        });

        // Event listener untuk tombol close dan klik di luar modal
        closeCallModalBtn.addEventListener('click', closeCallModal);
        callWaiterModal.addEventListener('click', (e) => {
            if (e.target === callWaiterModal) closeCallModal();
        });

        // Event listener untuk submit form panggilan via AJAX
        callWaiterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(appConfig.routes.callWaiterStore, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.message) {
                    alert(data.message); // Notifikasi sukses
                    closeCallModal();
                } else if(data.error) {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim panggilan.');
            });
        });
    }
});