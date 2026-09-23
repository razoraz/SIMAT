<!-- FILTER BAR & TABS HIBAH ASET -->
<div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        <!-- Tab Switcher (Semua, Masuk, Keluar) -->
        <div class="flex items-center space-x-1.5 p-1 bg-slate-950/80 rounded-2xl border border-slate-800">
            <button type="button" @click="activeTab = 'all'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2"
                :class="activeTab === 'all' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                <span>Semua Hibah</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="activeTab === 'all' ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-300'"
                    x-text="hibahList.length"></span>
            </button>

            <button type="button" @click="activeTab = 'masuk'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2"
                :class="activeTab === 'masuk' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                <span>🎁 Hibah Masuk</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="activeTab === 'masuk' ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-300'"
                    x-text="hibahList.filter(h => h.tipe_hibah === 'masuk').length"></span>
            </button>

            <button type="button" @click="activeTab = 'keluar'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2"
                :class="activeTab === 'keluar' ? 'bg-rose-500 text-white shadow-md shadow-rose-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                <span>📤 Hibah Keluar</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="activeTab === 'keluar' ? 'bg-slate-950 text-rose-300' : 'bg-slate-800 text-slate-300'"
                    x-text="hibahList.filter(h => h.tipe_hibah === 'keluar').length"></span>
            </button>
        </div>

        <!-- Filter Controls (Tahun, Triwulan, Search) -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Filter Tahun -->
            <div class="flex items-center space-x-1.5">
                <span class="text-[11px] font-bold text-slate-400">Tahun:</span>
                <select x-model="selectedYear"
                    class="bg-slate-950 border border-slate-800 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    <option value="all">Semua Tahun</option>
                    <template x-for="y in availableYears" :key="y">
                        <option :value="y" x-text="y"></option>
                    </template>
                </select>
            </div>

            <!-- Filter Triwulan -->
            <div class="flex items-center space-x-1.5">
                <span class="text-[11px] font-bold text-slate-400">Triwulan:</span>
                <select x-model="selectedTw"
                    class="bg-slate-950 border border-slate-800 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    <option value="all">Semua Triwulan</option>
                    <option value="TW I">TW I (Jan - Mar)</option>
                    <option value="TW II">TW II (Apr - Jun)</option>
                    <option value="TW III">TW III (Jul - Sep)</option>
                    <option value="TW IV">TW IV (Okt - Des)</option>
                </select>
            </div>

            <!-- Search Input -->
            <div class="relative min-w-[220px]">
                <input type="text" x-model="searchQuery"
                    placeholder="Cari BAST, pihak, barang..."
                    class="w-full bg-slate-950 border border-slate-800 focus:border-amber-400 rounded-xl px-3 py-2 pl-8 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button type="button" x-show="searchQuery" @click="searchQuery = ''"
                    class="absolute right-2.5 top-2 text-xs text-slate-400 hover:text-white">
                    ✕
                </button>
            </div>

            <!-- Reset Filter -->
            <button type="button" x-show="selectedYear !== 'all' || selectedTw !== 'all' || searchQuery !== '' || activeTab !== 'all'"
                @click="resetFilter()"
                class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-colors">
                ↺ Reset
            </button>
        </div>

    </div>
</div>
