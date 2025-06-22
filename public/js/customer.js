document.addEventListener('DOMContentLoaded', function() {
    // --- Bagian Modal ---
    const modal = document.getElementById('addToCartModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const menuList = document.getElementById('menu-list');

    if (!modal || !menuList) return;

    // Buka Modal saat tombol di-klik
    menuList.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('add-to-cart-btn')) {
            const button = e.target;
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
    const closeModal = () => modal.style.display = 'none';
    closeModalBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    // --- Bagian Quantity Selector ---
    const qtyInput = document.getElementById('quantity');
    document.getElementById('decreaseQty').addEventListener('click', () => {
        if (qtyInput.value > 1) qtyInput.value--;
    });
    document.getElementById('increaseQty').addEventListener('click', () => {
        qtyInput.value++;
    });

    // --- Bagian Form Submission (AJAX) ---
    const addToCartForm = document.getElementById('addToCartForm');
    addToCartForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('cart.add') }}", {
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
                // Update cart count di header
                document.getElementById('cart-count').textContent = data.cartCount;
                alert(data.message); // Notifikasi sederhana
                closeModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    });
});