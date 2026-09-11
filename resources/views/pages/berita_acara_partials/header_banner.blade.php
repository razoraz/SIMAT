        <!-- Header Banner & Mini KPI Strip -->
        <div class="no-print bg-gradient-to-r from-purple-600/15 via-slate-900 to-slate-900 border border-purple-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                        <span>MODUL PENGESAHAN BAST (LIVE EDIT, TTD BSR-E, & BATALKAN TTD)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Pusat Cetak & Pengesahan BAST</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-3xl leading-relaxed">
                        Pengesahan Tanda Tangan Digital BSrE, Pembatalan TTD, Live Edit Dokumen Surat, dan Pencetakan 3 Jenis Berita Acara (Triwulan ASTAP, Distribusi Unit, & Mutasi Aset).
                    </p>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip (3 Jenis Berita Acara) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mt-6 pt-6 border-t border-slate-800/80">
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
            </div>
        </div>
