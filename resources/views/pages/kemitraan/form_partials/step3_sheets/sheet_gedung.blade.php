<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: GEDUNG & BANGUNAN (KIB C / AKUN 1.5.2.01.01.xx.003)    -->
<!-- SAMAKAN PERSIS DENGAN FORMAT LANGKAH 3 BELANJA MODAL (ASTAP)              -->
<!-- ========================================================================= -->
<div x-show="isGedung" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB C -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-blue-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB C -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
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
            <span class="text-[10px] font-mono font-bold text-blue-400 bg-blue-950/60 px-3 py-1.5 rounded-xl border border-blue-500/30 shrink-0 shadow-sm">
                Format KIB C (Gedung &amp; Bangunan)
            </span>
        </div>

        <!-- Grid 2 Kolom Spesifikasi Persis Seperti Langkah 3 Belanja Modal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- 1. Kondisi & Spesifikasi Bangunan (Persis Belanja Modal KIB C) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>🏗️ Kondisi &amp; Spesifikasi:</span>
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
                            <input type="number" step="0.01" min="0" x-model.number="formData.gedung_luas_lantai"
                                placeholder="Contoh: 850"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-amber-500 focus:outline-none transition-all">
                            <span class="absolute right-3 top-2 text-[10px] font-mono font-bold text-slate-500">m²</span>
                        </div>
                    </div>

                    <!-- Kondisi Bangunan -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Kondisi Bangunan <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="formData.gedung_kondisi"
                            @change="formData.kondisi = formData.gedung_kondisi"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500 focus:outline-none transition-all">
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
                        <select x-model="formData.gedung_bertingkat"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                            <option value="Tidak">Tidak Bertingkat (1 Lantai)</option>
                            <option value="Bertingkat">Bertingkat (2 Lantai atau Lebih)</option>
                        </select>
                    </div>

                    <!-- Beton / Tidak -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Konstruksi Beton / Rangka
                        </label>
                        <select x-model="formData.gedung_beton"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                            <option value="Beton Bertulang">Beton Bertulang (Permanen)</option>
                            <option value="Rangka Baja">Rangka Baja / Pre-cast</option>
                            <option value="Semi Permanen">Semi Permanen</option>
                            <option value="Kayu / Lainnya">Kayu / Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Status Tanah & Dokumen PBG/IMB (Persis Belanja Modal KIB C) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📜 Dokumen PBG/IMB &amp; Status Tanah:</span>
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Legalitas Bangunan</span>
                </div>

                <!-- Status Tanah Tempat Berdiri -->
                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Status Penguasaan Tanah Tempat Berdiri <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.gedung_status_tanah"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-cyan-500 focus:outline-none transition-all">
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
                        <input type="text" x-model="formData.gedung_dokumen_no"
                            placeholder="Contoh: PBG-3511/RSUD/2025"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                    </div>

                    <!-- Tanggal PBG / IMB -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Tanggal Terbit PBG / IMB
                        </label>
                        <input type="text" x-datepicker x-model="formData.gedung_dokumen_tgl"
                            placeholder="dd/mm/yyyy"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- 3. Fungsi & Peruntukan Operasional Gedung -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2 md:col-span-2 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                    <span class="text-xs font-bold text-blue-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>🏢 Fungsi &amp; Peruntukan Operasional Gedung:</span>
                    </span>
                    <span class="text-[9px] text-slate-400 font-medium">Penggunaan Bangunan</span>
                </div>
                <input type="text" x-model="formData.gedung_fungsi"
                    placeholder="Contoh: Gedung Parkir Terpadu 3 Lantai / Gedung Paviliun Rawat Inap VVIP / Area Kantin &amp; Pujasera Kemitraan"
                    class="w-full bg-slate-950 border border-slate-700 hover:border-blue-500 focus:border-blue-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-all font-medium">
            </div>

            <!-- 4. Letak / Alamat Lokasi Gedung -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2 md:col-span-2 shadow-md">
                <div class="flex items-center justify-between border-b border-amber-500/20 pb-1.5">
                    <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📍 Letak / Alamat Lokasi Fisik Gedung:</span>
                    </label>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Kompleks RSUD</span>
                </div>
                <input type="text" x-model="formData.alamat_barang"
                    placeholder="Contoh: Kompleks RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1 (Sayap Barat Rawat Inap)"
                    class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none transition-all">
            </div>

        </div>

    </div>

</div>
