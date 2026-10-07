@php
    $modulesList = [
        ['id' => 'astap', 'name' => 'Master ASTAP', 'icon' => '📦', 'desc' => 'Paket Pengadaan & Register NIBAR Individual'],
        ['id' => 'unit', 'name' => 'Unit & Paviliun', 'icon' => '🏥', 'desc' => 'Ruangan, Paviliun & Lokasi Penempatan RSUD'],
        ['id' => 'distribusi', 'name' => 'Distribusi Aset', 'icon' => '🚚', 'desc' => 'Pengajuan & Dokumen BAST Distribusi Antar-Ruang'],
        ['id' => 'mutasi', 'name' => 'Mutasi Aset', 'icon' => '🔄', 'desc' => 'Mutasi Internal (Ruangan) & Eksternal (OPD)'],
        ['id' => 'hibah', 'name' => 'Hibah Aset', 'icon' => '🎁', 'desc' => 'Arsip BAST Hibah Masuk & Hibah Keluar'],
        ['id' => 'kemitraan', 'name' => 'Kemitraan Aset', 'icon' => '🤝', 'desc' => 'Kerja Sama Operasi (KSO) & PKS Akun 1.5.2'],
        ['id' => 'belanja_barang', 'name' => 'Belanja Barang', 'icon' => '🛒', 'desc' => 'Faktur Belanja & Perbekalan Akun 5.1.02'],
        ['id' => 'reklas', 'name' => 'Reklasifikasi Aset', 'icon' => '⚖️', 'desc' => 'Arsip Transaksi Reklasifikasi & Koreksi 108'],
        ['id' => 'users', 'name' => 'Akun Pengguna', 'icon' => '👥', 'desc' => 'Akun Staf, Pegawai, & Otorisasi Sistem'],
    ];
@endphp

