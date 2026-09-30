<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: JALAN, IRIGASI & JARINGAN (KIB D / AKUN 1.3.4)         -->
<!-- MULTI-ITEM REPEATER JALAN, INSTALASI AIR/LISTRIK/JARINGAN BMD             -->
<!-- ========================================================================= -->
<div x-show="isJaringan" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB D -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-teal-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB D -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg border border-teal-500/30 shadow-inner">🛣️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Jalan, Irigasi &amp; Jaringan Pelimpahan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 font-mono font-bold text-[10px] border border-teal-500/40">
                            KIB D · Akun 1.3.4
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian jalan lingkungan RSUD, jembatan, instalasi pemipaan air, instalasi kabel listrik, dan jaringan telekomunikasi/IT.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-teal-400 bg-teal-950/60 px-3 py-1.5 rounded-xl border border-teal-500/30 shadow-sm">
                    Total: <span x-text="formData.jaringan_items ? formData.jaringan_items.length : 1"></span> Ruas / Instalasi
                </span>
            </div>
        </div>

        <!-- List Kartu Jaringan (Repeater) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.jaringan_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-teal-500/30 hover:border-teal-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-teal-500/20 text-teal-300 font-mono font-extrabold text-xs border border-teal-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🛣️ Ruas / Jaringan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.jaringan_nama_barang" x-text="item.jaringan_nama_barang"></span>
                            <span class="text-[11px] text-slate-400 font-mono" x-show="item.jaringan_panjang_m">
                                • Panjang: <strong class="text-indigo-300" x-text="(item.jaringan_panjang_m || 0).toLocaleString('id-ID') + ' m'"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(item))"></strong>
                            </span>
                        </div>

                        <button type="button" 
                                x-show="formData.jaringan_items.length > 1" 
                                @click="removeJaringanItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Ruas Ini</span>
                        </button>
                    </div>

                    <!-- Form Grid Jaringan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Kolom Kiri: Dimensi & Konstruksi -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-teal-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                🛣️ Identitas &amp; Dimensi Fisik Jaringan
                            </span>

                            <!-- Nama Ruas / Jaringan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Ruas / Instalasi Jaringan</label>
                                <input type="text" x-model="item.jaringan_nama_barang"
                                    placeholder="Contoh: Jaringan Pipa Air Medis / Saluran Pembuangan RSUD"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Konstruksi / Bahan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi / Spesifikasi Bahan</label>
                                <input type="text" x-model="item.jaringan_konstruksi"
                                    placeholder="Aspal Hotmix, Pipa HDPE, Kabel Tembaga NYY..."
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Panjang & Lebar -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Panjang (Meter)</label>
                                    <input type="number" step="0.01" min="0" x-model.number="item.jaringan_panjang_m"
                                        placeholder="0"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-indigo-300 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Lebar / Luas (m²)</label>
                                    <input type="number" step="0.01" min="0" x-model.number="item.jaringan_luas_m2"
                                        placeholder="0"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-indigo-300 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Lokasi & Nilai BMD -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-indigo-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                📍 Lokasi, Volume &amp; Nilai Perolehan BMD
                            </span>

                            <!-- Lokasi / Letak -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Titik Lokasi / Kawasan RSUD</label>
                                <input type="text" x-model="item.jaringan_lokasi"
                                    placeholder="Area Parkir Timur / Gedung Bedah Sentral"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Kuantitas & Satuan -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kuantitas (Volume)</label>
                                    <input type="number" min="1" x-model.number="item.jaringan_jumlah" @input="syncTotalsFromItems()"
                                        placeholder="1"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-center text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                    <input type="text" x-model="item.jaringan_satuan"
                                        placeholder="Ruas / Meter / Titik"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-center text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Nilai Satuan BMD -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nilai Perolehan BMD Ruas Ini (Rp)</span>
                                    <span class="text-emerald-400 font-mono text-[9px]">Sesuai BAMB / SKPD Asal</span>
                                </label>
                                <div class="flex items-center rounded-xl bg-slate-950 border border-slate-700 focus-within:border-emerald-400 focus-within:ring-1 focus-within:ring-emerald-400/30 overflow-hidden transition-all">
                                    <span class="px-3 py-2 bg-slate-900 border-r border-slate-800 text-slate-400 text-xs font-bold font-mono select-none flex items-center justify-center">
                                        Rp
                                    </span>
                                    <input type="text"
                                        :value="item.jaringan_nilai_satuan ? Number(item.jaringan_nilai_satuan).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            item.jaringan_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                            syncTotalsFromItems();
                                        "
                                        placeholder="0"
                                        class="w-full bg-transparent px-3 py-2 text-xs font-mono font-bold text-emerald-300 placeholder-slate-600 focus:outline-none">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>
        </div>

        <!-- Tombol Tambah Jaringan -->
        <div class="pt-2 flex justify-start">
            <button type="button" @click="addJaringanItem()"
                class="px-4 py-2.5 rounded-2xl bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 border border-teal-500/40 text-xs font-bold transition-all flex items-center space-x-2 shadow-lg shadow-teal-950/40 cursor-pointer">
                <span class="text-base">➕</span>
                <span>Tambah Ruas / Instalasi Jaringan Pelimpahan Lainnya</span>
            </button>
        </div>

    </div>
</div>
