<!-- ========================================================================= -->
<!-- MODAL PEMBATALAN KERJA SAMA KEMITRAAN (STATUS DIBATALKAN)                 -->
<!-- SIMAT-RK Bespoke Modern Dark UI Standard                                  -->
<!-- ========================================================================= -->
<template x-teleport="body">
<div x-show="showModalBatal" 
     x-cloak
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop Dark Glassmorphism -->
    <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity" 
         @click="if(!isSubmittingBatal) closeModalBatalKemitraan()"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="showModalBatal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-rose-500/30 shadow-2xl shadow-rose-950/50 text-left transition-all w-full max-w-lg my-auto p-5 sm:p-6 space-y-4">

            <!-- Header Modal -->
            <div class="flex items-start justify-between gap-3 border-b border-slate-800 pb-3.5">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        <span>🚫</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold tracking-widest uppercase font-mono text-rose-400 block mb-0.5">
                            Penghentian Kerja Sama / PKS
                        </span>
                        <h3 class="text-base font-black text-white">
                            Batalkan Status Kemitraan
                        </h3>
                    </div>
                </div>

                <button type="button" 
                        @click="if(!isSubmittingBatal) closeModalBatalKemitraan()" 
                        class="w-8 h-8 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white hover:bg-rose-500/20 hover:border-rose-500/30 border border-slate-700/60 flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Form Input Alasan Pembatalan -->
            <form @submit.prevent="submitBatalKemitraan()" class="space-y-4">
                <!-- Info Ringkas Aset yang Dibatalkan -->
                <div class="p-3.5 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-2 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-slate-400 font-semibold text-[10.5px]">Objek Kemitraan:</span>
                        <span class="font-bold text-white max-w-[240px] truncate" x-text="batalData.nama_barang || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-slate-400 font-semibold text-[10.5px]">Nomor PKS:</span>
                        <span class="font-mono text-cyan-300 font-bold" x-text="batalData.nomor_pks || '-'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-semibold text-[10.5px]">Mitra Rekanan:</span>
                        <span class="text-slate-200 font-semibold" x-text="batalData.mitra_nama || '-'"></span>
                    </div>
                </div>

                <!-- Konsekuensi Kontekstual -->
                <template x-if="batalData.is_dimanfaatkan">
                    <div class="p-3 rounded-xl bg-cyan-950/30 border border-cyan-500/30 text-[11px] text-cyan-200/90 leading-relaxed flex items-start gap-2">
                        <span class="text-base shrink-0">🏛️</span>
                        <p>
                            <strong>Aset BMD RSUD:</strong> Status operasional aset ini akan langsung dikembalikan menjadi <strong>Tersedia</strong> pada KIB sebelumnya. Riwayat kerja sama dan pembatalan tetap tersimpan secara permanen.
                        </p>
                    </div>
                </template>
                <template x-if="!batalData.is_dimanfaatkan">
                    <div class="p-3 rounded-xl bg-amber-950/30 border border-amber-500/30 text-[11px] text-amber-200/90 leading-relaxed flex items-start gap-2">
                        <span class="text-base shrink-0">📦</span>
                        <p>
                            <strong>Aset Ditambahkan Mitra:</strong> Status aset akan menjadi <strong>Dibatalkan</strong> (terkunci dari reklasifikasi atau pengubahan). Aset hanya dapat dilihat detail riwayatnya atau dihapus ke tempat sampah.
                        </p>
                    </div>
                </template>

                <!-- Input Alasan Pembatalan (Kenapa Kok Dibatalkan) -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                        <span>Alasan / Keterangan Pembatalan <span class="text-rose-400">*</span></span>
                        <span class="text-[9.5px] font-normal text-slate-400">Wajib diisi</span>
                    </label>
                    <textarea x-model="batalForm.alasan_batal" rows="3" required
                              placeholder="Jelaskan alasan kenapa kerja sama ini dibatalkan (misal: Rekanan wanprestasi, pemutusan kontrak sepihak sebelum masa konsesi berakhir, dll)..."
                              class="w-full bg-slate-950 border border-slate-700/80 focus:border-rose-400 focus:ring-1 focus:ring-rose-400/30 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none transition-all resize-none leading-relaxed"></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                    <button type="button" @click="closeModalBatalKemitraan()"
                            class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-all active:scale-95 cursor-pointer border border-slate-700/70">
                        Batal
                    </button>
                    <button type="button" @click="submitBatalKemitraan()" 
                            :disabled="isSubmittingBatal || !(batalForm.alasan_batal && batalForm.alasan_batal.trim())"
                            style="background: linear-gradient(135deg, #e11d48, #be123c) !important;"
                            class="px-5 py-2.5 rounded-xl text-white font-black text-xs shadow-lg shadow-rose-600/40 hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer border border-rose-400/40">
                        <template x-if="!isSubmittingBatal">
                            <span class="flex items-center gap-1.5">
                                <span class="text-sm">🚫</span>
                                <span>Ya, Batalkan Kemitraan</span>
                            </span>
                        </template>
                        <template x-if="isSubmittingBatal">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memproses Pembatalan...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
</template>
