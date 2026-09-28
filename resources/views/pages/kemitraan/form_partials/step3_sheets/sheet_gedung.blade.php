<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: GEDUNG & BANGUNAN (KIB C / AKUN 1.5.2.01.01.xx.003)    -->
<!-- ========================================================================= -->
<div x-show="isGedung" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-blue-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-blue-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-base border border-blue-500/30">🏢</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                        Spesifikasi Fisik Gedung &amp; Bangunan (KIB C)
                    </h3>
                    <p class="text-[11px] text-slate-400">Rincian konstruksi gedung, luas lantai, dokumen IMB/PBG, dan status tanah lokasi bangunan.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-blue-400 bg-blue-950/50 px-2.5 py-1 rounded-lg border border-blue-500/30 shrink-0">
                Format KIB C
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Konstruksi Bertingkat -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Konstruksi Bertingkat <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.gedung_bertingkat"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    <option value="Tidak">Tidak Bertingkat (1 Lantai)</option>
                    <option value="Bertingkat">Bertingkat (2 Lantai atau Lebih)</option>
                </select>
            </div>

            <!-- Konstruksi Beton -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Konstruksi Beton / Rangka <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.gedung_beton"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    <option value="Beton Bertulang">Beton Bertulang (Permanen)</option>
                    <option value="Rangka Baja">Rangka Baja / Pre-cast</option>
                    <option value="Semi Permanen">Semi Permanen</option>
                    <option value="Kayu / Lainnya">Kayu / Lainnya</option>
                </select>
            </div>

            <!-- Luas Total Lantai (m²) -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Luas Lantai Gedung (m²) <span class="text-rose-400">*</span>
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" x-model.number="formData.gedung_luas_lantai"
                        placeholder="Contoh: 850"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-blue-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono font-bold focus:outline-none">
                    <span class="absolute right-3.5 top-2.5 text-slate-400 text-xs font-mono font-bold">m²</span>
                </div>
            </div>

            <!-- Nomor Dokumen Gedung (IMB / PBG / SLF) -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Dokumen Gedung (IMB / PBG / SLF)
                </label>
                <input type="text" x-model="formData.gedung_dokumen_no"
                    placeholder="Contoh: PBG-3511/RSUD/2025"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-blue-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>

            <!-- Tanggal Dokumen Gedung -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal Terbit Dokumen PBG/IMB
                </label>
                <input type="text" x-datepicker x-model="formData.gedung_dokumen_tgl"
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-blue-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- Status Tanah Tempat Berdiri -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Status Tanah Tempat Gedung Berdiri <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.gedung_status_tanah"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    <option value="Tanah Milik RSUD">Tanah Hak Pakai Milik RSUD</option>
                    <option value="Tanah Milik Pemkab">Tanah Milik Pemerintah Kabupaten</option>
                    <option value="Tanah Sewa Mitra">Tanah Milik Pihak Ketiga (Sewa)</option>
                    <option value="Tanah Hak Pengelolaan">Tanah Hak Pengelolaan (HPL)</option>
                </select>
            </div>

            <!-- Fungsi Penggunaan Gedung -->
            <div class="sm:col-span-2 lg:col-span-3">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Fungsi &amp; Peruntukan Operasional Gedung <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.gedung_fungsi"
                    placeholder="Contoh: Gedung Parkir Terpadu 3 Lantai / Gedung Paviliun VVIP / Area Kantin & Pujasera Kemitraan..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-blue-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-semibold">
            </div>
        </div>
    </div>
</div>
