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

        <!-- Aksi & Periode Tahun Indicator -->
        <div class="flex flex-wrap items-center gap-2.5 self-start md:self-auto shrink-0">
            <!-- Tombol Buka Kamus Panduan PMDN 108 -->
            <button type="button" @click="openPanduan('')"
                class="flex items-center gap-1.5 px-3.5 py-3 rounded-2xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 text-xs font-bold transition-all duration-200 cursor-pointer shadow-md hover:shadow-indigo-500/20 active:scale-95"
                title="Kamus & Panduan Pengelompokan Barang PMDN 108 Khas RSUD">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span>Kamus Panduan KIB</span>
            </button>

            <!-- Periode & Tahun Indicator -->
            <div class="flex items-center gap-3 bg-slate-950/80 border border-slate-800 rounded-2xl px-4 py-3 shadow-inner">
                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahun Anggaran</p>
                    <p class="text-sm font-bold text-white font-mono">
                        {{ $selectedTahun }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stepper Navigation Bar (2 Tahap Bersih & Modern) -->
    <div class="mt-6 pt-5 border-t border-slate-800/80 grid grid-cols-1 sm:grid-cols-2 gap-3 relative z-10">
        <!-- Step 1 Tab -->
        <button type="button" @click="goToStep(1)"
                class="text-left p-3.5 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 shadow-md"
                :class="currentStep === 1
                    ? 'bg-indigo-950/40 border-indigo-500/50 shadow-indigo-500/10'
                    : 'bg-slate-950/60 border-slate-800/80 hover:border-slate-700 text-slate-400'">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl font-mono font-black text-xs flex items-center justify-center shrink-0 transition-all"
                     :class="currentStep === 1
                         ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30'
                         : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'">
                    <span x-show="currentStep === 1">1</span>
                    <span x-show="currentStep > 1">✓</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9.5px] font-bold uppercase tracking-wider block"
                          :class="currentStep === 1 ? 'text-indigo-400' : 'text-slate-500'">Langkah 1</span>
                    <span class="text-xs font-extrabold text-white block truncate">Formulir &amp; Parameter Reklasifikasi</span>
                </div>
            </div>
            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border shrink-0"
                  :class="currentStep === 1
                      ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30'
                      : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'">
                <span x-text="currentStep === 1 ? 'Aktif Diisi' : 'Selesai ✓'"></span>
            </span>
        </button>

        <!-- Step 2 Tab -->
        <button type="button" @click="goToStep(2)"
                class="text-left p-3.5 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 shadow-md"
                :class="currentStep === 2
                    ? 'bg-purple-950/40 border-purple-500/50 shadow-purple-500/10'
                    : 'bg-slate-950/60 border-slate-800/80 hover:border-slate-700 text-slate-400'">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl font-mono font-black text-xs flex items-center justify-center shrink-0 transition-all"
                     :class="currentStep === 2
                         ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-600/30'
                         : 'bg-slate-900 text-slate-500 border border-slate-800'">
                    <span>2</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9.5px] font-bold uppercase tracking-wider block"
                          :class="currentStep === 2 ? 'text-purple-400' : 'text-slate-500'">Langkah 2</span>
                    <span class="text-xs font-extrabold text-white block truncate">Lembar Verifikasi Reklasifikasi</span>
                </div>
            </div>
            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border shrink-0"
                  :class="currentStep === 2
                      ? 'bg-purple-500/20 text-purple-300 border-purple-500/30'
                      : 'bg-slate-900 text-slate-500 border-slate-800'">
                <span x-text="currentStep === 2 ? 'Verifikasi 🎯' : 'Tahap Akhir'"></span>
            </span>
        </button>
    </div>
</div>

