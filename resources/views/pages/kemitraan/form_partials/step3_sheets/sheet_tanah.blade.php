<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: TANAH (KIB A / AKUN 1.5.2.01.01.xx.001)               -->
<!-- SAMAKAN PERSIS DENGAN FORMAT LANGKAH 3 BELANJA MODAL (ASTAP)              -->
<!-- ========================================================================= -->
<div x-show="isTanah" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB A -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-emerald-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB A -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/30 shadow-inner">🌾</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik &amp; Legalitas Tanah
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/40">
                            KIB A
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian status hak tanah, dokumen sertifikat resmi, kondisi, luas bidang, batas wilayah, dan lokasi fisik lahan.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-950/60 px-3 py-1.5 rounded-xl border border-emerald-500/30 shrink-0 shadow-sm">
                Format KIB A (Tanah)
            </span>
        </div>

        <!-- Grid 2 Kolom Spesifikasi Persis Seperti Langkah 3 Belanja Modal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- 1. Status Tanah & Sertifikat -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📜 Status Tanah &amp; Sertifikat:</span>
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Legalitas Lahan</span>
                </div>

                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Hak / Status Penguasaan Tanah <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.tanah_hak"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                        <option value="Hak Pakai">Hak Pakai (Pemda / RSUD)</option>
                        <option value="Hak Pengelolaan">Hak Pengelolaan (HPL)</option>
                        <option value="Hak Milik Pemda">Hak Milik Pemerintah Kabupaten</option>
                        <option value="Tanah Adat / Ulayat">Tanah Adat / Ulayat</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Nomor Sertifikat Lahan
                        </label>
                        <input type="text" x-model="formData.tanah_sertifikat_no"
                            placeholder="Contoh: HP-108/Bondowoso/1998"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Tanggal Terbit Sertifikat
                        </label>
                        <input type="text" x-datepicker x-model="formData.tanah_sertifikat_tgl"
                            placeholder="dd/mm/yyyy"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- 2. Kondisi, Penggunaan & Luas Bidang -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📐 Kondisi, Penggunaan &amp; Luas:</span>
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Fisik &amp; Dimensi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Kondisi Lahan Tanah <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="formData.tanah_kondisi"
                            @change="formData.kondisi = formData.tanah_kondisi"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                            <option value="Baik">🟢 Baik (Siap Digunakan)</option>
                            <option value="Kurang Baik">🟡 Kurang Baik (Perlu Pematangan)</option>
                            <option value="Rusak Berat">🔴 Rusak Berat (Rawa / Longsor)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Luas Tanah (m²) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" x-model.number="formData.tanah_luas_m2"
                                @input="if(!formData.jumlah_volume || formData.jumlah_volume <= 1) formData.jumlah_volume = formData.tanah_luas_m2"
                                placeholder="Contoh: 1500"
                                class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-3 py-2 text-xs text-emerald-300 font-mono font-bold focus:border-emerald-400 focus:outline-none transition-all">
                            <span class="absolute right-3 top-2 text-[10px] font-mono font-bold text-slate-500">m²</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Penggunaan / Peruntukan Lahan <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" x-model="formData.tanah_penggunaan"
                        placeholder="Contoh: Area Parkir Terpadu / Gedung Paviliun Rawat Inap / Pujasera RSUD"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all font-medium">
                </div>
            </div>

            <!-- 3. Batas-Batas Bidang Tanah -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2 md:col-span-2 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                    <span class="text-xs font-bold text-teal-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>🧭 Batas-Batas Bidang Tanah:</span>
                    </span>
                    <span class="text-[9px] text-slate-400">Patok batas 4 penjuru mata angin</span>
                </div>
                <input type="text" x-model="formData.tanah_batas"
                    placeholder="Contoh: Utara: Jl. Piere Tendean, Timur: Pemukiman Warga, Selatan: Poliklinik Rawat Jalan, Barat: Saluran Air"
                    class="w-full bg-slate-950 border border-slate-700 hover:border-teal-500/60 focus:border-teal-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-all">
            </div>

            <!-- 4. Letak / Alamat Fisik Tanah -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2 md:col-span-2 shadow-md">
                <div class="flex items-center justify-between border-b border-amber-500/20 pb-1.5">
                    <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📍 Letak / Alamat Tanah &amp; Lokasi Fisik:</span>
                    </label>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik Lahan</span>
                </div>
                <input type="text" x-model="formData.tanah_alamat"
                    @input="if(!formData.alamat_barang || formData.alamat_barang.includes('RSUD')) formData.alamat_barang = formData.tanah_alamat"
                    placeholder="Contoh: Jl. Piere Tendean No. 1, Kelurahan Badean, Kec. Bondowoso (Area Kompleks RSUD Dr. H. Koesnandi)"
                    class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none transition-all">
            </div>

        </div>

    </div>

</div>
