<!-- ========================================================================= -->
<!-- HEADER BANNER & STEPPER NAVIGATION BAR (BELANJA BARANG 5.1.02)            -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header & Back -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('master.belanja_barang') }}"
                class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/50 text-slate-400 hover:text-indigo-400 transition-all shadow-lg shadow-black/20 group">
                <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-white tracking-tight">
                        Pencatatan Belanja Barang (Instalasi Perbekalan / Gudang)
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                        📦 BELI MANDIRI PERBEKALAN · BUKAN BELANJA MODAL
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
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
                <span>Katalog Belanja Barang</span>
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
                         :class="step === 1 ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : (step > 1 ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                        <span x-show="step <= 1">1</span>
                        <span x-show="step > 1">✓</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 1 ? 'text-indigo-400' : 'text-slate-500'">Langkah 1</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Dokumen Faktur &amp; Toko</span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 1 ? 'bg-indigo-500' : 'bg-slate-950'"></div>
            </button>

            <!-- Step 2 Tab -->
            <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="step === 2 ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : (step > 2 ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                        <span x-show="step <= 2">2</span>
                        <span x-show="step > 2">✓</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-indigo-400' : 'text-slate-500'">Langkah 2</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Klasifikasi 108</span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-indigo-500' : 'bg-slate-950'"></div>
            </button>

            <!-- Step 3 Tab -->
            <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="step === 3 ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                        <span>3</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-indigo-400' : 'text-slate-500'">Langkah 3</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="(hasSelectedKib ? kibLabel : 'Spesifikasi') + ' & Penempatan'"></span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 3 ? 'bg-indigo-500' : 'bg-slate-950'"></div>
            </button>

        </div>
    </div>
</div>
