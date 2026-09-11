        <!-- ========================================================================= -->
        <!-- HEADER BANNER & STATISTIK RINGKAS                                         -->
        <!-- ========================================================================= -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>KATALOG INVENTARIS ASET TETAP RSUD DR. H. KOESNANDI</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Data ASTAP</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pencatatan daftar aset tetap rumah sakit, tahun perolehan, lokasi penempatan unit/paviliun, nilai realisasi anggaran, serta status kondisi terkini.
                    </p>
                </div>
                
                <!-- Aksi Utama -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                    <a href="{{ route('bast.index') }}"
                        class="px-3.5 py-2.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>📑 Cetak BAST Triwulan</span>
                    </a>
                    @endif

                    <button type="button" @click="openExportModal()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-emerald-400 border border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Excel (Pilih TW)</span>
                    </button>

                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                    <a href="{{ route('astap.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah ASTAP</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="totalVolumeUnit + ' Aset (' + astaps.length + ' Item)'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Investasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono" x-text="totalInvestasiRupiah"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🟢</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Kondisi Baik</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300" x-text="kondisiBaikPercent"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Unit Tersebar</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="totalLokasiCount"></span>
                    </div>
                </div>
            </div>
        </div>
