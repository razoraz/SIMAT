<!-- HEADER BANNER & STATISTIK REKLASIFIKASI -->
<div class="relative overflow-hidden rounded-3xl bg-slate-900/90 border border-slate-800 p-6 md:p-8 shadow-xl backdrop-blur-xl">
    <!-- Subtle Ambient Glow -->
    <div class="absolute -top-24 -right-24 w-72 h-72 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-slate-800/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950/80 border border-slate-800 text-slate-300 text-xs font-semibold mb-3">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                <span>Standar PMDN 108 · RSUD Dr. H. Koesnandi</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span class="p-2.5 rounded-2xl bg-slate-950 border border-slate-800 text-indigo-400 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </span>
                <span>Reklasifikasi Aset Tetap</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-2 max-w-2xl leading-relaxed">
                Matriks rekonsiliasi dan pemindahan bukuan antar kelompok KIB A s/d F serta Aset Lainnya sesuai hierarki kode Permendagri No. 108 Tahun 2016.
            </p>
        </div>

        <!-- Periode Badge -->
        <div class="flex items-center gap-3 bg-slate-950/80 border border-slate-800 rounded-2xl px-4 py-3 shadow-inner self-start md:self-auto shrink-0">
            <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Periode Aktif</p>
                <p class="text-sm font-bold text-white">
                    Tahun {{ $selectedTahun }} · {{ $selectedTw === 'all' ? 'Seluruh Tahun' : 'Triwulan ' . $selectedTw }}
                </p>
            </div>
        </div>
    </div>

    <!-- 4 KARTU STATISTIK MATRIKS 5 KOLOM (DARK SLEEK, ADEM & SERAGAM) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mt-8">
        <!-- 1. Saldo Awal (Belanja Modal) -->
        <div class="bg-slate-950/80 border border-slate-800/90 hover:border-slate-700 rounded-2xl p-4.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saldo Awal Belanja</span>
                <span class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white font-mono mt-2 tracking-tight">
                Rp {{ number_format($grandTotal['awal'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Realisasi Belanja Modal (Aset)</p>
        </div>

        <!-- 2. Mutasi Tambah -->
        <div class="bg-slate-950/80 border border-slate-800/90 hover:border-slate-700 rounded-2xl p-4.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mutasi Tambah (+)</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-[10px] font-bold text-emerald-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Masuk</span>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white font-mono mt-2 tracking-tight">
                Rp {{ number_format($grandTotal['tambah'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Reklas Masuk / KDP Selesai</p>
        </div>

        <!-- 3. Mutasi Kurang -->
        <div class="bg-slate-950/80 border border-slate-800/90 hover:border-slate-700 rounded-2xl p-4.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mutasi Kurang (-)</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-[10px] font-bold text-rose-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                    </svg>
                    <span>Keluar</span>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white font-mono mt-2 tracking-tight">
                Rp {{ number_format($grandTotal['kurang'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Reklas Keluar / Ekstrakomptabel</p>
        </div>

        <!-- 4. Saldo Akhir Neraca -->
        <div class="bg-slate-950/80 border border-slate-800/90 hover:border-slate-700 rounded-2xl p-4.5 transition-all duration-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saldo Akhir Periode</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-bold text-indigo-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Neraca</span>
                </span>
            </div>
            <p class="text-xl sm:text-2xl font-black text-white font-mono mt-2 tracking-tight">
                Rp {{ number_format($grandTotal['akhir'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Saldo Awal + Tambah - Kurang</p>
        </div>
    </div>
</div>
