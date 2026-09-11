        <!-- FLOATING TOAST NOTIFICATION -->
        <div x-show="showToast" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed top-5 right-5 z-50 max-w-md bg-slate-900 border border-emerald-500/40 rounded-2xl p-4 shadow-2xl flex items-center space-x-3 text-xs text-white">
            <div class="p-2 rounded-xl bg-emerald-500/20 text-emerald-400 font-bold text-base shrink-0">
                ✓
            </div>
            <div class="flex-1">
                <p class="font-bold text-emerald-300">Pembaruan Kondisi Berhasil</p>
                <p class="text-slate-300 text-[11px] mt-0.5" x-text="toastMessage"></p>
            </div>
        </div>
