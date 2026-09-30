<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: TANAH (KIB A / AKUN 1.3.1 / PELIMPAHAN SKPD)           -->
<!-- MULTI-ITEM REPEATER BIDANG TANAH BMD                                      -->
<!-- ========================================================================= -->
<div x-show="isTanah" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB A -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-emerald-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB A -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/30 shadow-inner">🌾</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik &amp; Legalitas Tanah Pelimpahan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/40">
                            KIB A · Akun 1.3.1
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Dapat mencatat satu atau beberapa bidang tanah pelimpahan SKPD lengkap dengan status hak, sertifikat, luas, dan letak lokasi.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-950/60 px-3 py-1.5 rounded-xl border border-emerald-500/30 shadow-sm">
                    Total: <span x-text="formData.tanah_items ? formData.tanah_items.length : 1"></span> Bidang Tanah
                </span>
            </div>
        </div>

        <!-- List Kartu Bidang Tanah (Repeater) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.tanah_nama_barang" x-text="item.tanah_nama_barang"></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg border font-mono"
                                :class="(item.tanah_kondisi === 'Baik' || !item.tanah_kondisi) ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : (item.tanah_kondisi === 'Rusak Ringan' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40')"
                                x-text="'• Kondisi: ' + (item.tanah_kondisi || 'Baik')">
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono" x-show="item.tanah_luas_m2">
                                • Luas: <strong class="text-indigo-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></strong>
                            </span>
                        </div>

                        <button type="button" 
                                x-show="formData.tanah_items.length > 1" 
                                @click="removeTanahItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Bidang Ini</span>
                        </button>
                    </div>

                    <!-- Form Grid Bidang Tanah -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Kolom Kiri: Luas, Status Hak & Sertifikat -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                📜 Legalitas Sertifikat &amp; Hak Tanah
                            </span>

                            <!-- Nama Identitas Bidang Tanah -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama / Sebutan Bidang Tanah</label>
                                <input type="text" x-model="item.tanah_nama_barang"
                                    placeholder="Contoh: Tanah Bangunan Pelayanan Medis RSUD (Eks Dinkes)"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Hak Tanah & Luas -->
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Hak</label>
                                    <select x-model="item.tanah_hak" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        <option value="Hak Pakai">Hak Pakai</option>
                                        <option value="Hak Milik">Hak Milik</option>
                                        <option value="Hak Pengelolaan">HPL</option>
                                        <option value="Hak Guna Bangunan">HGB</option>
                                        <option value="Belum Bersertifikat">Belum</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas (m²)</label>
                                    <input type="number" step="0.01" min="0" x-model.number="item.tanah_luas_m2" @input="syncTotalsFromItems()"
                                        placeholder="0"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-indigo-300 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi</label>
                                    <select x-model="item.tanah_kondisi" @change="syncTotalsFromItems()"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-bold cursor-pointer">
                                        <option value="Baik">🟢 Baik</option>
                                        <option value="Rusak Ringan">🟡 Kurang</option>
                                        <option value="Rusak Berat">🔴 Rusak</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Nomor Sertifikat & Tanggal -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nomor Sertifikat</label>
                                    <input type="text" x-model="item.tanah_sertifikat_no"
                                        placeholder="No. Sertifikat Hak Pakai"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tanggal Sertifikat</label>
                                    <input type="date" x-model="item.tanah_sertifikat_tgl"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Penggunaan, Batas & Taksiran Nilai -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-indigo-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                📍 Penggunaan, Lokasi &amp; Nilai Fisik
                            </span>

                            <!-- Peruntukan / Penggunaan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Penggunaan / Peruntukan Lahan</label>
                                <input type="text" x-model="item.tanah_penggunaan"
                                    placeholder="Contoh: Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Alamat / Letak Lahan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Alamat / Lokasi Bidang Tanah</label>
                                <input type="text" x-model="item.tanah_alamat"
                                    placeholder="Jl. Piere Tendean No. 1, Bondowoso"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Nilai Fisik Perolehan BMD per Bidang (Rp) -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nilai Perolehan BMD Bidang Ini (Rp)</span>
                                    <span class="text-emerald-400 font-mono text-[9px]">Sesuai BAMB / SKPD Asal</span>
                                </label>
                                <div class="flex items-center rounded-xl bg-slate-950 border border-slate-700 focus-within:border-emerald-400 focus-within:ring-1 focus-within:ring-emerald-400/30 overflow-hidden transition-all">
                                    <span class="px-3 py-2 bg-slate-900 border-r border-slate-800 text-slate-400 text-xs font-bold font-mono select-none flex items-center justify-center">
                                        Rp
                                    </span>
                                    <input type="text"
                                        :value="item.tanah_nilai_fisik ? Number(item.tanah_nilai_fisik).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            item.tanah_nilai_fisik = raw ? parseInt(raw, 10) : 0;
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

        <!-- Tombol Tambah Bidang Tanah -->
        <div class="pt-2 flex justify-start">
            <button type="button" @click="addTanahItem()"
                class="px-4 py-2.5 rounded-2xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/40 text-xs font-bold transition-all flex items-center space-x-2 shadow-lg shadow-emerald-950/40 cursor-pointer">
                <span class="text-base">➕</span>
                <span>Tambah Bidang Tanah Pelimpahan Lainnya</span>
            </button>
        </div>

    </div>
</div>
