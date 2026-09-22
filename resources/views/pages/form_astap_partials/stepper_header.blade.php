        <!-- Top Navigation Bar (Back + Title) -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-3 sm:space-x-4 w-full sm:w-auto">
                <a href="{{ route('astap.index') }}" 
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center space-x-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[9px] sm:text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ MODE EDIT DATA ASTAP' : '📝 FORM PENAMBAHAN DATA ASTAP'"></span>
                    </div>
                    <h1 class="text-base sm:text-xl md:text-2xl font-extrabold text-white tracking-tight truncate" x-text="isEdit ? 'Ubah Data ASTAP: ' + (isTanah ? formData.tanah_nama_barang : (isMesin ? formData.mesin_nama_barang : (isGedung ? formData.gedung_nama_barang : (isJaringan ? formData.jaringan_nama_barang : (isAsetLainnya ? formData.lainnya_nama_barang : (isAtb ? formData.atb_nama_barang : (isKdp ? formData.kdp_nama_barang : 'Aset Tetap'))))))) : 'Input Penambahan Aset Tetap (ASTAP)'"></h1>
                </div>
            </div>
        </div>

        <!-- Multi-Step Stepper Header (1 s/d 4) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="currentStep === 1 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 1 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 1">1</span>
                            <span x-show="currentStep > 1">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 1 ? 'text-emerald-400' : 'text-slate-500'">Langkah 1</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Jenis Pengadaan</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 1 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab (Rekening Belanja & Jenis ASTAP PMDN 108) -->
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="currentStep === 2 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 2 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 2">2</span>
                            <span x-show="currentStep > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 2 ? 'text-emerald-400' : 'text-slate-500'">Langkah 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Rekening & Jenis 108</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 2 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab (Dokumen Pembelian / Rincian Belanja Modal) -->
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="currentStep === 3 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 3 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 3">3</span>
                            <span x-show="currentStep > 3">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 3 ? 'text-emerald-400' : 'text-slate-500'">Langkah 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="isTanah ? 'Rincian Tanah' : (isMesin ? 'Rincian Mesin' : (isGedung ? 'Rincian Gedung' : (isJaringan ? 'Rincian Jaringan' : (isAsetLainnya ? 'Rincian Lainnya' : (isAtb ? 'Rincian ATB' : (isKdp ? 'Rincian KDP' : 'Rincian Aset'))))))"></span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 3 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 4 Tab (Penyedia & PPK) -->
                <button type="button" @click="goToStep(4)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="currentStep === 4 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>4</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 4 ? 'text-emerald-400' : 'text-slate-500'">Langkah 4</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Penyedia, PPK & Ket.</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 4 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>
