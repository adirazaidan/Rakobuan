document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    const openSidebar = () => {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    };
    const closeSidebar = () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = ''; 
    };
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation(); 
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }
    overlay.addEventListener('click', closeSidebar);

    // Custom Alert Modal Functionality
    const customAlertModal = document.getElementById('customAlertModal');
    const alertModalTitle = document.getElementById('alertModalTitle');
    const alertModalMessage = document.getElementById('alertModalMessage');
    const alertModalIcon = document.getElementById('alertModalIcon');
    const alertModalConfirmBtn = document.getElementById('alertModalConfirmBtn');
    const closeAlertModalBtn = document.getElementById('closeAlertModalBtn');

    // Override native alert function
    window.customAlert = function(message, type = 'info', title = 'Pesan') {
        if (!customAlertModal) return;
        
        // Set modal content
        alertModalTitle.textContent = title;
        alertModalMessage.textContent = message;
        
        // Set icon based on type
        const iconMap = {
            'success': 'fas fa-check-circle',
            'warning': 'fas fa-exclamation-triangle',
            'danger': 'fas fa-times-circle',
            'info': 'fas fa-info-circle'
        };
        alertModalIcon.className = iconMap[type] || iconMap['info'];
        
        // Set modal class for styling
        customAlertModal.className = 'modal-overlay';
        customAlertModal.classList.add(`alert-${type}`);
        
        // Show modal
        customAlertModal.style.display = 'flex';
    };

    // Close modal function
    function closeCustomAlert() {
        if (customAlertModal) {
            customAlertModal.style.display = 'none';
            customAlertModal.classList.remove('alert-success', 'alert-warning', 'alert-danger', 'alert-info');
        }
    }

    // Event listeners for modal
    if (alertModalConfirmBtn) {
        alertModalConfirmBtn.addEventListener('click', closeCustomAlert);
    }
    if (closeAlertModalBtn) {
        closeAlertModalBtn.addEventListener('click', closeCustomAlert);
    }
    if (customAlertModal) {
        customAlertModal.addEventListener('click', (e) => {
            if (e.target === customAlertModal) {
                closeCustomAlert();
            }
        });
    }

    // Override native alert
    window.alert = function(message) {
        customAlert(message, 'info', 'Pesan');
    };

    // Custom confirm function that returns boolean immediately
    window.confirm = function(message) {
        return new Promise((resolve) => {
            const confirmModal = document.createElement('div');
            confirmModal.className = 'modal-overlay';
            confirmModal.style.display = 'flex';
            confirmModal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Konfirmasi</h4>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="alert-icon-wrapper">
                            <i class="fas fa-question-circle" style="color: var(--warning-color);"></i>
                        </div>
                        <p class="alert-message">${message}</p>
                        <div class="alert-actions">
                            <button type="button" class="btn btn-secondary">Batal</button>
                            <button type="button" class="btn btn-primary">OK</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(confirmModal);
            
            // Add event listeners programmatically within the Promise scope
            const closeBtn = confirmModal.querySelector('.modal-close');
            const cancelBtn = confirmModal.querySelector('.btn-secondary');
            const okBtn = confirmModal.querySelector('.btn-primary');
            
            const closeModal = (result) => {
                confirmModal.remove();
                resolve(result);
            };
            
            closeBtn.addEventListener('click', () => closeModal(false));
            cancelBtn.addEventListener('click', () => closeModal(false));
            okBtn.addEventListener('click', () => closeModal(true));
            
            confirmModal.addEventListener('click', (e) => {
                if (e.target === confirmModal) {
                    closeModal(false);
                }
            });
        });
    };

    // Handle delete buttons and cancel buttons with proper confirmation
    document.addEventListener('click', function(e) {
        if (e.target.closest('button[type="submit"]') && e.target.closest('form')) {
            const button = e.target.closest('button[type="submit"]');
            const form = e.target.closest('form');
            
            // Check if this is a delete form
            if (form.querySelector('input[name="_method"][value="DELETE"]') || 
                form.action.includes('destroy') || 
                button.classList.contains('custom-action-btn-danger')) {
                
                e.preventDefault();
                
                // Determine confirmation message based on the form action
                let confirmMessage = 'Yakin ingin menghapus data ini?';
                
                if (form.action.includes('products')) {
                    confirmMessage = 'Yakin ingin menghapus menu ini?';
                } else if (form.action.includes('outlets')) {
                    confirmMessage = 'Apakah Anda yakin ingin menghapus outlet ini?';
                } else if (form.action.includes('orders')) {
                    confirmMessage = 'Yakin ingin menghapus pesanan ini secara permanen?';
                } else if (form.action.includes('dining-tables')) {
                    confirmMessage = 'Yakin ingin menghapus meja ini?';
                } else if (form.action.includes('discounts')) {
                    confirmMessage = 'Yakin ingin menghapus diskon ini?';
                } else if (form.action.includes('categories')) {
                    confirmMessage = 'Yakin ingin menghapus kategori ini? Semua menu di dalamnya juga akan terhapus.';
                } else if (form.action.includes('calls')) {
                    confirmMessage = 'Yakin ingin menghapus panggilan ini?';
                }
                
                // Show confirmation dialog
                confirm(confirmMessage).then((confirmed) => {
                    if (confirmed) {
                        form.submit();
                    }
                });
            }
            
            // Check if this is a cancel button (updateStatus with cancelled status)
            if (form.action.includes('updateStatus') && 
                form.querySelector('input[name="status"][value="cancelled"]')) {
                
                e.preventDefault();
                
                // Show confirmation dialog for cancel
                confirm('Yakin ingin membatalkan pesanan ini?').then((confirmed) => {
                    if (confirmed) {
                        form.submit();
                    }
                });
            }
            
            // Additional check for cancel button by button text
            if (form.action.includes('updateStatus') && 
                (button.textContent.trim().includes('Batal') || 
                 button.querySelector('span') && button.querySelector('span').textContent.trim() === 'Batal')) {
                
                e.preventDefault();
                
                // Show confirmation dialog for cancel
                confirm('Yakin ingin membatalkan pesanan ini?').then((confirmed) => {
                    if (confirmed) {
                        form.submit();
                    }
                });
            }
        }
    });

    // Handle clear session forms
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('clear-session-btn')) {
            e.preventDefault();
            const form = e.target.closest('form');
            if (form) {
                confirm('Yakin ingin membersihkan sesi ini?').then((confirmed) => {
                    if (confirmed) {
                        form.submit();
                    }
                });
            }
        }
    });

    // Image preview function for forms
    window.previewImage = function(event) {
        const reader = new FileReader();
        const imageField = document.getElementById("img-preview");

        reader.onload = function(){
            if (reader.readyState == 2) {
                imageField.style.display = 'block';
                imageField.src = reader.result;
            }
        }
        reader.readAsDataURL(event.target.files[0]);
    };

    // Select2 initialization for admin forms
    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({
                placeholder: '-- Pilih Menu --',
                allowClear: true
            });
        }
    });

    // Logout confirmation function
    window.showLogoutConfirm = function() {
        const confirmModal = document.createElement('div');
        confirmModal.className = 'modal-overlay';
        confirmModal.style.display = 'flex';
        confirmModal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Konfirmasi Keluar</h4>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert-icon-wrapper">
                        <i class="fas fa-sign-out-alt" style="color: var(--warning-color);"></i>
                    </div>
                    <p class="alert-message">Apakah Anda yakin ingin keluar dari sistem admin?</p>
                    <div class="alert-actions">
                        <button type="button" class="btn btn-secondary">Batal</button>
                        <button type="button" class="btn btn-primary">Keluar</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(confirmModal);
        
        // Add event listeners
        const closeBtn = confirmModal.querySelector('.modal-close');
        const cancelBtn = confirmModal.querySelector('.btn-secondary');
        const logoutBtn = confirmModal.querySelector('.btn-primary');
        
        const closeModal = () => {
            confirmModal.remove();
        };
        
        const logout = () => {
            closeModal();
            document.getElementById('logout-form').submit();
        };
        
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        logoutBtn.addEventListener('click', logout);
        
        confirmModal.addEventListener('click', (e) => {
            if (e.target === confirmModal) {
                closeModal();
            }
        });
    };
});