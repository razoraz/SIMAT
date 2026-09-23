<!-- MODAL: HIBAH KELUAR (PENGURANGAN BARANG RSUD DIHIBAHKAN KE LUAR) -->
<div x-show="showModalHibahKeluar" x-cloak @click.self="showModalHibahKeluar = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
    style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px);">
    
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-6 my-auto">
        <!-- Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-xl shrink-0">
                    📤
                </div>
                <div>
                    <h3 class="text-lg font-black text-white">
                        Catat Barang Dihibahkan (Hibah Keluar)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Proses pengurangan barang inventaris RSUD yang diserahkan/dihibahkan ke pihak luar berdasarkan BAST resmi.
                    </p>
                </div>
            </div>
            <button type="button" @click="showModalHibahKeluar = false"
                class="w-8 h-8 rounded-full bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitHibahKeluar" class="space-y-4 text-xs">
            <!-- 1. Pilih Aset Aktif RSUD -->
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-200">
                    Pilih Barang Inventaris RSUD <span class="text-rose-400">*</span>
                </label>
                <div class="relative">
                    <input type="text" x-model="searchAsetKeluar"
                        @focus="isAsetKeluarDropdownOpen = true"
                        placeholder="Ketik untuk mencari nama barang atau kode..."
                        class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none">
                    
                    <!-- Dropdown List Hasil Cari -->
                    <div x-show="isAsetKeluarDropdownOpen" @click.away="isAsetKeluarDropdownOpen = false"
                        class="absolute z-20 mt-1 w-full max-h-52 overflow-y-auto bg-slate-950 border border-slate-800 rounded-xl shadow-2xl p-1.5 space-y-1 custom-scrollbar">
                        <template x-for="a in filteredActiveAstaps" :key="a.id">
                            <div @click="selectAstapForKeluar(a)"
                                class="p-2.5 rounded-lg bg-slate-900 hover:bg-rose-500/10 border border-slate-800 hover:border-rose-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                <div class="min-w-0 pr-3">
                                    <div class="font-bold text-white group-hover:text-rose-300 truncate" x-text="a.nama_barang"></div>
                                    <div class="text-[10px] text-slate-400" x-text="a.kode_barang + ' • ' + a.jumlah_volume + ' ' + a.satuan + ' • TA ' + a.tahun_perolehan"></div>
                                </div>
                                <span class="text-[11px] font-bold text-rose-400 shrink-0">Pilih →</span>
                            </div>
                        </template>
                        <template x-if="filteredActiveAstaps.length === 0">
                            <div class="p-3 text-center text-slate-500 text-[11px]">
                                Tidak ada aset aktif yang cocok.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Selected Aset Preview Card -->
                <div x-show="selectedAstapForKeluar" class="p-3 rounded-xl bg-rose-500/5 border border-rose-500/20 flex items-center justify-between">
                    <div>
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Aset Terpilih:</span>
                        <div class="font-bold text-white text-xs" x-text="selectedAstapForKeluar?.nama_barang"></div>
                        <div class="text-[10px] text-slate-400" x-text="selectedAstapForKeluar?.kode_barang + ' • Tersedia: ' + (selectedAstapForKeluar?.registers?.length || selectedAstapForKeluar?.jumlah_volume) + ' ' + selectedAstapForKeluar?.satuan"></div>
                    </div>
                    <button type="button" @click="selectedAstapForKeluar = null; searchAsetKeluar = ''" class="text-[11px] font-bold text-rose-400 hover:underline">
                        ✕ Ganti
                    </button>
                </div>
            </div>

            <!-- 2. Pilihan Register Tertentu (Jika Multi-Unit) -->
            <div x-show="selectedAstapForKeluar && selectedAstapForKeluar.registers && selectedAstapForKeluar.registers.length > 1" class="space-y-1.5 p-3 rounded-xl bg-slate-950 border border-slate-800">
                <label class="block font-bold text-slate-300 text-[11px] flex items-center justify-between">
                    <span>Pilih Unit Register NIBAR yang Dihibahkan:</span>
                    <button type="button" @click="toggleSelectAllRegisters()" class="text-[10px] text-amber-400 hover:underline">
                        <span x-text="keluarData.register_ids.length === selectedAstapForKeluar?.registers?.length ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                    </button>
                </label>
                <div class="max-h-36 overflow-y-auto space-y-1 custom-scrollbar pr-1">
                    <template x-for="reg in (selectedAstapForKeluar?.registers || [])" :key="reg.id">
                        <label class="p-2 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-between hover:border-slate-700 cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" :value="reg.id" x-model="keluarData.register_ids" @change="recomputeKeluarValue()" class="rounded border-slate-700 text-rose-500 focus:ring-rose-400">
                                <span class="font-mono text-[10px] font-bold text-emerald-400" x-text="reg.nibar"></span>
                            </div>
                            <span class="text-[10px] text-slate-400" x-text="reg.ruang + ' (' + reg.kondisi + ')'"></span>
                        </label>
                    </template>
                </div>
            </div>

            <!-- 3. Penerima Hibah (Instansi Pihak Ketiga) -->
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-200">
                    Instansi / Pihak Penerima Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="keluarData.penerima_hibah" required
                    placeholder="Contoh: Puskesmas Tamanan, Dinas Kesehatan Bondowoso, RSUD Besuki..."
                    class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- 4. Nomor & Tanggal BAST -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-200">
                        Nomor BAST Hibah Keluar <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" x-model="keluarData.nomor_bast" required
                        placeholder="Contoh: 020/BAST-KELUAR/RSUD/2026"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-200">
                        Tanggal BAST Hibah <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" x-model="keluarData.tanggal_bast" required
                        class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                </div>
            </div>

            <!-- 5. Nilai Aset, Tahun, & Triwulan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-200">
                        Nilai Buku / Aset (Rp) <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" x-model.number="keluarData.nilai_aset" min="0" step="any" required
                        class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 text-xs text-white font-bold font-mono focus:outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-200">
                        Tahun Pengurangan <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" x-model.number="keluarData.tahun" min="2000" max="2100" required
                        class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-200">
                        Triwulan <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="keluarData.triwulan" required
                        class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                        <option value="TW I">TW I</option>
                        <option value="TW II">TW II</option>
                        <option value="TW III">TW III</option>
                        <option value="TW IV">TW IV</option>
                    </select>
                </div>
            </div>

            <!-- 6. Keterangan Alasan Pengurangan Hibah -->
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-200">
                    Alasan / Keterangan Penyerahan Hibah
                </label>
                <textarea x-model="keluarData.keterangan" rows="2"
                    placeholder="Contoh: Dihibahkan guna menunjang pelayanan kesehatan dasar di Puskesmas sesuai Surat Keputusan Bupati..."
                    class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
            </div>

            <!-- Caution Banner -->
            <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-start space-x-2 text-[11px] text-rose-300">
                <span class="text-base leading-none">⚠️</span>
                <div>
                    Barang yang diproses hibah keluar akan otomatis ditandai statusnya sebagai <span class="font-bold text-white">"Dihibahkan"</span> dan dicatat pada <span class="font-bold text-white">Daftar Pengurangan Aset Tetap (Sheet 2 Sipenerbang)</span>.
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-3">
                <button type="button" @click="showModalHibahKeluar = false"
                    class="px-4 py-2.5 rounded-xl text-slate-400 hover:text-white font-bold text-xs">
                    Batal
                </button>
                <button type="submit" :disabled="isSubmittingKeluar"
                    class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-black text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 disabled:opacity-50 cursor-pointer">
                    <span x-show="!isSubmittingKeluar">📤 Simpan Hibah Keluar</span>
                    <span x-show="isSubmittingKeluar">Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>
