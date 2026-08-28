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

    <!-- Tailwind CSS (Vite) & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

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
            #print-area-bast, #print-area-distribusi, #print-area-triwulan, #print-area-kir {
                display: block !important;
                width: 100% !important;
                max-height: none !important;
                overflow: visible !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.2cm 1.2cm 1.2cm 1.2cm;
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
            <main class="flex-1 p-3 sm:p-6 lg:p-8 {{ ($fullWidth ?? false) ? 'w-full max-w-none' : 'max-w-7xl mx-auto w-full' }}">
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
