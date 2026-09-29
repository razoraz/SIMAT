<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: ASET TETAP LAINNYA (KIB E / AKUN 1.3.5 / PELIMPAHAN)   -->
<!-- MULTI-ITEM REPEATER BUKU, KESENIAN, HEWAN/TANAMAN, SOFTWARE BMD          -->
<!-- ========================================================================= -->
<div x-show="isLainnya" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB E -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-fuchsia-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-fuchsia-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB E -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-fuchsia-500/20 text-fuchsia-400 flex items-center justify-center text-lg border border-fuchsia-500/30 shadow-inner">📦</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Aset Tetap Lainnya Pelimpahan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-fuchsia-500/20 text-fuchsia-300 font-mono font-bold text-[10px] border border-fuchsia-500/40">
                            KIB E · Akun 1.3.5
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian buku perpustakaan/medis, barang bercorak kesenian/kebudayaan, tanaman/hewan, atau lisensi software SIMRS.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-fuchsia-400 bg-fuchsia-950/60 px-3 py-1.5 rounded-xl border border-fuchsia-500/30 shadow-sm">
                    Total: <span x-text="formData.lainnya_items ? formData.lainnya_items.length : 1"></span> Item
                </span>
            </div>
        </div>

        <!-- List Kartu Lainnya (Repeater) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.lainnya_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-fuchsia-500/30 hover:border-fuchsia-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-fuchsia-500/20 text-fuchsia-300 font-mono font-extrabold text-xs border border-fuchsia-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>📦 Item #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.lainnya_judul" x-text="item.lainnya_judul"></span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Qty: <strong class="text-indigo-300" x-text="(item.lainnya_jumlah || 1) + ' ' + (item.lainnya_satuan || 'Eks / Buah')"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(item))"></strong>
                            </span>
                        </div>

                        <button type="button" 
                                x-show="formData.lainnya_items.length > 1" 
                                @click="removeLainnyaItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Item Ini</span>
                        </button>
                    </div>

                    <!-- Form Grid Lainnya -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Kolom Kiri: Identitas Karya/Item -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-fuchsia-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                📦 Identitas Karya, Buku &amp; Spesifikasi
                            </span>

                            <!-- Judul / Nama Item -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul Buku / Nama Karya Seni / Lisensi Software</label>
                                <input type="text" x-model="item.lainnya_judul"
                                    placeholder="Contoh: Buku Farmakope Indonesia Edisi VI / Lukisan Hiasan Dinding"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-fuchsia-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Jenis / Kategori -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kategori Aset Tetap Lainnya</label>
                                <select x-model="item.lainnya_jenis" class="w-full bg-slate-950 border border-slate-700 focus:border-fuchsia-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Buku / Kepustakaan Medis">Buku / Kepustakaan Medis</option>
                                    <option value="Barang Bercorak Kesenian">Barang Bercorak Kesenian / Budaya</option>
                                    <option value="Hewan / Ternak / Tanaman">Hewan / Ternak / Tanaman Hias</option>
                                    <option value="Aset Tak Berwujud / Software">Aset Tak Berwujud (ATB) / Software Lisensi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <!-- Asal Daerah / Pengarang / Pencipta -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Pengarang</label>
                                    <input type="text" x-model="item.lainnya_pencipta"
                                        placeholder="Kemenkes RI / Artis..."
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-fuchsia-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan / Spesifikasi</label>
                                    <input type="text" x-model="item.lainnya_spesifikasi"
                                        placeholder="Kertas Lux, Kanvas, Digital..."
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-fuchsia-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Volume & Nilai BMD -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <span class="text-xs font-bold text-indigo-400 block uppercase tracking-wider border-b border-slate-800 pb-1.5">
                                🔢 Volume, Satuan &amp; Nilai Perolehan BMD
                            </span>

                            <!-- Kuantitas & Satuan -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kuantitas (Volume)</label>
                                    <input type="number" min="1" x-model.number="item.lainnya_jumlah" @input="syncTotalsFromItems()"
                                        placeholder="1"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-center text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                    <input type="text" x-model="item.lainnya_satuan"
                                        placeholder="Eksemplar / Buah / Lisensi"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-center text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Nilai Satuan BMD -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nilai Perolehan BMD Satuan (Rp)</span>
                                    <span class="text-emerald-400 font-mono text-[9px]">Sesuai BAMB / SKPD Asal</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold font-mono">Rp</span>
                                    <input type="text"
                                        :value="item.lainnya_nilai_satuan ? Number(item.lainnya_nilai_satuan).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            item.lainnya_nilai_satuan = raw ? parseInt(raw, 10) : 0;
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

        <!-- Tombol Tambah Lainnya -->
        <div class="pt-2 flex justify-start">
            <button type="button" @click="addLainnyaItem()"
                class="px-4 py-2.5 rounded-2xl bg-fuchsia-500/15 hover:bg-fuchsia-500/25 text-fuchsia-300 border border-fuchsia-500/40 text-xs font-bold transition-all flex items-center space-x-2 shadow-lg shadow-fuchsia-950/40 cursor-pointer">
                <span class="text-base">➕</span>
                <span>Tambah Item Aset Tetap Lainnya Pelimpahan</span>
            </button>
        </div>

    </div>
</div>
