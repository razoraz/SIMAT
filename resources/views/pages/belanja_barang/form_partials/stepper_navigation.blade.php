<!-- ========================================================================= -->
<!-- STEPPER BOTTOM NAVIGATION CONTROLS                                         -->
<!-- ========================================================================= -->
<div class="border-t border-slate-800 pt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <button type="button" x-show="step > 1" @click="goToStep(step - 1)"
            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
            <span>← Sebelumnya</span>
        </button>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" x-show="step < 3" @click="goToStep(step + 1)"
            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
            <span>Lanjutkan (Langkah <span x-text="step + 1"></span>) →</span>
        </button>

        <button type="submit" x-show="step === 3" :disabled="isSubmitting"
            class="w-full sm:w-auto px-8 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white text-xs font-extrabold shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer disabled:opacity-50 active:scale-95">
            <span x-show="!isSubmitting">💾 Simpan Belanja Barang</span>
            <span x-show="isSubmitting" class="flex items-center gap-1.5">
                <svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan Data...
            </span>
        </button>
    </div>
</div>
