<!-- Stepper Navigation Bar (3 Langkah Bersih) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl">
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        
        <!-- Step 1 Tab -->
        <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                     :class="step === 1 ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : (step > 1 ? 'bg-purple-500/20 text-purple-400 border border-purple-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                    <span x-show="step <= 1">1</span>
                    <span x-show="step > 1">✓</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 1 ? 'text-purple-400' : 'text-slate-500'">Langkah 1</span>
                    <span class="text-[11px] sm:text-xs font-bold text-white block truncate">BAMB &amp; SKPD Pengirim</span>
                </div>
            </div>
            <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 1 ? 'bg-purple-500' : 'bg-slate-950'"></div>
        </button>

        <!-- Step 2 Tab -->
        <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                     :class="step === 2 ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : (step > 2 ? 'bg-purple-500/20 text-purple-400 border border-purple-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                    <span x-show="step <= 2">2</span>
                    <span x-show="step > 2">✓</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-purple-400' : 'text-slate-500'">Langkah 2</span>
                    <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Klasifikasi 108</span>
                </div>
            </div>
            <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-purple-500' : 'bg-slate-950'"></div>
        </button>

        <!-- Step 3 Tab (Rincian Teknis KIB & Penempatan Ruangan) -->
        <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                     :class="step === 3 ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                    <span>3</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-purple-400' : 'text-slate-500'">Langkah 3</span>
                    <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="(hasSelectedKib ? kibLabel : 'Spesifikasi') + ' & Penempatan'"></span>
                </div>
            </div>
            <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 3 ? 'bg-purple-500' : 'bg-slate-950'"></div>
        </button>

    </div>
</div>
