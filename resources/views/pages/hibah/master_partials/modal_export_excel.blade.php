<!-- ========================================================================= -->
<!-- MODAL PILIH TAHUN, TRIWULAN & FORMAT EKSPOR EXCEL HIBAH BMD (DARK LUXURY) -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showModalExport" x-cloak
         class="fixed inset-0 z-[99999] w-screen h-screen flex items-center justify-center p-3 sm:p-4 bg-slate-950/85 backdrop-blur-2xl modal-backdrop-full overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showModalExport = false"
             class="bg-slate-900 border border-slate-700/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 max-w-xl w-full shadow-2xl flex flex-col max-h-[90vh] my-auto relative"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between shrink-0 pb-3 border-b border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <div class="p-2.5 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 text-lg shadow-inner">
                        🎁
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white leading-tight">Ekspor Laporan Hibah Aset (BMD)</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Buku Realisasi Hibah &amp; Pengurangan Mutasi Aset (Standar BPKAD Bondowoso)</p>
                    </div>
                </div>
                <button type="button" @click="showModalExport = false" 
                        class="text-slate-400 hover:text-white p-1 rounded-xl hover:bg-slate-800 text-xl font-bold leading-none transition-all cursor-pointer">&times;</button>
            </div>

            <!-- Form Filter Periode & Format Ekspor (Scrollable) -->
            <div class="space-y-3.5 overflow-y-auto pr-1.5 -mr-1 custom-scrollbar py-3 flex-1 min-h-0">
                
                <!-- 1. PILIHAN LEMBAR SHEET EXCEL -->
                <div class="space-y-1.5 p-3 rounded-2xl bg-amber-950/20 border border-amber-500/30">
                    <label class="block text-amber-300 font-bold text-xs flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span>📑 FORMAT LEMBAR SHEET EXCEL</span>
                            <span class="text-rose-400">*</span>
                        </span>
                        <span class="text-[10px] text-amber-400/80 font-mono">Permendagri 108</span>
                    </label>
                    <select x-model="exportConfig.format"
                            class="w-full bg-slate-950 border border-amber-500/40 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-amber-400">
                        <option value="all">🌐 Paket Lengkap Mutasi Hibah (3 Sheet: Rekapitulasi + Masuk + Keluar)</option>
                        <option value="masuk">🎁 Hanya Sheet Hibah Masuk (Penambahan Aset BMD)</option>
                        <option value="keluar">📤 Hanya Sheet Pengurangan Hibah Keluar (Penyerahan Keluar)</option>
                    </select>
                    <p class="text-[10.5px] text-amber-300/80 leading-relaxed">
                        Data aset hibah akan diekspor dengan kop surat resmi RSUD Dr. H. Koesnandi &amp; Pemkab Bondowoso.
                    </p>
                </div>

                <!-- 2. PILIHAN TAHUN ANGGARAN -->
                <div>
                    <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📅 TAHUN ANGGARAN PEMBUKUAN</span>
                        <span class="text-[10px] text-slate-400">Periode BAST</span>
                    </label>
                    <select x-model="exportConfig.tahun"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-amber-400">
                        <option value="all">Semua Tahun (Seluruh Riwayat Transaksi Hibah)</option>
                        <template x-for="yr in availableYears" :key="yr">
                            <option :value="yr" x-text="'Tahun Anggaran ' + yr"></option>
                        </template>
                    </select>
                </div>

                <!-- 3. PILIHAN TRIWULAN (TW I - IV / SEMUA) -->
                <div>
                    <label class="block text-amber-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📊 TRIWULAN LAPORAN HIBAH</span>
                        <span class="text-[10px] text-amber-400/80 font-mono">TW I - IV</span>
                    </label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <button type="button" @click="exportConfig.triwulan = 'all'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportConfig.triwulan === 'all' ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>📑 Semua (Tahunan)</span>
                            <span x-show="exportConfig.triwulan === 'all'" class="text-amber-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportConfig.triwulan = 'TW I'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportConfig.triwulan === 'TW I' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>🌱 Triwulan I (TW I)</span>
                            <span x-show="exportConfig.triwulan === 'TW I'" class="text-emerald-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportConfig.triwulan = 'TW II'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportConfig.triwulan === 'TW II' ? 'bg-blue-500/20 text-blue-300 border-blue-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>☀️ Triwulan II (TW II)</span>
                            <span x-show="exportConfig.triwulan === 'TW II'" class="text-blue-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportConfig.triwulan = 'TW III'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportConfig.triwulan === 'TW III' ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>🍂 Triwulan III (TW III)</span>
                            <span x-show="exportConfig.triwulan === 'TW III'" class="text-amber-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportConfig.triwulan = 'TW IV'"
                            class="col-span-2 p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportConfig.triwulan === 'TW IV' ? 'bg-purple-500/20 text-purple-300 border-purple-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>❄️ Triwulan IV (TW IV - Akhir Tahun)</span>
                            <span x-show="exportConfig.triwulan === 'TW IV'" class="text-purple-400 font-black">✓</span>
                        </button>
                    </div>
                </div>

                <!-- 4. PILIHAN KLASIFIKASI KIB -->
                <div>
                    <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📦 KLASIFIKASI KATEGORI KIB</span>
                        <span class="text-[10px] text-slate-400 font-mono">Filter Opsional</span>
                    </label>
                    <select x-model="exportConfig.category"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-amber-400">
                        <option value="all">Semua Kategori KIB (Seluruh Jenis Aset Hibah)</option>
                        <option value="KIB A">KIB A - Tanah</option>
                        <option value="KIB B">KIB B - Peralatan &amp; Mesin / Alat Medis</option>
                        <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                        <option value="KIB D">KIB D - Jalan, Irigasi &amp; Jaringan</option>
                        <option value="KIB E">KIB E - Aset Tetap Lainnya (Buku / Seni)</option>
                        <option value="ATB">ATB - Aset Tidak Berwujud (Software / Lisensi)</option>
                    </select>
                </div>

                <!-- 5. BLOK TANDA TANGAN PPK -->
                <div class="space-y-3 p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800">
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider block">✍️ Blok Tanda Tangan Laporan PPK</span>
                    
                    <div class="space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-300">Nama Pejabat Pembuat Komitmen (PPK)</label>
                        <input type="text" x-model="exportConfig.ppkNama"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400">
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="space-y-1">
                            <label class="block text-[11px] font-semibold text-slate-300">NIP PPK</label>
                            <input type="text" x-model="exportConfig.ppkNip"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-amber-400">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[11px] font-semibold text-slate-300">Tanggal Cetak</label>
                            <input type="date" x-model="exportConfig.tanggalCetak"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-400">
                        </div>
                    </div>
                </div>

                <!-- Info Ringkasan Data Siap Ekspor -->
                <div class="p-2.5 rounded-xl border bg-slate-950/80 border-amber-500/30 flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="text-base">🎁</span>
                        <span class="text-slate-300 font-medium">Transaksi hibah siap diekspor:</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-lg font-mono font-bold text-xs border bg-amber-500/20 text-amber-300 border-amber-500/40"
                          x-text="exportFilteredCount + ' Transaksi'"></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" @click="showModalExport = false"
                    class="px-4 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="submitExportExcel()"
                    :disabled="isExporting"
                    class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-2 cursor-pointer active:scale-95 disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-text="isExporting ? 'Mengekspor Berkas...' : 'Unduh File Excel (.xlsx)'"></span>
                </button>
            </div>
        </div>
    </div>
</template>
