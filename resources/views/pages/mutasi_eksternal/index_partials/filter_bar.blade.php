<!-- Filter, Quick Tabs & Search Bar Mutasi Eksternal -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
    <div class="flex flex-col gap-4">
        
        <!-- Quick Filter Status Mutasi Eksternal Tabs -->
        <div class="flex items-center gap-2 flex-wrap text-xs bg-slate-950/60 p-2 rounded-2xl border border-slate-800/80">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider px-2.5 shrink-0 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                STATUS DOKUMEN:
            </span>

            {{-- Button Semua Status --}}
            <button type="button" @click="statusFilter = 'all'"
                :class="statusFilter === 'all' 
                    ? 'bg-indigo-600 text-white font-extrabold shadow-lg shadow-indigo-600/25 border-indigo-400 ring-2 ring-indigo-500/30' 
                    : 'bg-slate-900/90 text-slate-400 hover:text-white hover:bg-slate-800 border-slate-800'"
                class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                <span>Semua Dokumen</span>
                <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                    :class="statusFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400'"
                    x-text="countAll"></span>
            </button>

            {{-- Button Selesai / Disahkan --}}
            <button type="button" @click="statusFilter = 'selesai'"
                :class="statusFilter === 'selesai' 
                    ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-lg shadow-emerald-500/25 border-emerald-400 ring-2 ring-emerald-500/30' 
                    : 'bg-slate-900/90 text-slate-400 hover:text-emerald-300 hover:bg-slate-800 border-slate-800'"
                class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                <span>✓ Telah Disahkan / Selesai</span>
                <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                    :class="statusFilter === 'selesai' ? 'bg-slate-950/40 text-slate-950' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'"
                    x-text="countSelesai"></span>
            </button>

            {{-- Button Menunggu Verifikasi --}}
            <button type="button" @click="statusFilter = 'menunggu_verifikasi'"
                :class="statusFilter === 'menunggu_verifikasi' 
                    ? 'bg-amber-500 text-slate-950 font-extrabold shadow-lg shadow-amber-500/25 border-amber-400 ring-2 ring-amber-500/30' 
                    : 'bg-slate-900/90 text-slate-400 hover:text-amber-300 hover:bg-slate-800 border-slate-800'"
                class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                <span>⏳ Menunggu Verifikasi</span>
                <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                    :class="statusFilter === 'menunggu_verifikasi' ? 'bg-slate-950/40 text-slate-950' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
                    x-text="countMenunggu"></span>
            </button>
        </div>

        <!-- Filter Kategori KIB & Search Input Row -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
            <!-- Dropdown Filter Kategori KIB -->
            <div class="w-full sm:w-56 shrink-0">
                <select x-model="categoryFilter"
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-3.5 py-3 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all font-semibold">
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
                <input type="text" x-model="searchQuery" placeholder="Cari nomor BAST / nama SKPD asal / nama aset / NIBAR / dasar SK..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all">
                <svg class="w-4 h-4 text-indigo-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold cursor-pointer">&times;</button>
            </div>

            <!-- Status Count & Reset -->
            <div class="flex items-center space-x-2 shrink-0">
                <span class="px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                    Menampilkan <span class="text-indigo-400 font-bold" x-text="filteredMutasis.length"></span> dari <span class="text-white font-bold" x-text="mutasiEksternals.length"></span> Aset
                </span>
                <button type="button" @click="resetFilters()"
                    class="px-3.5 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition-all cursor-pointer">
                    🔄 Reset
                </button>
            </div>
        </div>
    </div>
</div>
