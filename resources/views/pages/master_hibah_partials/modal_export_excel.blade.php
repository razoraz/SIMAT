<!-- MODAL: EKSPOR EXCEL SIPENERBANG / HIBAH -->
<div x-show="showModalExport" x-cloak @click.self="showModalExport = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
    style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px);">
    
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 my-auto">
        <!-- Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-xl shrink-0">
                    📊
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">
                        Ekspor Sheet Hibah (Excel)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Download berkas spreadsheet berstandar Sipenerbang / Pemkab Bondowoso lengkap dengan kop dan tanda tangan.
                    </p>
                </div>
            </div>
            <button type="button" @click="showModalExport = false"
                class="w-8 h-8 rounded-full bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Options -->
        <div class="space-y-4 text-xs">
            <!-- 1. Pilihan Lembar Sheet -->
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-200">
                    Pilihan Format Sheet Excel
                </label>
                <div class="space-y-2">
                    <label class="p-3 rounded-xl border flex items-center space-x-3 cursor-pointer transition-all"
                        :class="exportConfig.format === 'all' ? 'bg-emerald-500/10 border-emerald-500/40 text-white' : 'bg-slate-950 border-slate-800 text-slate-300 hover:border-slate-700'">
                        <input type="radio" value="all" x-model="exportConfig.format" class="text-emerald-500 focus:ring-emerald-400">
                        <div>
                            <div class="font-bold">Paket Lengkap Hibah (3 Sheet Terintegrasi)</div>
                            <div class="text-[10px] text-slate-400">Sheet 1: Rekapitulasi Hibah • Sheet 2: Hibah Masuk • Sheet 3: Pengurangan Hibah Keluar</div>
                        </div>
                    </label>

                    <label class="p-3 rounded-xl border flex items-center space-x-3 cursor-pointer transition-all"
                        :class="exportConfig.format === 'masuk' ? 'bg-amber-400/10 border-amber-400/40 text-white' : 'bg-slate-950 border-slate-800 text-slate-300 hover:border-slate-700'">
                        <input type="radio" value="masuk" x-model="exportConfig.format" class="text-amber-400 focus:ring-amber-400">
                        <div>
                            <div class="font-bold">Hanya Sheet Hibah Masuk (Penambahan Aset)</div>
                            <div class="text-[10px] text-slate-400">Daftar perolehan hibah dari Kemenkes/Dinkes/Pihak Ketiga TA bersangkutan</div>
                        </div>
                    </label>

                    <label class="p-3 rounded-xl border flex items-center space-x-3 cursor-pointer transition-all"
                        :class="exportConfig.format === 'keluar' ? 'bg-rose-500/10 border-rose-500/40 text-white' : 'bg-slate-950 border-slate-800 text-slate-300 hover:border-slate-700'">
                        <input type="radio" value="keluar" x-model="exportConfig.format" class="text-rose-500 focus:ring-rose-400">
                        <div>
                            <div class="font-bold">Hanya Sheet Pengurangan Hibah Keluar</div>
                            <div class="text-[10px] text-slate-400">Daftar barang inventaris RSUD yang diserahkan/dihibahkan ke luar</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. Filter Periode -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-200">Tahun Anggaran</label>
                    <select x-model="exportConfig.tahun"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <option value="all">Semua Tahun</option>
                        <template x-for="y in availableYears" :key="y">
                            <option :value="y" x-text="y"></option>
                        </template>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-200">Triwulan</label>
                    <select x-model="exportConfig.triwulan"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <option value="all">Setahun Penuh (Tahunan)</option>
                        <option value="TW I">Triwulan I</option>
                        <option value="TW II">Triwulan II</option>
                        <option value="TW III">Triwulan III</option>
                        <option value="TW IV">Triwulan IV</option>
                    </select>
                </div>
            </div>

            <!-- 3. Pejabat Penandatangan PPK -->
            <div class="space-y-3 p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">✍️ Blok Tanda Tangan Laporan</span>
                
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-semibold text-slate-300">Nama Pejabat Pembuat Komitmen (PPK)</label>
                    <input type="text" x-model="exportConfig.ppkNama"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-semibold text-slate-300">NIP PPK</label>
                        <input type="text" x-model="exportConfig.ppkNip"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-semibold text-slate-300">Tanggal Cetak</label>
                        <input type="text" x-datepicker x-model="exportConfig.tanggalCetak"
                            placeholder="dd/mm/yyyy"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Info Standar Format -->
            <div class="p-3 rounded-xl bg-slate-950 border border-emerald-500/20 text-[10.5px] text-slate-400 flex items-center space-x-2">
                <span class="text-emerald-400 text-base">ℹ️</span>
                <span>Berkas Excel dihasilkan menggunakan format resmi Permendagri 108 dan siap diserahkan ke BPKAD Bondowoso.</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-3">
            <button type="button" @click="showModalExport = false"
                class="px-4 py-2.5 rounded-xl text-slate-400 hover:text-white font-bold text-xs">
                Batal
            </button>
            <button type="button" @click="executeExportExcel()" :disabled="isExporting"
                class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/25 transition-all flex items-center space-x-2 disabled:opacity-50 cursor-pointer">
                <span x-show="!isExporting">📥 Unduh File Excel (.xlsx)</span>
                <span x-show="isExporting">Membuat Berkas Excel...</span>
            </button>
        </div>
    </div>
</div>
