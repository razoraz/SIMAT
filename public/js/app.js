/**
 * main.js - JavaScript Utama Aplikasi
 * Sistem Manajemen Aset Rumah Sakit
 * ============================================
 */

// Menunggu seluruh DOM selesai dimuat sebelum menjalankan script
document.addEventListener('DOMContentLoaded', function () {

    // --------------------------------------------------------
    // SIDEBAR TOGGLE - Untuk tampilan mobile/tablet
    // --------------------------------------------------------

    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar      = document.getElementById('appSidebar');
    const overlay      = document.getElementById('sidebarOverlay');

    // Fungsi untuk membuka sidebar
    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    // Fungsi untuk menutup sidebar
    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Event klik tombol hamburger
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function () {
            if (sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    // Menutup sidebar ketika overlay diklik
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Menutup sidebar otomatis ketika layar diperbesar ke desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) {
            closeSidebar();
        }
    });

    // --------------------------------------------------------
    // AUTO-HIDE ALERT - Sembunyikan alert setelah 5 detik
    // --------------------------------------------------------
    const autoAlerts = document.querySelectorAll('.alert-auto-hide');
    autoAlerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // --------------------------------------------------------
    // KONFIRMASI HAPUS - Meminta konfirmasi sebelum menghapus
    // --------------------------------------------------------
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            // Mendapatkan nama aset dari atribut data
            const itemName = this.getAttribute('data-name') || 'data ini';
            const confirmUrl = this.getAttribute('href') || this.getAttribute('data-url');

            // Menampilkan dialog konfirmasi
            const confirmed = confirm(
                'Apakah Anda yakin ingin menghapus ' + itemName + '?\n\n' +
                'Data yang dihapus tidak dapat dikembalikan.'
            );

            if (confirmed && confirmUrl) {
                window.location.href = confirmUrl;
            }
        });
    });

    // --------------------------------------------------------
    // TOOLTIP BOOTSTRAP - Mengaktifkan tooltip Bootstrap
    // --------------------------------------------------------
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(function (el) {
        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Tooltip(el, {
                placement: 'top'
            });
        }
    });

    // --------------------------------------------------------
    // MENU AKTIF - Menandai menu yang sedang aktif berdasarkan URL
    // --------------------------------------------------------
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-item');

    sidebarLinks.forEach(function (link) {
        const href = link.getAttribute('href');
        if (href && currentPath.startsWith(href) && href !== '/') {
            link.classList.add('active');
        } else if (href === '/' && currentPath === '/') {
            link.classList.add('active');
        }
    });

    // --------------------------------------------------------
    // SEARCH FORM - Animasi fokus form pencarian
    // --------------------------------------------------------
    const searchInputs = document.querySelectorAll('.topbar-search input');
    searchInputs.forEach(function (input) {
        input.addEventListener('focus', function () {
            this.parentElement.style.width = '280px';
        });
        input.addEventListener('blur', function () {
            this.parentElement.style.width = '';
        });
    });

    // --------------------------------------------------------
    // FADE IN ANIMATION - Animasi halaman muncul
    // --------------------------------------------------------
    const pageContent = document.querySelector('.page-content');
    if (pageContent) {
        pageContent.classList.add('fade-in');
    }

});

// --------------------------------------------------------
// CHART WARNA KONDISI - Digunakan di halaman dashboard
// --------------------------------------------------------
function initKondisiChart(canvasId, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Tidak Digunakan'],
            datasets: [{
                data: data,
                backgroundColor: ['#2E7D32', '#F57F17', '#C62828', '#546E7A'],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 13, family: 'Nunito' },
                        usePointStyle: true
                    }
                }
            },
            cutout: '65%'
        }
    });
}

// --------------------------------------------------------
// CHART KATEGORI - Digunakan di halaman dashboard
// --------------------------------------------------------
function initKategoriChart(canvasId, labels, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Aset',
                data: data,
                backgroundColor: '#1976D2',
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F0F4F8' },
                    ticks: {
                        font: { size: 12, family: 'Nunito' },
                        stepSize: 1
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12, family: 'Nunito' }
                    }
                }
            }
        }
    });
}
