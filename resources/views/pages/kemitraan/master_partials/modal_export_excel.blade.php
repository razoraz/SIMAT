<!-- ========================================================================= -->
<!-- MODAL PILIH TAHUN, TRIWULAN & SKEMA EKSPOR EXCEL KEMITRAAN (AKUN 1.5.2)    -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showExportModal" x-cloak
         class="fixed inset-0 z-[99999] w-screen h-screen flex items-center justify-center p-3 sm:p-4 bg-slate-950/85 backdrop-blur-2xl modal-backdrop-full overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.away="showExportModal = false"
             class="bg-slate-900 border border-slate-700/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 max-w-xl w-full shadow-2xl flex flex-col max-h-[90vh] my-auto relative"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between shrink-0 pb-3 border-b border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <div class="p-2.5 rounded-2xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 text-lg shadow-inner">
                        🤝
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white leading-tight">Ekspor Laporan Aset Kemitraan</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Buku Induk Aset Kemitraan Pihak Ketiga (Akun 1.5.2 · PMDN 108)</p>
                    </div>
                </div>
                <button type="button" @click="showExportModal = false" class="text-slate-400 hover:text-white p-1 rounded-xl hover:bg-slate-800 text-xl font-bold leading-none transition-all">&times;</button>
            </div>

            <!-- Form Filter Periode & Skema Ekspor (Scrollable) -->
            <div class="space-y-3.5 overflow-y-auto pr-1.5 -mr-1 custom-scrollbar py-3 flex-1 min-h-0">
                
                <!-- 1. SKEMA KERJA SAMA (AKUN 1.5.2 PERMENDAGRI 108) -->
                <div class="space-y-1.5 p-3 rounded-2xl bg-cyan-950/20 border border-cyan-500/30">
                    <label class="block text-cyan-300 font-bold text-xs flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span>🤝 SKEMA KERJA SAMA (AKUN 1.5.2)</span>
                            <span class="text-rose-400">*</span>
                        </span>
                        <span class="text-[10px] text-cyan-400/80 font-mono">Permendagri 108</span>
                    </label>
                    <select x-model="exportKemitraanSkema"
                            class="w-full bg-slate-950 border border-cyan-500/40 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-cyan-400">
                        <option value="all">🌐 Semua Skema Kemitraan (Akun 1.5.2 Lengkap)</option>
                        <option value="sewa">🏢 Sewa (Akun 1.5.2.01.01.01)</option>
                        <option value="ksp">🤝 Kerja Sama Pemanfaatan / KSP (Akun 1.5.2.01.01.02)</option>
                        <option value="bgs_bsg">🏗️ Bangun Guna Serah / BSG (Akun 1.5.2.01.01.03)</option>
                        <option value="kspi">⚡ Kerja Sama Penyediaan Infrastruktur / KSPI (Akun 1.5.2.01.01.04)</option>
                    </select>
                    <p class="text-[10.5px] text-cyan-300/80 leading-relaxed">
                        Data aset akan diekspor sesuai format klasifikasi akun resmi standar Permendagri 108.
                    </p>
                </div>

                <!-- 2. PILIHAN TAHUN ANGGARAN / PEROLEHAN PKS -->
                <div>
                    <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📅 TAHUN ANGGARAN / PEROLEHAN</span>
                        <span class="text-[10px] text-slate-400">Periode PKS</span>
                    </label>
                    <select x-model="exportYear"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-cyan-400">
                        <option value="all">Semua Tahun (Seluruh Riwayat PKS Kemitraan)</option>
                        <template x-for="yr in availableYears" :key="yr">
                            <option :value="yr" x-text="'Tahun Anggaran ' + yr"></option>
                        </template>
                    </select>
                </div>

                <!-- 3. PILIHAN TRIWULAN (TW I - IV / SEMUA) -->
                <div>
                    <label class="block text-cyan-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📊 TRIWULAN PKS / KONSESI</span>
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

                <!-- 4. PILIHAN KLASIFIKASI SHEET EXCEL (KIB A s/d E & EXTRACOM) -->
                <div>
                    <label class="block text-cyan-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📦 KLASIFIKASI KIB KEMITRAAN (AKUN 1.5.2)</span>
                        <span class="text-[10px] text-cyan-400 font-mono">Sheet Excel</span>
                    </label>
                    <select x-model="exportKemitraanCategory"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-cyan-400">
                        <option value="all">Semua KIB (Buku Aset Kemitraan Lengkap 7 Sheet: Rekap, KIB A s/d E &amp; Extracom)</option>
                        <option value="REKAP">Lembar Rekapitulasi Realisasi Kemitraan (Sheet 1)</option>
                        <option value="KIB A">KIB A - Tanah (Akun 1.5.2.01.01.xx.001)</option>
                        <option value="KIB B">KIB B - Peralatan &amp; Mesin / Fasilitas Medis (Akun 1.5.2.01.01.xx.002)</option>
                        <option value="KIB C">KIB C - Gedung &amp; Bangunan / BGS (Akun 1.5.2.01.01.xx.003)</option>
                        <option value="KIB D">KIB D - Jalan, Irigasi &amp; Jaringan (Akun 1.5.2.01.01.xx.004)</option>
                        <option value="KIB E">KIB E - Aset Tetap Lainnya (Akun 1.5.2.01.01.xx.005)</option>
                        <option value="EXTRACOM">Extracom - Barang Ekstrakomtabel (&lt; Rp 300.000)</option>
                    </select>
                </div>

                <!-- Info Ringkasan Data Siap Ekspor -->
                <div class="p-2.5 rounded-xl border bg-slate-950/80 border-cyan-500/30 flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="text-base">🤝</span>
                        <span class="text-slate-300 font-medium">Aset kemitraan siap diekspor:</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-lg font-mono font-bold text-xs border bg-cyan-500/20 text-cyan-300 border-cyan-500/40"
                          x-text="exportFilteredCount + ' Item Data'"></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" @click="showExportModal = false"
                    class="px-4 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="submitExport()"
                    :disabled="isSubmittingExport"
                    class="px-5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-2 cursor-pointer active:scale-95 disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-text="isSubmittingExport ? 'Mengekspor...' : 'Unduh File Excel (.xlsx)'"></span>
                </button>
            </div>
        </div>
    </div>
</template>
