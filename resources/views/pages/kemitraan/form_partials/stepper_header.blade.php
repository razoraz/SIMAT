<!-- ========================================================================= -->
<!-- TOP HEADER & STEPPER PROGRESS INDICATOR (KEMITRAAN PIHAK KETIGA 1.5.2)   -->
<!-- DIBEDAKAN: 1. Aset Dimanfaatkan (Cyan 🏛️) | 2. Aset Ditambahkan (Emerald 📦) -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header Banner -->
    <div class="flex items-center justify-between p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-slate-900/90 border transition-all duration-300 shadow-xl"
         :class="tipeKemitraan === 'dimanfaatkan' ? 'border-cyan-500/30' : 'border-emerald-500/30'">
        <div class="flex items-center space-x-3.5 sm:space-x-4">
            <a href="{{ isset($astap) ? route('master.kemitraan') : route('astap.pilih_jenis') }}"
               class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center transition-all shadow-sm shrink-0"
               :class="tipeKemitraan === 'dimanfaatkan' ? 'hover:border-cyan-500/50 text-slate-400 hover:text-cyan-400' : 'hover:border-emerald-500/50 text-slate-400 hover:text-emerald-400'">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div class="min-w-0 flex-1">
                <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold mb-1 border transition-all"
                     :class="tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-400/15 text-cyan-300 border-cyan-400/30' : 'bg-emerald-400/15 text-emerald-300 border-emerald-400/30'">
                    <span x-show="tipeKemitraan === 'dimanfaatkan'">🏛️ {{ isset($astap) ? 'PERUBAHAN PEMANFAATAN BMD RSUD (AKUN 1.5.2)' : 'PENCATATAN PEMANFAATAN BMD MILIK RSUD KE MITRA (AKUN 1.5.2)' }}</span>
                    <span x-show="tipeKemitraan === 'ditambahkan'">📦 {{ isset($astap) ? 'PERUBAHAN ASET DITAMBAHKAN MITRA (AKUN 1.5.2)' : 'PENCATATAN ASET BARU DITAMBAHKAN MITRA REKANAN (AKUN 1.5.2)' }}</span>
                </div>
                <h1 class="text-base sm:text-xl md:text-2xl font-extrabold text-white tracking-tight truncate">
                    <span x-show="tipeKemitraan === 'dimanfaatkan'">{{ isset($astap) ? 'Ubah Data Pemanfaatan BMD RSUD' : 'Pencatatan Pemanfaatan BMD Milik RSUD ke Mitra' }}</span>
                    <span x-show="tipeKemitraan === 'ditambahkan'">{{ isset($astap) ? 'Ubah Data Aset Ditambahkan Mitra' : 'Pencatatan Aset Baru yang Ditambahkan oleh Mitra' }}</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">
                    <span x-show="tipeKemitraan === 'dimanfaatkan'">Pemanfaatan Barang Milik Daerah RSUD (Sewa Tanah, Gedung, Ruang Paviliun, atau KSP Alat) kepada pihak ketiga dengan penautan objek BMD.</span>
                    <span x-show="tipeKemitraan === 'ditambahkan'">Pengadaan dan penambahan alat medis, laboratorium, mesin, atau fasilitas baru yang didatangkan oleh pihak ketiga (Mitra KSO / BGS) untuk RSUD.</span>
                </p>
            </div>
        </div>

        <!-- Tombol Link Cepat Reklasifikasi & Master -->
        <div class="flex items-center gap-2">
            <a href="{{ route('master.kemitraan') }}"
                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-xs font-bold text-slate-300 hover:text-white transition-all shadow-sm">
                <span>📑</span>
                <span>Tabel Master</span>
            </a>
            <a href="{{ route('master.reklasifikasi') }}"
                class="hidden lg:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 hover:border-cyan-500/40 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <span>Matriks Reklasifikasi</span>
            </a>
        </div>
    </div>


    <!-- Stepper Navigation Bar (3 Langkah Bersih) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl">
        <div class="grid grid-cols-3 gap-3 sm:gap-4">
            
            <!-- Step 1 Tab -->
            <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0 relative"
                         :class="stepErrors[1] 
                            ? 'bg-rose-500/20 text-rose-300 border border-rose-500/60 shadow-lg shadow-rose-500/20' 
                            : (currentStep === 1 
                                ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30')
                                : (currentStep > 1 
                                    ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40')
                                    : 'bg-slate-950 text-slate-500 border border-slate-800'))">
                        <!-- Nomor / centang -->
                        <span x-show="!stepErrors[1] && currentStep <= 1">1</span>
                        <span x-show="!stepErrors[1] && currentStep > 1">✓</span>
                        <span x-show="stepErrors[1]">!</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate"
                              :class="stepErrors[1] ? 'text-rose-400' : (currentStep === 1 ? (tipeKemitraan === 'dimanfaatkan' ? 'text-cyan-400' : 'text-emerald-400') : 'text-slate-500')">Langkah 1</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate"
                              x-text="tipeKemitraan === 'dimanfaatkan' ? 'Objek BMD & PKS' : 'Mitra Penyedia & PKS'">
                        </span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all"
                     :class="stepErrors[1] ? 'bg-rose-500' : (currentStep >= 1 ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500' : 'bg-emerald-500') : 'bg-slate-950')"></div>
            </button>

            <!-- Step 2 Tab -->
            <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="stepErrors[2] 
                            ? 'bg-rose-500/20 text-rose-300 border border-rose-500/60 shadow-lg shadow-rose-500/20' 
                            : (currentStep === 2 
                                ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30')
                                : (currentStep > 2 
                                    ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40')
                                    : 'bg-slate-950 text-slate-500 border border-slate-800'))">
                        <span x-show="!stepErrors[2] && currentStep <= 2">2</span>
                        <span x-show="!stepErrors[2] && currentStep > 2">✓</span>
                        <span x-show="stepErrors[2]">!</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate"
                              :class="stepErrors[2] ? 'text-rose-400' : (currentStep === 2 ? (tipeKemitraan === 'dimanfaatkan' ? 'text-cyan-400' : 'text-emerald-400') : 'text-slate-500')">Langkah 2</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate"
                              x-text="tipeKemitraan === 'dimanfaatkan' ? 'Klasifikasi 108 Pemanfaatan' : 'Spesifikasi Barang Baru'">
                        </span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all"
                     :class="stepErrors[2] ? 'bg-rose-500' : (currentStep >= 2 ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500' : 'bg-emerald-500') : 'bg-slate-950')"></div>
            </button>

            <!-- Step 3 Tab -->
            <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                    <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                         :class="stepErrors[3] 
                            ? 'bg-rose-500/20 text-rose-300 border border-rose-500/60 shadow-lg shadow-rose-500/20' 
                            : (currentStep === 3 
                                ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30')
                                : 'bg-slate-950 text-slate-500 border border-slate-800')">
                        <span x-show="!stepErrors[3]">3</span>
                        <span x-show="stepErrors[3]">!</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate"
                              :class="stepErrors[3] ? 'text-rose-400' : (currentStep === 3 ? (tipeKemitraan === 'dimanfaatkan' ? 'text-cyan-400' : 'text-emerald-400') : 'text-slate-500')">Langkah 3</span>
                        <span class="text-[11px] sm:text-xs font-bold text-white block truncate"
                              x-text="tipeKemitraan === 'dimanfaatkan' ? 'Verifikasi Pemanfaatan' : 'Verifikasi Aset Baru'">
                        </span>
                    </div>
                </div>
                <div class="h-1 sm:h-1.5 rounded-full w-full transition-all"
                     :class="stepErrors[3] ? 'bg-rose-500' : (currentStep >= 3 ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500' : 'bg-emerald-500') : 'bg-slate-950')"></div>
            </button>

        </div>
    </div>
</div>
