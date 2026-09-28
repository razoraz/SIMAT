<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: GEDUNG & BANGUNAN (KIB C / AKUN 1.5.2.01.01.xx.003)    -->
<!-- MULTI-ITEM REPEATER (MODEL PERSIS KIB B PERALATAN & MESIN)                 -->
<!-- ========================================================================= -->
<div x-show="isGedung" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB C -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-blue-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB C -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg border border-blue-500/30 shadow-inner">🏢</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik Gedung &amp; Bangunan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-mono font-bold text-[10px] border border-blue-500/40">
                            KIB C
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian konstruksi gedung, luas lantai, izin PBG/IMB, status penguasaan tanah, serta peruntukan operasional.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-blue-400 bg-blue-950/60 px-3 py-1.5 rounded-xl border border-blue-500/30 shadow-sm">
                    Total: <span x-text="formData.gedung_items ? formData.gedung_items.length : 1"></span> Bangunan
                </span>
            </div>
        </div>

        <!-- REPEATER DAFTAR GEDUNG & BANGUNAN -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.gedung_items" :key="idx">
                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 hover:border-blue-500/50 transition-all space-y-4 shadow-lg relative">
                    
                    <!-- Header Kartu Gedung -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-blue-500/20 text-blue-300 font-mono font-extrabold text-xs border border-blue-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🏢 Gedung / Bangunan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.gedung_nama_barang" x-text="item.gedung_nama_barang"></span>
                            <span class="text-[10.5px] text-slate-400 font-mono" x-show="item.gedung_luas_lantai">
                                • Luas: <strong class="text-cyan-300" x-text="(item.gedung_luas_lantai || 0) + ' m²'"></strong>
                            </span>
                            <span class="text-[10.5px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Gedung -->
                        <button type="button"
                            x-show="formData.gedung_items.length > 1"
                            @click="removeGedungItem(idx)"
                            class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer self-end sm:self-auto">
                            <span>🗑️ Hapus Gedung</span>
                        </button>
                    </div>

                    <!-- Input Nama Bangunan -->
                    <div>
                        <label class="block text-slate-400 text-[10.5px] mb-1 font-semibold">
                            Nama Spesifik Gedung / Ruang Bangunan <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="item.gedung_nama_barang"
                            placeholder="Contoh: Gedung Rawat Inap Paviliun Barat 3 Lantai / Gedung Parkir Terpadu"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-blue-500 focus:border-blue-500 rounded-xl px-3.5 py-2 text-xs text-white font-bold focus:outline-none transition-all">
                    </div>

                    <!-- Grid Form Spesifikasi Gedung -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- 1. Kondisi & Spesifikasi Bangunan -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🏗️ Kondisi &amp; Konstruksi:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Struktur Fisik</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Luas Total Lantai (m²) -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Luas Lantai Gedung (m²) <span class="text-rose-400">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" step="0.01" min="0" x-model.number="item.gedung_luas_lantai"
                                            placeholder="850"
                                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 focus:outline-none transition-all">
                                        <span class="absolute right-3 top-2 text-[10px] font-mono font-bold text-slate-500">m²</span>
                                    </div>
                                </div>

                                <!-- Kondisi Bangunan -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Kondisi Bangunan <span class="text-rose-400">*</span>
                                    </label>
                                    <select x-model="item.gedung_kondisi"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Baik">🟢 Baik (B) &mdash; Siap Digunakan</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Bertingkat / Tidak -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Konstruksi Bertingkat
                                    </label>
                                    <select x-model="item.gedung_bertingkat"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Tidak">Tidak Bertingkat (1 Lantai)</option>
                                        <option value="Bertingkat">Bertingkat (2 Lantai atau Lebih)</option>
                                    </select>
                                </div>

                                <!-- Beton / Tidak -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Konstruksi Beton / Rangka
                                    </label>
                                    <select x-model="item.gedung_beton"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Beton Bertulang">Beton Bertulang (Permanen)</option>
                                        <option value="Rangka Baja">Rangka Baja / Pre-cast</option>
                                        <option value="Semi Permanen">Semi Permanen</option>
                                        <option value="Kayu / Lainnya">Kayu / Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Status Tanah & Dokumen PBG/IMB -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📜 Dokumen PBG/IMB &amp; Status:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Legalitas</span>
                            </div>

                            <!-- Status Tanah Tempat Berdiri -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Status Penguasaan Tanah Tempat Berdiri <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="item.gedung_status_tanah"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-cyan-500 focus:outline-none transition-all">
                                    <option value="Tanah Milik RSUD">Tanah Hak Pakai Milik RSUD</option>
                                    <option value="Tanah Milik Pemkab">Tanah Milik Pemerintah Kabupaten</option>
                                    <option value="Tanah Sewa Mitra">Tanah Milik Pihak Ketiga (Sewa)</option>
                                    <option value="Tanah Hak Pengelolaan">Tanah Hak Pengelolaan (HPL)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Nomor PBG / IMB -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Nomor PBG / IMB / SLF
                                    </label>
                                    <input type="text" x-model="item.gedung_dokumen_no"
                                        placeholder="PBG-3511/RSUD/2026"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                </div>

                                <!-- Tanggal PBG / IMB -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Tanggal Terbit PBG / IMB
                                    </label>
                                    <input type="text" x-datepicker x-model="item.gedung_dokumen_tgl"
                                        placeholder="dd/mm/yyyy"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Fungsi & Peruntukan Operasional -->
                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1.5 md:col-span-2">
                            <label class="block text-slate-400 text-[10px] font-semibold">
                                Fungsi &amp; Peruntukan Operasional Gedung
                            </label>
                            <input type="text" x-model="item.gedung_fungsi"
                                placeholder="Contoh: Gedung Rawat Inap Kelas VVIP / Kantin &amp; Pujasera Kemitraan"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-medium focus:border-blue-500 focus:outline-none transition-all">
                        </div>

                        <!-- 4. Volume, Satuan & Taksiran Nilai -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-emerald-500/30 md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3 shadow-inner">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Jumlah Volume / Bangunan <span class="text-rose-400">*</span>
                                </label>
                                <input type="number" min="1" x-model.number="item.gedung_jumlah_bangunan"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Satuan <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.gedung_satuan"
                                    placeholder="Gedung / Unit / Bangunan"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-slate-400 text-[10px] font-semibold">
                                        Taksiran Nilai Satuan (Rp) <span class="text-rose-400">*</span>
                                    </label>
                                    <span class="text-[9.5px] font-mono text-emerald-400 font-bold" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></span>
                                </div>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-2 text-[10px] font-mono text-slate-500">Rp</span>
                                    <input type="text"
                                        :value="item.gedung_nilai_satuan ? Number(item.gedung_nilai_satuan).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            item.gedung_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                        "
                                        placeholder="0"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 pl-8 text-xs text-emerald-400 font-mono font-bold focus:border-emerald-500 focus:outline-none">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Gedung / Bangunan Baru -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addGedungItem()"
                class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 border border-blue-400/40 flex items-center space-x-2 transition-all cursor-pointer">
                <span>➕</span>
                <span>Tambah Bangunan / Gedung Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Gedung:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
            </div>
        </div>

    </div>

</div>
