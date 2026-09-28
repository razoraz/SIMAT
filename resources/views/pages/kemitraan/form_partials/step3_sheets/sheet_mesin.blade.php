<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: PERALATAN & MESIN (KIB B / AKUN 1.5.2.01.01.xx.002)   -->
<!-- REPEATER MULTI-ITEM PERSIS LANGKAH 3 BELANJA MODAL (ASTAP)                -->
<!-- ========================================================================= -->
<div x-show="isMesin" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB B Multi-Item Repeater -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-purple-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB B -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg border border-purple-500/30 shadow-inner">⚙️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Rincian Peralatan &amp; Mesin / Alkes Medis
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40">
                            KIB B
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bisa menambah 2 atau lebih barang dengan spesifikasi beda (Merk, Type, Ukuran, No Pabrik/SN, Bahan, Kondisi, Volume &amp; Taksiran Nilai).</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-purple-400 bg-purple-950/60 px-3 py-1.5 rounded-xl border border-purple-500/30 shadow-sm">
                    Total: <span x-text="formData.mesin_items ? formData.mesin_items.length : 1"></span> Barang / Unit
                </span>
            </div>
        </div>

        <!-- List Kartu Barang Peralatan & Mesin (Repeater Multi-Item) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.mesin_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <!-- Header Kartu Tiap Barang -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-purple-500/20 text-purple-300 font-mono font-extrabold text-xs border border-purple-500/40 flex items-center space-x-1.5">
                                <span>⚙️ Barang / Unit #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-[11px] text-slate-200 font-semibold" x-show="item.mesin_nama_barang || item.mesin_merk || item.mesin_type">
                                • <span x-text="item.mesin_nama_barang ? (item.mesin_nama_barang + ' • ') : ''"></span><span x-text="(item.mesin_merk || '') + ' ' + (item.mesin_type || '')"></span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Qty: <strong class="text-cyan-300" x-text="(item.mesin_jumlah_barang || 1) + ' ' + (item.mesin_satuan || 'Unit')"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getMesinSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Barang (Muncul jika > 1 item) -->
                        <button type="button" 
                                x-show="formData.mesin_items.length > 1" 
                                @click="removeMesinItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Barang Ini</span>
                        </button>
                    </div>

                    <!-- Grid Form Pengisian Spesifikasi Peralatan dan Mesin -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- 1. Spesifikasi Fisik (Nama, Merk, Type, Ukuran & Tahun) -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>⚙️ Merk, Type &amp; Ukuran:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Identitas Fisik</span>
                            </div>

                            <!-- Nama Spesifik Barang Item Ini -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nama Spesifik Barang Aset #<span x-text="idx + 1"></span> <span class="text-rose-400">*</span></span>
                                    <span class="text-[9px] text-amber-400 font-bold" x-show="selectedSubSub" x-text="'Klasifikasi: ' + selectedSubSub.kode"></span>
                                </label>
                                <input type="text" x-model="item.mesin_nama_barang" @input="syncTotalsFromItems()"
                                       :placeholder="selectedSubSub ? selectedSubSub.nama : 'Contoh: Automated Chemistry Analyzer / Mesin Hemodialisis'"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                            </div>

                            <!-- Merk Barang -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Merk / Brand Pabrikan <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.mesin_merk" @input="syncTotalsFromItems()"
                                       placeholder="Contoh: Siemens / Mindray / Roche / Sysmex / Fresenius / GE"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Type / Model <span class="text-rose-400">*</span></label>
                                    <input type="text" x-model="item.mesin_type" @input="syncTotalsFromItems()"
                                           placeholder="Contoh: SOMATOM go.Now / DC-70 / 4008S"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Ukuran / Kapasitas</label>
                                    <input type="text" x-model="item.mesin_ukuran"
                                           placeholder="Contoh: 128 Slice / 300 Test/Jam"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                            </div>

                            <!-- Tahun Pembuatan Pabrik -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun Pembuatan Pabrik</label>
                                <input type="number" min="1990" max="2100" x-model.number="item.mesin_tahun_pembuatan"
                                       placeholder="Contoh: 2025"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                        </div>

                        <!-- 2. Spesifikasi No Pabrik, Bahan, Kondisi & Kendaraan -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🏷️ No Pabrik, Kendaraan, Bahan &amp; Kondisi:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Serial &amp; Spesifikasi</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">No Pabrik / Serial Number (SN)</label>
                                    <input type="text" x-model="item.mesin_no_pabrik"
                                           placeholder="SN-RAD-2026-88192"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                    <input type="text" x-model="item.mesin_bahan"
                                           placeholder="Logam &amp; Elektronik / Stainless"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Fisik Barang <span class="text-rose-400">*</span></label>
                                <select x-model="item.mesin_kondisi"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                                    <option value="Baik">🟢 Baik (B) &mdash; Siap Operasional</option>
                                    <option value="Kurang Baik">🟡 Kurang Baik (KB) &mdash; Perlu Kalibrasi / Setting</option>
                                    <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                </select>
                            </div>

                            <!-- Detail Kendaraan Bermotor (Khusus Ambulans / Kendaraan Operasional Kemitraan) -->
                            <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-1.5 transition-all">
                                <span class="text-[9.5px] font-bold text-slate-400 block uppercase tracking-wider">
                                    🚗 Legality Kendaraan (Khusus Ambulans / Kendaraan Operasional):
                                </span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-500 text-[9px] mb-0.5">No Rangka</label>
                                        <input type="text" x-model="item.mesin_no_rangka" placeholder="MH1JM..."
                                               class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px] mb-0.5">No Mesin</label>
                                        <input type="text" x-model="item.mesin_no_mesin" placeholder="JM51E..."
                                               class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px] mb-0.5">No BPKB</label>
                                        <input type="text" x-model="item.mesin_no_bpkb" placeholder="BPKB-88..."
                                               class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px] mb-0.5">No POLISI / Plat</label>
                                        <input type="text" x-model="item.mesin_no_polisi" placeholder="P 1080 RS"
                                               class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1 text-xs text-amber-300 font-mono font-bold focus:border-cyan-500 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 3. Volume & Nilai Taksiran Wajar Barang (Persis Belanja Modal) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-1.5">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Volume &amp; Taksiran Nilai Wajar Barang (Rp):</span>
                            </span>
                            <div class="flex items-center space-x-1.5 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-0.5 rounded-lg">
                                <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getMesinSubtotal(item)).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume) <span class="text-rose-400">*</span></label>
                                <input type="number" min="1" x-model.number="item.mesin_jumlah_barang" @input="syncTotalsFromItems()" placeholder="1"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan <span class="text-rose-400">*</span></label>
                                <input type="text" x-model="item.mesin_satuan" @input="syncTotalsFromItems()" placeholder="Unit / Buah / Set"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Taksiran Nilai Satuan (Rp) <span class="text-rose-400">*</span></span>
                                    <span class="text-[9px] font-bold text-emerald-400">Harga Wajar</span>
                                </label>
                                <input type="text" 
                                       :value="item.mesin_nilai_satuan ? Number(item.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                       @input="
                                           let raw = $event.target.value.replace(/\D/g, '');
                                           item.mesin_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                           $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           syncTotalsFromItems();
                                       "
                                       placeholder="Contoh: 185.000.000"
                                       class="w-full bg-slate-950 border border-slate-700 text-emerald-300 focus:border-emerald-500 rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-emerald-400 text-[10px] mb-1 font-bold">Sub Total Item #<span x-text="idx + 1"></span> (Rp)</label>
                                <div class="w-full bg-slate-950/90 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-black flex items-center justify-between shadow-inner">
                                    <span class="text-emerald-500 text-[10px]">Rp</span>
                                    <span x-text="Number(getMesinSubtotal(item)).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Barang / Unit Baru -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addMesinItem()"
                class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 border border-purple-400/40 flex items-center space-x-2 transition-all cursor-pointer">
                <span>➕</span>
                <span>Tambah Barang / Unit Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Mesin / Alkes:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
            </div>
        </div>

    </div>

</div>
