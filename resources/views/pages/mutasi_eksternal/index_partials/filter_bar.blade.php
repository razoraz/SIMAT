<!-- Filter, Direction Tabs & Search Bar Mutasi Eksternal -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
    <div class="flex flex-col gap-4">
        
        <!-- 1. TAB UTAMA ARAH MUTASI EKSTERNAL (MASUK VS KELUAR) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-1.5 p-1.5 bg-slate-950/90 rounded-2xl border border-slate-800/90 w-full sm:w-auto shadow-inner">
                <!-- Tab Mutasi Masuk -->
                <button type="button" @click="activeDirection = 'masuk'"
                    :class="activeDirection === 'masuk' 
                        ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 border-indigo-500/50' 
                        : 'bg-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/80 border-transparent'"
                    class="group flex-1 sm:flex-initial px-4 py-2.5 rounded-xl border text-xs font-bold transition-all duration-200 flex items-center justify-center space-x-2.5 cursor-pointer shrink-0 active:scale-[0.98]">
                    <svg class="w-4 h-4 shrink-0 transition-colors duration-200" :class="activeDirection === 'masuk' ? 'text-indigo-200' : 'text-slate-400 group-hover:text-slate-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Mutasi Masuk (Pelimpahan SKPD)</span>
                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-lg transition-all duration-200"
                        :class="activeDirection === 'masuk' ? 'bg-indigo-950/60 border border-indigo-400/30 text-indigo-100 shadow-sm' : 'bg-slate-900 text-slate-400 border border-slate-800 group-hover:text-slate-300'"
                        x-text="countMasuk"></span>
                </button>

                <!-- Tab Mutasi Keluar -->
                <button type="button" @click="activeDirection = 'keluar'"
                    :class="activeDirection === 'keluar' 
                        ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/30 border-cyan-500/50' 
                        : 'bg-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/80 border-transparent'"
                    class="group flex-1 sm:flex-initial px-4 py-2.5 rounded-xl border text-xs font-bold transition-all duration-200 flex items-center justify-center space-x-2.5 cursor-pointer shrink-0 active:scale-[0.98]">
                    <svg class="w-4 h-4 shrink-0 transition-colors duration-200" :class="activeDirection === 'keluar' ? 'text-cyan-200' : 'text-slate-400 group-hover:text-slate-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Mutasi Keluar (Transfer ke OPD)</span>
                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-lg transition-all duration-200"
                        :class="activeDirection === 'keluar' ? 'bg-cyan-950/60 border border-cyan-400/30 text-cyan-100 shadow-sm' : 'bg-slate-900 text-slate-400 border border-slate-800 group-hover:text-slate-300'"
                        x-text="countKeluar"></span>
                </button>
            </div>

            <!-- Hint Keterangan Arah Mutasi Aktif -->
            <div class="hidden sm:flex items-center space-x-2 text-[11px] text-slate-400 px-3 py-1.5 rounded-xl bg-slate-950/50 border border-slate-800/60">
                <template x-if="activeDirection === 'masuk'">
                    <span class="flex items-center space-x-1.5 text-indigo-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                        <span>Pelimpahan aset dari SKPD luar ke RSUD dr. H. Koesnadi</span>
                    </span>
                </template>
                <template x-if="activeDirection === 'keluar'">
                    <span class="flex items-center space-x-1.5 text-cyan-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span>Transfer / pemindahtanganan aset RSUD ke SKPD / OPD luar</span>
                    </span>
                </template>
            </div>
        </div>

        <!-- 2. Quick Filter Status Dokumen Tabs -->
        <div class="flex items-center gap-2 flex-wrap text-xs bg-slate-950/60 p-2 rounded-2xl border border-slate-800/80">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider px-2.5 shrink-0 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 transition-colors duration-200" :class="activeDirection === 'keluar' ? 'text-cyan-400' : 'text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                STATUS DOKUMEN:
            </span>

            {{-- Button Semua Status --}}
            <button type="button" @click="statusFilter = 'all'"
                :class="statusFilter === 'all' 
                    ? (activeDirection === 'keluar' ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/25 border-cyan-500/50' : 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/25 border-indigo-500/50')
                    : 'bg-slate-900/80 text-slate-400 hover:text-slate-200 hover:bg-slate-800/80 border-slate-800/80'"
                class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all duration-200 flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-[0.98]">
                <span>Semua Dokumen</span>
                <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold rounded-md transition-all duration-200"
                    :class="statusFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400'"
                    x-text="countAll"></span>
            </button>

            {{-- Button Selesai / Disahkan --}}
            <button type="button" @click="statusFilter = 'selesai'"
                :class="statusFilter === 'selesai' 
                    ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/25 border-emerald-500/50' 
                    : 'bg-slate-900/80 text-slate-400 hover:text-emerald-300 hover:bg-slate-800/80 border-slate-800/80'"
                class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all duration-200 flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-[0.98]">
                <span class="text-xs">✓</span>
                <span>Telah Disahkan / Selesai</span>
                <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold rounded-md transition-all duration-200"
                    :class="statusFilter === 'selesai' ? 'bg-emerald-950/60 border border-emerald-400/30 text-emerald-100' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'"
                    x-text="countSelesai"></span>
            </button>

            {{-- Button Menunggu Verifikasi --}}
            <template x-if="countMenunggu > 0">
                <button type="button" @click="statusFilter = 'menunggu_verifikasi'"
                    :class="statusFilter === 'menunggu_verifikasi' 
                        ? 'bg-amber-600 text-white shadow-sm shadow-amber-600/25 border-amber-500/50' 
                        : 'bg-slate-900/80 text-slate-400 hover:text-amber-300 hover:bg-slate-800/80 border-slate-800/80'"
                    class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all duration-200 flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-[0.98]">
                    <span>⏳ Menunggu Verifikasi</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold rounded-md transition-all duration-200"
                        :class="statusFilter === 'menunggu_verifikasi' ? 'bg-amber-950/60 border border-amber-400/30 text-amber-100' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
                        x-text="countMenunggu"></span>
                </button>
            </template>
        </div>

        <!-- 3. Filter Kategori KIB & Search Input Row -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
            <!-- Dropdown Filter Kategori KIB -->
            <div class="w-full sm:w-56 shrink-0">
                <select x-model="categoryFilter"
                    :class="activeDirection === 'keluar' ? 'focus:border-cyan-500 focus:ring-cyan-500/40' : 'focus:border-indigo-500 focus:ring-indigo-500/40'"
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-3.5 py-3 text-xs text-slate-200 focus:outline-none focus:ring-1 transition-all font-semibold">
                    <option value="all">🔍 Semua Kategori KIB</option>
                    <option value="KIB A">🌾 KIB A (Tanah)</option>
                    <option value="KIB B">⚙️ KIB B (Peralatan & Mesin)</option>
                    <option value="KIB C">🏢 KIB C (Gedung & Bangunan)</option>
                    <option value="KIB D">🛣️ KIB D (Jalan, Jaringan, Irigasi)</option>
                    <option value="KIB E">📦 KIB E (Aset Tetap Lainnya)</option>
                    <option value="ATB">💡 Aset Tak Berwujud (ATB)</option>
                </select>
            </div>

            <!-- Search Input -->
            <div class="relative flex-1 w-full">
                <input type="text" x-model="searchQuery" 
                    :placeholder="activeDirection === 'masuk' ? 'Cari nomor BAST / nama SKPD asal / nama aset / NIBAR / dasar SK...' : 'Cari nomor BAST / OPD tujuan / nama aset RSUD / NIBAR / dasar SK...'"
                    :class="activeDirection === 'keluar' ? 'focus:border-cyan-500 focus:ring-cyan-500/40' : 'focus:border-indigo-500 focus:ring-indigo-500/40'"
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-1 transition-all">
                <svg class="w-4 h-4 absolute left-4 top-3.5 transition-colors duration-200" :class="activeDirection === 'keluar' ? 'text-cyan-400' : 'text-indigo-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold cursor-pointer">&times;</button>
            </div>

            <!-- Status Count & Reset -->
            <div class="flex items-center space-x-2 shrink-0">
                <span class="px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                    Menampilkan <span class="font-bold transition-colors duration-200" :class="activeDirection === 'keluar' ? 'text-cyan-400' : 'text-indigo-400'" x-text="filteredMutasis.length"></span> dari <span class="text-white font-bold" x-text="activeDirection === 'masuk' ? countMasuk : countKeluar"></span> Aset
                </span>
                <button type="button" @click="resetFilters()"
                    class="px-3.5 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition-all cursor-pointer">
                    🔄 Reset
                </button>
            </div>
        </div>
    </div>
</div>
