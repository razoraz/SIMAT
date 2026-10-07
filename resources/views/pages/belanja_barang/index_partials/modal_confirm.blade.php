<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI HAPUS BELANJA BARANG (Sesuai Referensi Gambar 1)         -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="confirmDeleteModalOpen" x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="if (!isDeleting) closeConfirmDelete()"
             x-show="confirmDeleteModalOpen"
             x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-slate-900 border border-rose-500/40 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden my-auto">

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
                        Apakah Anda yakin ingin memindahkan data belanja barang ini ke Recycle Bin (Tong Sampah)? Seluruh unit register NIBAR terkait juga akan dipindahkan ke Recycle Bin.
                    </p>
                </div>
            </div>

            <!-- Item Target Box -->
            <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">ITEM TARGET:</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-950/70 border border-rose-800/60 text-rose-300">
                        BELANJA BARANG (5.1.02)
                    </span>
                </div>
                <p class="text-xs sm:text-sm font-bold text-cyan-300 truncate font-mono" x-text="deleteTitle"></p>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-3 border-t border-slate-800/90 flex items-center justify-end space-x-2.5">
                <button type="button" @click="closeConfirmDelete()" :disabled="isDeleting"
                    class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs sm:text-sm border border-slate-700 transition-all active:scale-95 cursor-pointer disabled:opacity-50">
                    Batal
                </button>
                <button type="button" @click="executeDelete()" :disabled="isDeleting"
                    class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-lg shadow-rose-600/30 transition-all active:scale-95 cursor-pointer flex items-center space-x-2 bg-rose-600 hover:bg-rose-500 text-white border border-rose-500/30 disabled:opacity-50">
                    <svg x-show="!isDeleting" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!isDeleting">Pindahkan ke Tong Sampah</span>
                    <span x-show="isDeleting">Memindahkan...</span>
                </button>
            </div>
        </div>
    </div>
</template>
