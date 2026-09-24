<!-- Top Navigation Bar (Back + Title) -->
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
    <div class="flex items-center space-x-3 sm:space-x-4 w-full sm:w-auto">
        <a href="{{ route('astap.pilih_jenis') }}" 
           class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-950 border border-slate-800 hover:border-amber-500/50 text-slate-400 hover:text-amber-400 flex items-center justify-center transition-all shadow-sm shrink-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="min-w-0 flex-1">
            <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-amber-400/15 text-amber-300 border border-amber-400/30 text-[9px] sm:text-[10px] font-bold mb-1">
                <span>🎁 PENCATATAN ASET HIBAH / BANTUAN MASUK</span>
            </div>
            <h1 class="text-base sm:text-xl md:text-2xl font-extrabold text-white tracking-tight truncate">
                Pencatatan Aset Hibah Masuk (SIMAT-RK)
            </h1>
            <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">
                Pendaftaran perolehan aset dari bantuan/hibah masuk tanpa pagu APBD, terintegrasi penuh ke inventaris KIB & penempatan ruangan RSUD.
            </p>
        </div>
    </div>
</div>

<!-- Multi-Step Stepper Header (3 Langkah) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        
        <!-- Step 1 Tab -->
        <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                     style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                     :class="currentStep === 1 ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : (currentStep > 1 ? 'bg-amber-400/20 text-amber-400 border border-amber-400/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                    <span x-show="currentStep <= 1">1</span>
                    <span x-show="currentStep > 1">✓</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 1 ? 'text-amber-400' : 'text-slate-500'">Langkah 1</span>
                    <span class="text-[11px] sm:text-xs font-bold text-white block truncate">BAST & Pemberi Hibah</span>
                </div>
            </div>
            <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 1 ? 'bg-amber-400' : 'bg-slate-950'"></div>
        </button>

        <!-- Step 2 Tab (Rekening Belanja & Jenis ASTAP PMDN 108) -->
        <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                     style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                     :class="currentStep === 2 ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : (currentStep > 2 ? 'bg-amber-400/20 text-amber-400 border border-amber-400/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                    <span x-show="currentStep <= 2">2</span>
                    <span x-show="currentStep > 2">✓</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 2 ? 'text-amber-400' : 'text-slate-500'">Langkah 2</span>
                    <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Klasifikasi 108 & Akun</span>
                </div>
            </div>
            <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 2 ? 'bg-amber-400' : 'bg-slate-950'"></div>
        </button>

        <!-- Step 3 Tab (Rincian Aset KIB & Penempatan Ruangan) -->
        <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                     style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                     :class="currentStep === 3 ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                    <span>3</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 3 ? 'text-amber-400' : 'text-slate-500'">Langkah 3</span>
                    <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="isTanah ? 'Rincian Tanah & Lokasi' : (isMesin ? 'Rincian Mesin & Ruang' : (isGedung ? 'Rincian Gedung & Ruang' : (isJaringan ? 'Rincian Jaringan & Ruang' : (isAsetLainnya ? 'Rincian Lainnya' : (isAtb ? 'Rincian ATB' : (isKdp ? 'Rincian KDP' : 'Rincian Aset & Penempatan'))))))"></span>
                </div>
            </div>
            <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 3 ? 'bg-amber-400' : 'bg-slate-950'"></div>
        </button>

    </div>
</div>
