<!-- HEADER BANNER & STATISTIK REKLASIFIKASI (2 BARIS MANDIRI BERKELAS) -->
<div class="space-y-4">
    <!-- BARIS 1: TOP HEADER BANNER (MANDIRI) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden backdrop-blur-xl">
        <!-- Subtle Ambient Glow -->
        <div class="absolute -top-20 -left-20 w-52 h-52 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-52 h-52 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl shrink-0 shadow-lg shadow-indigo-500/10 mt-0.5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        Reklasifikasi Aset Tetap
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/10 text-indigo-300 border border-indigo-500/25 font-mono shadow-sm">
                        Standar PMDN 108 · RSUD Dr. H. Koesnandi
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Matriks rekonsiliasi dan pemindahan bukuan antar kelompok KIB A s/d F serta Aset Lainnya sesuai hierarki kode Permendagri No. 108 Tahun 2016.
                </p>
            </div>
        </div>

        <!-- Periode Aktif Badge (Kanan) -->
        <div class="flex items-center gap-3 bg-slate-950/80 border border-slate-800 rounded-2xl px-4 py-3 shadow-inner relative z-10 shrink-0 self-start md:self-auto">
            <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-indigo-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Periode Aktif</p>
                <p class="text-sm font-bold text-white font-mono">
                    Tahun {{ $selectedTahun }} · {{ $selectedTw === 'all' ? 'Seluruh Tahun' : 'Triwulan ' . $selectedTw }}
                </p>
            </div>
        </div>
    </div>

    <!-- BARIS 2: 4 KARTU STATISTIK KPI MANDIRI (COMPACT, RAPI & ANTI-OVERFLOW) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        <!-- 1. Saldo Awal (Belanja Modal) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-4.5 shadow-xl hover:border-slate-700 transition-all duration-200">
            <div class="flex items-center justify-between text-slate-400 mb-1.5">
                <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Saldo Awal Belanja</span>
                <div class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-1 flex items-baseline gap-1 truncate">
                <span class="text-xs font-bold text-slate-500 font-mono">Rp</span>
                <span class="text-lg sm:text-xl font-black text-white font-mono tracking-tight">
                    {{ number_format($grandTotal['awal'], 0, ',', '.') }}
                </span>
            </div>
            <p class="text-[10.5px] text-slate-400 mt-1 truncate">
                Total Realisasi Belanja Modal (Aset)
            </p>
        </div>

        <!-- 2. Mutasi Tambah -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-4.5 shadow-xl hover:border-emerald-500/30 transition-all duration-200">
            <div class="flex items-center justify-between text-slate-400 mb-1.5">
                <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Mutasi Tambah (+)</span>
                <span class="text-[9.5px] font-bold px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 shrink-0">
                    + Masuk
                </span>
            </div>
            <div class="mt-1 flex items-baseline gap-1 truncate">
                <span class="text-xs font-bold text-emerald-500/70 font-mono">Rp</span>
                <span class="text-lg sm:text-xl font-black text-emerald-400 font-mono tracking-tight">
                    {{ number_format($grandTotal['tambah'], 0, ',', '.') }}
                </span>
            </div>
            <p class="text-[10.5px] text-slate-400 mt-1 truncate">
                Reklas Masuk / KDP Selesai
            </p>
        </div>

        <!-- 3. Mutasi Kurang -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-4.5 shadow-xl hover:border-rose-500/30 transition-all duration-200">
            <div class="flex items-center justify-between text-slate-400 mb-1.5">
                <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Mutasi Kurang (-)</span>
                <span class="text-[9.5px] font-bold px-2 py-0.5 rounded-md bg-rose-500/15 text-rose-400 border border-rose-500/25 shrink-0">
                    - Keluar
                </span>
            </div>
            <div class="mt-1 flex items-baseline gap-1 truncate">
                <span class="text-xs font-bold text-rose-500/70 font-mono">Rp</span>
                <span class="text-lg sm:text-xl font-black text-rose-400 font-mono tracking-tight">
                    {{ number_format($grandTotal['kurang'], 0, ',', '.') }}
                </span>
            </div>
            <p class="text-[10.5px] text-slate-400 mt-1 truncate">
                Reklas Keluar / Ekstrakomptabel
            </p>
        </div>

        <!-- 4. Saldo Akhir Neraca -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-4.5 shadow-xl hover:border-cyan-500/30 transition-all duration-200">
            <div class="flex items-center justify-between text-slate-400 mb-1.5">
                <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Saldo Akhir Periode</span>
                <span class="text-[9.5px] font-bold px-2 py-0.5 rounded-md bg-cyan-500/15 text-cyan-400 border border-cyan-500/25 shrink-0">
                    Neraca
                </span>
            </div>
            <div class="mt-1 flex items-baseline gap-1 truncate">
                <span class="text-xs font-bold text-slate-500 font-mono">Rp</span>
                <span class="text-lg sm:text-xl font-black text-white font-mono tracking-tight">
                    {{ number_format($grandTotal['akhir'], 0, ',', '.') }}
                </span>
            </div>
            <p class="text-[10.5px] text-slate-400 mt-1 truncate">
                Saldo Awal + Tambah - Kurang
            </p>
        </div>
    </div>
</div>
