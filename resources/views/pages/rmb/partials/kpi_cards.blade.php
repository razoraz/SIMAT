<style>
    .kpi-rmb-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .kpi-rmb-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.875rem;
        }
        .kpi-rmb-card-last {
            grid-column: span 2 / span 2;
        }
    }
    @media (min-width: 1024px) {
        .kpi-rmb-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }
        .kpi-rmb-card-row1 {
            grid-column: span 2 / span 2;
        }
        .kpi-rmb-card-row2 {
            grid-column: span 3 / span 3;
        }
    }
    @media (min-width: 1400px) {
        .kpi-rmb-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        }
        .kpi-rmb-card-row1,
        .kpi-rmb-card-row2,
        .kpi-rmb-card-last {
            grid-column: span 1 / span 1 !important;
        }
    }
</style>

<div class="kpi-rmb-grid items-stretch">
    {{-- Card 1: Saldo Awal Belanja Modal Kasda --}}
    <div class="kpi-rmb-card-row1 h-full flex flex-col justify-between p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-cyan-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-cyan-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between gap-2 text-slate-400 mb-2.5 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-cyan-400 flex items-center gap-1.5 min-w-0 truncate">
                <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="truncate">Belanja Modal Kas</span>
            </span>
            <span class="px-2 py-0.5 rounded-md text-[9.5px] font-mono font-bold bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 shrink-0 whitespace-nowrap">LRA Kas</span>
        </div>
        <div class="relative z-10">
            <div class="text-base sm:text-lg font-mono font-black text-white tracking-tight break-words">
                Rp {{ number_format($kpis['total_belanja_kas'] ?? 0, 0, ',', '.') }}
            </div>
            @if(($kpis['saldo_awal_lampau'] ?? 0) > 0)
                <div class="mt-1 text-[10px] font-mono text-cyan-300/80 truncate">
                    LRA: Rp {{ number_format($kpis['belanja_modal_tahun_berjalan'] ?? 0, 0, ',', '.') }} &bull; Awal: Rp {{ number_format($kpis['saldo_awal_lampau'], 0, ',', '.') }}
                </div>
            @endif
        </div>
        <p class="relative z-10 text-[10px] text-slate-400 mt-1.5 leading-snug">
            Basis saldo awal mutasi &amp; belanja modal APBD/BLUD.
        </p>
    </div>

    {{-- Card 2: Total Mutasi Penambahan (+) --}}
    <div class="kpi-rmb-card-row1 h-full flex flex-col justify-between p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-emerald-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between gap-2 text-slate-400 mb-2.5 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-emerald-400 flex items-center gap-1.5 min-w-0 truncate">
                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="truncate">Total Penambahan</span>
            </span>
            <span class="px-2 py-0.5 rounded-md text-[9.5px] font-mono font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 shrink-0 whitespace-nowrap">8 Kolom</span>
        </div>
        <div class="relative z-10 text-base sm:text-lg font-mono font-black text-emerald-300 tracking-tight break-words">
            +Rp {{ number_format($kpis['total_penambahan'] ?? 0, 0, ',', '.') }}
        </div>
        <p class="relative z-10 text-[10px] text-slate-400 mt-1.5 leading-snug">
            Belanja modal, hibah masuk, belanja barang, mutasi &amp; KDP.
        </p>
    </div>

    {{-- Card 3: Total Mutasi Pengurangan (-) --}}
    <div class="kpi-rmb-card-row1 h-full flex flex-col justify-between p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-rose-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-rose-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between gap-2 text-slate-400 mb-2.5 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-rose-400 flex items-center gap-1.5 min-w-0 truncate">
                <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
                </svg>
                <span class="truncate">Total Pengurangan</span>
            </span>
            <span class="px-2 py-0.5 rounded-md text-[9.5px] font-mono font-bold bg-rose-500/10 text-rose-300 border border-rose-500/20 shrink-0 whitespace-nowrap">10 Kolom</span>
        </div>
        <div class="relative z-10 text-base sm:text-lg font-mono font-black text-rose-300 tracking-tight break-words">
            -Rp {{ number_format($kpis['total_pengurangan'] ?? 0, 0, ',', '.') }}
        </div>
        <p class="relative z-10 text-[10px] text-slate-400 mt-1.5 leading-snug">
            Ekstrakom, hibah keluar, mutasi OPD, koreksi &amp; temuan BPK.
        </p>
    </div>

    {{-- Card 4: Saldo Akhir Aset Tetap --}}
    <div class="kpi-rmb-card-row2 h-full flex flex-col justify-between p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-indigo-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between gap-2 text-slate-400 mb-2.5 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-indigo-400 flex items-center gap-1.5 min-w-0 truncate">
                <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="truncate">Saldo Akhir Aset</span>
            </span>
            <span class="px-2 py-0.5 rounded-md text-[9.5px] font-mono font-bold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 shrink-0 whitespace-nowrap">KIB A-F</span>
        </div>
        <div class="relative z-10 text-base sm:text-lg font-mono font-black text-indigo-200 tracking-tight break-words">
            Rp {{ number_format($kpis['saldo_akhir_aset'] ?? 0, 0, ',', '.') }}
        </div>
        <p class="relative z-10 text-[10px] text-slate-400 mt-1.5 leading-snug">
            Nilai buku fisik aset tetap tercatat di neraca daerah.
        </p>
    </div>

    {{-- Card 5: Status Rekonsiliasi Neraca (Balance Indicator) --}}
    <div class="kpi-rmb-card-row2 kpi-rmb-card-last h-full flex flex-col justify-between p-4 rounded-2xl border shadow-xl backdrop-blur-sm relative overflow-hidden group transition-all min-w-0"
         :class="isBalance ? 'bg-emerald-950/20 border-emerald-500/40' : 'bg-rose-950/20 border-rose-500/40'">
        <div class="relative z-10 flex items-center justify-between gap-2 mb-2.5 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold flex items-center gap-1.5 min-w-0 truncate"
                  :class="isBalance ? 'text-emerald-400' : 'text-rose-400'">
                <svg class="w-3.5 h-3.5 shrink-0" :class="isBalance ? 'text-emerald-400' : 'text-rose-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="truncate">Status Neraca Kas</span>
            </span>
            <span class="px-2 py-0.5 rounded-md text-[9.5px] font-mono font-black shrink-0 whitespace-nowrap"
                  :class="isBalance ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'"
                  x-text="isBalance ? 'BALANCE' : 'SELISIH'"></span>
        </div>
        <div class="relative z-10 text-base sm:text-lg font-mono font-black tracking-tight break-words"
             :class="isBalance ? 'text-emerald-300' : 'text-rose-300'">
            <span x-text="isBalance ? 'Rp 0 (Sempurna)' : 'Selisih Rp ' + Number(selisihNominal).toLocaleString('id-ID')"></span>
        </div>
        <p class="relative z-10 text-[10px] text-slate-400 mt-1.5 leading-snug"
           x-text="isBalance ? 'Saldo buku aset &amp; penyeimbang klop 100% dengan LRA kas.' : 'Terdapat perbedaan yang perlu ditelusuri di kertas kerja.'">
        </p>
    </div>
</div>
