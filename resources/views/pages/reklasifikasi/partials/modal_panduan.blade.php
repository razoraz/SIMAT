<!-- ============================================================
     MODAL KAMUS PANDUAN KIB PMDN 108 (STANDALONE ALPINE)
     Trigger: window.dispatchEvent(new CustomEvent('open-panduan-kib'))
     Standalone x-data = TIDAK bergantung parent masterReklasifikasi()
     ============================================================ -->
<div x-data="{
        showPanduanModal: false,
        panduanSearch: '',
        panduanFilterKib: 'all',

        init() {
            window.__panduanModal = this;
            window.addEventListener('open-panduan-kib', () => {
                this.panduanSearch = '';
                this.panduanFilterKib = 'all';
                this.showPanduanModal = true;
            });
        },

        get panduanList() {
            return window.panduanKibList || [];
        },

        get filteredPanduanList() {
            const q = (this.panduanSearch || '').toLowerCase().trim();
            const kib = this.panduanFilterKib;
            return this.panduanList.filter(item => {
                const matchKib = (kib === 'all' || item.kib === kib || (kib === 'ASET LAINNYA' && item.kib === 'KEMITRAAN'));
                if (!matchKib) return false;
                if (!q) return true;
                return item.prefix.toLowerCase().includes(q) ||
                       item.nama.toLowerCase().includes(q) ||
                       item.contohRs.toLowerCase().includes(q) ||
                       item.subRincian.toLowerCase().includes(q) ||
                       (item.kib && item.kib.toLowerCase().includes(q));
            });
        },
     }"
     @keydown.escape.window="showPanduanModal = false">

    <!-- Teleport ke <body> agar berada di atas sidebar (z-50) & topbar (z-30) -->
    <template x-teleport="body">
        <div x-show="showPanduanModal"
             style="display:none; z-index: 99999 !important;"
             @keydown.escape.window="showPanduanModal = false"
             class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-6 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <!-- Backdrop Blur (Full Screen Cover) -->
            <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md" @click="showPanduanModal = false"></div>

            <!-- Modal Dialog Container -->
            <div class="relative w-full max-w-4xl max-h-[88vh] flex flex-col bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden z-10 my-auto"
                 x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <!-- Header Strip Accent -->
            <div class="h-1 w-full bg-gradient-to-r from-cyan-500 via-indigo-500 to-purple-500 shrink-0"></div>

            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-slate-800 bg-slate-950/70 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                            Kamus &amp; Panduan Pengelompokan Aset (PMDN 108)
                        </h3>
                        <p class="text-xs text-slate-400">Pedoman klasifikasi barang, kemitraan, dan contoh riil di RSUD Dr. H. Koesnandi</p>
                    </div>
                </div>
                <button type="button" @click="showPanduanModal = false"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Filter & Search Bar Dalam Modal -->
            <div class="p-4 border-b border-slate-800/80 bg-slate-950/40 flex flex-col sm:flex-row items-center gap-3 shrink-0">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" x-model="panduanSearch" placeholder="Cari kelompok, kode prefix (misal: 1.5.2, 1.3.2.01), atau nama barang..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-800/90 border border-slate-700 text-xs text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 transition-all">
                </div>

                <!-- KIB Category Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 scrollbar-none">
                    <template x-for="kib in ['all', 'KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F', 'KEMITRAAN', 'ASET LAINNYA', 'KOREKSI']" :key="kib">
                        <button type="button" @click="panduanFilterKib = kib"
                            :class="panduanFilterKib === kib ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700'"
                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold whitespace-nowrap transition-colors cursor-pointer"
                            x-text="kib === 'all' ? 'Semua' : (kib === 'KEMITRAAN' ? '🤝 Kemitraan (1.5.2)' : kib)">
                        </button>
                    </template>
                </div>
            </div>

            <!-- Body Modal: Daftar Kartu Panduan (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 scrollbar-thin scrollbar-thumb-slate-700">
                <!-- State: data belum terload -->
                <template x-if="panduanList.length === 0">
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-slate-500 mb-3">
                            <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <p class="text-xs text-slate-400">Memuat data kamus KIB...</p>
                    </div>
                </template>

                <template x-for="item in filteredPanduanList" :key="item.prefix">
                    <div class="bg-slate-800/60 border border-slate-700/70 hover:border-indigo-500/50 rounded-2xl p-4 transition-all duration-200 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 pb-2.5 border-b border-slate-700/60">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2 py-0.5 rounded-md font-mono text-xs font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/30"
                                    x-text="item.prefix">
                                </span>
                                <h4 class="text-sm font-black text-white" x-text="item.nama"></h4>
                            </div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-slate-900 border border-slate-700 text-slate-300 self-start"
                                x-text="item.kib">
                            </span>
                        </div>

                        <!-- Contoh Riil di RSUD Dr. H. Koesnandi -->
                        <div class="mt-3 bg-indigo-950/30 border border-indigo-500/20 rounded-xl p-3">
                            <div class="flex items-center gap-1.5 text-xs font-extrabold text-indigo-400 mb-1">
                                <span>🏥</span>
                                <span>Contoh Barang Riil Khas RSUD Dr. H. Koesnandi:</span>
                            </div>
                            <p class="text-xs text-indigo-200/90 leading-relaxed" x-text="item.contohRs"></p>
                        </div>

                        <!-- Sub-Rincian Resmi Permendagri 108 -->
                        <div class="mt-2.5 pt-2 flex items-start gap-2 text-[11px] text-slate-400">
                            <span class="font-bold text-slate-300 shrink-0">Sub-Rincian PMDN:</span>
                            <span class="leading-relaxed text-slate-300" x-text="item.subRincian"></span>
                        </div>
                    </div>
                </template>

                <!-- State Kosong jika Pencarian Tidak Ditemukan -->
                <template x-if="panduanList.length > 0 && filteredPanduanList.length === 0">
                    <div class="p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-slate-500 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-white">Tidak Ada Informasi yang Cocok</h4>
                        <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain atau pilih kategori KIB yang berbeda.</p>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between text-xs text-slate-400 shrink-0">
                <span>💡 Tips: Gunakan filter KIB di atas untuk mempersempit pencarian kategori aset</span>
                <button type="button" @click="showPanduanModal = false"
                    class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all cursor-pointer">
                    Tutup Panduan
                </button>
            </div>
        </div>
    </template>
</div>
