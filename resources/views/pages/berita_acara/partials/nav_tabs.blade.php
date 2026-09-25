        <!-- ========================================================================= -->
        <!-- NAVIGATION 3 TABS: TAB 1 (TRIWULAN) | TAB 2 (DISTRIBUSI) | TAB 3 (MUTASI)  -->
        <!-- ========================================================================= -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                
                <!-- Tab 1 Button -->
                <button type="button" @click="activeTab = 'triwulan'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'triwulan' ? 'bg-purple-500/20 text-purple-300 border-2 border-purple-500/50 shadow-lg shadow-purple-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'triwulan' ? 'bg-purple-500 text-slate-950 shadow-md shadow-purple-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🏛️</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'triwulan' ? 'text-purple-400' : 'text-slate-500'">Pengadaan ASTAP</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">1. BAST Triwulan ASTAP</span>
                        <span class="text-[10px] text-slate-400 block truncate">Rekap 8 Kategori & SPK Perolehan</span>
                    </div>
                </button>

                <!-- Tab 2 Button -->
                <button type="button" @click="activeTab = 'distribusi'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'distribusi' ? 'bg-teal-500/20 text-teal-300 border-2 border-teal-500/50 shadow-lg shadow-teal-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'distribusi' ? 'bg-teal-500 text-slate-950 shadow-md shadow-teal-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🚚</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'distribusi' ? 'text-teal-400' : 'text-slate-500'">Penyerahan Unit</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">2. BAST Distribusi Aset</span>
                        <span class="text-[10px] text-slate-400 block truncate">Serah Terima Sekali Transaksi Unit</span>
                    </div>
                </button>

                <!-- Tab 3 Button -->
                <button type="button" @click="activeTab = 'mutasi'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'mutasi' ? 'bg-rose-500/20 text-rose-300 border-2 border-rose-500/50 shadow-lg shadow-rose-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'mutasi' ? 'bg-rose-500 text-slate-950 shadow-md shadow-rose-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🔄</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'mutasi' ? 'text-rose-400' : 'text-slate-500'">Pemindahan Ruangan</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">3. BAST Mutasi Aset</span>
                        <span class="text-[10px] text-slate-400 block truncate">Pemindahan Barang Ruang Asal &rarr; Tujuan</span>
                    </div>
                </button>

            </div>
        </div>
