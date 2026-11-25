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

<div class="kpi-rmb-grid">
    {{-- Card 1: Saldo Awal Belanja Modal Kasda --}}
    <div class="kpi-rmb-card-row1 p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-cyan-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-cyan-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between gap-2 text-slate-400 mb-2 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-cyan-400 flex items-center gap-1.5 min-w-0 truncate">
                <span class="shrink-0">💰</span>
                <span class="truncate">Belanja Modal (Kasda)</span>
            </span>
            <span class="px-2 py-0.5 rounded text-[9.5px] font-mono bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 shrink-0">LRA Kas</span>
        </div>
        <div class="text-base sm:text-lg font-mono font-black text-white tracking-tight break-words">
            Rp {{ number_format($kpis['total_belanja_kas'] ?? 0, 0, ',', '.') }}
        </div>
        <p class="text-[10px] text-slate-400 mt-1 leading-snug">
            Total perolehan belanja modal kas APBD/BLUD tahun berjalan.
        </p>
    </div>

    {{-- Card 2: Total Mutasi Penambahan (+) --}}
    <div class="kpi-rmb-card-row1 p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-emerald-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between gap-2 text-slate-400 mb-2 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-emerald-400 flex items-center gap-1.5 min-w-0 truncate">
                <span class="shrink-0">➕</span>
                <span class="truncate">Total Penambahan (+)</span>
            </span>
            <span class="px-2 py-0.5 rounded text-[9.5px] font-mono bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 shrink-0">8 Kolom</span>
        </div>
        <div class="text-base sm:text-lg font-mono font-black text-emerald-300 tracking-tight break-words">
            +Rp {{ number_format($kpis['total_penambahan'] ?? 0, 0, ',', '.') }}
        </div>
        <p class="text-[10px] text-slate-400 mt-1 leading-snug">
            Belanja, hibah masuk, belanja barang, mutasi &amp; KDP masuk.
        </p>
    </div>

    {{-- Card 3: Total Mutasi Pengurangan (-) --}}
    <div class="kpi-rmb-card-row1 p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-rose-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-rose-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between gap-2 text-slate-400 mb-2 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-rose-400 flex items-center gap-1.5 min-w-0 truncate">
                <span class="shrink-0">➖</span>
                <span class="truncate">Total Pengurangan (−)</span>
            </span>
            <span class="px-2 py-0.5 rounded text-[9.5px] font-mono bg-rose-500/10 text-rose-300 border border-rose-500/20 shrink-0">10 Kolom</span>
        </div>
        <div class="text-base sm:text-lg font-mono font-black text-rose-300 tracking-tight break-words">
            -Rp {{ number_format($kpis['total_pengurangan'] ?? 0, 0, ',', '.') }}
        </div>
        <p class="text-[10px] text-slate-400 mt-1 leading-snug">
            Ekstrakom, hibah keluar, mutasi OPD, reklas rusak &amp; temuan BPK.
        </p>
    </div>

    {{-- Card 4: Saldo Akhir Aset Tetap --}}
    <div class="kpi-rmb-card-row2 p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-sm relative overflow-hidden group hover:border-indigo-500/40 transition-all min-w-0">
        <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-indigo-500/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between gap-2 text-slate-400 mb-2 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold text-indigo-400 flex items-center gap-1.5 min-w-0 truncate">
                <span class="shrink-0">🏛️</span>
                <span class="truncate">Saldo Akhir Aset Tetap</span>
            </span>
            <span class="px-2 py-0.5 rounded text-[9.5px] font-mono bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 shrink-0">KIB A-F</span>
        </div>
        <div class="text-base sm:text-lg font-mono font-black text-indigo-200 tracking-tight break-words">
            Rp {{ number_format($kpis['saldo_akhir_aset'] ?? 0, 0, ',', '.') }}
        </div>
        <p class="text-[10px] text-slate-400 mt-1 leading-snug">
            Nilai buku fisik aset tetap resmi tercatat di neraca daerah.
        </p>
    </div>

    {{-- Card 5: Status Rekonsiliasi Neraca (Balance Indicator) --}}
    <div class="kpi-rmb-card-row2 kpi-rmb-card-last p-4 rounded-2xl border shadow-xl backdrop-blur-sm relative overflow-hidden group transition-all min-w-0"
         :class="isBalance ? 'bg-emerald-950/20 border-emerald-500/40' : 'bg-rose-950/20 border-rose-500/40'">
        <div class="flex items-center justify-between gap-2 mb-2 min-w-0">
            <span class="text-[10px] font-mono uppercase tracking-wider font-extrabold flex items-center gap-1.5 min-w-0 truncate"
                  :class="isBalance ? 'text-emerald-400' : 'text-rose-400'">
                <span class="shrink-0">⚖️</span>
                <span class="truncate">Status Neraca Kas</span>
            </span>
            <span class="px-2 py-0.5 rounded text-[9.5px] font-mono font-black shrink-0"
                  :class="isBalance ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'"
                  x-text="isBalance ? 'BALANCE' : 'SELISIH'"></span>
        </div>
        <div class="text-base sm:text-lg font-mono font-black tracking-tight break-words"
             :class="isBalance ? 'text-emerald-300' : 'text-rose-300'">
            <span x-text="isBalance ? 'Rp 0 (Sempurna)' : 'Selisih Rp ' + Number(selisihNominal).toLocaleString('id-ID')"></span>
        </div>
        <p class="text-[10px] text-slate-400 mt-1 leading-snug"
           x-text="isBalance ? 'Saldo buku aset &amp; penyeimbang 100% klop dengan belanja kasda.' : 'Terdapat perbedaan yang perlu ditelusuri di kertas kerja rekonsiliasi.'">
        </p>
    </div>
</div>
