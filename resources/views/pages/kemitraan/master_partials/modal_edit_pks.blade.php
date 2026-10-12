<!-- ========================================================================= -->
<!-- MODAL EDIT METADATA PERJANJIAN KERJA SAMA (PKS) KEMITRAAN (KMT-03)         -->
<!-- ========================================================================= -->
<div x-show="showModalEditPks" 
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
         @click="if(!isSubmittingEditPks) closeEditPksModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="showModalEditPks"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-amber-500/30 text-left shadow-2xl transition-all w-full max-w-2xl my-8">

            <!-- Modal Header -->
            <div class="relative bg-gradient-to-r from-amber-950/40 via-slate-900 to-slate-950 p-6 border-b border-amber-500/20 flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        ✏️
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold tracking-wider uppercase text-amber-400 block mb-0.5 font-mono">
                            Modul Master Kemitraan (Akun 1.5.2)
                        </span>
                        <h3 class="text-lg font-black text-white flex items-center gap-2">
                            <span>Edit Metadata Dokumen PKS</span>
                        </h3>
                    </div>
                </div>

                <button type="button" 
                        @click="if(!isSubmittingEditPks) closeEditPksModal()" 
                        class="w-9 h-9 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white hover:bg-rose-500/20 hover:border-rose-500/30 border border-slate-700/60 flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Modal Form Body -->
            <form @submit.prevent="submitEditPksForm()" class="p-6 space-y-4">
                <!-- Info Ringkas Objek Aset -->
                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 block">Objek Aset Kemitraan:</span>
                        <h4 class="text-xs font-bold text-white truncate mt-0.5" x-text="editPksForm.nama_barang || 'Aset Kemitraan'"></h4>
                    </div>
                    <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-bold bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 shrink-0"
                          x-text="'KIB: ' + (editPksForm.category || '1.5.2')"></span>
                </div>

                <!-- 1. Nomor PKS & Skema Kemitraan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Nomor PKS / Kontrak <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="editPksForm.nomor_pks" required
                               placeholder="Contoh: 000.1.2/045/RSUD/2026"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 font-mono transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Skema Kemitraan <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <select x-model="editPksForm.skema_kemitraan" required
                                    style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                    class="w-full bg-slate-950 border border-slate-800 focus:border-amber-400 rounded-xl px-3.5 py-2 pr-7 text-xs font-semibold text-slate-200 focus:outline-none cursor-pointer transition-all">
                                <option value="Sewa">Sewa (1.5.2.01.01.01)</option>
                                <option value="KSP">KSP / KSO - Pemanfaatan (1.5.2.01.01.02)</option>
                                <option value="BGS/BSG">BGS / BSG (1.5.2.01.01.03)</option>
                                <option value="KSPI">KSPI - Infrastruktur (1.5.2.01.01.04)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-amber-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Mitra Nama & Pimpinan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Nama Rekanan Mitra (PT / CV / Lembaga) <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="editPksForm.mitra_nama" required
                               placeholder="Contoh: PT. Medika Cahaya Abadi"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Pimpinan / Direktur Mitra
                        </label>
                        <input type="text" x-model="editPksForm.mitra_pimpinan"
                               placeholder="Nama direktur / penandatangan PKS"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 transition-all">
                    </div>
                </div>

                <!-- 3. Alamat Mitra -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">
                        Alamat Domisili Mitra
                    </label>
                    <input type="text" x-model="editPksForm.mitra_alamat"
                           placeholder="Alamat kantor mitra pihak ketiga"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 transition-all">
                </div>

                <!-- 4. Periode Konsesi (Tanggal PKS, Mulai, Selesai) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Tanggal PKS
                        </label>
                        <input type="date" x-model="editPksForm.tanggal_pks"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400 font-mono transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Tanggal Mulai Konsesi
                        </label>
                        <input type="date" x-model="editPksForm.tanggal_mulai"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400 font-mono transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Tanggal Selesai Konsesi
                        </label>
                        <input type="date" x-model="editPksForm.tanggal_selesai"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400 font-mono transition-all">
                    </div>
                </div>

                <!-- 5. Jangka Waktu & Nilai Aset -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Jangka Waktu (Masa Konsesi)
                        </label>
                        <input type="text" x-model="editPksForm.jangka_waktu"
                               placeholder="Contoh: 5 Tahun / 36 Bulan"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Taksiran Nilai Aset / Kontrak (Rp)
                        </label>
                        <input type="number" step="any" min="0" x-model="editPksForm.nilai_aset"
                               placeholder="Nominal rupiah taksiran wajar"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 font-mono transition-all">
                    </div>
                </div>

                <!-- 6. Keterangan Tambahan -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">
                        Keterangan / Catatan Tambahan
                    </label>
                    <textarea x-model="editPksForm.keterangan" rows="2"
                              placeholder="Keterangan klausul, ruang lingkup, atau catatan kerja sama..."
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-400 transition-all"></textarea>
                </div>

                <!-- Footer Tombol Aksi -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="closeEditPksModal()" 
                            :disabled="isSubmittingEditPks"
                            class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>

                    <button type="submit" 
                            :disabled="isSubmittingEditPks"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-amber-500/25 active:scale-95 flex items-center gap-2 cursor-pointer">
                        <template x-if="isSubmittingEditPks">
                            <span class="inline-block w-3.5 h-3.5 border-2 border-slate-950 border-t-transparent rounded-full animate-spin"></span>
                        </template>
                        <span x-text="isSubmittingEditPks ? 'Menyimpan Perubahan...' : 'Simpan Perubahan PKS'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
