<!-- ========================================================================= -->
<!-- MODAL EXTEND / PERPANJANG MASA KONSESI (ADENDUM PKS KEMITRAAN)            -->
<!-- ========================================================================= -->
<div x-show="showModalExtend" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop Dark Glassmorphism -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
         @click="if(!isSubmittingExtend) closeExtendModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="showModalExtend"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-emerald-500/30 text-left shadow-2xl transition-all w-full max-w-2xl my-8">

            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-emerald-950/50 via-slate-900 to-slate-950 p-6 border-b border-emerald-500/20 flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        🔄
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold tracking-wider uppercase text-emerald-400 block mb-0.5">
                            Adendum Perjanjian Kerja Sama (PKS)
                        </span>
                        <h3 class="text-lg font-black text-white flex items-center gap-2">
                            <span>Perpanjang Masa Konsesi / Pemanfaatan</span>
                        </h3>
                    </div>
                </div>

                <button type="button" 
                        @click="if(!isSubmittingExtend) closeExtendModal()" 
                        class="w-9 h-9 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white hover:bg-rose-500/20 hover:border-rose-500/30 border border-slate-700/60 flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Modal Form Body -->
            <form @submit.prevent="submitExtendForm()" class="p-6 space-y-5" enctype="multipart/form-data">
                <!-- Info Ringkas Objek Aset Berjalan -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2.5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 block">Objek Kerja Sama:</span>
                            <h4 class="text-sm font-bold text-white mt-0.5" x-text="extendData.nama_barang || 'Aset Kemitraan'"></h4>
                            <div class="flex items-center gap-2 mt-1 text-xs text-slate-300">
                                <span class="text-cyan-400 font-semibold" x-text="extendData.mitra_nama || 'Mitra Rekanan'"></span>
                                <span>•</span>
                                <span class="font-mono text-slate-400" x-text="'PKS: ' + (extendData.nomor_pks || '-')"></span>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-bold"
                              :class="extendData.status_konsesi === 'Konsesi Berakhir' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'"
                              x-text="extendData.status_konsesi || 'Aktif'">
                        </span>
                    </div>

                    <div class="pt-2 border-t border-slate-800/70 grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-500 block">Tgl Selesai Saat Ini:</span>
                            <span class="font-mono font-bold text-amber-400" x-text="extendData.tanggal_selesai_lama_fmt || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Riwayat Extend:</span>
                            <span class="font-bold text-slate-300" x-text="(extendData.jumlah_extend || 0) + ' kali diperpanjang'"></span>
                        </div>
                        <div class="col-span-2 sm:col-span-1" x-show="extendData.is_dimanfaatkan && (extendData.linked_mitra_count > 0)">
                            <span class="text-slate-500 block">Aset Mitra Terpasang:</span>
                            <span class="font-bold text-emerald-400 flex items-center gap-1">
                                <span>📦</span>
                                <span x-text="extendData.linked_mitra_count + ' Aset Rekanan'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Input Dokumen Adendum -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Nomor Surat Adendum / SK Perpanjangan <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" 
                               x-model="extendForm.nomor_adendum" 
                               required
                               placeholder="Contoh: 445/ADD-01/PKS-KEMITRAAN/2026"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Tanggal Dokumen Adendum <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" 
                               x-model="extendForm.tanggal_adendum" 
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono">
                    </div>
                </div>

                <!-- Input Tanggal Selesai Baru & Quick Shortcuts -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-300">
                            Tanggal Berakhir Konsesi Baru (New End Date) <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[10px] text-emerald-400 font-semibold">Pilih cepat durasi:</span>
                    </div>

                    <div class="flex items-center gap-1.5 mb-2 overflow-x-auto pb-1">
                        <button type="button" @click="setExtendDuration(6, 'months')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[10.5px] font-bold transition-all cursor-pointer">
                            +6 Bulan
                        </button>
                        <button type="button" @click="setExtendDuration(1, 'years')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[10.5px] font-bold transition-all cursor-pointer">
                            +1 Tahun
                        </button>
                        <button type="button" @click="setExtendDuration(2, 'years')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[10.5px] font-bold transition-all cursor-pointer">
                            +2 Tahun
                        </button>
                        <button type="button" @click="setExtendDuration(3, 'years')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[10.5px] font-bold transition-all cursor-pointer">
                            +3 Tahun
                        </button>
                        <button type="button" @click="setExtendDuration(5, 'years')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[10.5px] font-bold transition-all cursor-pointer">
                            +5 Tahun
                        </button>
                    </div>

                    <input type="date" 
                           x-model="extendForm.tanggal_selesai_baru" 
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-emerald-500/40 text-xs text-white focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all font-mono font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">
                        Masa konsesi akan otomatis berstatus <strong>Aktif</strong> kembali terhitung sampai tanggal selesai baru di atas.
                    </p>
                </div>

                <!-- Nilai Kontribusi / Taksiran Baru & Upload Berkas Opsional -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Penyesuaian Nilai Kontribusi / Sewa (Rp) <span class="text-slate-500 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" 
                               x-model="extendForm.nilai_aset_baru" 
                               placeholder="Contoh: 150.000.000"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono">
                        <span class="text-[10px] text-slate-500 block mt-1">Biarkan kosong jika tidak ada perubahan nilai tarif sewa/kontribusi.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Berkas Scan PDF Adendum <span class="text-slate-500 font-normal">(Opsional)</span>
                        </label>
                        <input type="file" 
                               @change="handleExtendFileChange($event)"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 file:mr-2.5 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-emerald-500/20 file:text-emerald-300 hover:file:bg-emerald-500/30 cursor-pointer">
                        <span class="text-[10px] text-slate-500 block mt-1">Format PDF, JPG, PNG (Maks 10MB).</span>
                    </div>
                </div>

                <!-- Catatan / Keterangan Adendum -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">
                        Keterangan / Klausul Dasar Perpanjangan <span class="text-slate-500 font-normal">(Opsional)</span>
                    </label>
                    <textarea x-model="extendForm.keterangan_adendum" 
                              rows="2"
                              placeholder="Dasar pertimbangan perpanjangan kontrak pemanfaatan / kesepakatan kedua belah pihak..."
                              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="closeExtendModal()" 
                            :disabled="isSubmittingExtend"
                            class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all cursor-pointer">
                        Batal
                    </button>

                    <button type="submit" 
                            :disabled="isSubmittingExtend"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-extrabold shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2 cursor-pointer active:scale-95 disabled:opacity-50">
                        <template x-if="isSubmittingExtend">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span>💾 Simpan &amp; Terapkan Perpanjangan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