<!-- EXECUTIVE AUDIT HEADER & STREAMLINED SUMMARY -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900/95 to-slate-950 border border-slate-800 p-5 md:p-6 shadow-2xl">
    <div class="absolute -right-12 -top-12 w-64 h-64 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-40 -bottom-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-red-500/15 border border-red-500/30 text-red-300 text-xs font-bold mb-2 shadow-sm">
                <span>♻️ Audit Trail & Central Recycle Bin</span>
            </div>
            <h1 class="text-xl md:text-2xl font-black text-white tracking-tight flex items-center gap-2.5">
                <span>Pusat Data Terhapus</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700/80 font-normal">SIMAT-RK</span>
            </h1>
            <p class="text-slate-400 text-xs mt-1 max-w-xl leading-relaxed">
                Arsip terpusat seluruh inventaris dan dokumen transaksi yang dinonaktifkan sementara. Pulihkan data ke katalog aktif atau musnahkan secara permanen.
            </p>
        </div>

        <!-- EXECUTIVE AUDIT SUMMARY (COMPACT DOCK) -->
        <div class="flex items-center gap-2.5 sm:gap-3 shrink-0 flex-wrap">
            <div class="px-4 py-2.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-left shadow-inner min-w-[120px]">
                <span class="text-[9.5px] uppercase tracking-wider font-bold text-slate-400 block">Total Terhapus</span>
                <div class="flex items-baseline gap-1 mt-0.5">
                    <span class="text-xl font-black text-white font-mono" x-text="totalCount">0</span>
                    <span class="text-[10px] text-slate-400">item</span>
                </div>
            </div>
            <div class="px-4 py-2.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-left shadow-inner min-w-[130px]">
                <span class="text-[9.5px] uppercase tracking-wider font-bold text-slate-400 block">Aktivitas 30 Hari</span>
                <div class="flex items-baseline gap-1 mt-0.5">
                    <span class="text-xl font-black text-amber-400 font-mono" x-text="totalThisMonth">0</span>
                    <span class="text-[10px] text-amber-500/80">arsip</span>
                </div>
            </div>
            <div class="px-3.5 py-2.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <div>
                    <span class="text-[10px] uppercase font-bold text-emerald-300 block leading-tight">Audit Log</span>
                    <span class="text-[10px] text-slate-400 block leading-tight">Terlindungi</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- UNIFIED CONTROL TOOLBAR: MODULE DROPDOWN + SEARCH + QUICK TIME FILTERS -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-4 shadow-xl space-y-4">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        
        <!-- 1. BESPOKE MODULE DROPDOWN SELECTOR (CUSTOM LUXURY POPOVER, NEVER CLIPPED) -->
        <div class="relative w-full lg:w-96 shrink-0" x-data="{ openModDropdown: false }" @click.outside="openModDropdown = false" @keydown.escape.window="openModDropdown = false">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    Kategori Modul:
                </span>
                <span class="text-[10px] text-slate-500 font-mono">8 Kategori Tersedia</span>
            </div>

            <!-- TRIGGER BUTTON -->
            <button type="button" @click="openModDropdown = !openModDropdown"
                class="w-full bg-slate-950 border border-slate-700/80 hover:border-cyan-500/80 rounded-2xl p-2.5 px-3.5 shadow-lg transition-all flex items-center justify-between gap-3 cursor-pointer group focus:outline-none focus:ring-2 focus:ring-cyan-500/40 text-left">
                
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-lg shrink-0 group-hover:scale-105 transition-transform shadow-inner">
                        <span x-show="activeModule === 'astap'">📦</span>
                        <span x-show="activeModule === 'unit'">🏥</span>
                        <span x-show="activeModule === 'distribusi'">🚚</span>
                        <span x-show="activeModule === 'mutasi'">🔄</span>
                        <span x-show="activeModule === 'hibah'">🎁</span>
                        <span x-show="activeModule === 'kemitraan'">🤝</span>
                        <span x-show="activeModule === 'belanja_barang'">🛒</span>
                        <span x-show="activeModule === 'reklas'">⚖️</span>
                        <span x-show="activeModule === 'users'">👥</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs font-black text-white group-hover:text-cyan-300 transition-colors block truncate" x-text="activeModuleName"></span>
                        <span class="text-[10px] text-slate-400 block truncate" x-text="getModuleCount(activeModule) + ' data terhapus'"></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold"
                        :class="getModuleCount(activeModule) > 0 ? 'bg-red-500/20 text-red-300 border border-red-500/30' : 'bg-slate-800 text-slate-400'"
                        x-text="getModuleCount(activeModule)">0</span>
                    <svg class="w-4 h-4 text-cyan-400 transition-transform duration-200" :class="openModDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            <!-- FLOATING POPOVER (ABSOLUTE, Z-50, UNCLIPPED!) -->
            <div x-show="openModDropdown"
                 x-transition:enter="transition ease-out duration-150 transform"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-98"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-2 scale-98"
                 class="absolute left-0 top-full mt-2 w-full sm:w-[480px] z-50 bg-slate-950/98 backdrop-blur-2xl border border-slate-700 rounded-3xl p-3 shadow-2xl shadow-black ring-1 ring-white/10"
                 style="display: none;">
                
                <div class="p-2 pb-2 mb-2 border-b border-slate-800/80 flex items-center justify-between">
                    <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-slate-400">Pilih Kategori Inventaris</span>
                    <span class="text-[10px] text-slate-500">9 Modul</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 max-h-[360px] overflow-y-auto">
                    @foreach($modulesList as $mod)
                        <button type="button" @click="changeTab('{{ $mod['id'] }}'); openModDropdown = false;"
                            class="p-2 rounded-xl border text-left transition-all flex items-center justify-between gap-2.5 cursor-pointer group/item"
                            :class="activeModule === '{{ $mod['id'] }}'
                                ? 'bg-slate-900 border-cyan-500/80 shadow-md shadow-cyan-500/10'
                                : 'bg-slate-900/40 hover:bg-slate-900 border-slate-800/80 hover:border-slate-700'">
                            
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0"
                                    :class="activeModule === '{{ $mod['id'] }}' ? 'bg-cyan-500/20 border border-cyan-500/40' : 'bg-slate-950 border border-slate-800'">
                                    <span>{{ $mod['icon'] }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs font-bold truncate"
                                            :class="activeModule === '{{ $mod['id'] }}' ? 'text-cyan-300' : 'text-slate-200 group-hover/item:text-white'">
                                            {{ $mod['name'] }}
                                        </span>
                                        <template x-if="activeModule === '{{ $mod['id'] }}'">
                                            <span class="text-cyan-400 text-[10px] font-black">✓</span>
                                        </template>
                                    </div>
                                    <span class="text-[9.5px] text-slate-400 truncate block">{{ $mod['desc'] }}</span>
                                </div>
                            </div>

                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold shrink-0"
                                :class="{
                                    'bg-red-500 text-white shadow-sm': getModuleCount('{{ $mod['id'] }}') > 0,
                                    'bg-slate-800 text-slate-500': getModuleCount('{{ $mod['id'] }}') === 0
                                }"
                                x-text="getModuleCount('{{ $mod['id'] }}')">0</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 2. SEARCH INPUT -->
        <div class="relative flex-1 w-full pt-1 lg:pt-0">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                    Pencarian Data:
                </span>
                <span class="text-[10px] text-slate-500 hidden sm:inline" x-text="'Cari di ' + activeModuleName"></span>
            </div>
            <div class="relative">
                <input type="text" x-model="searchQuery" :placeholder="searchPlaceholder"
                    class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 rounded-2xl px-4 py-2.5 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/50 transition-all">
                <svg class="w-4 h-4 text-red-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-2.5 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
            </div>
        </div>

        <!-- 3. QUICK TIME FILTER PRESETS -->
        <div class="shrink-0 w-full lg:w-auto pt-1 lg:pt-0">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    Filter Waktu:
                </span>
                <span class="text-[10px] text-slate-500 font-mono" x-text="timeFilter === 'all' ? 'Semua Waktu' : (timeFilter === '7d' ? '7 Hari Terakhir' : '30 Hari Terakhir')"></span>
            </div>
            <div class="flex items-center p-1 bg-slate-950 rounded-2xl border border-slate-800/80">
                <button type="button" @click="setTimeFilter('all')"
                    :class="timeFilter === 'all' 
                        ? 'bg-slate-800 text-white font-bold shadow-sm' 
                        : 'text-slate-400 hover:text-slate-200'"
                    class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer">
                    Semua
                </button>
                <button type="button" @click="setTimeFilter('7d')"
                    :class="timeFilter === '7d' 
                        ? 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40 shadow-sm' 
                        : 'text-slate-400 hover:text-slate-200 border border-transparent'"
                    class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1">
                    <span>⚡ 7 Hari</span>
                </button>
                <button type="button" @click="setTimeFilter('30d')"
                    :class="timeFilter === '30d' 
                        ? 'bg-rose-500/20 text-rose-300 font-bold border border-rose-500/40 shadow-sm' 
                        : 'text-slate-400 hover:text-slate-200 border border-transparent'"
                    class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-1">
                    <span>📅 30 Hari</span>
                </button>
            </div>
        </div>

    </div>

    <!-- STATUS INFO BARIS BAWAH -->
    <div class="pt-3 border-t border-slate-800/60 flex items-center justify-between text-[11px] text-slate-400">
        <div>
            Kategori saat ini: <strong class="text-white" x-text="activeModuleName"></strong>
        </div>
        <div>
            Menampilkan <span class="font-bold text-white font-mono" x-text="filteredItems.length"></span> dari <span class="font-mono text-cyan-300 font-bold" x-text="currentList.length"></span> data terhapus
        </div>
    </div>
</div>
