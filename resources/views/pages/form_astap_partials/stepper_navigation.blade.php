            <!-- Bottom Navigation Between Steps -->
            <div class="pt-6 sm:pt-8 mt-6 sm:mt-8 border-t border-slate-800 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                <div class="w-full sm:w-auto">
                    <button type="button" x-show="currentStep > 1" @click="prevStep()"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all flex items-center justify-center space-x-2">
                        <span>&larr; Langkah Sebelumnya</span>
                    </button>
                </div>

                    @php
                        $cancelUrl = request('from') === 'eksternal' ? route('mutasi.eksternal') : route('astap.index');
                    @endphp
                    <a href="{{ $cancelUrl }}" class="flex-1 sm:flex-initial text-center px-4 sm:px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                        Batal
                    </a>

                    <!-- Next Step Button -->
                    <button type="button" x-show="currentStep < totalSteps" @click="nextStep()"
                            class="flex-1 sm:flex-initial px-5 sm:px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2">
                        <span>Lanjut Langkah <span x-text="currentStep + 1"></span> &rarr;</span>
                    </button>

                    <!-- Submit Button -->
                    <button type="button" x-show="currentStep === totalSteps" @click="submitForm()"
                            class="flex-1 sm:flex-initial px-5 sm:px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center space-x-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Data ASTAP Lengkap'"></span>
                    </button>
                </div>
            </div>
