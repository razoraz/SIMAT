<!-- MODAL KONFIRMASI HAPUS REKLASIFIKASI -->
<div x-show="showConfirmDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showConfirmDelete = false"></div>

    <div class="relative w-full max-w-md bg-slate-900 border border-slate-700 rounded-2xl p-6 shadow-2xl z-10 text-center">
        <div class="w-12 h-12 rounded-full bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 mx-auto mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h3 class="text-base font-bold text-white mb-2">Batalkan Transaksi Reklasifikasi?</h3>
        <p class="text-xs text-slate-400 leading-relaxed mb-6">
            Apakah Anda yakin ingin membatalkan transaksi reklasifikasi untuk <span class="font-bold text-slate-200" x-text="deleteTargetName"></span>? Nilai mutasi akan dikembalikan ke saldo asalnya.
        </p>

        <div class="flex items-center justify-center gap-3">
            <button type="button" @click="showConfirmDelete = false"
                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
                Batal
            </button>
            <button type="button" @click="executeHapusReklas()"
                class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition-all">
                Ya, Hapus Transaksi
            </button>
        </div>
    </div>
</div>
