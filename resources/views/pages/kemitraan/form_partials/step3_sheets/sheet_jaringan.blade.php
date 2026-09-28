<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: JALAN, IRIGASI & JARINGAN (KIB D / AKUN 1.5.2.xx.004) -->
<!-- SAMAKAN PERSIS DENGAN FORMAT LANGKAH 3 BELANJA MODAL (ASTAP)              -->
<!-- ========================================================================= -->
<div x-show="isJaringan" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB D -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-teal-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB D -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg border border-teal-500/30 shadow-inner">🛣️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Teknis Jalan, Irigasi &amp; Jaringan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 font-mono font-bold text-[10px] border border-teal-500/40">
                            KIB D
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian konstruksi jalan/jaringan, dimensi bentang (panjang &amp; lebar), luas total, kondisi, dan dokumen kontrak teknis.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-teal-400 bg-teal-950/60 px-3 py-1.5 rounded-xl border border-teal-500/30 shrink-0 shadow-sm">
                Format KIB D (Jalan &amp; Jaringan)
            </span>
        </div>

        <!-- Grid 2 Kolom Spesifikasi Persis Seperti Langkah 3 Belanja Modal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- 1. Konstruksi & Dimensi Jaringan (Persis Belanja Modal KIB D) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>🏗️ Konstruksi &amp; Dimensi:</span>
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Fisik Jaringan</span>
                </div>

                <!-- Konstruksi Jaringan -->
                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Konstruksi Jaringan / Jalan <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" x-model="formData.jaringan_konstruksi"
                        placeholder="Contoh: Aspal Hotmix / Paving K-300 / Pipa HDPE Gas Medis / Fiber Optic"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                </div>

                <!-- Dimensi 3 Kolom: Panjang, Lebar, Luas -->
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Panjang (M)</label>
                        <input type="number" step="0.01" min="0" x-model.number="formData.jaringan_panjang"
                            @input="
                                if (formData.jaringan_panjang && formData.jaringan_lebar) {
                                    formData.jaringan_luas = parseFloat(((parseFloat(formData.jaringan_panjang) || 0) * (parseFloat(formData.jaringan_lebar) || 0)).toFixed(2));
                                }
                            "
                            placeholder="350"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Lebar (M)</label>
                        <input type="number" step="0.01" min="0" x-model.number="formData.jaringan_lebar"
                            @input="
                                if (formData.jaringan_panjang && formData.jaringan_lebar) {
                                    formData.jaringan_luas = parseFloat(((parseFloat(formData.jaringan_panjang) || 0) * (parseFloat(formData.jaringan_lebar) || 0)).toFixed(2));
                                }
                            "
                            placeholder="4.5"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas (m²)</label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" x-model.number="formData.jaringan_luas"
                                placeholder="1575"
                                class="w-full bg-slate-950 border border-teal-500/40 rounded-xl px-2 py-2 text-xs text-teal-300 font-mono font-bold focus:border-teal-400 focus:outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Kondisi Jaringan -->
                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Kondisi Jaringan / Jalan Saat Diterima <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.jaringan_kondisi"
                        @change="formData.kondisi = formData.jaringan_kondisi"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500 focus:outline-none transition-all">
                        <option value="Baik">🟢 Baik (B) &mdash; Operasional Normal</option>
                        <option value="Kurang Baik">🟡 Kurang Baik (KB) &mdash; Perlu Perawatan</option>
                        <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                    </select>
                </div>
            </div>

            <!-- 2. Dokumen / Kontrak Teknis & Lokasi (Persis Belanja Modal KIB D) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📜 Dokumen / Kontrak Teknis:</span>
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Administrasi Teknis</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Nomor Dokumen -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Nomor Dokumen / Kontrak Teknis
                        </label>
                        <input type="text" x-model="formData.jaringan_dokumen_no"
                            placeholder="Contoh: DOK-JAR/2026/01"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                    </div>

                    <!-- Tanggal Dokumen -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Tanggal Dokumen Kontrak
                        </label>
                        <input type="text" x-datepicker x-model="formData.jaringan_dokumen_tgl"
                            placeholder="dd/mm/yyyy"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Lokasi Rute / Titik Jaringan -->
                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Lokasi / Rute Penempatan Jaringan di RSUD
                    </label>
                    <input type="text" x-model="formData.alamat_barang"
                        placeholder="Contoh: Jalur Instalasi Sentral Gas Medis Paviliun Barat - Timur RSUD"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                </div>
            </div>

        </div>

    </div>

</div>
