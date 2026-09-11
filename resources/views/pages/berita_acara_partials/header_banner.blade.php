        <!-- Header Banner & Mini KPI Strip -->
        <div class="no-print bg-gradient-to-r from-purple-600/15 via-slate-900 to-slate-900 border border-purple-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                        <span>MODUL PENGESAHAN BAST (LIVE EDIT, TTD BSR-E, & BATALKAN TTD)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Pusat Cetak & Pengesahan BAST</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pengesahan Tanda Tangan Digital BSrE, Pembatalan TTD, Live Edit Dokumen Surat, dan Pencetakan 3 Jenis Berita Acara (Triwulan ASTAP, Distribusi Unit, & Mutasi Aset).
                    </p>
                </div>
                
                <!-- Quick Print Buttons (Tersusun Sejajar Rapi) -->
                <div class="flex flex-wrap lg:flex-nowrap items-center gap-2 sm:gap-3 shrink-0">
                    <button type="button" @click="activeTab = 'triwulan'; openPrintTriwulan('TW2')"
                        class="px-3.5 py-2.5 rounded-2xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🏛️ BAST Triwulan</span>
                    </button>
                    <button type="button" @click="activeTab = 'distribusi'; if (distribusiList.length > 0) openPrintDistribusi(distribusiList[0])"
                        class="px-3.5 py-2.5 rounded-2xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🚚 BAST Distribusi</span>
                    </button>
                    <button type="button" @click="activeTab = 'mutasi'; openPrintMutasi(mutasiList[0])"
                        class="px-3.5 py-2.5 rounded-2xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🔄 BAST Mutasi</span>
                    </button>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏛️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Triwulan ASTAP</span>
                        <span class="text-sm sm:text-base font-extrabold text-white">4 Periode Rekap</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🚚</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Distribusi Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300" x-text="distribusiList.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 text-lg">🔄</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Mutasi Aset</span>
                        <span class="text-sm sm:text-base font-extrabold text-rose-300" x-text="mutasiList.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">✍️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Status TTD BSrE</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">TTD & Batal TTD</span>
                    </div>
                </div>
            </div>
        </div>
