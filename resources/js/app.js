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