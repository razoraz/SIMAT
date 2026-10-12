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

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="showModalExtend"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-emerald-500/40 text-left shadow-2xl transition-all w-full max-w-2xl my-auto max-h-[88vh] flex flex-col">

            <!-- Modal Header (Pinned / Sticky) -->
            <div class="shrink-0 relative bg-gradient-to-r from-emerald-950/70 via-slate-900 to-slate-950 p-5 sm:p-6 border-b border-emerald-500/20 flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-xl shrink-0 shadow-inner ring-1 ring-emerald-500/30">
                        🔄
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold tracking-wider uppercase text-emerald-400 block mb-0.5 font-mono">
                            Adendum Perjanjian Kerja Sama (PKS)
                        </span>
                        <h3 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                            <span>Perpanjang Masa Konsesi / Pemanfaatan</span>
                        </h3>
                    </div>
                </div>

                <button type="button" 
                        @click="if(!isSubmittingExtend) closeExtendModal()" 
                        class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white hover:bg-rose-500/20 hover:border-rose-500/30 border border-slate-700/60 flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Form Wrapper with Scrollable Body & Pinned Footer -->
            <form @submit.prevent="submitExtendForm()" class="flex flex-col flex-1 overflow-hidden" enctype="multipart/form-data">
                
                <!-- Scrollable Form Body -->
                <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4 custom-scrollbar">
                    
                    <!-- 1. Info Ringkas Objek Aset Berjalan (Compact High-Scannability Card) -->
                    <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800/90 shadow-inner space-y-2.5 relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-28 h-28 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="flex items-start justify-between gap-3 relative z-10">
                            <div class="min-w-0">
                                <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 block">Objek Kerja Sama:</span>
                                <h4 class="text-xs sm:text-sm font-black text-white mt-0.5 leading-snug break-words" x-text="extendData.nama_barang || 'Aset Kemitraan'"></h4>
                                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                                    <span class="text-cyan-400 font-bold flex items-center gap-1">
                                        <span>🏢</span>
                                        <span x-text="extendData.mitra_nama || 'Mitra Rekanan'"></span>
                                    </span>
                                    <span class="text-slate-600">•</span>
                                    <span class="font-mono text-slate-400 flex items-center gap-1">
                                        <span>📜</span>
                                        <span x-text="'PKS: ' + (extendData.nomor_pks || '-')"></span>
                                    </span>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-mono font-black shrink-0 border uppercase tracking-wider"
                                  :class="extendData.status_konsesi === 'Konsesi Berakhir' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'"
                                  x-text="extendData.status_konsesi || 'Aktif'">
                            </span>
                        </div>

                        <div class="pt-2.5 border-t border-slate-800/70 grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-[11px] relative z-10">
                            <div>
                                <span class="text-slate-500 block text-[10px]">Tgl Selesai Saat Ini:</span>
                                <span class="font-mono font-bold text-amber-400" x-text="extendData.tanggal_selesai_lama_fmt || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px]">Riwayat Extend:</span>
                                <span class="font-bold text-slate-300" x-text="(extendData.jumlah_extend || 0) + ' kali diperpanjang'"></span>
                            </div>
                            <div class="col-span-2 sm:col-span-1" x-show="extendData.is_dimanfaatkan && (extendData.linked_mitra_count > 0)">
                                <span class="text-slate-500 block text-[10px]">Aset Rekanan Terpasang:</span>
                                <span class="font-bold text-emerald-400 flex items-center gap-1">
                                    <span>📦</span>
                                    <span x-text="extendData.linked_mitra_count + ' Aset Mitra'"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Legalitas Adendum (Nomor & Tanggal Dokumen) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                Nomor Surat Adendum / SK Perpanjangan <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" 
                                   x-model="extendForm.nomor_adendum" 
                                   required
                                   placeholder="Contoh: 445/ADD-01/PKS-KEMITRAAN/2026"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                Tanggal Dokumen Adendum <span class="text-rose-400">*</span>
                            </label>
                            <input type="date" 
                                   x-model="extendForm.tanggal_adendum" 
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono [color-scheme:dark]">
                        </div>
                    </div>

                    <!-- 3. Periode Konsesi Baru (New End Date & Quick Duration Pills) -->
                    <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-2.5">
                        <div class="flex flex-wrap items-center justify-between gap-1.5">
                            <label class="text-[11px] font-bold text-slate-300">
                                Tanggal Berakhir Konsesi Baru (New End Date) <span class="text-rose-400">*</span>
                            </label>
                            <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider">⚡ Pilih Cepat Durasi:</span>
                        </div>

                        <!-- Shortcut Pills -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="setExtendDuration(6, 'months')" 
                                    class="px-2.5 py-1 rounded-lg bg-slate-800/90 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[11px] font-bold transition-all cursor-pointer active:scale-95 shadow-sm">
                                +6 Bulan
                            </button>
                            <button type="button" @click="setExtendDuration(1, 'years')" 
                                    class="px-2.5 py-1 rounded-lg bg-slate-800/90 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[11px] font-bold transition-all cursor-pointer active:scale-95 shadow-sm">
                                +1 Tahun
                            </button>
                            <button type="button" @click="setExtendDuration(2, 'years')" 
                                    class="px-2.5 py-1 rounded-lg bg-slate-800/90 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[11px] font-bold transition-all cursor-pointer active:scale-95 shadow-sm">
                                +2 Tahun
                            </button>
                            <button type="button" @click="setExtendDuration(3, 'years')" 
                                    class="px-2.5 py-1 rounded-lg bg-slate-800/90 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[11px] font-bold transition-all cursor-pointer active:scale-95 shadow-sm">
                                +3 Tahun
                            </button>
                            <button type="button" @click="setExtendDuration(5, 'years')" 
                                    class="px-2.5 py-1 rounded-lg bg-slate-800/90 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/40 text-[11px] font-bold transition-all cursor-pointer active:scale-95 shadow-sm">
                                +5 Tahun
                            </button>
                        </div>

                        <input type="date" 
                               x-model="extendForm.tanggal_selesai_baru" 
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-emerald-500/40 text-xs sm:text-sm text-emerald-300 focus:outline-none focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400 transition-all font-mono font-bold [color-scheme:dark]">
                        <p class="text-[10px] text-slate-400">
                            Masa konsesi akan otomatis berstatus <span class="text-emerald-400 font-bold">Aktif</span> kembali terhitung sampai tanggal selesai baru di atas.
                        </p>
                    </div>

                    <!-- 4. Penyesuaian Nilai Finansial & Upload Berkas Adendum -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Nilai Baru -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                Penyesuaian Nilai Kontribusi / Sewa (Rp) <span class="text-slate-500 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-xs font-mono font-bold text-slate-500 pointer-events-none">Rp</span>
                                <input type="text" 
                                       x-model="extendForm.nilai_aset_baru" 
                                       @input="formatExtendNilai($event)"
                                       placeholder="Biarkan kosong jika tetap"
                                       class="w-full pl-10 pr-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono">
                            </div>
                            <span class="text-[10px] text-slate-500 block mt-1">Biarkan kosong jika tidak ada perubahan nilai tarif sewa/kontribusi.</span>
                        </div>

                        <!-- Custom Styled File Upload (Eliminates browser white pill) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                Berkas Scan PDF Adendum <span class="text-slate-500 font-normal">(Opsional)</span>
                            </label>
                            <label class="flex items-center justify-between w-full px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all group">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-emerald-400 text-sm group-hover:scale-110 transition-transform">📄</span>
                                    <span class="text-xs text-slate-400 group-hover:text-slate-200 truncate"
                                          x-text="extendForm.berkas_adendum ? extendForm.berkas_adendum.name : 'Pilih scan PDF / Dokumen...'"></span>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg bg-slate-800 group-hover:bg-emerald-500/20 text-slate-300 group-hover:text-emerald-300 text-[10px] font-bold shrink-0 transition-colors">
                                    Pilih File
                                </span>
                                <input type="file" 
                                       @change="handleExtendFileChange($event)"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="hidden">
                            </label>
                            <p class="text-[10px] text-slate-500 mt-1">Format PDF, JPG, PNG (Maks 10MB).</p>
                            <template x-if="extendForm.berkas_adendum">
                                <div class="flex items-center justify-between mt-1 px-1 text-[10px]">
                                    <span class="text-emerald-400 font-mono truncate" x-text="'File dipilih: ' + (extendForm.berkas_adendum.size ? (extendForm.berkas_adendum.size / 1024 / 1024).toFixed(2) + ' MB' : '')"></span>
                                    <button type="button" @click="extendForm.berkas_adendum = null" class="text-rose-400 hover:text-rose-300 font-bold cursor-pointer">
                                        ✕ Batal
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 5. Keterangan / Klausul Dasar Perpanjangan -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Keterangan / Klausul Dasar Perpanjangan <span class="text-slate-500 font-normal">(Opsional)</span>
                        </label>
                        <textarea x-model="extendForm.keterangan_adendum" 
                                  rows="2"
                                  placeholder="Contoh: Kesepakatan perpanjangan konsesi alat laboratorium sesuai surat permohonan rekanan No. 12/SPA/2026..."
                                  class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"></textarea>
                    </div>

                </div>

                <!-- Modal Footer (Pinned / Always Visible at Bottom - Never gets cut off) -->
                <div class="shrink-0 px-5 sm:px-6 py-3.5 bg-slate-950/95 border-t border-slate-800 flex items-center justify-between gap-3">
                    <div class="hidden sm:flex items-center gap-2 text-[11px] text-slate-400 font-mono">
                        <span>Konsesi Baru:</span>
                        <span class="text-emerald-400 font-bold" x-text="extendForm.tanggal_selesai_baru || '-'"></span>
                    </div>

                    <div class="flex items-center gap-2.5 ml-auto">
                        <button type="button" 
                                @click="closeExtendModal()" 
                                :disabled="isSubmittingExtend"
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all cursor-pointer active:scale-95">
                            Batal
                        </button>

                        <button type="submit" 
                                :disabled="isSubmittingExtend"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 text-xs font-black shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2 cursor-pointer active:scale-95 disabled:opacity-50">
                            <template x-if="isSubmittingExtend">
                                <svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-slate-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmittingExtend ? 'Menyimpan...' : '💾 Simpan & Terapkan Perpanjangan'"></span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
