<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI PENGAKTIFAN KEMBALI (REAKTIVASI) MUTASI EKSTERNAL       -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showReaktivasiModal" x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="if (!isReactivating) showReaktivasiModal = false"
             x-show="showReaktivasiModal"
             x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-slate-900 border border-emerald-500/40 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden my-auto">

            <!-- Subtle Top Accent Strip -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>

            <!-- Header Icon & Title -->
            <div class="flex items-start space-x-3.5 pt-1">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border border-emerald-500/30 bg-emerald-500/20 text-emerald-400 shadow-inner">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div class="space-y-1 min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight">Konfirmasi Pengaktifan Kembali Mutasi</h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Apakah Anda yakin ingin membatalkan status pembatalan dokumen ini? Status dokumen akan dipulihkan menjadi <strong class="text-emerald-400 font-bold">Disahkan (Selesai)</strong> dan data inventaris terkait akan diaktifkan kembali.
                    </p>
                </div>
            </div>

            <!-- Item Target Box -->
            <template x-if="itemToReaktivasi">
                <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">DOKUMEN SASARAN:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="itemToReaktivasi.tipe === 'keluar' ? 'bg-cyan-950/70 border border-cyan-800/60 text-cyan-300' : 'bg-indigo-950/70 border border-indigo-800/60 text-indigo-300'"
                              x-text="itemToReaktivasi.tipe === 'keluar' ? '📤 Mutasi Keluar OPD' : '📥 Mutasi Masuk OPD'"></span>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-xs sm:text-sm font-bold text-white truncate font-mono"
                           x-text="(itemToReaktivasi.nama_barang || itemToReaktivasi.nama || 'Aset') + (itemToReaktivasi.kode_108 ? ' (' + itemToReaktivasi.kode_108 + ')' : '')"></p>
                        <div class="flex items-center space-x-2 text-[11px] text-slate-400 font-mono">
                            <span x-text="'No. BAST: ' + (itemToReaktivasi.kode || itemToReaktivasi.mutasi_nomor_bamb || itemToReaktivasi.bast_nomor || '-')"></span>
                            <span>•</span>
                            <span class="text-emerald-400 font-bold" x-text="itemToReaktivasi.nilai_perolehan_formatted || formatRupiah(itemToReaktivasi.nilai_perolehan)"></span>
                        </div>
                    </div>
                    <template x-if="itemToReaktivasi.tipe === 'keluar'">
                        <div class="p-2 rounded-xl bg-cyan-950/40 border border-cyan-800/40 text-[10.5px] text-cyan-300">
                            ℹ️ Seluruh register unit terkait akan kembali tercatat sebagai <strong>Mutasi Keluar OPD</strong> menuju instansi penerima.
                        </div>
                    </template>
                    <template x-if="itemToReaktivasi.tipe !== 'keluar'">
                        <div class="p-2 rounded-xl bg-emerald-950/40 border border-emerald-800/40 text-[10.5px] text-emerald-300">
                            ℹ️ Data aset pelimpahan OPD ini akan otomatis <strong>dipulihkan kembali ke inventaris Master ASTAP RSUD</strong> dan register NIBAR aktif kembali.
                        </div>
                    </template>
                </div>
            </template>

            <!-- Textarea Input Alasan Pengaktifan Kembali -->
            <div class="space-y-1.5">
                <label for="reaktivasiAlasanInput" class="block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                    Alasan Pengaktifan Kembali (Reaktivasi) <span class="text-emerald-400">*</span>
                </label>
                <textarea id="reaktivasiAlasanInput"
                          x-model="reaktivasiAlasan"
                          rows="3"
                          placeholder="Tuliskan alasan pengaktifan kembali dokumen transaksi mutasi eksternal ini secara detail..."
                          class="w-full bg-slate-950 border border-slate-700/80 rounded-2xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/40 transition-all font-sans resize-none"></textarea>
                <div class="flex items-center justify-between text-[10px] text-slate-500">
                    <span>Wajib diisi untuk catatan riwayat audit sistem.</span>
                    <span x-text="reaktivasiAlasan.length + '/1000'"></span>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-3 border-t border-slate-800/90 flex items-center justify-end space-x-2.5">
                <button type="button" @click="showReaktivasiModal = false" :disabled="isReactivating"
                    class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs sm:text-sm border border-slate-700 transition-all active:scale-95 cursor-pointer disabled:opacity-50">
                    Batal
                </button>
                <button type="button" @click="executeReaktivasi()" :disabled="isReactivating || !reaktivasiAlasan.trim()"
                    class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm shadow-lg shadow-emerald-600/30 transition-all active:scale-95 cursor-pointer flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-500 text-white border border-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!isReactivating" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <svg x-show="isReactivating" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-show="!isReactivating">Ya, Aktifkan Kembali Transaksi</span>
                    <span x-show="isReactivating">Mengaktifkan...</span>
                </button>
            </div>
        </div>
    </div>
</template>

