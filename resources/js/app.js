import './bootstrap';
import 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;

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

    /**
     * =================================
     * BAGIAN UTAMA ADMIN DASHBOARD
     * =================================
     */
    
    /**
     * =================================
     * BAGIAN GLOBAL: Notifikasi & Listener Real-time
     * Berjalan di semua halaman admin.
     * =================================
     */
    const notificationBar = document.getElementById('newOrderNotification');
    if (notificationBar && typeof window.Echo !== 'undefined') {
        
        const notificationSound = new Audio('/sounds/notification.mp3');
        
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
        window.Echo.private('orders')
            .listen('.NewOrderReceived', (e) => {
                console.log('Global event: NewOrderReceived', e.order);
                if (window.location.pathname.includes('/admin/orders')) {
                    window.location.reload();
                } else {
                    showNotification(`Pesanan Baru dari Meja ${e.order.table_number}! Klik untuk melihat.`, '/admin/orders');
                }
            });
        window.Echo.private('calls')
            .listen('.NewCallReceived', (e) => {
                console.log('Global event: NewCallReceived', e.call);
                if (window.location.pathname.includes('/admin/calls')) {
                    window.location.reload();
                } else {
                    showNotification(`Panggilan Baru dari Meja ${e.call.table_number}! Klik untuk melihat.`, '/admin/calls');
                }
            });
            window.Echo.private('layout-tables').listen('.TableStatusUpdated', (e) => {
                console.log('Global event: TableStatusUpdated diterima untuk Table ID:', e.tableId);
                const tableId = e.tableId;
                const tableGrid = document.querySelector('.table-visual-grid');

                if (tableGrid) {
                    const oldCard = document.getElementById(`table-card-${tableId}`);
                    const oldStatusIsOccupied = oldCard ? oldCard.classList.contains('is-occupied') : false;

                    fetch(`/admin/dining-tables/${tableId}/render`)
                        .then(response => response.text())
                        .then(html => {
                            const cardToUpdate = document.getElementById(`table-card-${tableId}`);

                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = html;
                            const newCard = tempDiv.firstElementChild;
                            if (!newCard) {
                                if (cardToUpdate) cardToUpdate.remove(); 
                                return;
                            }

                            const newStatusIsOccupied = newCard.classList.contains('is-occupied');
                            
                            if (!oldStatusIsOccupied && newStatusIsOccupied) {
                                showNotification(`Meja ${newCard.querySelector('.table-visual-name').textContent} sekarang Diduduki.`, '/admin/dining-tables');
                            } else if (oldStatusIsOccupied && !newStatusIsOccupied) {
                                showNotification(`Meja ${newCard.querySelector('.table-visual-name').textContent} sekarang Tersedia.`, '/admin/dining-tables');
                            }
                            
                            if (cardToUpdate) {
                                cardToUpdate.outerHTML = html;
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
                    customAlert('Terjadi kesalahan. Silakan refresh halaman.', 'danger', 'Error');
                });
            }
        });
        tableGrid.addEventListener('click', function(e) {
            const historyButton = e.target.closest('.btn-view-history');
            if (historyButton) {
                const historyData = JSON.parse(historyButton.dataset.history);
                const customerName = historyButton.dataset.customerName;
                const historyModal = document.getElementById('historyModal');
                const historyBody = historyModal.querySelector('.modal-body');

                let html = `<p><strong>Riwayat Sesi</strong> untuk <strong>${customerName}</strong></p>`;

                if (historyData.orders && historyData.orders.length > 0) {
                    html += '<h5>Riwayat Pesanan:</h5>';
                    historyData.orders.forEach(order => {

                        const orderTime = new Date(order.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                        html += `<div class="history-group is-order-history">`; 
                        html += `<p style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong>Pesanan #${order.order_number}</strong>
                                    <span class="text-muted" style="font-size: 0.85rem;">${orderTime}</span>
                                </p>`;
                        html += '<ul class="order-item-list">';
                        order.order_items.forEach(item => {
                            const isDelivered = order.status === 'completed'; 
                            html += `<li class="order-item-row ${isDelivered ? 'item-delivered' : ''}" style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="item-info">
                                            <span class="item-quantity">${item.quantity}x</span>
                                            <span class="item-name">${item.product.name}</span>
                                            ${item.notes ?
                                                `<small class="item-note" title="${item.notes}">
                                                    <i class="fas fa-sticky-note"></i> ${item.notes}
                                                </small>`
                                                : ''
                                            }
                                        </div>
                                    </li>`;
                        });
                        html += '</ul></div>';
                    });
                } else {
                    html += '<h5>Riwayat Pesanan:</h5>';
                    html += '<p class="text-muted">Tidak ada riwayat pesanan.</p>';
                }


                if (historyData.calls && historyData.calls.length > 0) {
                    html += '<h5 style="margin-top: 1rem;">Riwayat Panggilan:</h5>';
                    historyData.calls.forEach(call => {
                        const isCallCompleted = call.status === 'handled' || call.status === 'completed';
                        const callTime = new Date(call.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                        html += `<div class="history-group is-call">`;
                        html += `<p style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong>Panggilan #${call.call_number}</strong>
                                    <span class="text-muted" style="font-size: 0.85rem;">${callTime}</span>
                                </p>`;

                        html += `<ul class="call-item-list-condensed" style="list-style: none; padding: 0;">`;
                        html += `<li class="call-item-row ${isCallCompleted ? 'item-delivered' : ''}" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9em; padding: 0.25rem 0;">
                                        <div class="item-info">
                                            <span class="item-quantity">1x</span>
                                            <span class="item-name">Permintaan Pelayan</span>
                                            ${call.notes ?
                                                `<small class="item-note" title="${call.notes}">
                                                    <i class="fas fa-comment-dots"></i> ${call.notes}
                                                </small>`
                                                : `<small class="item-note"><i class="fas fa-comment-dots"></i> Tanpa catatan</small>`
                                            }
                                        </div>
                                    </li>`;
                        html += `</ul>`;
                        html += `</div>`;
                    });
                } else {
                    html += '<h5 style="margin-top: 1rem;">Riwayat Panggilan:</h5>';
                    html += '<p class="text-muted">Tidak ada riwayat panggilan.</p>';
                }

                if ((!historyData.orders || historyData.orders.length === 0) && (!historyData.calls || historyData.calls.length === 0)) {
                    html = '<p class="text-muted">Tidak ada riwayat pesanan atau panggilan untuk sesi ini.</p>';
                }

                historyBody.innerHTML = html;
                historyModal.style.display = 'flex';
            }
        });


    

        /**
         * ==============================================================
         * LOGIKA BARU: TIMER UNTUK MEMERIKSA STATUS TERLAMBAT REAL-TIME
         * ==============================================================
         */
        const checkOverdueStatus = () => {
            const now = new Date();
            const fifteenMinutesInMs = 15 * 60 * 1000;

            document.querySelectorAll('.order-item-row.item-pending').forEach(itemRow => {
                const createdAt = new Date(itemRow.dataset.createdAt);
                const timeDiff = now - createdAt;

                if (timeDiff > fifteenMinutesInMs) {
                    const itemNameEl = itemRow.querySelector('.item-name');
                    if (!itemNameEl.querySelector('.overdue-warning')) {
                        const warningBadge = document.createElement('span');
                        warningBadge.className = 'overdue-warning';
                        warningBadge.title = 'Pesanan ini sudah lebih dari 15 menit!';
                        warningBadge.innerHTML = `<i class="fas fa-clock"></i> Terlambat`;
                        itemNameEl.appendChild(warningBadge);
                    }
                }
            });

            document.querySelectorAll('.call-item-row.item-pending').forEach(callRow => {
                const createdAt = new Date(callRow.dataset.createdAt);
                const timeDiff = now - createdAt;

                if (timeDiff > fifteenMinutesInMs) {
                    const callNoteEl = callRow.querySelector('.call-note');
                    if (!callNoteEl.querySelector('.overdue-warning')) {
                        const warningBadge = document.createElement('span');
                        warningBadge.className = 'overdue-warning';
                        warningBadge.title = 'Panggilan ini sudah lebih dari 15 menit!';
                        warningBadge.innerHTML = `<i class="fas fa-clock"></i> Terlambat`;
                        callNoteEl.appendChild(warningBadge);
                    }
                }
            });
            
            console.log('Overdue status checked.');
        };

        checkOverdueStatus();
        setInterval(checkOverdueStatus, 60000);
    }
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

    /**
     * ==============================================================
     * LOGIKA BARU: REAL-TIME UPDATE UNTUK HALAMAN MANAJEMEN MENU
     * ==============================================================
     */
    const firstProductRow = document.querySelector('[id^="product-row-"]');

    if (firstProductRow && typeof window.Echo !== 'undefined') {

        console.log("Product management page detected. Listening for stock updates...");
        window.Echo.channel('products')
            .listen('.StockUpdated', (e) => {
                console.log('Admin received StockUpdated event:', e);
                const productRow = document.getElementById(`product-row-${e.productId}`);
                if (productRow) {
                    const stockCell = productRow.querySelector('.product-stock');
                    const statusCell = productRow.querySelector('.product-status');
                    if (stockCell) {
                        stockCell.textContent = e.newStock;
                    }
                    if (statusCell) {
                        if (e.isAvailable) {
                            statusCell.innerHTML = `<span style="color: green;">Tersedia</span>`;
                        } else {
                            statusCell.innerHTML = `<span style="color: red;">Habis</span>`;
                        }
                    }
                    productRow.classList.add('is-updated');
                    setTimeout(() => {
                        productRow.classList.remove('is-updated');
                    }, 1500); 
                }
            });
    }

    /**
     * ==============================================================
     * LOGIKA STOK KOSONG
     * ==============================================================
     */
    const stockInput = document.getElementById('stock');
        const isAvailableCheckbox = document.getElementById('is_available');

        // Fungsi untuk memeriksa stok dan memperbarui checkbox
        function updateAvailability() {
            const currentStock = parseInt(stockInput.value, 10);

            if (currentStock > 0) {
                isAvailableCheckbox.checked = true;
            } else {
                isAvailableCheckbox.checked = false;
            }
        }

        updateAvailability();

        stockInput.addEventListener('input', updateAvailability);

});   

