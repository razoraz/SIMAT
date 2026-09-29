<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: GEDUNG & BANGUNAN (KIB C / AKUN 1.3.3 / PELIMPAHAN)    -->
<!-- MULTI-ITEM REPEATER GEDUNG & BANGUNAN BMD                                 -->
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
                            Spesifikasi Fisik Gedung &amp; Bangunan Pelimpahan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-mono font-bold text-[10px] border border-blue-500/40">
                            KIB C · Akun 1.3.3
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian konstruksi gedung, luas lantai, izin IMB/PBG, status kepemilikan tanah, serta peruntukan operasional di RSUD.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-blue-400 bg-blue-950/60 px-3 py-1.5 rounded-xl border border-blue-500/30 shadow-sm">
                    Total: <span x-text="formData.gedung_items ? formData.gedung_items.length : 1"></span> Bangunan
                </span>
            </div>
        </div>

        <!-- List Kartu Gedung (Repeater) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.gedung_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-blue-500/30 hover:border-blue-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-blue-500/20 text-blue-300 font-mono font-extrabold text-xs border border-blue-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🏢 Gedung / Bangunan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.gedung_nama_barang" x-text="item.gedung_nama_barang"></span>
                            <span class="text-[11px] text-slate-400 font-mono" x-show="item.gedung_luas_m2">
                                • Luas: <strong class="text-indigo-300" x-text="(item.gedung_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></strong>
                            </span>
                        </div>

                        <button type="button" 
                                x-show="formData.gedung_items.length > 1" 
                                @click="removeGedungItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Gedung Ini</span>
                        </button>
                    </div>

                    <!-- Form Grid Gedung -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Kolom Kiri: Konstruksi, Luas & Tingkat -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-blue-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                🏗️ Konstruksi &amp; Dimensi Bangunan
                            </span>

                            <!-- Nama Gedung -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama / Sebutan Bangunan Gedung</label>
                                <input type="text" x-model="item.gedung_nama_barang"
                                    placeholder="Contoh: Gedung Rawat Inap Terpadu Paviliun Mawar"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Luas Lantai & Jumlah Lantai / Bertingkat -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas Bangunan (m²)</label>
                                    <input type="number" step="0.01" min="0" x-model.number="item.gedung_luas_m2" @input="syncTotalsFromItems()"
                                        placeholder="0"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-indigo-300 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat</label>
                                    <select x-model="item.gedung_bertingkat" class="w-full bg-slate-950 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        <option value="Tidak">Tidak (1 Lantai)</option>
                                        <option value="Bertingkat 2 Lantai">Bertingkat 2 Lantai</option>
                                        <option value="Bertingkat 3 Lantai">Bertingkat 3 Lantai</option>
                                        <option value="Bertingkat > 3 Lantai">Bertingkat &gt; 3 Lantai</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Konstruksi Beton & Status Tanah -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Beton</label>
                                    <select x-model="item.gedung_beton" class="w-full bg-slate-950 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        <option value="Beton">Beton Bertulang</option>
                                        <option value="Bukan Beton">Bukan Beton / Kayu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Penguasaan Tanah</label>
                                    <input type="text" x-model="item.gedung_status_tanah"
                                        placeholder="Tanah Pemkab Bondowoso"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-blue-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Dokumen IMB/PBG & Nilai BMD -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-indigo-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                📄 Izin Bangunan &amp; Nilai Perolehan BMD
                            </span>

                            <!-- Dokumen PBG / IMB -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nomor Izin Gedung (PBG / IMB)</label>
                                <input type="text" x-model="item.gedung_dokumen_no"
                                    placeholder="Nomor IMB / PBG Gedung"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>

                            <!-- Kuantitas & Satuan -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Bangunan (Qty)</label>
                                    <input type="number" min="1" x-model.number="item.gedung_jumlah_bangunan" @input="syncTotalsFromItems()"
                                        placeholder="1"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-center text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                    <input type="text" x-model="item.gedung_satuan"
                                        placeholder="Gedung / Unit"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-center text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Nilai Perolehan BMD per Gedung -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nilai Perolehan BMD Gedung Ini (Rp)</span>
                                    <span class="text-emerald-400 font-mono text-[9px]">Sesuai BAMB / SKPD Asal</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold font-mono">Rp</span>
                                    <input type="text"
                                        :value="item.gedung_nilai_satuan ? Number(item.gedung_nilai_satuan).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            item.gedung_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                            syncTotalsFromItems();
                                        "
                                        placeholder="0"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 pl-9 text-xs font-mono font-bold text-emerald-300 focus:outline-none">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>
        </div>

        <!-- Tombol Tambah Gedung -->
        <div class="pt-2 flex justify-start">
            <button type="button" @click="addGedungItem()"
                class="px-4 py-2.5 rounded-2xl bg-blue-500/15 hover:bg-blue-500/25 text-blue-300 border border-blue-500/40 text-xs font-bold transition-all flex items-center space-x-2 shadow-lg shadow-blue-950/40 cursor-pointer">
                <span class="text-base">➕</span>
                <span>Tambah Bangunan Gedung Pelimpahan Lainnya</span>
            </button>
        </div>

    </div>
</div>
