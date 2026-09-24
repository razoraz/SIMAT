<!-- ========================================================================= -->
<!-- STEPPER NAVIGATION CONTROLS (KEMITRAAN PIHAK KETIGA 1.5.2)                -->
<!-- ========================================================================= -->
<div class="pt-6 sm:pt-8 mt-6 sm:mt-8 border-t border-slate-800 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
    <div class="w-full sm:w-auto">
        <button type="button" x-show="currentStep > 1" @click="prevStep()"
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all flex items-center justify-center space-x-2 cursor-pointer">
            <span>&larr; Langkah Sebelumnya</span>
        </button>
    </div>

    <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
        <a href="{{ route('astap.pilih_jenis') }}" class="flex-1 sm:flex-initial text-center px-4 sm:px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
            Batal
        </a>

        <!-- Next Step Button -->
        <button type="button" x-show="currentStep < 3" @click="nextStep()"
                class="flex-1 sm:flex-initial px-5 sm:px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center justify-center space-x-2 cursor-pointer">
            <span>Lanjut Langkah <span x-text="currentStep + 1"></span> &rarr;</span>
        </button>

        <!-- Submit Button -->
        <button type="button" x-show="currentStep === 3" @click="submitForm()" :disabled="isSubmitting"
                class="flex-1 sm:flex-initial px-6 sm:px-8 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-extrabold text-xs shadow-xl shadow-cyan-500/30 transition-all flex items-center justify-center space-x-2 active:scale-95 disabled:opacity-50 cursor-pointer">
            <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Aset Kemitraan'"></span>
        </button>
    </div>
</div>
