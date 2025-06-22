document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');

    // Buat elemen overlay secara dinamis
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);

    // Fungsi untuk membuka sidebar
    const openSidebar = () => {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Mencegah scroll di belakang
    };

    // Fungsi untuk menutup sidebar
    const closeSidebar = () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Mengizinkan scroll kembali
    };

    // Tambahkan event listener ke tombol hamburger
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Mencegah event "klik" menyebar
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }
    
    // Tambahkan event listener ke overlay untuk menutup sidebar
    overlay.addEventListener('click', closeSidebar);
});