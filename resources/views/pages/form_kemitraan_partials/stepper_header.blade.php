<!-- ========================================================================= -->
<!-- TOP HEADER & STEPPER PROGRESS INDICATOR (KEMITRAAN PIHAK KETIGA 1.5.2)   -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header Banner -->
    <div class="flex items-center justify-between p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl">
        <div class="flex items-center space-x-3.5 sm:space-x-4">
            <a href="{{ route('astap.pilih_jenis') }}"
               class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-950 border border-slate-800 hover:border-cyan-500/50 text-slate-400 hover:text-cyan-400 flex items-center justify-center transition-all shadow-sm shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-cyan-400/15 text-cyan-300 border border-cyan-400/30 text-[9px] sm:text-[10px] font-bold mb-1">
                    <span>🤝 PENCATATAN ASET KEMITRAAN PIHAK KETIGA (KSO / BGS)</span>
                </div>
                <h1 class="text-base sm:text-xl md:text-2xl font-extrabold text-white tracking-tight truncate">
                    Pencatatan Aset Kemitraan (SIMAT-RK)
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">
                    Pendaftaran perolehan aset kerja sama pihak ketiga (Akun 1.5.2 PMDN 108: KSO, KSP, BGS, Sewa) terintegrasi ke inventaris ruangan RSUD.
                </p>
            </div>
        </div>

        <!-- Tombol Link Cepat Reklasifikasi -->
        <a href="{{ route('master.reklasifikasi') }}"
            class="hidden lg:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-cyan-500/40 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
            </svg>
            <span>Matriks Reklasifikasi Neraca</span>
        </a>
    </div>

    <!-- Stepper Navigation Bar (3 Langkah Bersih) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl">
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            
            <!-- Step 1 Tab -->
            <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="currentStep === 1 ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : (currentStep > 1 ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                        <span x-show="currentStep <= 1">1</span>
                        <span x-show="currentStep > 1">✓</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 1 ? 'text-cyan-400' : 'text-slate-500'">Langkah 1</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Dokumen PKS &amp; Mitra</span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 1 ? 'bg-cyan-500' : 'bg-slate-950'"></div>
            </button>

            <!-- Step 2 Tab -->
            <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="currentStep === 2 ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : (currentStep > 2 ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                        <span x-show="currentStep <= 2">2</span>
                        <span x-show="currentStep > 2">✓</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 2 ? 'text-cyan-400' : 'text-slate-500'">Langkah 2</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Klasifikasi 108 (Akun 1.5.2)</span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 2 ? 'bg-cyan-500' : 'bg-slate-950'"></div>
            </button>

            <!-- Step 3 Tab -->
            <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="currentStep === 3 ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                        <span>3</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 3 ? 'text-cyan-400' : 'text-slate-500'">Langkah 3</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="(kibLabel ? kibLabel : 'Spesifikasi') + ' & Penempatan'"></span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 3 ? 'bg-cyan-500' : 'bg-slate-950'"></div>
            </button>

        </div>
    </div>
</div>
