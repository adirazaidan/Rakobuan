import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * =================================
     * BAGIAN GLOBAL: Notifikasi & Listener Real-time
     * Berjalan di semua halaman admin.
     * =================================
     */
    const notificationBar = document.getElementById('newOrderNotification');
    if (notificationBar && typeof window.Echo !== 'undefined') {
        
        const notificationSound = new Audio('/sounds/notification.mp3');
        
        // Fungsi notifikasi yang bisa dipakai ulang
        const showNotification = (message, url = null) => {
            notificationSound.play().catch(error => console.error("Gagal memutar suara:", error));
            notificationBar.innerHTML = `<i class="fa-solid fa-bell fa-shake"></i> ${message}`;
            notificationBar.classList.add('show');

            if (url) {
                notificationBar.style.cursor = "pointer";
                notificationBar.onclick = () => { window.location.href = url; };
            } else {
                notificationBar.style.cursor = "default";
                notificationBar.onclick = null;
            }

            setTimeout(() => {
                notificationBar.classList.remove('show');
            }, 8000);
        };

        console.log("Global listeners are now active on all admin pages.");

        // 1. Listener Global untuk Pesanan Baru
        window.Echo.private('orders')
            .listen('.NewOrderReceived', (e) => {
                console.log('Global event: NewOrderReceived', e.order);
                if (window.location.pathname.includes('/admin/orders')) {
                    window.location.reload();
                } else {
                    showNotification(`Pesanan Baru dari Meja ${e.order.table_number}! Klik untuk melihat.`, '/admin/orders');
                }
            });

        // 2. Listener Global untuk Panggilan Baru
        window.Echo.private('calls')
            .listen('.NewCallReceived', (e) => {
                console.log('Global event: NewCallReceived', e.call);
                if (window.location.pathname.includes('/admin/calls')) {
                    window.location.reload();
                } else {
                    showNotification(`Panggilan Baru dari Meja ${e.call.table_number}! Klik untuk melihat.`, '/admin/calls');
                }
            });

        // 3. Listener Global untuk Perubahan Status Meja
        window.Echo.private('layout-tables').listen('.TableStatusUpdated', (e) => {
            console.log('Global event: TableStatusUpdated diterima untuk Table ID:', e.tableId);
            const tableId = e.tableId;
            const tableGrid = document.querySelector('.table-visual-grid');

            // 1. Lakukan Update Visual JIKA kita berada di halaman Layout Meja
            if (tableGrid) {
                const cardToReplace = document.getElementById(`table-card-${tableId}`);
                
                // Sebelum me-refresh, kita simpan status lamanya untuk notifikasi
                const oldStatusIsOccupied = cardToReplace ? cardToReplace.classList.contains('is-occupied') : false;

                fetch(`/admin/dining-tables/${tableId}/render`)
                    .then(response => response.text())
                    .then(html => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        const newCard = tempDiv.firstElementChild;
                        const newStatusIsOccupied = newCard.classList.contains('is-occupied');

                        // 2. Tampilkan Notifikasi setelah mendapatkan status baru
                        if (!oldStatusIsOccupied && newStatusIsOccupied) {
                            showNotification(`Meja ${newCard.querySelector('.table-visual-name').textContent} sekarang Diduduki.`, '/admin/dining-tables');
                        } else if (oldStatusIsOccupied && !newStatusIsOccupied) {
                            showNotification(`Meja ${newCard.querySelector('.table-visual-name').textContent} sekarang Tersedia.`, '/admin/dining-tables');
                        }
                        
                        // 3. Ganti kartu di halaman
                        if (cardToReplace) {
                            cardToReplace.outerHTML = html;
                        } else {
                            tableGrid.insertAdjacentHTML('beforeend', html);
                        }
                    })
                    .catch(error => console.error('Error fetching new card HTML:', error));
            }
        });
    }

    /**
     * ==============================================================
     * LOGIKA INTERAKSI SPESIFIK (HANYA BERJALAN DI HALAMAN LAYOUT MEJA)
     * ==============================================================
     */
    const tableGrid = document.querySelector('.table-visual-grid');
    if (tableGrid) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Listener untuk semua aksi submit form (antar, clear session)
        tableGrid.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.classList.contains('deliver-form') || form.classList.contains('clear-session-form')) {
                e.preventDefault();
                const button = form.querySelector('button');
                if(button) button.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: new FormData(form)
                })
                .catch(error => {
                    console.error("Error:", error);
                    if(button) button.disabled = false;
                    alert('Terjadi kesalahan. Silakan refresh halaman.');
                });
            }
        });
        
        // Listener untuk tombol 'Lihat Riwayat'
        tableGrid.addEventListener('click', function(e) {
            const historyButton = e.target.closest('.btn-view-history');
            if (historyButton) {
                const historyData = JSON.parse(historyButton.dataset.history);
                const customerName = historyButton.dataset.customerName;
                const historyModal = document.getElementById('historyModal');
                const historyBody = historyModal.querySelector('.modal-body'); // Ambil elemen dengan benar
                
                let html = `<p><strong>Riwayat Sesi</strong> untuk <strong>${customerName}</strong></p>`;

                // Bagian untuk Riwayat Pesanan
                if (historyData.orders && historyData.orders.length > 0) {
                    html += '<h5>Riwayat Pesanan:</h5>';
                    historyData.orders.forEach(order => {
                        html += `<div class="history-group">`;
                        html += `<p><strong>Pesanan #${order.id}</strong> - Total: Rp ${parseInt(order.total_price).toLocaleString('id-ID')}</p>`;
                        html += '<ul class="order-item-list">';
                        order.order_items.forEach(item => {
                            html += `<li class="order-item-row item-delivered">
                                        <div class="item-info">
                                            <span class="item-quantity">${item.quantity}x</span>
                                            <span class="item-name">${item.product.name}</span>
                                        </div>
                                    </li>`;
                        });
                        html += '</ul></div>';
                    });
                }

                // Bagian untuk Riwayat Panggilan
                if (historyData.calls && historyData.calls.length > 0) {
                    html += '<h5 style="margin-top: 1rem;">Riwayat Panggilan:</h5>';
                    html += '<ul class="call-item-list">';
                    historyData.calls.forEach(call => {
                        const callTime = new Date(call.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        html += `<li class="call-item-row">
                                    <span class="call-note"><i class="fas fa-comment-dots"></i> ${call.notes || 'Memanggil pelayan'}</span>
                                    <span class="text-muted" style="font-size: 0.8em;">${callTime}</span>
                                </li>`;
                    });
                    html += '</ul>';
                }

                historyBody.innerHTML = html;
                historyModal.style.display = 'flex';
            }
        });
    }

    // Listener untuk menutup modal riwayat
    const historyModal = document.getElementById('historyModal');
    if(historyModal) {
        const closeBtn = historyModal.querySelector('.modal-close');
        if(closeBtn) closeBtn.addEventListener('click', () => historyModal.style.display = 'none');
        historyModal.addEventListener('click', (e) => {
            if(e.target === historyModal) historyModal.style.display = 'none';
        });
    }

    // =======================================================
    // ===== LOGIKA BARU UNTUK LENCANA NOTIFIKASI SIDEBAR =====
    // =======================================================
    const orderBadge = document.getElementById('order-badge');
    const callBadge = document.getElementById('call-badge');

    const updateBadges = (counts) => {
        if (orderBadge) {
            if (counts.pending_orders > 0) {
                orderBadge.textContent = counts.pending_orders;
                orderBadge.classList.remove('d-none');
            } else {
                orderBadge.classList.add('d-none');
            }
        }

        if (callBadge) {
            if (counts.pending_calls > 0) {
                callBadge.textContent = counts.pending_calls;
                callBadge.classList.remove('d-none');
            } else {
                callBadge.classList.add('d-none');
            }
        }
    };


    const fetchInitialCounts = () => {
        fetch('/admin/notifications/counts')
            .then(response => response.json())
            .then(data => updateBadges(data))
            .catch(error => console.error('Error fetching notification counts:', error));
    };

    fetchInitialCounts();
    
   
    if (typeof window.Echo !== 'undefined') {
        window.Echo.private('orders').listen('.NewOrderReceived', (e) => {
            fetchInitialCounts(); 
        });
        window.Echo.private('calls').listen('.NewCallReceived', (e) => {
            fetchInitialCounts(); 
        });
    }
});