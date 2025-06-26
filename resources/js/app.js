import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * =================================
     * NOTIFIKASI REAL-TIME (UNTUK HALAMAN ORDER & PANGGILAN)
     * =================================
     */
    const notificationBar = document.getElementById('newOrderNotification');
    if (notificationBar) {
        const notificationSound = new Audio('/sounds/notification.mp3');
        const showNotification = (message) => {
            notificationSound.play().catch(error => console.error("Gagal memutar suara:", error));
            notificationBar.innerHTML = `<i class="fa-solid fa-bell fa-shake"></i> ${message}`;
            notificationBar.classList.add('show');
            setTimeout(() => {
                notificationBar.classList.remove('show');
                setTimeout(() => window.location.reload(), 500);
            }, 5000);
        };

        // Listener untuk halaman Orderan
        if (document.querySelector('.order-container')) {
            window.Echo.private('orders')
                .listen('.NewOrderReceived', (e) => {
                    console.log('Event NewOrderReceived Diterima:', e.order);
                    showNotification(`Pesanan Baru dari Meja ${e.order.table_number}!`);
                });
        }

        // Listener untuk halaman Panggilan
        if (document.querySelector('.call-container')) {
            window.Echo.private('calls')
                .listen('.NewCallReceived', (e) => {
                    console.log('Event NewCallReceived Diterima:', e.call);
                    showNotification(`Panggilan Baru dari Meja ${e.call.table_number}!`);
                });
        }
    }

    /**
     * ==============================================================
     * REAL-TIME UPDATE UNTUK HALAMAN LAYOUT MEJA (HTML over the Wire)
     * ==============================================================
     */
    const tableGrid = document.querySelector('.table-visual-grid');

    if (tableGrid) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Listener untuk semua aksi submit form di dalam grid
        tableGrid.addEventListener('submit', function(e) {
            // Hanya tangani form 'antar' dengan AJAX
            if (e.target.classList.contains('deliver-form')) {
                e.preventDefault();
                const form = e.target;
                const url = form.action;
                const button = form.querySelector('button');
                button.disabled = true;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: new FormData(form)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server responded with status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        alert('Gagal memperbarui status dari server.');
                        button.disabled = false;
                    }
                    // Jika sukses, kita tidak melakukan apa-apa karena event real-time akan menangani update.
                })
                .catch(error => {
                    console.error("Error updating delivery status:", error);
                    alert('Gagal memperbarui status. Cek console untuk detail.');
                    button.disabled = false;
                });
            }
        });
        
        // Listener untuk tombol 'Lihat Riwayat'
        tableGrid.addEventListener('click', function(e) {
            const historyButton = e.target.closest('.btn-view-history');
            if (historyButton) {
                const orderData = JSON.parse(historyButton.dataset.order);
                const historyModal = document.getElementById('historyModal');
                const historyBody = document.getElementById('historyModalBody');
                
                let itemsHtml = '<ul class="order-item-list">';
                orderData.order_items.forEach(item => {
                    itemsHtml += `<li class="order-item-row item-delivered">
                                    <div class="item-info">
                                        <span class="item-quantity">${item.quantity}x</span>
                                        <span class="item-name">${item.product.name}</span>
                                    </div>
                                    <span>Rp ${ (item.price * item.quantity).toLocaleString('id-ID') }</span>
                                  </li>`;
                });
                itemsHtml += '</ul>';

                let totalHtml = `<div class="receipt-total" style="margin-top:1rem; padding-top:1rem; border-top: 1px solid #ccc;">
                                    <span>Total</span>
                                    <strong>Rp ${ parseInt(orderData.total_price).toLocaleString('id-ID') }</strong>
                                 </div>`;

                historyBody.innerHTML = `
                    <p><strong>Pesanan #${orderData.id}</strong> untuk <strong>${orderData.customer_name}</strong></p>
                    ${itemsHtml}
                    ${totalHtml}
                `;
                
                historyModal.style.display = 'flex';
            }
        });

        // Listener utama dari Pusher untuk semua pembaruan status meja
        window.Echo.private('layout-tables')
            .listen('TableStatusUpdated', (e) => {
                console.log('Event Diterima untuk Table ID:', e.tableId);
                const tableId = e.tableId;
                const cardToReplace = document.getElementById(`table-card-${tableId}`);
                
                // Minta HTML kartu yang sudah ter-update dari server
                fetch(`/admin/dining-tables/${tableId}/render`)
                    .then(response => response.text())
                    .then(html => {
                        if (cardToReplace) {
                            // Ganti kartu lama dengan yang baru
                            cardToReplace.outerHTML = html;
                        } else {
                            // Jika ini meja baru, tambahkan ke grid
                            tableGrid.insertAdjacentHTML('beforeend', html);
                        }
                    })
                    .catch(error => console.error('Error fetching new card HTML:', error));
            });
            
        console.log("Listening for table status updates (HTML over the Wire)...");
    }

    // Listener untuk menutup modal riwayat
    const historyModal = document.getElementById('historyModal');
    if(historyModal) {
        const closeBtn = document.getElementById('closeHistoryModalBtn');
        closeBtn.addEventListener('click', () => historyModal.style.display = 'none');
        historyModal.addEventListener('click', (e) => {
            if(e.target === historyModal) {
                historyModal.style.display = 'none';
            }
        });
    }
});