        {{-- ===== MULTI-STEP STEPPER HEADER (TEMA MERAH ROSE MUTASI) ===== --}}
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="step === 1 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 1 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 1">1</span>
                            <span x-show="step > 1">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 1 ? 'text-rose-400 font-black' : (step > 1 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 1</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Jenis Pengajuan</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 1 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab (Unit Pengirim & Penerima) -->
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="step === 2 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 2 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 2">2</span>
                            <span x-show="step > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-rose-400 font-black' : (step > 2 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Unit Pengirim & Penerima</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab (Pilih Barang Aset Multi & Alasan) -->
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="step === 3 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : (step > 3 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 3">3</span>
                            <span x-show="step > 3">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-rose-400 font-black' : (step > 3 ? 'text-rose-400' : 'text-slate-500')">LANGKAH 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Pilih Aset & Alasan</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 3 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 4 Tab -->
                <button type="button" @click="goToStep(4)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;"
                             :class="step === 4 ? 'bg-rose-500 text-slate-950 shadow-lg shadow-rose-500/30 font-black' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>4</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 4 ? 'text-rose-400 font-black' : 'text-slate-500'">LANGKAH 4</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Review & BAMB</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 4 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>

        @if(isset($errors) && $errors->any())
        <div class="bg-rose-500/10 border border-rose-500/30 rounded-2xl px-5 py-3 text-rose-300 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif
