<!-- ========================================================================= -->
<!-- FILTER BAR & PENCARIAN MASTER KEMITRAAN ASET (AKUN 1.5.2)                 -->
<!-- REAKTIF CLIENT-SIDE 0MS TANPA RELOAD PERSIS SEPERTI DATA ASTAP            -->
<!-- ========================================================================= -->
<div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-4">
    <div class="space-y-4">
        
        <!-- Baris Atas: Live Search Bar + Counter Badge & Tombol Reset (0ms Instant) -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
            <div class="relative flex-1 w-full">
                <input type="text" x-model="filterSearch"
                    placeholder="Cari nama barang / rekanan mitra / nomor PKS kemitraan / kode 108 / NIBAR..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-11 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-all">
                <svg class="w-4 h-4 text-cyan-400 absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button type="button" x-show="filterSearch" @click="filterSearch = ''"
                    class="absolute right-3.5 top-2.5 text-slate-500 hover:text-white text-xs font-bold p-1 cursor-pointer transition-colors"
                    title="Kosongkan pencarian">&times;</button>
            </div>

            <div class="flex items-center space-x-2 shrink-0 self-end sm:self-auto">
                <span class="px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                    Menampilkan <span class="text-cyan-400 font-bold" x-text="countVisibleAll"></span> dari <span class="text-white font-bold" x-text="allMetaList.length"></span> Data
                </span>

                <button type="button" @click="resetAllFilters()"
                    title="Reset Seluruh Filter ke Default Tanpa Reload"
                    class="group/rf inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold border border-slate-700 transition-all cursor-pointer active:scale-95">
                    <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/rf:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Reset Filter</span>
                </button>
            </div>
        </div>

        <!-- Baris Bawah: 5 Dropdown Filter (KIB, Skema, Tahun, Triwulan, Status Konsesi) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 pt-3 border-t border-slate-800/80">
            <!-- 1. Filter Klasifikasi KIB (seperti di Data ASTAP: milih KIB Tanah langsung disuguhkan!) -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Klasifikasi KIB</label>
                <div class="relative">
                    <select x-model="filterKib"
                        style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-cyan-400 rounded-xl px-3 py-2 pr-7 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer hover:bg-slate-900/80 transition-all">
                        <option value="all" class="bg-slate-900 text-slate-200 py-1">Semua KIB</option>
                        <option value="KIB A" class="bg-slate-900 text-amber-300 py-1">KIB A - Tanah</option>
                        <option value="KIB B" class="bg-slate-900 text-cyan-300 py-1">KIB B - Peralatan &amp; Mesin</option>
                        <option value="KIB C" class="bg-slate-900 text-purple-300 py-1">KIB C - Gedung &amp; Bangunan</option>
                        <option value="KIB D" class="bg-slate-900 text-teal-300 py-1">KIB D - Jalan &amp; Jaringan</option>
                        <option value="KIB E" class="bg-slate-900 text-orange-300 py-1">KIB E - Aset Tetap Lainnya</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-cyan-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- 2. Filter Skema Kemitraan -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Skema Kemitraan</label>
                <div class="relative">
                    <select x-model="filterSkema"
                        style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-cyan-400 rounded-xl px-3 py-2 pr-7 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer hover:bg-slate-900/80 transition-all">
                        <option value="all" class="bg-slate-900 text-slate-200 py-1">Semua Skema</option>
                        <option value="Sewa" class="bg-slate-900 text-cyan-300 py-1">Sewa (1.5.2.01.01.01)</option>
                        <option value="KSP" class="bg-slate-900 text-purple-300 py-1">KSP / KSO - Pemanfaatan (1.5.2.01.01.02)</option>
                        <option value="BGS/BSG" class="bg-slate-900 text-blue-300 py-1">BGS / BSG (1.5.2.01.01.03)</option>
                        <option value="KSPI" class="bg-slate-900 text-teal-300 py-1">KSPI - Infrastruktur (1.5.2.01.01.04)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-cyan-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- 3. Filter Tahun -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tahun Perolehan</label>
                <div class="relative">
                    <select x-model="filterTahun"
                        style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-cyan-400 rounded-xl px-3 py-2 pr-7 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer hover:bg-slate-900/80 transition-all">
                        <option value="all" class="bg-slate-900 text-slate-200 py-1">Semua Tahun</option>
                        <template x-for="yr in availableYears" :key="yr">
                            <option :value="yr" class="bg-slate-900 text-cyan-300 py-1" x-text="yr"></option>
                        </template>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-cyan-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- 4. Filter Triwulan -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Periode Triwulan</label>
                <div class="relative">
                    <select x-model="filterTriwulan"
                        style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-cyan-400 rounded-xl px-3 py-2 pr-7 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer hover:bg-slate-900/80 transition-all">
                        <option value="all" class="bg-slate-900 text-slate-200 py-1">Semua Triwulan</option>
                        <option value="TW I" class="bg-slate-900 text-cyan-300 py-1">Triwulan I (TW I)</option>
                        <option value="TW II" class="bg-slate-900 text-cyan-300 py-1">Triwulan II (TW II)</option>
                        <option value="TW III" class="bg-slate-900 text-cyan-300 py-1">Triwulan III (TW III)</option>
                        <option value="TW IV" class="bg-slate-900 text-cyan-300 py-1">Triwulan IV (TW IV)</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-cyan-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <!-- 5. Filter Status Konsesi -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Status Konsesi</label>
                <div class="relative">
                    <select x-model="filterStatus"
                        style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-cyan-400 rounded-xl px-3 py-2 pr-7 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer hover:bg-slate-900/80 transition-all">
                        <option value="all" class="bg-slate-900 text-slate-200 py-1">Semua Status</option>
                        <option value="Aktif" class="bg-slate-900 text-emerald-300 py-1">🟢 Aktif</option>
                        <option value="Konsesi Berakhir" class="bg-slate-900 text-amber-300 py-1">🟠 Konsesi Berakhir</option>
                        <option value="Selesai" class="bg-slate-900 text-blue-300 py-1">🔵 Selesai</option>
                        <option value="Dihentikan" class="bg-slate-900 text-rose-300 py-1">🔴 Dihentikan</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-cyan-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
