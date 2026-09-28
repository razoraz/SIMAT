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
                    <span class="text-[10px] text-slate-500 font-medium">data</span>
                </div>
            </div>
            <div class="px-4 py-2.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-left shadow-inner min-w-[120px]">
                <span class="text-[9.5px] uppercase tracking-wider font-bold text-rose-400 block">30 Hari Terakhir</span>
                <div class="flex items-baseline gap-1 mt-0.5">
                    <span class="text-xl font-black text-rose-300 font-mono" x-text="totalThisMonth">{{ $totalThisMonth }}</span>
                    <span class="text-[10px] text-slate-500 font-medium">aktivitas</span>
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

    <!-- COMPACT SEGMENTED PILL NAVIGATION BAR -->
    <div class="mt-5 pt-4 border-t border-slate-800/80">
        <div class="flex items-center justify-between mb-2.5">
            <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                Kategori Modul Data:
            </span>
            <span class="text-[10px] text-slate-500 font-medium" x-text="'Sedang melihat: ' + activeModuleName"></span>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-1.5 scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
            <!-- 1. Master ASTAP -->
            <button type="button" @click="changeTab('astap')"
                :style="activeModule === 'astap' ? 'border-color: #3b82f6; box-shadow: 0 0 14px rgba(59, 130, 246, 0.35);' : ''"
                :class="activeModule === 'astap' 
                    ? 'bg-blue-500/20 border-blue-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>📦 Master ASTAP</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'astap' ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('astap')">0</span>
            </button>

            <!-- 2. Unit & Paviliun -->
            <button type="button" @click="changeTab('unit')"
                :style="activeModule === 'unit' ? 'border-color: #06b6d4; box-shadow: 0 0 14px rgba(6, 182, 212, 0.35);' : ''"
                :class="activeModule === 'unit' 
                    ? 'bg-cyan-500/20 border-cyan-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>🏥 Unit & Paviliun</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'unit' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('unit')">0</span>
            </button>

            <!-- 3. Distribusi Aset -->
            <button type="button" @click="changeTab('distribusi')"
                :style="activeModule === 'distribusi' ? 'border-color: #10b981; box-shadow: 0 0 14px rgba(16, 185, 129, 0.35);' : ''"
                :class="activeModule === 'distribusi' 
                    ? 'bg-emerald-500/20 border-emerald-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>🚚 Distribusi Aset</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'distribusi' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('distribusi')">0</span>
            </button>

            <!-- 4. Mutasi Aset -->
            <button type="button" @click="changeTab('mutasi')"
                :style="activeModule === 'mutasi' ? 'border-color: #f59e0b; box-shadow: 0 0 14px rgba(245, 158, 11, 0.35);' : ''"
                :class="activeModule === 'mutasi' 
                    ? 'bg-amber-500/20 border-amber-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>🔄 Mutasi Aset</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'mutasi' ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('mutasi')">0</span>
            </button>

            <!-- 5. Hibah Aset -->
            <button type="button" @click="changeTab('hibah')"
                :style="activeModule === 'hibah' ? 'border-color: #a855f7; box-shadow: 0 0 14px rgba(168, 85, 247, 0.35);' : ''"
                :class="activeModule === 'hibah' 
                    ? 'bg-purple-500/20 border-purple-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>🎁 Hibah Aset</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'hibah' ? 'bg-purple-500 text-white' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('hibah')">0</span>
            </button>

            <!-- 6. Kemitraan Aset -->
            <button type="button" @click="changeTab('kemitraan')"
                :style="activeModule === 'kemitraan' ? 'border-color: #06b6d4; box-shadow: 0 0 14px rgba(6, 182, 212, 0.35);' : ''"
                :class="activeModule === 'kemitraan' 
                    ? 'bg-cyan-500/20 border-cyan-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>🤝 Kemitraan Aset</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'kemitraan' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('kemitraan')">0</span>
            </button>

            <!-- 7. Belanja Barang -->
            <button type="button" @click="changeTab('belanja_barang')"
                :style="activeModule === 'belanja_barang' ? 'border-color: #10b981; box-shadow: 0 0 14px rgba(16, 185, 129, 0.35);' : ''"
                :class="activeModule === 'belanja_barang' 
                    ? 'bg-emerald-500/20 border-emerald-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>🛒 Belanja Barang</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'belanja_barang' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('belanja_barang')">0</span>
            </button>

            <!-- 8. Akun Pengguna -->
            <button type="button" @click="changeTab('users')"
                :style="activeModule === 'users' ? 'border-color: #f43f5e; box-shadow: 0 0 14px rgba(244, 63, 94, 0.35);' : ''"
                :class="activeModule === 'users' 
                    ? 'bg-rose-500/20 border-rose-500 text-white font-bold' 
                    : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                class="px-3.5 py-2 rounded-xl border text-xs whitespace-nowrap transition-all flex items-center gap-2 cursor-pointer shrink-0">
                <span>👥 Akun Pengguna</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono font-bold"
                    :class="activeModule === 'users' ? 'bg-rose-500 text-white' : 'bg-slate-800 text-slate-400'"
                    x-text="getModuleCount('users')">0</span>
            </button>
        </div>
    </div>
</div>

<!-- TOOLBAR: SEARCH & QUICK TIME FILTER RIBBON -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-4 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
    <!-- PENCARIAN TEKS BEBAS -->
    <div class="relative flex-1 w-full">
        <input type="text" x-model="searchQuery" :placeholder="searchPlaceholder"
            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/50 transition-all">
        <svg class="w-4 h-4 text-red-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-2.5 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
    </div>

    <!-- QUICK TIME FILTER PRESETS & INFO -->
    <div class="flex items-center gap-2 shrink-0 w-full md:w-auto justify-between md:justify-end flex-wrap">
        <!-- Group Tombol Filter Waktu -->
        <div class="flex items-center p-1 bg-slate-950 rounded-2xl border border-slate-800/80">
            <button type="button" @click="setTimeFilter('all')"
                :class="timeFilter === 'all' 
                    ? 'bg-slate-800 text-white font-bold shadow-sm' 
                    : 'text-slate-400 hover:text-slate-200'"
                class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer">
                Semua Waktu
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

        <!-- Info Hasil Pencarian / Filter -->
        <div class="text-[11px] text-slate-400 px-2 font-medium hidden sm:block">
            Menampilkan <span class="font-bold text-white font-mono" x-text="filteredItems.length"></span> dari <span class="font-mono" x-text="currentList.length"></span> data
        </div>
    </div>
</div>
