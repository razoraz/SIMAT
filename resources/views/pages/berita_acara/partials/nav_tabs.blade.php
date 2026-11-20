<!-- ========================================================================= -->
<!-- NAVIGATION 6 TABS: 2-IN-1 COMPACT INTERACTIVE KPI TABS                     -->
<!-- ========================================================================= -->
<div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl mb-5">
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-2.5">
        
        <!-- Tab 1: Triwulan ASTAP -->
        <button type="button" @click="activeTab = 'triwulan'"
            class="p-3 rounded-2xl transition-all duration-200 flex flex-col justify-between text-left group cursor-pointer relative hover:-translate-y-0.5"
            :class="activeTab === 'triwulan' ? 'bg-gradient-to-b from-purple-500/20 via-purple-500/10 to-transparent text-purple-300 border-2 border-purple-500/50 shadow-lg shadow-purple-500/10 ring-1 ring-purple-500/30' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900/60'">
            <div class="flex items-center justify-between mb-2 w-full">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 transition-transform group-hover:scale-105"
                     :class="activeTab === 'triwulan' ? 'bg-purple-500 text-slate-950 shadow-sm shadow-purple-500/30 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                    <span>🏛️</span>
                </div>
                <span class="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                      :class="activeTab === 'triwulan' ? 'bg-purple-500/30 text-purple-200 border border-purple-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                    4 Rekap
                </span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-wider block" :class="activeTab === 'triwulan' ? 'text-purple-400' : 'text-slate-400'">Pengadaan ASTAP</span>
                <span class="text-xs font-extrabold block text-white truncate group-hover:text-purple-200 transition-colors">1. BAST Triwulan</span>
                <span class="text-[9.5px] text-slate-400 block truncate">Rekap 8 Kategori KIB</span>
            </div>
        </button>

        <!-- Tab 2: Distribusi Unit -->
        <button type="button" @click="activeTab = 'distribusi'"
            class="p-3 rounded-2xl transition-all duration-200 flex flex-col justify-between text-left group cursor-pointer relative hover:-translate-y-0.5"
            :class="activeTab === 'distribusi' ? 'bg-gradient-to-b from-teal-500/20 via-teal-500/10 to-transparent text-teal-300 border-2 border-teal-500/50 shadow-lg shadow-teal-500/10 ring-1 ring-teal-500/30' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900/60'">
            <div class="flex items-center justify-between mb-2 w-full">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 transition-transform group-hover:scale-105"
                     :class="activeTab === 'distribusi' ? 'bg-teal-500 text-slate-950 shadow-sm shadow-teal-500/30 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                    <span>🚚</span>
                </div>
                <span class="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                      :class="activeTab === 'distribusi' ? 'bg-teal-500/30 text-teal-200 border border-teal-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'"
                      x-text="(distribusiList || []).length + ' Unit'">
                </span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-wider block" :class="activeTab === 'distribusi' ? 'text-teal-400' : 'text-slate-400'">Penyerahan Ruangan</span>
                <span class="text-xs font-extrabold block text-white truncate group-hover:text-teal-200 transition-colors">2. BAST Distribusi</span>
                <span class="text-[9.5px] text-slate-400 block truncate">Gudang ke Paviliun</span>
            </div>
        </button>

        <!-- Tab 3: Mutasi Aset (Internal & Eksternal) -->
        <button type="button" @click="activeTab = 'mutasi'"
            class="p-3 rounded-2xl transition-all duration-200 flex flex-col justify-between text-left group cursor-pointer relative hover:-translate-y-0.5"
            :class="activeTab === 'mutasi' ? 'bg-gradient-to-b from-indigo-500/20 via-indigo-500/10 to-transparent text-indigo-300 border-2 border-indigo-500/50 shadow-lg shadow-indigo-500/10 ring-1 ring-indigo-500/30' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900/60'">
            <div class="flex items-center justify-between mb-2 w-full">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 transition-transform group-hover:scale-105"
                     :class="activeTab === 'mutasi' ? 'bg-indigo-500 text-slate-950 shadow-sm shadow-indigo-500/30 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                    <span>🔄</span>
                </div>
                <span class="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                      :class="activeTab === 'mutasi' ? 'bg-indigo-500/30 text-indigo-200 border border-indigo-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'"
                      x-text="((mutasiList || []).length + (mutasiEksternalList || []).length) + ' Mutasi'">
                </span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-wider block" :class="activeTab === 'mutasi' ? 'text-indigo-400' : 'text-slate-400'">Pemindahan Aset</span>
                <span class="text-xs font-extrabold block text-white truncate group-hover:text-indigo-200 transition-colors">3. BAST Mutasi</span>
                <span class="text-[9.5px] text-slate-400 block truncate">Internal &amp; Antar-OPD</span>
            </div>
        </button>

        <!-- Tab 4: Hibah Aset (Masuk & Keluar BMD) -->
        <button type="button" @click="activeTab = 'hibah'"
            class="p-3 rounded-2xl transition-all duration-200 flex flex-col justify-between text-left group cursor-pointer relative hover:-translate-y-0.5"
            :class="activeTab === 'hibah' ? 'bg-gradient-to-b from-emerald-500/20 via-emerald-500/10 to-transparent text-emerald-300 border-2 border-emerald-500/50 shadow-lg shadow-emerald-500/10 ring-1 ring-emerald-500/30' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900/60'">
            <div class="flex items-center justify-between mb-2 w-full">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 transition-transform group-hover:scale-105"
                     :class="activeTab === 'hibah' ? 'bg-emerald-500 text-slate-950 shadow-sm shadow-emerald-500/30 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                    <span>🎁</span>
                </div>
                <span class="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                      :class="activeTab === 'hibah' ? 'bg-emerald-500/30 text-emerald-200 border border-emerald-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'"
                      x-text="(hibahList || []).length + ' Hibah'">
                </span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-wider block" :class="activeTab === 'hibah' ? 'text-emerald-400' : 'text-slate-400'">Naskah Hibah</span>
                <span class="text-xs font-extrabold block text-white truncate group-hover:text-emerald-200 transition-colors">4. BAST Hibah</span>
                <span class="text-[9.5px] text-slate-400 block truncate">Masuk &amp; Keluar Daerah</span>
            </div>
        </button>

        <!-- Tab 5: Kemitraan (Pemanfaatan BMD Akun 1.5.2) -->
        <button type="button" @click="activeTab = 'kemitraan'"
            class="p-3 rounded-2xl transition-all duration-200 flex flex-col justify-between text-left group cursor-pointer relative hover:-translate-y-0.5"
            :class="activeTab === 'kemitraan' ? 'bg-gradient-to-b from-amber-500/20 via-amber-500/10 to-transparent text-amber-300 border-2 border-amber-500/50 shadow-lg shadow-amber-500/10 ring-1 ring-amber-500/30' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900/60'">
            <div class="flex items-center justify-between mb-2 w-full">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 transition-transform group-hover:scale-105"
                     :class="activeTab === 'kemitraan' ? 'bg-amber-500 text-slate-950 shadow-sm shadow-amber-500/30 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                    <span>🤝</span>
                </div>
                <span class="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                      :class="activeTab === 'kemitraan' ? 'bg-amber-500/30 text-amber-200 border border-amber-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'"
                      x-text="(kemitraanList || []).length + ' PKS'">
                </span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-wider block" :class="activeTab === 'kemitraan' ? 'text-amber-400' : 'text-slate-400'">Akun 1.5.2 Mitra</span>
                <span class="text-xs font-extrabold block text-white truncate group-hover:text-amber-200 transition-colors">5. BAST Kemitraan</span>
                <span class="text-[9.5px] text-slate-400 block truncate">Sewa &amp; Pemanfaatan</span>
            </div>
        </button>

        <!-- Tab 6: Belanja Barang / Rekanan Pengadaan -->
        <button type="button" @click="activeTab = 'belanja_barang'"
            class="p-3 rounded-2xl transition-all duration-200 flex flex-col justify-between text-left group cursor-pointer relative hover:-translate-y-0.5"
            :class="activeTab === 'belanja_barang' ? 'bg-gradient-to-b from-sky-500/20 via-sky-500/10 to-transparent text-sky-300 border-2 border-sky-500/50 shadow-lg shadow-sky-500/10 ring-1 ring-sky-500/30' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900/60'">
            <div class="flex items-center justify-between mb-2 w-full">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 transition-transform group-hover:scale-105"
                     :class="activeTab === 'belanja_barang' ? 'bg-sky-500 text-slate-950 shadow-sm shadow-sky-500/30 font-bold' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                    <span>🏪</span>
                </div>
                <span class="text-[9.5px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                      :class="activeTab === 'belanja_barang' ? 'bg-sky-500/30 text-sky-200 border border-sky-500/40' : 'bg-slate-900 text-slate-400 border border-slate-800'"
                      x-text="(belanjaBarangList || []).length + ' Faktur'">
                </span>
            </div>
            <div class="min-w-0">
                <span class="text-[9px] font-bold uppercase tracking-wider block" :class="activeTab === 'belanja_barang' ? 'text-sky-400' : 'text-slate-400'">Pengadaan Rekanan</span>
                <span class="text-xs font-extrabold block text-white truncate group-hover:text-sky-200 transition-colors">6. BAST Rekanan</span>
                <span class="text-[9.5px] text-slate-400 block truncate">Faktur Belanja Modal</span>
            </div>
        </button>

    </div>
</div>
