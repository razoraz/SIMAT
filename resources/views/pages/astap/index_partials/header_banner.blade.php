        <!-- ========================================================================= -->
        <!-- HEADER BANNER & STATISTIK RINGKAS                                         -->
        <!-- ========================================================================= -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-4 sm:p-6 lg:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <style>
                /* Mode Minimize / Layar Sempit / Sidebar Terbuka (< 1280px): Kolom Vertikal Rata Tengah */
                @media (max-width: 1279px) {
                    .astap-banner-wrap {
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        gap: 1.25rem !important;
                    }
                    .astap-text-col {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        width: 100% !important;
                        padding-right: 0 !important;
                    }
                    .astap-badge,
                    .astap-desc {
                        margin-left: auto !important;
                        margin-right: auto !important;
                        text-align: center !important;
                    }
                    .astap-btn-wrap {
                        justify-content: center !important;
                        width: 100% !important;
                    }
                }
                /* Mode Fullscreen / Layar Luas (>= 1280px): Rata Kiri Sejajar (Row) */
                @media (min-width: 1280px) {
                    .astap-banner-wrap {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                    }
                    .astap-text-col {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: flex-start !important;
                        text-align: left !important;
                    }
                    .astap-badge,
                    .astap-desc {
                        margin-left: 0 !important;
                        margin-right: 0 !important;
                        text-align: left !important;
                    }
                    .astap-btn-wrap {
                        justify-content: flex-end !important;
                    }
                }
            </style>

            <div class="astap-banner-wrap flex flex-row items-center justify-between gap-5 sm:gap-6 relative z-10">
                <div class="astap-text-col flex-1 min-w-0">
                    <div class="astap-badge inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] sm:text-xs font-bold mb-3 max-w-[95%] sm:max-w-fit">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <span class="tracking-wider whitespace-nowrap truncate sm:whitespace-normal">KATALOG INVENTARIS ASET TETAP RSUD DR. H. KOESNANDI</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight break-words">Katalog Data ASTAP</h1>
                    <p class="astap-desc text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed break-words">
                        Pencatatan daftar aset tetap rumah sakit, tahun perolehan, lokasi penempatan unit/paviliun, nilai realisasi anggaran, serta status kondisi terkini.
                    </p>
                </div>
                
                <!-- Aksi Utama: Flex-wrap aman di mobile, sejajar rapi di desktop -->
                <div class="astap-btn-wrap flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-2.5 shrink-0 w-full lg:w-auto">
                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                    <a href="{{ route('bast.index') }}"
                        class="px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 active:scale-95 whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>📑 Cetak BAST Triwulan</span>
                    </a>
                    @endif

                    @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'export'))
                    <button type="button" @click="openExportModal()"
                        class="px-3 sm:px-3.5 py-2 sm:py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-emerald-400 border border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 cursor-pointer active:scale-95 whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Excel (Pilih TW)</span>
                    </button>
                    @endif

                    @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'create'))
                    <a href="{{ route('astap.pilih_jenis') }}"
                        class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah ASTAP</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-base sm:text-lg shrink-0">📦</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Aset</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-white truncate block" x-text="totalVolumeUnit + ' Aset (' + astaps.length + ' Item)'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-base sm:text-lg shrink-0">💰</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Investasi</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-emerald-400 font-mono truncate block" x-text="totalInvestasiRupiah"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-base sm:text-lg shrink-0">🟢</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Kondisi Baik</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-teal-300 truncate block" x-text="kondisiBaikPercent"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-base sm:text-lg shrink-0">🏥</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Unit Tersebar</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-purple-300 truncate block" x-text="totalLokasiCount"></span>
                    </div>
                </div>
            </div>
        </div>
