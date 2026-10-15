<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI PEMBATALAN TRANSAKSI MUTASI EKSTERNAL                   -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showBatalModal" x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="if (!isCancelling) showBatalModal = false"
             x-show="showBatalModal"
             x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-slate-900 border border-rose-500/40 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden my-auto">

            <!-- Subtle Top Accent Strip -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-500 via-rose-600 to-amber-500"></div>

            <!-- Header Icon & Title -->
            <div class="flex items-start space-x-3.5 pt-1">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border border-rose-500/30 bg-rose-500/20 text-rose-400 shadow-inner">
                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="space-y-1 min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight">Konfirmasi Pembatalan Mutasi Eksternal</h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Apakah Anda yakin ingin membatalkan transaksi dokumen ini? Status dokumen akan menjadi <strong class="text-rose-400 font-bold">Dibatalkan</strong> dan status inventaris register terkait akan dipulihkan kembali.
                    </p>
                </div>
            </div>

            <!-- Item Target Box -->
            <template x-if="itemToBatal">
                <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">DOKUMEN SASARAN:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="itemToBatal.tipe === 'keluar' ? 'bg-cyan-950/70 border border-cyan-800/60 text-cyan-300' : 'bg-indigo-950/70 border border-indigo-800/60 text-indigo-300'"
                              x-text="itemToBatal.tipe === 'keluar' ? '📤 Mutasi Keluar OPD' : '📥 Mutasi Masuk OPD'"></span>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-xs sm:text-sm font-bold text-white truncate font-mono"
                           x-text="(itemToBatal.nama_barang || itemToBatal.nama || 'Aset') + (itemToBatal.kode_108 ? ' (' + itemToBatal.kode_108 + ')' : '')"></p>
                        <div class="flex items-center space-x-2 text-[11px] text-slate-400 font-mono">
                            <span x-text="'No. BAST: ' + (itemToBatal.kode || itemToBatal.mutasi_nomor_bamb || '-')"></span>
                            <span>•</span>
                            <span class="text-emerald-400 font-bold" x-text="itemToBatal.nilai_perolehan_formatted || formatRupiah(itemToBatal.nilai_perolehan)"></span>
                        </div>
                    </div>
                    <template x-if="itemToBatal.tipe === 'keluar'">
                        <div class="p-2 rounded-xl bg-cyan-950/40 border border-cyan-800/40 text-[10.5px] text-cyan-300">
                            ℹ️ Seluruh register unit terkait yang berstatus <strong>Mutasi Keluar OPD</strong> akan otomatis dikembalikan ke inventaris aktif RSUD (Tersedia / Ditempatkan).
                        </div>
                    </template>
                </div>
            </template>

            <!-- Textarea Input Alasan Pembatalan -->
            <div class="space-y-1.5">
                <label for="batalAlasanInput" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                    Alasan Pembatalan <span class="text-rose-400">*</span>
                </label>
                <textarea id="batalAlasanInput"
                          x-model="batalAlasan"
                          rows="3"
                          placeholder="Tuliskan alasan pembatalan dokumen mutasi eksternal ini secara detail..."
                          class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500/40 transition-all font-sans resize-none"></textarea>
                <div class="flex items-center justify-between text-[10px] text-slate-500">
                    <span>Wajib diisi untuk catatan riwayat audit sistem.</span>
                    <span x-text="batalAlasan.length + '/1000'"></span>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-3 border-t border-slate-800/90 flex items-center justify-end space-x-2.5">
                <button type="button" @click="showBatalModal = false" :disabled="isCancelling"
                    class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs sm:text-sm border border-slate-700 transition-all active:scale-95 cursor-pointer disabled:opacity-50">
                    Batal
                </button>
                <button type="button" @click="executeBatal()" :disabled="isCancelling || !batalAlasan.trim()"
                    class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-lg shadow-rose-600/30 transition-all active:scale-95 cursor-pointer flex items-center space-x-2 bg-rose-600 hover:bg-rose-500 text-white border border-rose-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!isCancelling" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="isCancelling" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!isCancelling">Ya, Batalkan Transaksi</span>
                    <span x-show="isCancelling">Membatalkan...</span>
                </button>
            </div>
        </div>
    </div>
</template>
