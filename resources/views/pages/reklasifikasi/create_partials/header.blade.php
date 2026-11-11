<!-- HEADER & BREADCRUMB CREATE REKLASIFIKASI -->
<div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden backdrop-blur-xl">
    <!-- Ambient Decoration -->
    <div class="absolute -top-20 -right-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
        <div>
            <!-- Back Button & Breadcrumb -->
            <div class="flex flex-wrap items-center gap-3 mb-3">
                <a href="{{ route('master.reklasifikasi', ['tahun' => $selectedTahun]) }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Reklasifikasi</span>
                </a>
                <span class="text-slate-600">/</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-950/80 border border-slate-800 text-[11px] font-semibold text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    <span>Standar PMDN 108 · RSUD Dr. H. Koesnandi</span>
                </span>
            </div>

            <!-- Page Title -->
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <span class="p-2.5 rounded-2xl bg-slate-950 border border-slate-800 text-indigo-400 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </span>
                <span>Catat Transaksi Reklasifikasi Baru</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-2 max-w-3xl leading-relaxed">
                Formulir resmi penatausahaan pemindahan bukuan antar kelompok KIB A s/d F, Batas Kapitalisasi (Ekstrakomptabel), Koreksi Rekening PMDN 108, Bantuan Hibah, dan Mutasi Antar-OPD.
            </p>
        </div>

        <!-- Periode & Tahun Indicator -->
        <div class="flex items-center gap-3 bg-slate-950/80 border border-slate-800 rounded-2xl px-4 py-3 shadow-inner self-start md:self-auto shrink-0">
            <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahun Anggaran</p>
                <p class="text-sm font-bold text-white font-mono">
                    T.A. {{ $selectedTahun }}
                </p>
            </div>
        </div>
    </div>
</div>

