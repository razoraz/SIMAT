            <!-- Header Banner & Mini KPI Strip -->
            <div class="bg-gradient-to-r from-rose-600/15 via-slate-900 to-slate-900 border border-rose-500/30 rounded-3xl p-5 sm:p-7 shadow-2xl mb-6 relative overflow-hidden">
            <style>
                /* Mode Layar Medium ke Bawah (< 1024px): Rata Tengah Simetris */
                @media (max-width: 1023px) {
                    .mutasi-internal-banner-wrap {
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        gap: 1.25rem !important;
                    }
                    .mutasi-internal-text-col {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        width: 100% !important;
                        padding-right: 0 !important;
                    }
                    .mutasi-internal-badge,
                    .mutasi-internal-desc {
                        margin-left: auto !important;
                        margin-right: auto !important;
                        text-align: center !important;
                    }
                    .mutasi-internal-btn-wrap {
                        justify-content: center !important;
                        width: 100% !important;
                    }
                }
                /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
                @media (min-width: 1024px) {
                    .mutasi-internal-banner-wrap {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                    }
                    .mutasi-internal-text-col {
                        align-items: flex-start !important;
                        text-align: left !important;
                    }
                    .mutasi-internal-badge,
                    .mutasi-internal-desc {
                        margin-left: 0 !important;
                        margin-right: 0 !important;
                        text-align: left !important;
                    }
                    .mutasi-internal-btn-wrap {
                        justify-content: flex-end !important;
                    }
                }
            </style>

            <div class="mutasi-internal-banner-wrap flex flex-row items-start sm:items-center justify-between gap-4 relative z-10">
                <div class="mutasi-internal-text-col flex-1 min-w-0 pr-0 lg:pr-2">
                    <div class="mutasi-internal-badge inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold mb-3 max-w-full">
                        <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse shrink-0"></span>
                        <span class="whitespace-nowrap truncate sm:whitespace-normal">PERPINDAHAN & MUTASI INTERNAL RUANGAN ASET RSUD</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight break-words">Katalog Mutasi Internal (Antar Ruangan)</h1>
                    <p class="mutasi-internal-desc text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed break-words">
                        Pencatatan perpindahan lokasi unit penempatan barang antar ruangan/instalasi di lingkungan RSUD dr. H. Koesnadi, pelacakan riwayat pergerakan aset, dan pencetakan Berita Acara Mutasi Barang (BAMB).
                    </p>
                </div>
                
                @if(Auth::user()?->role === 'master_admin' || Auth::user()?->role === 'sub_admin' || Auth::user()?->canDo('mutasi', 'create'))
                <div class="mutasi-internal-btn-wrap flex items-center gap-2.5 shrink-0 w-full lg:w-auto">
                    <a href="{{ route('mutasi.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 shrink-0 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Pengajuan Mutasi Baru</span>
                    </a>
                </div>
                @endif
            </div>

            <!-- Mini Summary KPI Cards Strip (Responsive Native Grid - Fullscreen 5-Col & Balanced 2-Row) -->
            <style>
                .kpi-mutasi-grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 0.75rem;
                }
                @media (max-width: 639px) {
                    .kpi-mutasi-card-last {
                        grid-column: span 2 / span 2;
                    }
                }
                @media (min-width: 640px) {
                    .kpi-mutasi-grid {
                        grid-template-columns: repeat(6, minmax(0, 1fr));
                    }
                    .kpi-mutasi-card-row1 {
                        grid-column: span 2 / span 2;
                    }
                    .kpi-mutasi-card-row2 {
                        grid-column: span 3 / span 3;
                    }
                }
                @media (min-width: 1200px) {
                    .kpi-mutasi-grid {
                        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                    }
                    .kpi-mutasi-card-row1,
                    .kpi-mutasi-card-row2,
                    .kpi-mutasi-card-last {
                        grid-column: span 1 / span 1 !important;
                    }
                }
            </style>
            <div class="kpi-mutasi-grid mt-6 pt-6 border-t border-slate-800/80">
                <!-- 1. Total Mutasi -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3 min-w-0 kpi-mutasi-card-row1">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 text-sm flex items-center justify-center shrink-0">🔄</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10.5px] uppercase tracking-wider font-semibold text-slate-400 block leading-tight">Total Mutasi</span>
                        <div class="text-sm sm:text-base font-extrabold text-white leading-tight mt-0.5 flex items-baseline gap-1">
                            <span x-text="countAll"></span>
                            <span class="text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Telah Disetujui -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3 min-w-0 kpi-mutasi-card-row1">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 text-sm flex items-center justify-center shrink-0">✅</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10.5px] uppercase tracking-wider font-semibold text-slate-400 block leading-tight">Telah Disetujui</span>
                        <div class="text-sm sm:text-base font-extrabold text-emerald-300 leading-tight mt-0.5 flex items-baseline gap-1">
                            <span x-text="countSelesai"></span>
                            <span class="text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Menunggu Admin -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3 min-w-0 kpi-mutasi-card-row1">
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 text-sm flex items-center justify-center shrink-0">⏳</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10.5px] uppercase tracking-wider font-semibold text-slate-400 block leading-tight">Menunggu Admin</span>
                        <div class="text-sm sm:text-base font-extrabold text-cyan-300 leading-tight mt-0.5 flex items-baseline gap-1">
                            <span x-text="countMenungguAdmin"></span>
                            <span class="text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Menunggu Penerima -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3 min-w-0 kpi-mutasi-card-row2">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 text-sm flex items-center justify-center shrink-0">⏳</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10.5px] uppercase tracking-wider font-semibold text-slate-400 block leading-tight">Menunggu Penerima</span>
                        <div class="text-sm sm:text-base font-extrabold text-amber-300 leading-tight mt-0.5 flex items-baseline gap-1">
                            <span x-text="countMenungguPenerima"></span>
                            <span class="text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Ditolak -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3 min-w-0 kpi-mutasi-card-row2 kpi-mutasi-card-last">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 text-sm flex items-center justify-center shrink-0">🚫</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10.5px] uppercase tracking-wider font-semibold text-slate-400 block leading-tight">Ditolak</span>
                        <div class="text-sm sm:text-base font-extrabold text-rose-300 leading-tight mt-0.5 flex items-baseline gap-1">
                            <span x-text="countDitolak"></span>
                            <span class="text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
