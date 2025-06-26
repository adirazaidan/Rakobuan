import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * =================================
     * LISTENER NOTIFIKASI GLOBAL (SELALU AKTIF DI SEMUA HALAMAN ADMIN)
     * =================================
     */
    const notificationBar = document.getElementById('newOrderNotification');
    // Hanya jalankan jika elemen notifikasi ada di layout
    if (notificationBar) {
        const notificationSound = new Audio('/sounds/notification.mp3');
        
        // Buat satu fungsi notifikasi yang bisa dipakai ulang
        const showNotification = (message) => {
            notificationSound.play().catch(error => console.error("Gagal memutar suara:", error));
            notificationBar.innerHTML = `<i class="fa-solid fa-bell fa-shake"></i> ${message}`;
            notificationBar.classList.add('show');
            // Alih-alih reload, kita beri link untuk pindah ke halaman yang relevan
            notificationBar.onclick = () => {
                if (message.includes('Pesanan')) {
                    window.location.href = '/admin/orders';
                } else if (message.includes('Panggilan')) {
                    window.location.href = '/admin/calls';
                }
            };
            // Sembunyikan setelah beberapa detik
            setTimeout(() => {
                notificationBar.classList.remove('show');
            }, 8000); // Waktu tampil notifikasi lebih lama
        };

        // Listener untuk notifikasi pesanan baru (sekarang menjadi global)
        console.log("Listening for new orders globally...");
        window.Echo.private('orders')
            .listen('.NewOrderReceived', (e) => {
                console.log('Global event NewOrderReceived Diterima:', e.order);
                showNotification(`Pesanan Baru dari Meja ${e.order.table_number}! Klik untuk melihat.`);
            });

        // Listener untuk notifikasi panggilan baru (sekarang menjadi global)
        console.log("Listening for new calls globally...");
        window.Echo.private('calls')
            .listen('.NewCallReceived', (e) => {
                console.log('Global event NewCallReceived Diterima:', e.call);
                showNotification(`Panggilan Baru dari Meja ${e.call.table_number}! Klik untuk melihat.`);
            });
    }


    /**
     * ==============================================================
     * LOGIKA SPESIFIK UNTUK HALAMAN LAYOUT MEJA
     * ==============================================================
     */
    const tableGrid = document.querySelector('.table-visual-grid');
    if (tableGrid && typeof window.Echo !== 'undefined') {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Listener untuk form 'antar' via AJAX
        tableGrid.addEventListener('submit', function(e) {
            if (e.target.classList.contains('deliver-form')) {
                e.preventDefault();
                const form = e.target;
                const url = form.action;
                const button = form.querySelector('button');
                button.disabled = true;

                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: new FormData(form)
                })
                .then(response => {
                    if (!response.ok) throw new Error(`Server error: ${response.statusText}`);
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        alert('Gagal memperbarui status dari server.');
                        button.disabled = false;
                    }
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

        // Listener untuk memperbarui kartu meja secara spesifik
        window.Echo.private('layout-tables')
            .listen('.TableStatusUpdated', (e) => {
                console.log('Event Diterima:', e.table);
                const table = e.table; // Sekarang kita menerima seluruh objek meja
                const tableId = table.id;
                const cardToReplace = document.getElementById(`table-card-${tableId}`);

                // --- BAGIAN LOGIKA NOTIFIKASI BARU ---
                const notificationBar = document.getElementById('newOrderNotification');
                const notificationSound = new Audio('/sounds/notification.mp3');
                let notificationMessage = '';

                // Tentukan pesan notifikasi berdasarkan perubahan status
                if (cardToReplace) { // Hanya tampilkan notif jika kartu sudah ada (update)
                    const oldStatus = cardToReplace.classList.contains('is-occupied') ? 'diduduki' : 'tersedia';
                    const newStatus = table.session_id ? 'diduduki' : 'tersedia';

                    if (oldStatus === 'tersedia' && newStatus === 'diduduki') {
                        notificationMessage = `Meja ${table.name} sekarang diduduki!`;
                    } else if (oldStatus === 'diduduki' && newStatus === 'tersedia') {
                        notificationMessage = `Meja ${table.name} sekarang tersedia.`;
                    }
                }
                
                // Tampilkan notifikasi jika ada pesan
                if (notificationMessage && notificationBar) {
                    notificationSound.play().catch(error => console.error("Gagal memutar suara:", error));
                    notificationBar.innerHTML = `<i class="fa-solid fa-bell fa-shake"></i> ${notificationMessage}`;
                    notificationBar.classList.add('show');
                    setTimeout(() => {
                        notificationBar.classList.remove('show');
                    }, 5000);
                }
                // --- AKHIR BAGIAN LOGIKA NOTIFIKASI ---


                // Minta HTML kartu yang sudah ter-update dari server (Logika ini tetap sama)
                fetch(`/admin/dining-tables/${tableId}/render`)
                    .then(response => response.text())
                    .then(html => {
                        if (cardToReplace) {
                            cardToReplace.outerHTML = html;
                        } else {
                            tableGrid.insertAdjacentHTML('beforeend', html);
                        }
                    })
                    .catch(error => console.error('Error fetching new card HTML:', error));
            });
            
        console.log("Listening for specific table status updates on this page...");
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