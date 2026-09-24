<!-- ========================================================================= -->
<!-- BOTTOM NAVIGATION BUTTONS                                                 -->
<!-- ========================================================================= -->
<div class="pt-6 border-t border-slate-800 flex items-center justify-between">
    <div>
        <button type="button" x-show="step > 1" @click="prevStep()"
            class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-all flex items-center space-x-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span>Kembali</span>
        </button>
    </div>

    <div class="flex items-center space-x-3">
        <a href="{{ $backUrl }}"
            class="px-4 py-2.5 rounded-xl text-slate-400 hover:text-rose-400 font-bold text-xs transition-colors">
            Batal
        </a>

        <!-- Next Step Button -->
        <button type="button" x-show="step < 3" @click="nextStep()"
            class="px-6 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-white font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-2 cursor-pointer">
            <span>Lanjutkan</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <!-- Final Submit Button (Langkah 3) -->
        <button type="submit" x-show="step === 3" :disabled="isSubmitting"
            class="px-8 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-slate-950 font-black text-xs shadow-xl shadow-emerald-500/25 transition-all flex items-center space-x-2 cursor-pointer">
            <span x-show="!isSubmitting" x-text="isEdit ? '💾 Simpan Perubahan Mutasi Eksternal' : '💾 Simpan Mutasi Eksternal'">💾 Simpan Mutasi Eksternal</span>
            <span x-show="isSubmitting">Menyimpan...</span>
        </button>
    </div>
</div>
