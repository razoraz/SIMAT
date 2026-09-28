<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: PERALATAN & MESIN (KIB B / AKUN 1.5.2.01.01.xx.002)   -->
<!-- SAMAKAN PERSIS DENGAN FORMAT LANGKAH 3 BELANJA MODAL (ASTAP)              -->
<!-- ========================================================================= -->
<div x-show="isMesin" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB B -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-purple-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB B -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg border border-purple-500/30 shadow-inner">⚙️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Teknis Peralatan &amp; Mesin / Alkes
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40">
                            KIB B
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian merk pabrikan, tipe model, nomor seri (SN), ukuran kapasitas, bahan, kondisi, dan legalitas kendaraan.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-purple-400 bg-purple-950/60 px-3 py-1.5 rounded-xl border border-purple-500/30 shrink-0 shadow-sm">
                Format KIB B (Peralatan &amp; Mesin)
            </span>
        </div>

        <!-- Grid 2 Kolom Spesifikasi Persis Seperti Langkah 3 Belanja Modal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- 1. Merk, Type, Ukuran & Tahun Pembuatan (Persis Belanja Modal KIB B) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>⚙️ Merk, Type &amp; Ukuran:</span>
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Identitas Fisik</span>
                </div>

                <!-- Merk Barang -->
                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Merk / Brand Pabrikan <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" x-model="formData.merk"
                        placeholder="Contoh: Siemens / Mindray / Roche / Sysmex / Daikin / Dell"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Type / Model -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Type / Model Barang <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.type"
                            placeholder="Contoh: SOMATOM go.Now / Cobas c311 / OptiPlex"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                    </div>

                    <!-- Ukuran / Kapasitas -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Ukuran / Kapasitas Teknis
                        </label>
                        <input type="text" x-model="formData.ukuran"
                            placeholder="Contoh: 128 Slice / 300 Test/Jam / 2 PK / 16GB"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Tahun Pembuatan Pabrik -->
                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Tahun Pembuatan Pabrik
                    </label>
                    <input type="number" min="1990" max="2100" x-model.number="formData.tahun_pembuatan"
                        placeholder="Contoh: 2025"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                </div>
            </div>

            <!-- 2. No Pabrik, Bahan, Kondisi & Kendaraan (Persis Belanja Modal KIB B) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>🏷️ No Pabrik, Kendaraan, Bahan &amp; Kondisi:</span>
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Serial &amp; Spesifikasi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Nomor Pabrik / SN -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            No Pabrik / Serial Number (SN)
                        </label>
                        <input type="text" x-model="formData.no_pabrik"
                            placeholder="SN-RAD-2026-88192"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                    </div>

                    <!-- Bahan Pembuatan -->
                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Bahan Pembuatan Utama
                        </label>
                        <input type="text" x-model="formData.bahan"
                            placeholder="Logam &amp; Elektronik / Stainless Steel"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Kondisi Barang (Dropdown Standar Simat: Baik, Kurang Baik, Rusak Berat) -->
                <div>
                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                        Kondisi Fisik Barang Saat Diterima <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.kondisi"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                        <option value="Baik">🟢 Baik (B) &mdash; Siap Operasional</option>
                        <option value="Kurang Baik">🟡 Kurang Baik (KB) &mdash; Perlu Kalibrasi / Setting</option>
                        <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                    </select>
                </div>

                <!-- Detail Kendaraan Bermotor (Hanya jika alat berupa Ambulans / Mobil Operasional Kemitraan) -->
                <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-1.5 transition-all">
                    <span class="text-[9.5px] font-bold text-slate-400 block uppercase tracking-wider">
                        🚗 Legality Kendaraan (Khusus Ambulans / Kendaraan Bermotor):
                    </span>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-slate-500 text-[9px] mb-0.5">No Rangka</label>
                            <input type="text" x-model="formData.no_rangka" placeholder="MH1JM..."
                                class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-500 text-[9px] mb-0.5">No Mesin</label>
                            <input type="text" x-model="formData.no_mesin" placeholder="JM51E..."
                                class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-500 text-[9px] mb-0.5">No BPKB</label>
                            <input type="text" x-model="formData.no_bpkb" placeholder="BPKB-88..."
                                class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-500 text-[9px] mb-0.5">No Polisi / Plat</label>
                            <input type="text" x-model="formData.no_polisi" placeholder="P 1080 RS"
                                class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-amber-300 font-mono font-bold focus:border-cyan-500 focus:outline-none">
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
