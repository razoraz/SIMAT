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
                
                <!-- Search Input with Dropdown (Visible when no asset selected) -->
                <div x-show="!selectedAstapForKeluar" class="relative" @click.outside="isAsetKeluarDropdownOpen = false">
                    <div class="relative">
                        <input type="text" x-model="searchAsetKeluar"
                            @focus="isAsetKeluarDropdownOpen = true"
                            @click="isAsetKeluarDropdownOpen = true"
                            @input="isAsetKeluarDropdownOpen = true"
                            placeholder="Ketik untuk mencari nama barang atau kode..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-rose-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    
                    <!-- Dropdown List Hasil Cari (Maksimal 5 item terlihat, selebihnya scroll) -->
                    <div x-show="isAsetKeluarDropdownOpen"
                        style="max-height: 260px; overflow-y: auto;"
                        class="absolute z-30 mt-1 w-full bg-slate-950 border border-slate-800 rounded-xl shadow-2xl p-1.5 space-y-1 custom-scrollbar">
                        <template x-for="a in filteredActiveAstaps" :key="a.id">
                            <div @click="selectAstapForKeluar(a)"
                                class="p-2.5 rounded-lg bg-slate-900 hover:bg-rose-500/10 border border-slate-800 hover:border-rose-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                <div class="min-w-0 pr-3 space-y-0.5">
                                    <div class="font-bold text-white group-hover:text-rose-300 truncate" x-text="a.nama_barang"></div>
                                    <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-slate-400">
                                        <span class="font-mono text-slate-300" x-text="a.kode_barang"></span>
                                        <span>•</span>
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20" x-text="'Tersedia: ' + (a.registers?.length || a.jumlah_volume) + ' ' + a.satuan"></span>
                                        <span>•</span>
                                        <span x-text="'TA ' + a.tahun_perolehan"></span>
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold text-rose-400 shrink-0">Pilih →</span>
                            </div>
                        </template>

                        <!-- Indikator Gulir jika aset lebih dari 5 -->
                        <div x-show="filteredActiveAstaps.length > 5"
                            class="sticky bottom-0 bg-slate-950/95 backdrop-blur-sm border-t border-slate-800/80 -mx-1.5 -mb-1.5 px-3 py-1.5 text-center text-[10px] text-slate-400 font-medium flex items-center justify-center space-x-1.5 rounded-b-xl">
                            <span>↕️</span>
                            <span>Gulir untuk melihat <strong class="text-rose-400 font-bold" x-text="filteredActiveAstaps.length - 5"></strong> aset lainnya (<span x-text="filteredActiveAstaps.length"></span> total tersedia)</span>
                        </div>

                        <template x-if="filteredActiveAstaps.length === 0">
                            <div class="p-4 text-center text-slate-500 text-[11px]">
                                Tidak ada aset inventaris berstatus <span class="font-bold text-emerald-400">"Tersedia"</span> yang cocok.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Selected Aset Preview Card (Visible when asset selected) -->
                <div x-show="selectedAstapForKeluar" class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-between shadow-sm">
                    <div class="space-y-0.5">
                        <span class="text-rose-400 text-[10px] uppercase font-extrabold tracking-wider block">✓ Aset Terpilih:</span>
                        <div class="font-bold text-white text-sm" x-text="selectedAstapForKeluar?.nama_barang"></div>
                        <div class="text-[10px] text-slate-300 font-mono" x-text="(selectedAstapForKeluar?.kode_barang || '-') + ' • Tersedia: ' + (availableRegisters.length || selectedAstapForKeluar?.jumlah_volume) + ' ' + (selectedAstapForKeluar?.satuan || 'Unit')"></div>
                    </div>
                    <button type="button" @click="clearAstapSelection()" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-rose-400 hover:text-rose-300 border border-rose-500/30 text-xs font-bold transition flex items-center space-x-1 cursor-pointer">
                        <span>✕</span>
                        <span>Ganti Barang</span>
                    </button>
                </div>
            </div>

            <!-- 2. Pilihan Register Tertentu (Hanya yang Berstatus Tersedia) -->
            <div x-show="selectedAstapForKeluar && availableRegisters.length > 1" class="space-y-1.5 p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                <label class="block font-bold text-slate-300 text-[11px] flex items-center justify-between">
                    <span class="flex items-center space-x-1.5">
                        <span>Pilih Unit Register NIBAR yang Dihibahkan:</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Status: Tersedia</span>
                    </span>
                    <button type="button" @click="toggleSelectAllRegisters()" class="text-[10px] text-amber-400 hover:underline cursor-pointer">
                        <span x-text="keluarData.register_ids.length === availableRegisters.length ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                    </button>
                </label>
                <div style="max-height: 195px; overflow-y: auto;" class="space-y-1.5 custom-scrollbar pr-1.5">
                    <template x-for="reg in availableRegisters" :key="reg.id">
                        <label class="p-2 rounded-xl bg-slate-900 border border-slate-800/80 flex items-center justify-between hover:border-slate-700 cursor-pointer transition">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" :value="reg.id" x-model="keluarData.register_ids" @change="recomputeKeluarValue()" class="rounded border-slate-700 text-rose-500 focus:ring-rose-400">
                                <span class="font-mono text-[10px] font-bold text-emerald-400" x-text="reg.nibar"></span>
                                <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Tersedia</span>
                            </div>
                            <span class="text-[10px] text-slate-400" x-text="(reg.ruang || '-') + ' (' + (reg.kondisi || 'Baik') + ')'"></span>
                        </label>
                    </template>
                </div>
                <!-- Indikator gulir jika NIBAR lebih dari 5 unit -->
                <div x-show="availableRegisters.length > 5"
                    class="pt-1.5 text-center text-[10px] text-slate-400 border-t border-slate-800/60 flex items-center justify-center space-x-1.5">
                    <span>↕️</span>
                    <span>Gulir untuk melihat unit register lainnya (<span class="font-bold text-amber-400" x-text="keluarData.register_ids.length"></span> dari <span x-text="availableRegisters.length"></span> unit terpilih)</span>
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
                    <input type="text" x-datepicker x-model="keluarData.tanggal_bast" required
                        placeholder="dd/mm/yyyy"
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
