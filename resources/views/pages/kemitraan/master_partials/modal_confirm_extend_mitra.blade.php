<!-- ========================================================================= -->
<!-- MODAL KONFIRMASI EXTEND ASET MITRA (TABEL 2) SETELAH TABEL 1 DI-EXTEND    -->
<!-- ========================================================================= -->
<template x-teleport="body">
<div x-show="showModalConfirmExtendMitra" 
     x-cloak
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 10000;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop Dark Glassmorphism -->
    <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity" 
         @click="if(!isSubmittingExtendMitra) closeConfirmExtendMitraModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="showModalConfirmExtendMitra"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-emerald-500/40 text-left shadow-2xl transition-all w-full max-w-2xl my-8">

            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-emerald-950/70 via-slate-900 to-slate-950 p-6 border-b border-emerald-500/30 flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        📦
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold tracking-wider uppercase text-emerald-400 block mb-0.5">
                            Sinkronisasi Lanjutan (Tabel 2: Aset Ditambahkan Mitra)
                        </span>
                        <h3 class="text-lg font-black text-white flex items-center gap-2">
                            <span>Perpanjang Aset Rekanan Terpasang?</span>
                        </h3>
                    </div>
                </div>

                <button type="button" 
                        @click="if(!isSubmittingExtendMitra) closeConfirmExtendMitraModal()" 
                        class="w-9 h-9 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white hover:bg-rose-500/20 hover:border-rose-500/30 border border-slate-700/60 flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Info Konteks Induk Berhasil Diperpanjang -->
                <div class="p-4 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 flex items-start gap-3">
                    <span class="text-2xl mt-0.5">✅</span>
                    <div class="text-xs text-slate-300 leading-relaxed">
                        <p class="font-bold text-white mb-1">
                            Objek pemanfaatan RSUD telah berhasil diperpanjang!
                        </p>
                        <p class="text-slate-300">
                            PKS objek pemanfaatan kini berlaku sampai dengan 
                            <strong class="text-emerald-400 font-mono" x-text="extendMitraState.tanggal_selesai_fmt"></strong> 
                            berdasarkan Adendum <span class="font-mono text-cyan-300" x-text="extendMitraState.nomor_adendum"></span>.
                        </p>
                        <p class="text-slate-400 mt-1 text-[11px]">
                            Terdeteksi <strong class="text-emerald-400" x-text="(extendMitraState.linked_mitra_assets || []).length"></strong> aset milik mitra rekanan (Tabel 2) yang terpasang di objek ini. Silakan tentukan apakah masa konsesi aset-aset mitra tersebut ikut diperpanjang.
                        </p>
                    </div>
                </div>

                <!-- Kontrol Pilih Semua / Batalkan -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                        <span>📋</span>
                        <span>Daftar Aset Mitra Terhubung:</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-slate-800 text-emerald-400"
                              x-text="selectedMitraIds.length + ' dipilih'"></span>
                    </span>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="selectAllMitra()" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition-colors cursor-pointer">
                            Centang Semua
                        </button>
                        <span class="text-slate-600">|</span>
                        <button type="button" @click="deselectAllMitra()" class="text-[11px] font-bold text-slate-400 hover:text-slate-300 transition-colors cursor-pointer">
                            Batal Pilih
                        </button>
                    </div>
                </div>

                <!-- Checklist Daftar Aset Mitra -->
                <div class="max-h-60 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                    <template x-for="(mitra, idx) in (extendMitraState.linked_mitra_assets || [])" :key="mitra.id">
                        <label :for="'mitra-chk-' + mitra.id" 
                               class="flex items-start gap-3 p-3 rounded-2xl border transition-all cursor-pointer"
                               :class="selectedMitraIds.includes(mitra.id) ? 'bg-emerald-950/25 border-emerald-500/40' : 'bg-slate-950/60 border-slate-800/80 opacity-70 hover:opacity-100'">
                            <input type="checkbox" 
                                   :id="'mitra-chk-' + mitra.id"
                                   :value="mitra.id"
                                   x-model="selectedMitraIds"
                                   class="mt-1 w-4 h-4 rounded text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-500 focus:ring-offset-slate-900 cursor-pointer">

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h5 class="text-xs font-bold text-white truncate" x-text="mitra.nama_barang"></h5>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 shrink-0"
                                          x-text="mitra.status_konsesi || 'Aktif'"></span>
                                </div>

                                <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-400 flex-wrap">
                                    <span class="font-mono text-cyan-400" x-text="'NIBAR: ' + (mitra.nibar || '-')"></span>
                                    <span>•</span>
                                    <span class="font-mono" x-text="'Kode: ' + (mitra.kode_108 || '-')"></span>
                                </div>

                                <div class="mt-1.5 flex items-center gap-1.5 text-[10.5px]">
                                    <span class="text-slate-400">Konsesi:</span>
                                    <span class="font-mono text-slate-400" x-text="mitra.tanggal_selesai_lama"></span>
                                    <span class="text-emerald-400">➔</span>
                                    <span class="font-mono font-bold text-emerald-300 bg-emerald-500/20 px-1.5 py-0.2 rounded" x-text="extendMitraState.tanggal_selesai_fmt"></span>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>

                <!-- Input Catatan Tambahan (Opsional) -->
                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Catatan Adendum Aset Mitra <span class="text-slate-500 font-normal">(Opsional)</span>
                    </label>
                    <input type="text" 
                           x-model="extendMitraKeterangan"
                           placeholder="Contoh: Mengikuti perpanjangan masa konsesi PKS ruangan/gedung"
                           class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                </div>

                <!-- Footer Buttons -->
                <div class="pt-4 border-t border-slate-800 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                    <button type="button" 
                            @click="closeConfirmExtendMitraModal()" 
                            :disabled="isSubmittingExtendMitra"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all cursor-pointer">
                        Lewati (Jangan Perpanjang Aset Mitra)
                    </button>

                    <button type="button" 
                            @click="submitExtendMitraBatch()"
                            :disabled="isSubmittingExtendMitra || selectedMitraIds.length === 0"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-extrabold shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95 disabled:opacity-50">
                        <template x-if="isSubmittingExtendMitra">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span>Ya, Perpanjang (<span x-text="selectedMitraIds.length"></span>) Aset Mitra</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
