            <!-- Header Banner & Mini KPI Strip -->
            <div class="bg-gradient-to-r from-teal-600/15 via-slate-900 to-slate-900 border border-teal-500/30 rounded-3xl p-4 sm:p-6 lg:p-7 shadow-2xl mb-6 relative overflow-hidden">
            <div class="distribusi-banner-wrap flex flex-row items-start sm:items-center justify-between gap-4 relative z-10">
                <div class="distribusi-text-col flex-1 min-w-0 pr-0 sm:pr-2">
                    <div class="distribusi-badge inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] sm:text-xs font-bold mb-3 max-w-[95%] sm:max-w-fit break-words">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse shrink-0"></span>
                        <span class="truncate sm:whitespace-normal">{{ $isSubAdmin ? 'PENGAJUAN & PERMOHONAN BARANG ASET RUANGAN' : 'PENYERAHAN & ALOKASI BARANG ASET RSUD' }}</span>
                    </div>
                    <h1 class="distribusi-title text-lg sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight break-words">{{ $isSubAdmin ? 'Katalog Pengajuan Baru' : 'Katalog Distribusi ASTAP' }}</h1>
                    <p class="distribusi-desc text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed break-words">
                        {{ $isSubAdmin 
                            ? 'Daftar pengajuan permohonan alokasi barang aset ruangan. Anda dapat mengajukan permintaan kebutuhan barang baru untuk unit/ruangan Anda.' 
                            : 'Pengelolaan alokasi penyerahan barang aset dari inventaris ke paviliun rawat inap, unit RSUD, dan instalasi RSUD. Mendukung distribusi beberapa barang sekaligus dalam satu transaksi.' }}
                    </p>
                </div>
                
                @if(Auth::user()?->role === 'master_admin' || Auth::user()?->role === 'sub_admin' || Auth::user()?->canDo('distribusi', 'create'))
                <div class="distribusi-btn-wrap flex items-center gap-2.5 shrink-0 w-full sm:w-auto">
                    <a href="{{ route('distribusi.create') }}"
                        class="w-full sm:w-auto justify-center px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="whitespace-nowrap">{{ $isSubAdmin ? 'Input Pengajuan Baru' : 'Input Distribusi Baru' }}</span>
                    </a>
                </div>
                @endif
            </div>

            <!-- Mini Summary KPI Cards Strip (Responsive Native Grid - Mobile First, Balanced 2-Row & Fullscreen 5-Col) -->
            <style>
                @media (max-width: 1279px) {
                    .distribusi-banner-wrap {
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        gap: 1.25rem !important;
                    }
                    .distribusi-text-col {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        width: 100% !important;
                        padding-right: 0 !important;
                    }
                    .distribusi-badge,
                    .distribusi-desc {
                        margin-left: auto !important;
                        margin-right: auto !important;
                        text-align: center !important;
                    }
                    .distribusi-title {
                        text-align: center !important;
                        width: 100% !important;
                    }
                    .distribusi-btn-wrap {
                        justify-content: center !important;
                        margin-left: auto !important;
                        margin-right: auto !important;
                        width: 100% !important;
                    }
                }
                .kpi-distribusi-grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 0.5rem;
                }
                @media (min-width: 480px) {
                    .kpi-distribusi-grid {
                        gap: 0.75rem;
                    }
                }
                @media (max-width: 639px) {
                    .kpi-distribusi-card-last {
                        grid-column: span 2 / span 2;
                    }
                }
                @media (min-width: 640px) {
                    .kpi-distribusi-grid {
                        grid-template-columns: repeat(6, minmax(0, 1fr));
                    }
                    .kpi-distribusi-card-row1 {
                        grid-column: span 2 / span 2;
                    }
                    .kpi-distribusi-card-row2 {
                        grid-column: span 3 / span 3;
                    }
                }
                @media (min-width: 1400px) {
                    .kpi-distribusi-grid {
                        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                    }
                    .kpi-distribusi-card-row1,
                    .kpi-distribusi-card-row2,
                    .kpi-distribusi-card-last {
                        grid-column: span 1 / span 1 !important;
                    }
                }
            </style>
            <div class="kpi-distribusi-grid mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0 kpi-distribusi-card-row1">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-teal-500/10 text-teal-400 text-xs sm:text-sm flex items-center justify-center shrink-0">🚚</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10.5px] uppercase tracking-wide font-semibold text-slate-400 block leading-tight truncate sm:whitespace-nowrap">Total Distribusi</span>
                        <div class="text-xs sm:text-base font-extrabold text-white leading-tight mt-0.5 flex items-baseline gap-1 whitespace-nowrap">
                            <span x-text="countAll"></span>
                            <span class="text-[10px] sm:text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0 kpi-distribusi-card-row1">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-500/10 text-emerald-400 text-xs sm:text-sm flex items-center justify-center shrink-0">📦</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10.5px] uppercase tracking-wide font-semibold text-slate-400 block leading-tight truncate sm:whitespace-nowrap">Telah Diterima</span>
                        <div class="text-xs sm:text-base font-extrabold text-emerald-300 leading-tight mt-0.5 flex items-baseline gap-1 whitespace-nowrap">
                            <span x-text="countSelesai"></span>
                            <span class="text-[10px] sm:text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0 kpi-distribusi-card-row1">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-cyan-500/10 text-cyan-400 text-xs sm:text-sm flex items-center justify-center shrink-0">⏳</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10.5px] uppercase tracking-wide font-semibold text-slate-400 block leading-tight truncate sm:whitespace-nowrap">Menunggu Admin</span>
                        <div class="text-xs sm:text-base font-extrabold text-cyan-300 leading-tight mt-0.5 flex items-baseline gap-1 whitespace-nowrap">
                            <span x-text="countMenungguAdmin"></span>
                            <span class="text-[10px] sm:text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0 kpi-distribusi-card-row2">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-500/10 text-amber-400 text-xs sm:text-sm flex items-center justify-center shrink-0">⏳</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10.5px] uppercase tracking-wide font-semibold text-slate-400 block leading-tight truncate sm:whitespace-nowrap">Menunggu Penerima</span>
                        <div class="text-xs sm:text-base font-extrabold text-amber-300 leading-tight mt-0.5 flex items-baseline gap-1 whitespace-nowrap">
                            <span x-text="countMenungguPenerima"></span>
                            <span class="text-[10px] sm:text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0 kpi-distribusi-card-row2 kpi-distribusi-card-last">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-rose-500/10 text-rose-400 text-xs sm:text-sm flex items-center justify-center shrink-0">🚫</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10.5px] uppercase tracking-wide font-semibold text-slate-400 block leading-tight truncate sm:whitespace-nowrap">Ditolak</span>
                        <div class="text-xs sm:text-base font-extrabold text-rose-300 leading-tight mt-0.5 flex items-baseline gap-1 whitespace-nowrap">
                            <span x-text="countDitolak"></span>
                            <span class="text-[10px] sm:text-[11px] font-medium text-slate-400">Transaksi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
