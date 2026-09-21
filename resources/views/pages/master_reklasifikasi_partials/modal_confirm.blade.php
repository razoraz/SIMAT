<!-- MODAL KONFIRMASI HAPUS REKLASIFIKASI (Sleek Dark Theme) -->
<div x-show="showConfirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
    <div @click.away="showConfirmDelete = false"
         x-show="showConfirmDelete"
         x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-slate-900 border border-rose-500/40 rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden">
        
        <!-- Subtle Top Accent Strip -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-500 to-red-600"></div>

        <!-- Header Icon & Title -->
        <div class="flex items-start space-x-3.5 pt-1">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border border-rose-500/30 bg-rose-500/20 text-rose-400 shadow-inner">
                <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="space-y-1 min-w-0 flex-1">
                <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight">Konfirmasi Pindahkan ke Tong Sampah</h3>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Apakah Anda yakin ingin membatalkan transaksi reklasifikasi ini? Nilai mutasi akan dikembalikan ke saldo asalnya.
                </p>
            </div>
        </div>

        <!-- Item Target Preview Card -->
        <template x-if="deleteTargetName">
            <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800/90 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Item Target:</span>
                <p class="text-xs sm:text-sm font-bold text-cyan-300 truncate font-mono" x-text="deleteTargetName"></p>
            </div>
        </template>

        <!-- Footer Action Buttons (Persis sesuai foto referensi) -->
        <div class="pt-3 border-t border-slate-800/90 flex items-center justify-end space-x-2.5">
            <button type="button" @click="showConfirmDelete = false"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs sm:text-sm border border-slate-700 transition-all active:scale-95 cursor-pointer">
                Batal
            </button>
            <button type="button" @click="executeHapusReklas()"
                class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-2 bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30 border border-rose-500/30">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Pindahkan ke Tong Sampah</span>
            </button>
        </div>
    </div>
</div>
