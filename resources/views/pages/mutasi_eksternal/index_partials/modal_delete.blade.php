<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI HAPUS / SOFT-DELETE MUTASI EKSTERNAL (PELIMPAHAN SKPD)   -->
<!-- ========================================================================= -->
<div x-show="showDeleteModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 modal-backdrop-full"
     style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(32px); -webkit-backdrop-filter: blur(32px); z-index: 60;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div @click.away="if (!isDeleting) showDeleteModal = false"
         class="w-full max-w-md bg-slate-900 border border-rose-500/30 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-4 text-center relative overflow-hidden my-auto"
         style="background-color: #0f172a;">

        <!-- Subtle Top Accent Strip -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-500 via-rose-500 to-rose-600"></div>

        <!-- Icon Warning Glow -->
        <div class="w-14 h-14 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center text-2xl mx-auto shadow-lg shadow-rose-500/20">
            🗑️
        </div>

        <div>
            <h3 class="text-base font-extrabold text-white">
                Pindahkan ke Recycle Bin?
            </h3>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                Anda akan menonaktifkan data pelimpahan aset SKPD berikut:
            </p>

            <!-- Card Info Aset yang Akan Dihapus -->
            <template x-if="itemToDelete">
                <div class="mt-3 p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-left space-y-1.5 shadow-inner">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Barang / Aset:</span>
                        <span class="px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30" x-text="itemToDelete.category || 'BMD'"></span>
                    </div>
                    <p class="text-xs font-black text-white truncate" x-text="itemToDelete.nama_barang || itemToDelete.nama || 'Aset'"></p>
                    
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800/80 text-[11px]">
                        <div>
                            <span class="text-[9.5px] text-slate-500 block uppercase">Asal SKPD:</span>
                            <span class="text-slate-300 font-semibold truncate block" x-text="itemToDelete.opd_asal || '-'"></span>
                        </div>
                        <div>
                            <span class="text-[9.5px] text-slate-500 block uppercase">Nilai Perolehan:</span>
                            <span class="font-mono font-extrabold text-emerald-400 block" x-text="itemToDelete.nilai_perolehan_formatted || itemToDelete.jumlah_realisasi || 'Rp 0'"></span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Input Alasan Penghapusan (Opsional) -->
            <div class="mt-3 text-left">
                <label class="block text-[10.5px] font-bold text-slate-300 mb-1 flex items-center justify-between">
                    <span>Alasan Penghapusan:</span>
                    <span class="text-[9.5px] text-slate-500 font-normal">Opsional</span>
                </label>
                <input type="text" x-model="deleteAlasan" placeholder="Misal: Salah input data, duplikasi, revisi BAST..."
                    class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none transition-colors shadow-inner">
            </div>

            <!-- Catatan Soft Delete -->
            <div class="text-[11px] text-cyan-300/90 mt-3.5 bg-cyan-950/30 p-2.5 rounded-xl border border-cyan-500/20 text-left flex items-start gap-2">
                <span class="text-base leading-none shrink-0">♻️</span>
                <span class="leading-relaxed"><strong>Sistem Soft Delete:</strong> Data pelimpahan dan nomor register NIBAR terkait akan diarsipkan ke <strong>Tong Sampah (Recycle Bin)</strong> dan dapat dipulihkan kembali sewaktu-waktu.</span>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-center gap-3 pt-2">
            <button type="button" @click="showDeleteModal = false" :disabled="isDeleting"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all disabled:opacity-50 cursor-pointer">
                Batal
            </button>
            <button type="button" @click="executeDelete()" :disabled="isDeleting"
                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-extrabold shadow-lg shadow-rose-600/30 transition-all flex items-center gap-2 disabled:opacity-50 cursor-pointer">
                <svg x-show="isDeleting" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-show="!isDeleting">Ya, Pindahkan ke Sampah</span>
                <span x-show="isDeleting">Menghapus...</span>
            </button>
        </div>
    </div>
</div>
