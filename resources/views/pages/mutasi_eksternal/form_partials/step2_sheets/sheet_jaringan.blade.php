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
                            Spesifikasi Fisik Jalan, Irigasi &amp; Jaringan Pelimpahan
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
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg border font-mono"
                                :class="(item.jaringan_kondisi === 'Baik' || !item.jaringan_kondisi) ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : (item.jaringan_kondisi === 'Rusak Ringan' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40')"
                                x-text="'• Kondisi: ' + (item.jaringan_kondisi || 'Baik')">
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono" x-show="item.jaringan_luas_m2">
                                • Luas: <strong class="text-indigo-300" x-text="(item.jaringan_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
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

                    <!-- Form Grid Jaringan (Dibuat Sama Persis dengan KIB C Gedung) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Kolom Kiri: Konstruksi, Luas & Tingkat -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-teal-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                🏗️ Konstruksi &amp; Dimensi Jaringan
                            </span>

                            <!-- Nama Ruas / Jaringan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama / Sebutan Ruas / Instalasi Jaringan</label>
                                <input type="text" x-model="item.jaringan_nama_barang"
                                    placeholder="Contoh: Jaringan Pipa Air Medis / Saluran Pembuangan RSUD"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Luas Jaringan & Konstruksi Bertingkat / Layang -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas Jaringan (m²)</label>
                                    <input type="number" step="0.01" min="0" x-model.number="item.jaringan_luas_m2" @input="syncTotalsFromItems()"
                                        placeholder="0"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-indigo-300 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat</label>
                                    <select x-model="item.jaringan_bertingkat" class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        <option value="Tidak">Tidak (1 Jalur / Permukaan)</option>
                                        <option value="Bertingkat 2 Lantai">Bertingkat / Konstruksi Layang</option>
                                        <option value="Bertingkat 3 Lantai">Bertingkat 3 Lantai / Tower</option>
                                        <option value="Bawah Tanah">Bawah Tanah / Terpendam</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Kondisi Jaringan & Konstruksi Beton -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                        <span>Kondisi Jaringan <span class="text-rose-400">*</span></span>
                                    </label>
                                    <select x-model="item.jaringan_kondisi" @change="syncTotalsFromItems()"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-bold cursor-pointer">
                                        <option value="Baik">🟢 Baik</option>
                                        <option value="Rusak Ringan">🟡 Rusak Ringan</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Beton</label>
                                    <select x-model="item.jaringan_beton" class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        <option value="Beton">Beton Bertulang</option>
                                        <option value="Aspal">Aspal Hotmix</option>
                                        <option value="Pipa / HDPE">Pipa / HDPE</option>
                                        <option value="Bukan Beton">Bukan Beton / Kayu / Kabel</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Status Tanah -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Penguasaan Tanah</label>
                                <input type="text" x-model="item.jaringan_status_tanah"
                                    placeholder="Tanah Pemkab Bondowoso"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>

                        <!-- Kolom Kanan: Dokumen Izin / Kontrak & Nilai BMD -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-indigo-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                📄 Izin / Kontrak &amp; Nilai Perolehan BMD
                            </span>

                            <!-- Dokumen Kontrak / Izin Jaringan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nomor Dokumen Kontrak / Izin Jaringan</label>
                                <input type="text" x-model="item.jaringan_dokumen_no"
                                    placeholder="Nomor Dokumen Kontrak / Berita Acara Jaringan"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>

                            <!-- Kuantitas & Satuan -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Ruas (Qty)</label>
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

                            <!-- Nilai Perolehan BMD per Ruas -->
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
