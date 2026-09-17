        <!-- ========================================================================= -->
        <!-- MODAL PILIH TAHUN & TRIWULAN UNTUK EKSPOR EXCEL                           -->
        <!-- ========================================================================= -->
        <div x-show="showExportModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-sm overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="showExportModal = false"
                 class="bg-slate-900 border border-slate-700/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 max-w-lg w-full shadow-2xl flex flex-col max-h-[82vh] my-auto"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between shrink-0 pb-2.5 border-b border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-base">
                            📊
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white leading-tight">Ekspor Laporan ASTAP</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pilih Tahun &amp; Triwulan pengadaan untuk format Excel resmi.</p>
                        </div>
                    </div>
                    <button type="button" @click="showExportModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold leading-none">&times;</button>
                </div>

                <!-- Form Filter Periode Ekspor (Scrollable with min-h-0) -->
                <div class="space-y-3 overflow-y-auto pr-1.5 -mr-1 custom-scrollbar py-2.5 flex-1 min-h-0">
                    <!-- 0. Pilihan Jenis Format Laporan Ekspor -->
                    <div>
                        <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                            <span>📑 FORMAT LAPORAN EXCEL</span>
                            <span class="text-[10px] text-slate-400">Pilih Jenis Dokumen</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="exportFormatType = 'sipenerbang'"
                                class="p-2 sm:p-2.5 rounded-xl border text-left transition-all flex flex-col justify-between cursor-pointer"
                                :class="exportFormatType === 'sipenerbang' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/60 shadow-lg' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <div class="flex items-center justify-between w-full">
                                    <span class="text-xs font-bold">📘 SIPENERBANG</span>
                                    <span x-show="exportFormatType === 'sipenerbang'" class="text-emerald-400 font-black text-xs">✓</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 leading-snug">Buku Induk Aset Tetap (9 Sheet / Per KIB)</span>
                            </button>
                            <button type="button" @click="exportFormatType = 'rekap_triwulan'"
                                class="p-2 sm:p-2.5 rounded-xl border text-left transition-all flex flex-col justify-between cursor-pointer"
                                :class="exportFormatType === 'rekap_triwulan' ? 'bg-purple-500/20 text-purple-300 border-purple-500/60 shadow-lg' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <div class="flex items-center justify-between w-full">
                                    <span class="text-xs font-bold">📑 Rekap Triwulan</span>
                                    <span x-show="exportFormatType === 'rekap_triwulan'" class="text-purple-400 font-black text-xs">✓</span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 leading-snug">Paket 4 Sheet: Daftar AT, Pengurangan, Reklas & RMB</span>
                            </button>
                        </div>
                    </div>

                    <!-- 1. Pilihan Tahun Anggaran -->
                    <div>
                        <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                            <span>📅 TAHUN ANGGARAN</span>
                            <span class="text-[10px] text-slate-400">Periode Pelaporan</span>
                        </label>
                        <select x-model="exportYear"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Tahun (Seluruh Riwayat Aset 1980 - Sekarang)</option>
                            <template x-for="yr in availableYears" :key="yr">
                                <option :value="yr" x-text="'Tahun Anggaran ' + yr"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 2. Pilihan Triwulan Pengadaan -->
                    <div>
                        <label class="block text-cyan-300 font-bold text-xs mb-1 flex items-center justify-between">
                            <span>📊 TRIWULAN PENGADAAN (BAST)</span>
                            <span class="text-[10px] text-cyan-400/80 font-mono">TW I - IV</span>
                        </label>
                        <div class="grid grid-cols-2 gap-1.5">
                            <button type="button" @click="exportTriwulan = 'all'"
                                class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                                :class="exportTriwulan === 'all' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>📑 Semua (Tahunan)</span>
                                <span x-show="exportTriwulan === 'all'" class="text-cyan-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW I'"
                                class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                                :class="exportTriwulan === 'TW I' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>🌱 Triwulan I (TW I)</span>
                                <span x-show="exportTriwulan === 'TW I'" class="text-emerald-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW II'"
                                class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                                :class="exportTriwulan === 'TW II' ? 'bg-blue-500/20 text-blue-300 border-blue-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>☀️ Triwulan II (TW II)</span>
                                <span x-show="exportTriwulan === 'TW II'" class="text-blue-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW III'"
                                class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                                :class="exportTriwulan === 'TW III' ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>🍂 Triwulan III (TW III)</span>
                                <span x-show="exportTriwulan === 'TW III'" class="text-amber-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW IV'"
                                class="col-span-2 p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                                :class="exportTriwulan === 'TW IV' ? 'bg-purple-500/20 text-purple-300 border-purple-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>❄️ Triwulan IV (TW IV - Akhir Tahun)</span>
                                <span x-show="exportTriwulan === 'TW IV'" class="text-purple-400 font-black">✓</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Pilihan Kategori KIB (Khusus Format SIPENERBANG) -->
                    <div x-show="exportFormatType === 'sipenerbang'">
                        <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                            <span>📦 KLASIFIKASI KIB</span>
                            <span class="text-[10px] text-slate-400">Sheet Excel</span>
                        </label>
                        <select x-model="exportCategory"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua KIB (Buku Aset Lengkap 9 Sheet)</option>
                            <option value="REKAP">Lembar Rekapitulasi Realisasi(Sheet 1)</option>
                            <option value="KIB A">KIB A - Tanah</option>
                            <option value="KIB B">KIB B - Peralatan &amp; Mesin</option>
                            <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                            <option value="KIB D">KIB D - Jalan &amp; Jaringan</option>
                            <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                            <option value="KIB F">KIB F - Konstruksi KDP</option>
                            <option value="ATB">ATB - Aset Tidak Berwujud</option>
                            <option value="EXTRACOM">Extracom</option>
                        </select>
                    </div>

                    <!-- 3. Pilihan Lembar Sheet (Khusus Format Rekap Triwulan: Semua / Salah Satu Saja) -->
                    <div x-show="exportFormatType === 'rekap_triwulan'">
                        <label class="block text-purple-300 font-bold text-xs mb-1 flex items-center justify-between">
                            <span>📑 PILIHAN SHEET REKAPITULASI</span>
                            <span class="text-[10px] text-purple-400/80 font-mono">Sheet Excel</span>
                        </label>
                        <select x-model="exportRekapSheet"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-purple-500">
                            <option value="all">Semua Sheet (Paket Lengkap 4 Sheet)</option>
                            <option value="sheet1" x-text="'1. Daftar AT ' + (exportTriwulan === 'all' ? 'Tahunan' : exportTriwulan)"></option>
                            <option value="sheet2">2. Daftar Pengurangan AT RSDK</option>
                            <option value="sheet3">3. Reklas RSDK</option>
                            <option value="sheet4">4. RMB (excel) RSDK</option>
                        </select>
                    </div>

                    <!-- Info Ringkasan Data -->
                    <div class="p-2 sm:p-2.5 bg-slate-950/80 border border-emerald-500/30 rounded-xl flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="text-emerald-400 text-base">📋</span>
                            <span class="text-slate-300 font-medium">Aset siap diekspor:</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-xs border border-emerald-500/40"
                              x-text="exportFilteredCount + ' Item Data'"></span>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-2.5 border-t border-slate-800 flex items-center justify-end space-x-2 shrink-0">
                    <button type="button" @click="showExportModal = false"
                        class="px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitExport()"
                        :disabled="isSubmittingExport"
                        class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 cursor-pointer active:scale-95 disabled:opacity-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-text="isSubmittingExport ? 'Mengekspor...' : 'Unduh File Excel'"></span>
                    </button>
                </div>
            </div>
        </div>
