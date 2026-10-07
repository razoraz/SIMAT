<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI HIBAH KELUAR & HAPUS TRANSAKSI (HIGH Z-INDEX z-index: 99999) -->
<!-- ========================================================================= -->

<!-- 1. MODAL KONFIRMASI PROSES HIBAH KELUAR -->
<div x-show="showConfirmKeluarModal" x-cloak
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.92); backdrop-filter: blur(16px);">
    
    <div @click.away="if (!isSubmittingKeluar) { showConfirmKeluarModal = false; showModalHibahKeluar = true; }"
         x-show="showConfirmKeluarModal"
         x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-slate-900 border border-rose-500/50 rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 relative overflow-hidden my-auto">
        
        <!-- Top Gradient Accent Strip -->
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-rose-500 via-pink-500 to-amber-500"></div>

        <!-- Header Icon & Title -->
        <div class="flex items-start justify-between gap-3 pt-1">
            <div class="flex items-start space-x-3.5">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border border-rose-500/30 bg-rose-500/20 text-rose-400 shadow-inner text-xl">
                    📤
                </div>
                <div class="space-y-1 min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight">
                        Konfirmasi Penyerahan Hibah Keluar?
                    </h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                        Pastikan seluruh data penyerahan hibah di bawah ini telah sesuai sebelum disimpan ke sistem.
                    </p>
                </div>
            </div>
            <button type="button" @click="showConfirmKeluarModal = false; showModalHibahKeluar = true;" :disabled="isSubmittingKeluar"
                class="w-8 h-8 rounded-full bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer shrink-0">
                ✕
            </button>
        </div>

        <!-- Ringkasan Target Hibah Keluar -->
        <div class="p-4 bg-slate-950/90 rounded-2xl border border-slate-800 space-y-2 text-xs">
            <!-- Nama Barang -->
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Barang Yang Dihibahkan:</span>
                <p class="font-extrabold text-white text-sm" x-text="selectedAstapForKeluar?.nama_barang || '-'"></p>
                <p class="font-mono text-[10px] text-cyan-400" x-text="selectedAstapForKeluar?.kode_barang || '-'"></p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800/80">
                <div>
                    <span class="text-[10px] text-slate-400 block">Penerima Hibah:</span>
                    <strong class="text-rose-300 font-bold" x-text="keluarData.penerima_hibah"></strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block">Volume Dihibahkan:</span>
                    <strong class="text-teal-300 font-mono font-bold" x-text="(keluarData.register_ids.length > 0 ? keluarData.register_ids.length : 1) + ' ' + (selectedAstapForKeluar?.satuan || 'Unit')"></strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block">Nomor BAST:</span>
                    <span class="font-mono text-slate-200" x-text="keluarData.nomor_bast"></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block">Total Nilai Aset:</span>
                    <strong class="font-mono text-amber-300 font-black" x-text="formatRupiah(keluarData.nilai_aset)"></strong>
                </div>
            </div>
        </div>

        <!-- Peringatan Efek Transaksi -->
        <div class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-[11px] text-amber-300/90 flex items-start space-x-2">
            <span class="text-sm">⚠️</span>
            <p class="leading-relaxed">
                Unit barang yang dihibahkan akan otomatis dialihkan statusnya menjadi <strong class="text-white">"Dihibahkan"</strong> dan dicatat ke dalam <strong class="text-white">Daftar Pengurangan Aset Tetap (Sheet 2 Sipenerbang)</strong>.
            </p>
        </div>

        <!-- Footer Action Buttons -->
        <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-3">
            <button type="button" @click="showConfirmKeluarModal = false; showModalHibahKeluar = true;" :disabled="isSubmittingKeluar"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95 cursor-pointer disabled:opacity-50">
                ← Kembali Edit
            </button>
            <button type="button" @click="executeSubmitHibahKeluar()" :disabled="isSubmittingKeluar"
                class="px-5 py-2.5 rounded-xl font-black text-xs shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-2 bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30 border border-rose-500/40 disabled:opacity-50">
                <span x-show="!isSubmittingKeluar">✓ Ya, Proses Simpan Hibah</span>
                <span x-show="isSubmittingKeluar">Memproses...</span>
            </button>
        </div>
    </div>
</div>


<!-- 2. MODAL KONFIRMASI HAPUS / BATALKAN TRANSAKSI HIBAH -->
<div x-show="showConfirmDeleteModal" x-cloak
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.92); backdrop-filter: blur(16px);">
    
    <div @click.away="if (!isDeleting) showConfirmDeleteModal = false"
         x-show="showConfirmDeleteModal"
         x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-slate-900 border border-rose-500/50 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative overflow-hidden my-auto">
        
        <!-- Top Gradient Accent Strip -->
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-red-600 via-rose-500 to-orange-500"></div>

        <!-- Header Icon & Title -->
        <div class="flex items-start justify-between gap-3 pt-1">
            <div class="flex items-start space-x-3.5">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border border-rose-500/30 bg-rose-500/20 text-rose-400 shadow-inner">
                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="space-y-1 min-w-0 flex-1">
                    <h3 class="text-base sm:text-lg font-black text-white leading-snug tracking-tight">
                        Pindahkan ke Pusat Data Terhapus?
                    </h3>
                    <p class="text-slate-300 text-xs leading-relaxed">
                        Catatan hibah akan dipindahkan ke <strong class="text-amber-300">Pusat Data Terhapus (Recycle Bin)</strong>. Jika ini hibah keluar, unit barang akan otomatis dipulihkan ke inventaris aktif dan data transaksi dapat dipulihkan sewaktu-waktu.
                    </p>
                </div>
            </div>
            <button type="button" @click="showConfirmDeleteModal = false" :disabled="isDeleting"
                class="w-8 h-8 rounded-full bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer shrink-0">
                ✕
            </button>
        </div>

        <!-- Item Target Info -->
        <template x-if="itemToDelete">
            <div class="p-3.5 bg-slate-950/90 rounded-2xl border border-slate-800 space-y-1 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Jenis Transaksi:</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black"
                        :class="itemToDelete.tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'"
                        x-text="itemToDelete.tipe_hibah === 'masuk' ? '🎁 HIBAH MASUK' : '📤 HIBAH KELUAR'"></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400">Nama Barang:</span>
                    <p class="font-bold text-white text-xs truncate" x-text="itemToDelete.astap?.nama_barang || itemToDelete.nama_barang || '-'"></p>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                    <span class="text-[10px] text-slate-400">Nomor BAST:</span>
                    <span class="font-mono text-cyan-300" x-text="itemToDelete.nomor_bast"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-slate-400">Nilai Aset:</span>
                    <strong class="font-mono text-amber-300" x-text="formatRupiah(itemToDelete.nilai_aset)"></strong>
                </div>
            </div>
        </template>

        <!-- Footer Action Buttons -->
        <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
            <button type="button" @click="showConfirmDeleteModal = false" :disabled="isDeleting"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition-all active:scale-95 cursor-pointer disabled:opacity-50">
                Batal
            </button>
            <button type="button" @click="executeDeleteHibah()" :disabled="isDeleting"
                class="px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-2 bg-rose-600 hover:bg-rose-500 text-white shadow-rose-600/30 border border-rose-500/30 disabled:opacity-50">
                <span x-show="!isDeleting">Ya, Hapus Data</span>
                <span x-show="isDeleting">Menghapus...</span>
            </button>
        </div>
    </div>
</div>
