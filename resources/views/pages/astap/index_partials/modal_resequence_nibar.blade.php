        <!-- ========================================================================= -->
        <!-- MODAL DIALOG: RAPIKAN & URUTKAN ULANG NIBAR (AUTO-RESEQUENCE)             -->
        <!-- ========================================================================= -->
        <div x-show="showResequenceModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="showResequenceModal = false"
                 class="bg-slate-900 border border-amber-500/50 rounded-3xl p-6 max-w-xl w-full shadow-2xl space-y-5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-500/30 text-2xl shadow-md">
                            🔄
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">Rapikan &amp; Urutkan Ulang NIBAR</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Menyusun ulang nomor register agar berurutan rapi tanpa ada nomor yang loncat/kosong.</p>
                        </div>
                    </div>
                    <button type="button" @click="showResequenceModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <!-- Alert Penjelasan & Peringatan -->
                <div class="space-y-3">
                    <div class="p-4 rounded-2xl bg-cyan-950/40 border border-cyan-500/40 space-y-2">
                        <div class="flex items-center space-x-2 text-cyan-300 font-bold text-xs">
                            <span>ℹ️</span>
                            <span>Bagaimana Fitur Ini Bekerja?</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Ketika ada unit register yang <strong>dihapus</strong>, nomor register setelahnya bisa meninggalkan celah/nomor kosong. 
                            Sistem akan <strong>merapatkan kembali urutan NIBAR khusus untuk aset yang BELUM DITEMPATKAN (di gudang)</strong> agar berurutan rapi.
                        </p>
                        <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-[10.5px] text-emerald-300 space-y-1">
                            <span class="font-bold block">🔒 Perlindungan Aset yang Sudah Ditempatkan:</span>
                            <span class="text-slate-300 block leading-normal">Aset yang <strong>sudah ditempatkan di ruangan/unit (ada lokasi paviliun/ruang) DIKUNCI</strong> dan tidak akan berubah nomor registernya agar stiker label QR fisik yang telah ditempel di ruangan tetap aman dan valid.</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-950/40 border border-amber-500/50 space-y-2">
                        <div class="flex items-center space-x-2 text-amber-300 font-bold text-xs">
                            <span>⚠️</span>
                            <span>Peringatan Penting Sebelum Melanjutkan:</span>
                        </div>
                        <ul class="text-[11px] text-amber-200/90 list-disc list-inside space-y-1 leading-relaxed">
                            <li>Hanya aset berstatus <strong>Belum Ditempatkan (Gudang)</strong> yang disesuaikan nomor urutnya jika ada celah kosong di bawahnya.</li>
                            <li>Jika tidak ada aset gudang di atasnya untuk mengisi celah, posisi nomor urut yang sudah ditempatkan tetap dipertahankan hingga ada pengadaan baru atau aset dikembalikan ke gudang.</li>
                        </ul>
                    </div>
                </div>

                <!-- Form Filter Scope Resequence -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3.5">
                    <span class="text-xs font-extrabold text-slate-200 block uppercase tracking-wider">🎯 Pilih Lingkup Data yang Ingin Dibereskan:</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- 1. Pilihan Tahun Anggaran -->
                        <div>
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📅 Tahun Perolehan</label>
                            <select x-model="resequenceYear"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-amber-500">
                                <option value="all">Semua Tahun (Seluruh Database)</option>
                                <template x-for="yr in availableYears" :key="yr">
                                    <option :value="yr" x-text="'Tahun Anggaran ' + yr"></option>
                                </template>
                            </select>
                        </div>

                        <!-- 2. Pilihan Kategori KIB -->
                        <div>
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📦 Klasifikasi Aset (KIB)</label>
                            <select x-model="resequenceCategory"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-amber-500">
                                <option value="all">Semua Kategori (KIB A s/d ATB)</option>
                                <option value="KIB A">KIB A - Tanah</option>
                                <option value="KIB B">KIB B - Peralatan &amp; Mesin</option>
                                <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                                <option value="KIB D">KIB D - Jalan &amp; Jaringan</option>
                                <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                                <option value="KIB F">KIB F - Konstruksi KDP</option>
                                <option value="ATB">ATB - Aset Tidak Berwujud</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Actions -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showResequenceModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitResequence()"
                        :disabled="isSubmittingResequence"
                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-2 cursor-pointer active:scale-95 disabled:opacity-50">
                        <svg class="w-4 h-4 text-slate-950" :class="{ 'animate-spin': isSubmittingResequence }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-text="isSubmittingResequence ? 'Sedang Merapikan NIBAR...' : '⚡ Ya, Rapikan NIBAR Sekarang'"></span>
                    </button>
                </div>
            </div>
        </div>
