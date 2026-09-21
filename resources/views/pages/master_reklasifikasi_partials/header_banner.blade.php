<!-- HEADER BANNER & STATISTIK REKLASIFIKASI -->
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950/80 border border-slate-700/60 p-6 md:p-8 shadow-2xl backdrop-blur-xl">
    <!-- Ambient Glow Decoration -->
    <div class="absolute -top-24 -right-24 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-ping"></span>
                <span>Standar PMDN 108 · RSUD Dr. H. Koesnandi</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-indigo-600/20 border border-indigo-500/40 text-indigo-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </span>
                Reklasifikasi Aset Tetap
            </h1>
            <p class="text-sm text-slate-400 mt-2 max-w-2xl leading-relaxed">
                Matriks rekonsiliasi dan pemindahan bukuan antar kelompok KIB A s/d F serta Aset Lainnya sesuai hierarki kode Permendagri No. 108 Tahun 2016.
            </p>
        </div>

        <!-- Periode Badge -->
        <div class="flex items-center gap-3 bg-slate-800/80 border border-slate-700/80 rounded-xl px-4 py-3 shadow-inner">
            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Periode Aktif</p>
                <p class="text-base font-bold text-white">
                    Tahun {{ $selectedTahun }} · {{ $selectedTw === 'all' ? 'Seluruh Tahun' : 'Triwulan ' . $selectedTw }}
                </p>
            </div>
        </div>
    </div>

    <!-- 4 KARTU STATISTIK MATRIKS 5 KOLOM -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
        <!-- 1. Saldo Awal (Belanja Modal) -->
        <div class="bg-slate-800/60 border border-slate-700/60 hover:border-slate-600 rounded-xl p-4 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400">Saldo Awal (Belanja Modal)</span>
                <span class="p-1.5 rounded-lg bg-blue-500/10 text-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-xl font-black text-white mt-2">
                Rp {{ number_format($grandTotal['awal'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Total Realisasi Belanja Modal</p>
        </div>

        <!-- 2. Mutasi Tambah -->
        <div class="bg-slate-800/60 border border-emerald-500/20 hover:border-emerald-500/40 rounded-xl p-4 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-400">Mutasi Tambah (+)</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </span>
            </div>
            <p class="text-xl font-black text-emerald-400 mt-2">
                Rp {{ number_format($grandTotal['tambah'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Reklas Masuk / KDP Selesai</p>
        </div>

        <!-- 3. Mutasi Kurang -->
        <div class="bg-slate-800/60 border border-rose-500/20 hover:border-rose-500/40 rounded-xl p-4 transition-all duration-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-rose-400">Mutasi Kurang (-)</span>
                <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </span>
            </div>
            <p class="text-xl font-black text-rose-400 mt-2">
                Rp {{ number_format($grandTotal['kurang'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Reklas Keluar / Ekstrakomptabel</p>
        </div>

        <!-- 4. Saldo Akhir Neraca -->
        <div class="bg-gradient-to-br from-indigo-900/40 to-slate-800/80 border border-indigo-500/30 hover:border-indigo-400/50 rounded-xl p-4 transition-all duration-200 shadow-lg shadow-indigo-950/40">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-indigo-300">Saldo Akhir Per Periode</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/20 text-indigo-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-xl font-black text-white mt-2">
                Rp {{ number_format($grandTotal['akhir'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-indigo-300/80 mt-1">Saldo Awal + Tambah - Kurang</p>
        </div>
    </div>
</div>
