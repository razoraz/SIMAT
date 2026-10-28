<!-- FILTER BAR & TABS HIBAH ASET -->
<div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
    <!-- Baris 1: Tab Switcher (Kiri) & Info Counter + Reset Button (Kanan) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <!-- Tab Switcher (Semua, Masuk, Keluar) -->
        <div class="flex items-center space-x-1.5 p-1 bg-slate-950/80 rounded-2xl border border-slate-800/90 shadow-inner">
            <button type="button" @click="activeTab = 'all'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer"
                :class="activeTab === 'all' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                <span>Semua Hibah</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="activeTab === 'all' ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-300'"
                    x-text="hibahList.length"></span>
            </button>

            <button type="button" @click="activeTab = 'masuk'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer"
                :class="activeTab === 'masuk' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                <span>🎁 Hibah Masuk</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="activeTab === 'masuk' ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-300'"
                    x-text="hibahList.filter(h => h.tipe_hibah === 'masuk').length"></span>
            </button>

            <button type="button" @click="activeTab = 'keluar'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer"
                :class="activeTab === 'keluar' ? 'bg-rose-500 text-white shadow-md shadow-rose-500/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                <span>📤 Hibah Keluar</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                    :class="activeTab === 'keluar' ? 'bg-slate-950 text-rose-300' : 'bg-slate-800 text-slate-300'"
                    x-text="hibahList.filter(h => h.tipe_hibah === 'keluar').length"></span>
            </button>
        </div>

        <!-- Counter & Reset Filter (Sisi Kanan) -->
        <div class="flex items-center space-x-2.5 w-full sm:w-auto justify-between sm:justify-end">
            <span class="px-3.5 py-2 rounded-xl bg-slate-950/80 border border-slate-800 text-[11px] font-semibold text-slate-400">
                Menampilkan <span class="text-amber-400 font-bold" x-text="filteredHibahList.length"></span> dari <span class="text-white font-bold" x-text="hibahList.length"></span> Transaksi
            </span>

            <button type="button" 
                x-show="selectedYear !== 'all' || selectedTw !== 'all' || searchQuery !== '' || activeTab !== 'all'"
                @click="resetFilter()"
                title="Reset Seluruh Filter ke Default"
                class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold border border-slate-700 transition-all cursor-pointer active:scale-95 shadow-sm">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <!-- Baris 2: Search Bar Lega & Dropdowns Filter (Tahun, Triwulan) -->
    <div class="pt-3 border-t border-slate-800/80 flex flex-col md:flex-row items-center gap-3">
        <!-- Live Search Bar (Flex-1) -->
        <div class="relative flex-1 w-full">
            <input type="text" x-model="searchQuery"
                placeholder="Cari nomor BAST, nama rekanan/pemberi/penerima, nama barang, kode 108..."
                class="w-full bg-slate-950 border border-slate-800 focus:border-amber-400 rounded-2xl px-4 py-2.5 pl-11 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
            <svg class="w-4 h-4 text-amber-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button type="button" x-show="searchQuery" @click="searchQuery = ''"
                class="absolute right-3.5 top-2.5 text-slate-400 hover:text-white text-xs font-bold p-1 cursor-pointer transition-colors"
                title="Kosongkan pencarian">
                ✕
            </button>
        </div>

        <!-- Filter Controls (Tahun & Triwulan) -->
        <div class="flex items-center gap-2.5 w-full md:w-auto shrink-0">
            <!-- Filter Tahun -->
            <div class="relative flex-1 md:w-44">
                <select x-model="selectedYear"
                    style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                    class="w-full bg-slate-950 border border-slate-800 focus:border-amber-400 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer hover:bg-slate-900/80 transition-all">
                    <option value="all" class="bg-slate-900 text-slate-200">Semua Tahun</option>
                    <template x-for="y in availableYears" :key="y">
                        <option :value="y" class="bg-slate-900 text-amber-300" x-text="'Tahun ' + y"></option>
                    </template>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-amber-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Filter Triwulan -->
            <div class="relative flex-1 md:w-48">
                <select x-model="selectedTw"
                    style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                    class="w-full bg-slate-950 border border-slate-800 focus:border-amber-400 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer hover:bg-slate-900/80 transition-all">
                    <option value="all" class="bg-slate-900 text-slate-200">Semua Triwulan</option>
                    <option value="TW I" class="bg-slate-900 text-amber-300">TW I (Jan - Mar)</option>
                    <option value="TW II" class="bg-slate-900 text-amber-300">TW II (Apr - Jun)</option>
                    <option value="TW III" class="bg-slate-900 text-amber-300">TW III (Jul - Sep)</option>
                    <option value="TW IV" class="bg-slate-900 text-amber-300">TW IV (Okt - Des)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-amber-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
