        <!-- ========================================================================= -->
        <!-- NAVIGATION 6 TABS: PUSAT DOKUMEN BERITA ACARA TERPADU SIMAT-RK              -->
        <!-- ========================================================================= -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl mb-6">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-2.5">
                
                <!-- Tab 1: Triwulan ASTAP -->
                <button type="button" @click="activeTab = 'triwulan'"
                    class="p-3.5 rounded-2xl transition-all flex flex-col justify-between text-left group cursor-pointer relative"
                    :class="activeTab === 'triwulan' ? 'bg-purple-500/20 text-purple-300 border-2 border-purple-500/50 shadow-lg shadow-purple-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700'">
                    <div class="flex items-center justify-between mb-2 w-full">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                             :class="activeTab === 'triwulan' ? 'bg-purple-500 text-slate-950 shadow-md shadow-purple-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                            <span>🏛️</span>
                        </div>
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                              :class="activeTab === 'triwulan' ? 'bg-purple-500/30 text-purple-200 border border-purple-500/40' : 'bg-slate-900 text-slate-500 border border-slate-800'">
                            4 Dokumen
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9.5px] font-bold uppercase tracking-wider block" :class="activeTab === 'triwulan' ? 'text-purple-400' : 'text-slate-500'">Pengadaan ASTAP</span>
                        <span class="text-xs font-extrabold block text-white truncate">1. BAST Triwulan</span>
                        <span class="text-[9.5px] text-slate-400 block truncate">Rekap 8 Kategori KIB</span>
                    </div>
                </button>

                <!-- Tab 2: Distribusi Unit -->
                <button type="button" @click="activeTab = 'distribusi'"
                    class="p-3.5 rounded-2xl transition-all flex flex-col justify-between text-left group cursor-pointer relative"
                    :class="activeTab === 'distribusi' ? 'bg-teal-500/20 text-teal-300 border-2 border-teal-500/50 shadow-lg shadow-teal-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700'">
                    <div class="flex items-center justify-between mb-2 w-full">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                             :class="activeTab === 'distribusi' ? 'bg-teal-500 text-slate-950 shadow-md shadow-teal-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                            <span>🚚</span>
                        </div>
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                              :class="activeTab === 'distribusi' ? 'bg-teal-500/30 text-teal-200 border border-teal-500/40' : 'bg-slate-900 text-slate-500 border border-slate-800'"
                              x-text="(distribusiList || []).length + ' Transaksi'">
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9.5px] font-bold uppercase tracking-wider block" :class="activeTab === 'distribusi' ? 'text-teal-400' : 'text-slate-500'">Penyerahan Unit</span>
                        <span class="text-xs font-extrabold block text-white truncate">2. BAST Distribusi</span>
                        <span class="text-[9.5px] text-slate-400 block truncate">Gudang ke Ruangan</span>
                    </div>
                </button>

                <!-- Tab 3: Mutasi Aset (Internal & Eksternal) -->
                <button type="button" @click="activeTab = 'mutasi'"
                    class="p-3.5 rounded-2xl transition-all flex flex-col justify-between text-left group cursor-pointer relative"
                    :class="activeTab === 'mutasi' ? 'bg-indigo-500/20 text-indigo-300 border-2 border-indigo-500/50 shadow-lg shadow-indigo-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700'">
                    <div class="flex items-center justify-between mb-2 w-full">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                             :class="activeTab === 'mutasi' ? 'bg-indigo-500 text-slate-950 shadow-md shadow-indigo-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                            <span>🔄</span>
                        </div>
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                              :class="activeTab === 'mutasi' ? 'bg-indigo-500/30 text-indigo-200 border border-indigo-500/40' : 'bg-slate-900 text-slate-500 border border-slate-800'"
                              x-text="((mutasiList || []).length + (mutasiEksternalList || []).length) + ' Mutasi'">
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9.5px] font-bold uppercase tracking-wider block" :class="activeTab === 'mutasi' ? 'text-indigo-400' : 'text-slate-500'">Pemindahan Aset</span>
                        <span class="text-xs font-extrabold block text-white truncate">3. BAST Mutasi</span>
                        <span class="text-[9.5px] text-slate-400 block truncate">Internal & Eksternal</span>
                    </div>
                </button>

                <!-- Tab 4: Hibah Aset (Masuk & Keluar BMD) -->
                <button type="button" @click="activeTab = 'hibah'"
                    class="p-3.5 rounded-2xl transition-all flex flex-col justify-between text-left group cursor-pointer relative"
                    :class="activeTab === 'hibah' ? 'bg-emerald-500/20 text-emerald-300 border-2 border-emerald-500/50 shadow-lg shadow-emerald-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700'">
                    <div class="flex items-center justify-between mb-2 w-full">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                             :class="activeTab === 'hibah' ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                            <span>🎁</span>
                        </div>
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                              :class="activeTab === 'hibah' ? 'bg-emerald-500/30 text-emerald-200 border border-emerald-500/40' : 'bg-slate-900 text-slate-500 border border-slate-800'"
                              x-text="(hibahList || []).length + ' Hibah'">
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9.5px] font-bold uppercase tracking-wider block" :class="activeTab === 'hibah' ? 'text-emerald-400' : 'text-slate-500'">Naskah Hibah</span>
                        <span class="text-xs font-extrabold block text-white truncate">4. BAST Hibah BMD</span>
                        <span class="text-[9.5px] text-slate-400 block truncate">Masuk & Keluar Daerah</span>
                    </div>
                </button>

                <!-- Tab 5: Kemitraan (Pemanfaatan BMD Akun 1.5.2) -->
                <button type="button" @click="activeTab = 'kemitraan'"
                    class="p-3.5 rounded-2xl transition-all flex flex-col justify-between text-left group cursor-pointer relative"
                    :class="activeTab === 'kemitraan' ? 'bg-amber-500/20 text-amber-300 border-2 border-amber-500/50 shadow-lg shadow-amber-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700'">
                    <div class="flex items-center justify-between mb-2 w-full">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                             :class="activeTab === 'kemitraan' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                            <span>🤝</span>
                        </div>
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                              :class="activeTab === 'kemitraan' ? 'bg-amber-500/30 text-amber-200 border border-amber-500/40' : 'bg-slate-900 text-slate-500 border border-slate-800'"
                              x-text="(kemitraanList || []).length + ' PKS'">
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9.5px] font-bold uppercase tracking-wider block" :class="activeTab === 'kemitraan' ? 'text-amber-400' : 'text-slate-500'">Akun 1.5.2</span>
                        <span class="text-xs font-extrabold block text-white truncate">5. BAST Kemitraan</span>
                        <span class="text-[9.5px] text-slate-400 block truncate">Sewa & Pemanfaatan</span>
                    </div>
                </button>

                <!-- Tab 6: Belanja Barang / Rekanan Pengadaan -->
                <button type="button" @click="activeTab = 'belanja_barang'"
                    class="p-3.5 rounded-2xl transition-all flex flex-col justify-between text-left group cursor-pointer relative"
                    :class="activeTab === 'belanja_barang' ? 'bg-sky-500/20 text-sky-300 border-2 border-sky-500/50 shadow-lg shadow-sky-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80 hover:border-slate-700'">
                    <div class="flex items-center justify-between mb-2 w-full">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0"
                             :class="activeTab === 'belanja_barang' ? 'bg-sky-500 text-slate-950 shadow-md shadow-sky-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                            <span>🏪</span>
                        </div>
                        <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-md"
                              :class="activeTab === 'belanja_barang' ? 'bg-sky-500/30 text-sky-200 border border-sky-500/40' : 'bg-slate-900 text-slate-500 border border-slate-800'"
                              x-text="(belanjaBarangList || []).length + ' Faktur'">
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9.5px] font-bold uppercase tracking-wider block" :class="activeTab === 'belanja_barang' ? 'text-sky-400' : 'text-slate-500'">Rekanan Pengadaan</span>
                        <span class="text-xs font-extrabold block text-white truncate">6. BAST Rekanan</span>
                        <span class="text-[9.5px] text-slate-400 block truncate">Faktur Belanja Modal</span>
                    </div>
                </button>

            </div>
        </div>
