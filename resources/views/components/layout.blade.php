<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - SIMAT-RK RSUD Dr. H. Koesnandi</title>

    <!-- Favicon Logo RSUD -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">

    <!-- Tailwind CSS (Vite) & Alpine.js & Chart.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @keyframes pageFadeIn {
            from { opacity: 0.3; transform: translateY(3px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-fade-in {
            animation: pageFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Global Cursor Pointer untuk Semua Tombol & Elemen Interaktif */
        button,
        [type="button"],
        [type="submit"],
        [type="reset"],
        a[role="button"],
        .btn,
        select,
        summary {
            cursor: pointer !important;
        }

        button:disabled,
        [type="button"]:disabled,
        [type="submit"]:disabled,
        [type="reset"]:disabled,
        [disabled] {
            cursor: not-allowed !important;
        }

        /* Global Elegant Dark Scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #334155 #0b1120;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0b1120;
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 8px;
            border: 1px solid #1e293b;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0d9488;
        }


        @media print {
            .no-print, aside, header, footer, #topbar, nav, button, .no-print * {
                display: none !important;
            }
            body, main, html, div[x-data] {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                overflow: visible !important;
            }
            .fixed.inset-0 {
                position: static !important;
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .bg-slate-900, .bg-slate-950 {
                background: white !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #print-area-bast, #print-area-distribusi, #print-area-triwulan, #print-area-kir, #print-area-mutasi {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                max-height: none !important;
                overflow: visible !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                box-sizing: border-box !important;
            }
            @page {
                size: auto;
                margin: 12mm 15mm 12mm 15mm;
            }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased selection:bg-emerald-500 selection:text-slate-950">
    <div x-data="{ 
            sidebarOpen: window.innerWidth >= 1024,
            isMobile: window.innerWidth < 1024
         }" 
         @resize.window="
            isMobile = window.innerWidth < 1024;
            if (!isMobile) { sidebarOpen = true; }
         " 
         class="min-h-screen flex bg-slate-950 relative">
        
        <!-- Mobile Dark Backdrop Overlay -->
        <div x-show="sidebarOpen && isMobile" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden" x-cloak>
        </div>

        <!-- Panggil Sidebar -->
        @include('partials.sidebar')

        <!-- Area Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen w-full">
            <!-- Panggil Topbar -->
            @include('partials.topbar')

            <!-- Slot Konten Utama -->
            <main class="flex-1 p-3 sm:p-6 lg:p-8 page-fade-in {{ ($fullWidth ?? false) ? 'w-full max-w-none' : 'max-w-7xl mx-auto w-full' }}">
                
                <!-- BANNER NOTIFIKASI PEMBERITAHUAN SISTEM (Murni Blade Server-Side, Zero JS Bug) -->
                @if (session('success') || session('error') || session('warning') || session('info') || session('status'))
                    @php
                        $msg = session('success') ?? session('error') ?? session('warning') ?? session('info') ?? session('status');
                        $msgLower = strtolower($msg);

                        if (session('error')) {
                            $type = 'error';
                            $title = 'TERJADI KESALAHAN';
                            $icon = '⚠️';
                        } elseif (session('warning')) {
                            $type = 'warning';
                            $title = 'PERHATIAN';
                            $icon = '⚠️';
                        } elseif (session('info') || session('status')) {
                            $type = 'info';
                            $title = 'PEMBERITAHUAN SISTEM';
                            $icon = 'ℹ️';
                        } elseif (str_contains($msgLower, 'dihapus') || str_contains($msgLower, 'hapus')) {
                            $type = 'delete';
                            $title = 'BERHASIL DIHAPUS';
                            $icon = '🗑️';
                        } elseif (str_contains($msgLower, 'diperbarui') || str_contains($msgLower, 'diubah') || str_contains($msgLower, 'update') || str_contains($msgLower, 'ubah')) {
                            $type = 'update';
                            $title = 'BERHASIL DIPERBARUI';
                            $icon = '✏️';
                        } else {
                            $type = 'create';
                            $title = 'BERHASIL DITAMBAHKAN';
                            $icon = '➕';
                        }
                    @endphp

                    <div id="simat-flash-banner" x-data="{ showBanner: true }" x-show="showBanner"
                         class="mb-6 w-full bg-slate-900/95 border backdrop-blur-2xl rounded-3xl p-4 sm:p-5 shadow-2xl space-y-3 relative overflow-hidden transition-all duration-300
                         @if($type === 'create') border-emerald-500/40 shadow-emerald-950/30 bg-gradient-to-r from-emerald-500/15 via-slate-900/95 to-slate-900/95
                         @elseif($type === 'update') border-amber-500/40 shadow-amber-950/30 bg-gradient-to-r from-amber-500/15 via-slate-900/95 to-slate-900/95
                         @elseif($type === 'delete' || $type === 'error') border-rose-500/40 shadow-rose-950/30 bg-gradient-to-r from-rose-500/15 via-slate-900/95 to-slate-900/95
                         @elseif($type === 'warning') border-amber-500/40 shadow-amber-950/30 bg-gradient-to-r from-amber-500/15 via-slate-900/95 to-slate-900/95
                         @else border-cyan-500/40 shadow-cyan-950/30 bg-gradient-to-r from-cyan-500/15 via-slate-900/95 to-slate-900/95 @endif">
                        
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start space-x-3.5 min-w-0 flex-1">
                                <!-- Icon Badge 3D Glow -->
                                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl shrink-0 border shadow-lg
                                     @if($type === 'create') bg-emerald-500/20 text-emerald-300 border-emerald-500/30 shadow-emerald-500/10
                                     @elseif($type === 'update') bg-amber-500/20 text-amber-300 border-amber-500/30 shadow-amber-500/10
                                     @elseif($type === 'delete' || $type === 'error') bg-rose-500/20 text-rose-400 border-rose-500/30 shadow-rose-500/10
                                     @elseif($type === 'warning') bg-amber-500/20 text-amber-300 border-amber-500/30 shadow-amber-500/10
                                     @else bg-cyan-500/20 text-cyan-300 border-cyan-500/30 shadow-cyan-500/10 @endif">
                                    {{ $icon }}
                                </div>

                                <!-- Header & Subtitle Content -->
                                <div class="min-w-0 flex-1 space-y-0.5 pt-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border
                                             @if($type === 'create') bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                                             @elseif($type === 'update') bg-amber-500/20 text-amber-300 border-amber-500/30
                                             @elseif($type === 'delete' || $type === 'error') bg-rose-500/20 text-rose-300 border-rose-500/30
                                             @elseif($type === 'warning') bg-amber-500/20 text-amber-300 border-amber-500/30
                                             @else bg-cyan-500/20 text-cyan-300 border-cyan-500/30 @endif">
                                            {{ $title }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-semibold">• Berhasil Diproses</span>
                                    </div>
                                    <p class="text-xs sm:text-sm font-bold text-white leading-relaxed">{{ $msg }}</p>
                                </div>
                            </div>

                            <!-- Tombol Close (x) -->
                            <button type="button" @click="showBanner = false" onclick="this.closest('.mb-6').remove()" class="text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl w-8 h-8 flex items-center justify-center transition-all shrink-0 font-bold text-lg cursor-pointer">
                                &times;
                            </button>
                        </div>

                        <!-- Bottom Glowing Accent Line Bar -->
                        <div class="w-full bg-slate-800/80 rounded-full h-1 overflow-hidden">
                            <div class="h-full rounded-full w-full animate-pulse
                                 @if($type === 'create') bg-emerald-500
                                 @elseif($type === 'update') bg-amber-500
                                 @elseif($type === 'delete' || $type === 'error') bg-rose-500
                                 @elseif($type === 'warning') bg-amber-500
                                 @else bg-cyan-500 @endif"></div>
                        </div>
                    </div>

                    <script>
                        if ('scrollRestoration' in history) {
                            history.scrollRestoration = 'manual';
                        }
                        window.scrollTo({ top: 0, behavior: 'instant' });
                    </script>
                @endif

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const clientMsg = sessionStorage.getItem('flash_success') || sessionStorage.getItem('flash_error') || sessionStorage.getItem('flash_info');
                        if (clientMsg) {
                            const isError = !!sessionStorage.getItem('flash_error');
                            const isDelete = clientMsg.toLowerCase().includes('hapus') || clientMsg.toLowerCase().includes('dihapus');
                            const isUpdate = clientMsg.toLowerCase().includes('perbarui') || clientMsg.toLowerCase().includes('ubah') || clientMsg.toLowerCase().includes('update');
                            
                            sessionStorage.removeItem('flash_success');
                            sessionStorage.removeItem('flash_error');
                            sessionStorage.removeItem('flash_info');

                            if (!document.getElementById('simat-flash-banner')) {
                                const bannerDiv = document.createElement('div');
                                bannerDiv.id = 'simat-flash-banner';
                                bannerDiv.className = 'mb-6 w-full bg-slate-900/95 border backdrop-blur-2xl rounded-3xl p-4 sm:p-5 shadow-2xl space-y-3 relative overflow-hidden transition-all duration-300 ' + 
                                    (isError ? 'border-rose-500/40 shadow-rose-950/20' : (isDelete ? 'border-rose-500/40 shadow-rose-950/20' : (isUpdate ? 'border-amber-500/40 shadow-amber-950/20' : 'border-emerald-500/40 shadow-emerald-950/20')));
                                
                                const titleText = isError ? 'TERJADI KESALAHAN' : (isDelete ? 'BERHASIL DIHAPUS' : (isUpdate ? 'BERHASIL DIPERBARUI' : 'BERHASIL DITAMBAHKAN'));
                                const iconText = isError ? '⚠️' : (isDelete ? '🗑️' : (isUpdate ? '✏️' : '➕'));
                                const badgeClass = isError ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : (isDelete ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : (isUpdate ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'));
                                const lineClass = isError ? 'bg-rose-500' : (isDelete ? 'bg-rose-500' : (isUpdate ? 'bg-amber-500' : 'bg-emerald-500'));

                                bannerDiv.innerHTML = `
                                    <div class="flex items-start justify-between space-x-3">
                                        <div class="flex items-start space-x-3.5 min-w-0">
                                            <div class="p-2.5 sm:p-3 rounded-2xl bg-slate-800/80 border border-slate-700/60 text-xl sm:text-2xl shrink-0">
                                                ${iconText}
                                            </div>
                                            <div class="min-w-0 flex-1 space-y-0.5 pt-0.5">
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border ${badgeClass}">
                                                        ${titleText}
                                                    </span>
                                                    <span class="text-[10px] text-slate-400 font-semibold">• Berhasil Diproses</span>
                                                </div>
                                                <p class="text-xs sm:text-sm font-bold text-white leading-relaxed">${clientMsg}</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="this.closest('#simat-flash-banner').remove()" class="text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl w-8 h-8 flex items-center justify-center transition-all shrink-0 font-bold text-lg cursor-pointer">&times;</button>
                                    </div>
                                    <div class="w-full bg-slate-800/80 rounded-full h-1 overflow-hidden">
                                        <div class="h-full rounded-full w-full animate-pulse ${lineClass}"></div>
                                    </div>
                                `;

                                const mainContainer = document.querySelector('main');
                                if (mainContainer) {
                                    mainContainer.insertBefore(bannerDiv, mainContainer.firstChild);
                                    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
                                    window.scrollTo({ top: 0, behavior: 'instant' });
                                }
                            }
                        }
                    });
                </script>

                {{ $slot }}
            </main>

            <!-- Panggil Footer -->
            @include('partials.footer')
        </div>

    </div>

    <!-- GLOBAL SIMAT-RK CONFIRMATION DIALOG MODAL & TOAST SYSTEM (Sleek Dark Theme) -->
    <div x-data="{
            showConfirm: false,
            confirmData: {
                title: 'Konfirmasi Tindakan',
                message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                itemName: '',
                type: 'danger',
                btnText: 'Ya, Lanjutkan',
                onConfirm: null
            },
            toast: {
                show: false,
                message: '',
                type: 'success'
            },
            openConfirm(detail) {
                this.confirmData = {
                    title: detail.title || 'Konfirmasi Tindakan',
                    message: detail.message || 'Apakah Anda yakin ingin melanjutkan?',
                    itemName: detail.itemName || '',
                    type: detail.type || 'danger',
                    btnText: detail.btnText || (detail.type === 'danger' ? 'Ya, Hapus Data' : (detail.type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
                    onConfirm: detail.onConfirm
                };
                this.showConfirm = true;
            },
            executeConfirmed() {
                if (typeof this.confirmData.onConfirm === 'function') {
                    this.confirmData.onConfirm();
                }
                this.showConfirm = false;
            },
            openToast(detail) {
                this.toast.message = detail.message;
                this.toast.type = detail.type || 'success';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 4000);
            }
         }"
         @ask-confirm.window="openConfirm($event.detail)"
         @show-toast.window="openToast($event.detail)">
         
        <!-- GLOBAL CONFIRMATION MODAL -->
        <div x-show="showConfirm" x-cloak class="fixed inset-0 z-[999999] flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showConfirm = false"
                 x-show="showConfirm"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative overflow-hidden">
                
                <!-- Accent Line Header Bar -->
                <div class="h-1 -mx-6 -mt-6 mb-4"
                     :class="{
                         'bg-rose-500': confirmData.type === 'danger',
                         'bg-amber-500': confirmData.type === 'warning',
                         'bg-emerald-500': confirmData.type === 'success',
                         'bg-cyan-500': confirmData.type === 'info'
                     }"></div>

                <div class="flex items-start space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 font-bold border"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : (confirmData.type === 'success' ? '➕' : 'ℹ️'))"></span>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base font-extrabold text-white leading-snug" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <template x-if="confirmData.itemName">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Item Target:</span>
                        <p class="text-xs font-bold text-cyan-300 truncate font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showConfirm = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmed()"
                        class="px-5 py-2 rounded-xl font-extrabold text-xs shadow-md transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5"
                        :class="{
                            'bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30': confirmData.type === 'danger',
                            'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/30': confirmData.type === 'warning',
                            'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/30': confirmData.type === 'success',
                            'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/30': confirmData.type === 'info'
                        }">
                        <span x-text="confirmData.btnText"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- GLOBAL TOAST POPUP -->
        <div x-show="toast.show" x-cloak
             x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4 scale-95"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform opacity-100 translate-y-0 scale-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="fixed bottom-6 right-6 z-[999999] max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
             :class="{
                 'border-emerald-500/40 text-emerald-300': toast.type === 'success',
                 'border-rose-500/40 text-rose-300': toast.type === 'error',
                 'border-amber-500/40 text-amber-300': toast.type === 'warning',
                 'border-cyan-500/40 text-cyan-300': toast.type === 'info'
             }">
            <div class="flex items-center space-x-2.5 min-w-0">
                <span class="text-base shrink-0" x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '⚠️' : 'ℹ️')"></span>
                <p class="text-xs font-bold leading-snug truncate" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>
    </div>

    <script>
        window.askSimatConfirm = function(options) {
            window.dispatchEvent(new CustomEvent('ask-confirm', { detail: options }));
        };
        window.showSimatToast = function(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('show-toast', { detail: { message, type } }));
        };
        
        // Safeguard Override window.confirm agar pop-up bawaan browser tidak pernah muncul lagi
        window.confirm = function(message) {
            window.askSimatConfirm({
                title: '⚠️ Konfirmasi Tindakan',
                message: String(message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?').replace(/^[\s⚠️]+/, ''),
                type: 'danger',
                btnText: 'Ya, Lanjutkan'
            });
            return false;
        };

        // Helper form submit handler dengan konfirmasi kustom
        window.confirmSimatFormSubmit = function(event, message, title = 'Konfirmasi Hapus Data', itemName = '') {
            event.preventDefault();
            const form = event.target;
            window.askSimatConfirm({
                title: title,
                message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                itemName: itemName,
                type: 'danger',
                btnText: '🗑️ Ya, Hapus',
                onConfirm: function() {
                    form.submit();
                }
            });
            return false;
        };
    </script>
</body>
</html>
