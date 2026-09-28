{{-- ========================================================================= --}}
{{-- FLOATING GLASSMORPHISM BULK ACTION DOCK                                   --}}
{{-- ========================================================================= --}}
<div x-show="selectedIds.length > 0"
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="opacity-0 translate-y-12 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-12 scale-95"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 max-w-xl w-[92%] sm:w-auto"
     style="display: none;">
    <div class="bg-slate-900/95 backdrop-blur-xl border border-slate-700/80 rounded-2xl p-2.5 px-4 shadow-2xl shadow-black/80 flex items-center justify-between sm:justify-start gap-3.5 ring-1 ring-white/10">
        <!-- Counter Badge -->
        <div class="flex items-center gap-2 pr-3 border-r border-slate-800">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-bold text-white font-mono whitespace-nowrap">
                <span x-text="selectedIds.length" class="text-emerald-400 text-sm font-black"></span> item dipilih
            </span>
        </div>

        <!-- Tombol Aksi Massal -->
        <div class="flex items-center gap-2">
            <button type="button" @click="bulkRestore(currentTargetModule)"
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-slate-950 font-black text-xs transition-all shadow-lg shadow-emerald-500/20 active:scale-95 flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Pulihkan Terpilih</span>
            </button>

            <button type="button" @click="bulkForceDelete(currentTargetModule)"
                class="px-3.5 py-2 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 text-rose-300 font-bold text-xs transition-all shadow-sm active:scale-95 flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="hidden sm:inline">Musnahkan Permanen</span>
                <span class="sm:hidden">Hapus</span>
            </button>

            <!-- Tombol Batal Centang -->
            <button type="button" @click="selectedIds = []" title="Batalkan pilihan"
                class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors text-xs font-bold cursor-pointer">
                ✕
            </button>
        </div>
    </div>
</div>
