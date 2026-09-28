<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: PERALATAN & MESIN (KIB B / AKUN 1.5.2.01.01.xx.002)   -->
<!-- ========================================================================= -->
<div x-show="isMesin" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-cyan-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-base border border-cyan-500/30">⚙️</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                        Spesifikasi Teknis Peralatan &amp; Mesin / Alkes (KIB B)
                    </h3>
                    <p class="text-[11px] text-slate-400">Rincian merk pabrikan, tipe model, nomor seri, dan bahan peralatan medis atau mesin kemitraan.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-cyan-400 bg-cyan-950/50 px-2.5 py-1 rounded-lg border border-cyan-500/30 shrink-0">
                Format KIB B
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Merk / Brand Pabrikan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Merk / Brand Pabrikan <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.merk"
                    placeholder="Contoh: Roche / Sysmex / Fresenius / Siemens / GE..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>

            <!-- Tipe / Model Barang -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tipe / Model Barang <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.type"
                    placeholder="Contoh: Cobas c311 / 4008S / XN-1000 / Somatom Go..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>

            <!-- Nomor Seri Pabrik (Serial Number) -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Pabrik / Seri (Serial Number)
                </label>
                <input type="text" x-model="formData.no_pabrik"
                    placeholder="Contoh: SN-2026-X88921 / 0842-192..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>

            <!-- Ukuran / Kapasitas -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Ukuran / Kapasitas Teknis
                </label>
                <input type="text" x-model="formData.ukuran"
                    placeholder="Contoh: 300 Test/Jam / 500 VA / 120 x 80 cm / 2000 CC..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- Bahan / Material Pembuatan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Bahan / Material Utama
                </label>
                <input type="text" x-model="formData.bahan"
                    placeholder="Contoh: Stainless Steel Medis / Logam / Plastik ABS..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- Tahun Pembuatan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tahun Pembuatan Pabrik
                </label>
                <input type="number" min="1990" max="2100" x-model.number="formData.tahun_pembuatan"
                    placeholder="Contoh: 2025"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>

            <!-- Identitas Khusus Kendaraan (Opsional jika alat berupa ambulans / kendaraan operasional) -->
            <div class="sm:col-span-2 lg:col-span-3 pt-2 border-t border-slate-800/80">
                <span class="text-[11px] font-bold text-slate-400 block mb-2">🚗 Identitas Kendaraan Bermotor (Khusus Ambulans / Mobil Operasional Kemitraan):</span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1 font-mono">No. Rangka</label>
                        <input type="text" x-model="formData.no_rangka" placeholder="MH3..."
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1 font-mono">No. Mesin</label>
                        <input type="text" x-model="formData.no_mesin" placeholder="1TR-FE..."
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1 font-mono">No. Polisi / Plat</label>
                        <input type="text" x-model="formData.no_polisi" placeholder="P 1080 RS"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-cyan-400">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
