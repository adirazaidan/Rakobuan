import './bootstrap';

/**
 * Setelah Echo diinisialisasi di bootstrap.js,
 * kita langsung menyiapkan listener di sini.
 */

// Kita tambahkan pengecekan ini agar skrip hanya berjalan jika kita
// berada di halaman yang memiliki elemen .order-container (halaman order admin)
if (document.querySelector('.order-container')) {

    console.log("Admin order page detected. Initializing Echo listener...");

    // Inisialisasi audio player
    const notificationSound = new Audio('/sounds/notification.mp3');

    // Dengarkan siaran di channel privat 'orders'
    window.Echo.private('orders')
    .listen('NewOrderReceived', (e) => {
        console.log('Event NewOrderReceived Diterima:', e.order);

        // 1. Mainkan suara notifikasi
        notificationSound.play().catch(error => console.error("Gagal memutar suara:", error));

        // 2. Tampilkan notifikasi bar yang modern
        const notificationBar = document.getElementById('newOrderNotification');
        if (notificationBar) {
            // Isi pesan dan tampilkan bar
            notificationBar.innerHTML = `<i class="fa-solid fa-bell fa-shake"></i> Pesanan Baru dari Meja ${e.order.table_number}!`;
            notificationBar.classList.add('show');

            // 3. Atur timer untuk menyembunyikan notifikasi dan me-refresh halaman
            setTimeout(() => {
                notificationBar.classList.remove('show'); // Sembunyikan lagi barnya

                // Beri jeda sedikit lagi sebelum reload
                setTimeout(() => {
                    window.location.reload(); 
                }, 500); // 0.5 detik

            }, 5000); // Notifikasi akan terlihat selama 5 detik
        }
    });

    console.log("Listening for events on 'private-orders' channel...");
}

if (document.querySelector('.call-container')) {
    console.log("Admin call page detected. Initializing Echo listener for calls...");
    const notificationSound = new Audio('/sounds/notification.mp3');

    // Dengarkan di channel 'calls'
    window.Echo.private('calls')
        .listen('NewCallReceived', (e) => {
            console.log('Event NewCallReceived Diterima:', e.call);

            notificationSound.play().catch(error => console.error("Gagal memutar suara:", error));

            const notificationBar = document.getElementById('newOrderNotification');
            if (notificationBar) {
                notificationBar.innerHTML = `<i class="fa-solid fa-bell fa-shake"></i> Panggilan Baru dari Meja ${e.call.table_number}!`;
                notificationBar.classList.add('show');

                setTimeout(() => {
                    notificationBar.classList.remove('show');
                    setTimeout(() => {
                        window.location.reload(); 
                    }, 500);
                }, 5000);
            }
        });

    console.log("Listening for events on 'private-calls' channel...");
}

const tableGrid = document.querySelector('.table-visual-grid');

if (tableGrid) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Listener untuk menangani klik pada tombol 'antar'
    tableGrid.addEventListener('click', function(e) {
        const deliverButton = e.target.closest('.btn-deliver-action');
        if (deliverButton && !deliverButton.disabled) {
            const url = deliverButton.dataset.url;
            deliverButton.disabled = true; // Nonaktifkan tombol sementara

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Gagal memperbarui status.');
                    deliverButton.disabled = false; // Aktifkan kembali jika gagal
                }
                // Jika sukses, kita tidak perlu melakukan apa-apa,
                // karena event TableStatusUpdated akan menangani pembaruan UI.
            })
            .catch(error => {
                console.error("Error:", error);
                deliverButton.disabled = false;
            });
        }
    });

    // Listener utama untuk semua pembaruan status meja
    window.Echo.private('layout-tables')
        .listen('TableStatusUpdated', (e) => {
            console.log('Event TableStatusUpdated Diterima:', e.table);
            const table = e.table;
            const tableId = e.tableId;
            const cardToReplace = document.getElementById(`table-card-${tableId}`);

            if (cardToReplace) {
                // Buat permintaan untuk mengambil HTML baru dari server
                fetch(`/admin/dining-tables/${tableId}/render`)
                    .then(response => response.text())
                    .then(html => {
                        // Ganti seluruh elemen kartu yang lama dengan HTML yang baru
                        cardToReplace.outerHTML = html;
                    })
                    .catch(error => console.error('Error fetching new card HTML:', error));
            }
            const card = document.getElementById(`table-card-${table.id}`);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (card) {
                // 1. Update Class dan Status Teks berdasarkan session_id dan is_locked
                card.classList.remove('is-locked', 'is-occupied');
                const statusTextContainer = card.querySelector('.table-status-text');
                
                if (table.is_locked) {
                    card.classList.add('is-locked');
                    statusTextContainer.innerHTML = `<span class="status-locked">Terkunci</span>`;
                } else if (table.session_id) { // <-- GUNAKAN session_id UNTUK STATUS DIDUDUKI
                    card.classList.add('is-occupied');
                    statusTextContainer.innerHTML = `<span class="status-occupied">Diduduki</span>`;
                } else {
                    statusTextContainer.innerHTML = `<span class="status-available">Tersedia</span>`;
                }

                let orderDetailsContainer = card.querySelector('.table-card-order-details');
                if (table.active_order) {
                } else {
                    if (orderDetailsContainer) orderDetailsContainer.remove();
                }

                let footer = card.querySelector('.table-card-footer');
                if (table.session_id) {
                    if (!footer) {
                        footer = document.createElement('div');
                        footer.className = 'table-card-footer';
                        card.appendChild(footer);
                    }
                    footer.innerHTML = `
                        <form action="/admin/dining-tables/${table.id}/clear-session" method="POST" onsubmit="return confirm('Yakin ingin membersihkan sesi meja ini?');">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn btn-sm btn-info w-100">Clear Session</button>
                        </form>`;
                } else {
                    if (footer) footer.remove();
                }
            }
        });
    console.log("Listening for table status updates...");
}