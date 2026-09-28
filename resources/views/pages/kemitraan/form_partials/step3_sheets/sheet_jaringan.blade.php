<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: JALAN, IRIGASI & JARINGAN (KIB D / AKUN 1.5.2.xx.004) -->
<!-- ========================================================================= -->
<div x-show="isJaringan" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-teal-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-teal-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-base border border-teal-500/30">🌐</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                        Spesifikasi Teknis Jalan, Irigasi &amp; Jaringan (KIB D)
                    </h3>
                    <p class="text-[11px] text-slate-400">Rincian konstruksi jalan/jaringan, dimensi panjang/lebar, luas total, dan lokasi instalasi di RSUD.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-teal-400 bg-teal-950/50 px-2.5 py-1 rounded-lg border border-teal-500/30 shrink-0">
                Format KIB D
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Konstruksi Jaringan -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Konstruksi Jaringan / Jalan <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.jaringan_konstruksi"
                    placeholder="Contoh: Aspal Hotmix / Paving K-300 / Pipa HDPE Gas Medis / Fiber Optic Bawah Tanah..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>

            <!-- Luas Total (m²) -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Luas Total (m²)
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" x-model.number="formData.jaringan_luas"
                        placeholder="Contoh: 1200"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono font-bold focus:outline-none">
                    <span class="absolute right-3.5 top-2.5 text-slate-400 text-xs font-mono font-bold">m²</span>
                </div>
            </div>

            <!-- Panjang (Meter) -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Panjang Bentang (Meter)
                </label>
                <input type="number" step="0.01" min="0" x-model.number="formData.jaringan_panjang"
                    placeholder="Contoh: 350"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>

            <!-- Lebar (Meter) -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Lebar (Meter)
                </label>
                <input type="number" step="0.01" min="0" x-model.number="formData.jaringan_lebar"
                    placeholder="Contoh: 4.5"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>

            <!-- Nomor Dokumen Kontrak Teknis -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Dokumen / Kontrak Teknis
                </label>
                <input type="text" x-model="formData.jaringan_dokumen_no"
                    placeholder="Contoh: DOK-JAR/2026/01"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>

            <!-- Tanggal Dokumen Kontrak -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal Dokumen Kontrak
                </label>
                <input type="text" x-datepicker x-model="formData.jaringan_dokumen_tgl"
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>
    </div>
</div>
