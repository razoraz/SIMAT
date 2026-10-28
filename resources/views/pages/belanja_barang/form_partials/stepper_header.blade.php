<!-- ========================================================================= -->
<!-- HEADER BANNER & STEPPER NAVIGATION BAR (BELANJA BARANG PERBEKALAN)        -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header & Back -->
    <div class="flex items-center justify-between p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-slate-900/90 border border-indigo-500/30 transition-all duration-300 shadow-xl">
        <div class="flex items-center space-x-3.5 sm:space-x-4">
            <a href="{{ route('astap.pilih_jenis') }}"
               class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center transition-all shadow-sm shrink-0 hover:border-indigo-500/50 text-slate-400 hover:text-indigo-400"
               title="Kembali ke Menu Pilih Jenis ASTAP">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="min-w-0 flex-1">
                <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold mb-1 border transition-all bg-indigo-400/15 text-indigo-300 border-indigo-400/30">
                    <span>📦 PENCATATAN BELANJA BARANG (INSTALASI PERBEKALAN / GUDANG)</span>
                </div>
                <h1 class="text-base sm:text-xl md:text-2xl font-extrabold text-white tracking-tight truncate">
                    Pencatatan Belanja Barang (Perbekalan)
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">
                    Pengadaan langsung oleh Instalasi Perbekalan / Bagian Gudang menggunakan anggaran operasional sendiri (bukan belanja modal) untuk inventarisasi fisik &amp; Kartu Inventaris Ruangan (KIR).
                </p>
            </div>
        </div>

        <!-- Tombol Pintas ke Katalog Master Belanja Barang -->
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('master.belanja_barang') }}"
                class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-indigo-500/40 text-xs font-bold text-slate-300 hover:text-indigo-300 transition-all flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span class="hidden sm:inline">Katalog Belanja Barang</span>
            </a>
        </div>
    </div>

    <!-- Stepper Navigation Bar (3 Langkah Bersih) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl">
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            
            <!-- Step 1 Tab -->
            <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="stepErrors[1] 
                            ? 'bg-rose-500/20 text-rose-300 border border-rose-500/60 shadow-lg shadow-rose-500/20' 
                            : (currentStep === 1 
                                ? 'bg-indigo-500 text-white font-black shadow-lg shadow-indigo-500/30' 
                                : (currentStep > 1 
                                    ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40' 
                                    : 'bg-slate-950 text-slate-500 border border-slate-800'))">
                        <span x-show="!stepErrors[1] && currentStep <= 1">1</span>
                        <span x-show="!stepErrors[1] && currentStep > 1">✓</span>
                        <span x-show="stepErrors[1]">!</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate"
                              :class="stepErrors[1] ? 'text-rose-400' : (currentStep === 1 ? 'text-indigo-400' : 'text-slate-500')">Langkah 1</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Faktur &amp; Toko</span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all"
                     :class="stepErrors[1] ? 'bg-rose-500' : (currentStep >= 1 ? 'bg-indigo-500' : 'bg-slate-950')"></div>
            </button>

            <!-- Step 2 Tab -->
            <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="stepErrors[2] 
                            ? 'bg-rose-500/20 text-rose-300 border border-rose-500/60 shadow-lg shadow-rose-500/20' 
                            : (currentStep === 2 
                                ? 'bg-indigo-500 text-white font-black shadow-lg shadow-indigo-500/30' 
                                : (currentStep > 2 
                                    ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40' 
                                    : 'bg-slate-950 text-slate-500 border border-slate-800'))">
                        <span x-show="!stepErrors[2] && currentStep <= 2">2</span>
                        <span x-show="!stepErrors[2] && currentStep > 2">✓</span>
                        <span x-show="stepErrors[2]">!</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate"
                              :class="stepErrors[2] ? 'text-rose-400' : (currentStep === 2 ? 'text-indigo-400' : 'text-slate-500')">Langkah 2</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Klasifikasi 108 &amp; Fisik</span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all"
                     :class="stepErrors[2] ? 'bg-rose-500' : (currentStep >= 2 ? 'bg-indigo-500' : 'bg-slate-950')"></div>
            </button>

            <!-- Step 3 Tab -->
            <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="stepErrors[3] 
                            ? 'bg-rose-500/20 text-rose-300 border border-rose-500/60 shadow-lg shadow-rose-500/20' 
                            : (currentStep === 3 
                                ? 'bg-indigo-500 text-white font-black shadow-lg shadow-indigo-500/30' 
                                : 'bg-slate-950 text-slate-500 border border-slate-800')">
                        <span x-show="!stepErrors[3]">3</span>
                        <span x-show="stepErrors[3]">!</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate"
                              :class="stepErrors[3] ? 'text-rose-400' : (currentStep === 3 ? 'text-indigo-400' : 'text-slate-500')">Langkah 3</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Verifikasi &amp; Simpan</span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all"
                     :class="stepErrors[3] ? 'bg-rose-500' : (currentStep >= 3 ? 'bg-indigo-500' : 'bg-slate-950')"></div>
            </button>

        </div>
    </div>
</div>
