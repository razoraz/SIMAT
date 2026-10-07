<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI HAPUS BELANJA BARANG                                     -->
<!-- ========================================================================= -->
<div x-show="confirmDeleteModalOpen" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop Blur Overlay -->
    <div x-show="confirmDeleteModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/85 backdrop-blur-2xl modal-backdrop-full transition-opacity"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div x-show="confirmDeleteModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.away="closeConfirmDelete()"
            class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6">
            
            <div class="flex items-center space-x-3.5 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-rose-400 text-xl shrink-0 shadow-lg shadow-rose-500/10">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white">
                        Pindahkan ke Pusat Pemulihan?
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Menonaktifkan transaksi belanja barang dan inventaris terkait.
                    </p>
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/80 mb-4 space-y-2">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Barang Belanja:</span>
                    <span class="text-sm font-bold text-teal-300 block mt-0.5" x-text="deleteTitle"></span>
                </div>
                
                <div class="pt-2 border-t border-slate-800/80">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Alasan Penghapusan (Opsional):
                    </label>
                    <input type="text" x-model="deleteReason" placeholder="Misal: Salah input faktur, dibatalkan, barang retur..."
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-colors">
                </div>

                <p class="text-[11px] text-teal-400/90 pt-1.5 flex items-start gap-1.5">
                    <span class="text-sm leading-none">♻️</span>
                    <span><strong>Sistem Soft Delete:</strong> Data faktur toko dan register NIBAR akan dipindahkan ke <strong>Pusat Pemulihan Data (Recycle Bin)</strong> dan dapat Anda pulihkan kembali sewaktu-waktu.</span>
                </p>
            </div>

            <div class="flex items-center justify-end space-x-2">
                <button type="button" @click="closeConfirmDelete()"
                    :disabled="isDeleting"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all disabled:opacity-50">
                    Batal
                </button>
                <button type="button" @click="executeDelete()"
                    :disabled="isDeleting"
                    class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/20 transition-all flex items-center gap-1.5 disabled:opacity-50">
                    <span x-show="!isDeleting">Ya, Pindahkan ke Sampah</span>
                    <span x-show="isDeleting" class="flex items-center gap-1.5">
                        <svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
