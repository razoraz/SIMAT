<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: PERALATAN & MESIN (KIB B / AKUN 1.3.2 / PELIMPAHAN)    -->
<!-- MULTI-ITEM REPEATER PERALATAN, KENDARAAN & ALKES MEDIS                   -->
<!-- ========================================================================= -->
<div x-show="isMesin" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB B Multi-Item Repeater -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-indigo-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB B -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg border border-indigo-500/30 shadow-inner">⚙️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Rincian Peralatan, Mesin &amp; Alkes Medis Pelimpahan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 font-mono font-bold text-[10px] border border-indigo-500/40">
                            KIB B · Akun 1.3.2
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Dapat mencatat beberapa rincian unit peralatan/mesin pelimpahan dengan spesifikasi teknis lengkap (Merk, Tipe, No Seri/Rangka/Mesin/Polisi).</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-indigo-400 bg-indigo-950/60 px-3 py-1.5 rounded-xl border border-indigo-500/30 shadow-sm">
                    Total: <span x-text="formData.mesin_items ? formData.mesin_items.length : 1"></span> Barang / Rincian
                </span>
            </div>
        </div>

        <!-- List Kartu Barang Peralatan & Mesin (Repeater Multi-Item) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.mesin_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-indigo-500/30 hover:border-indigo-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <!-- Header Kartu Tiap Barang -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-indigo-500/20 text-indigo-300 font-mono font-extrabold text-xs border border-indigo-500/40 flex items-center space-x-1.5">
                                <span>⚙️ Barang / Unit #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-[11px] text-slate-200 font-semibold" x-show="item.mesin_nama_barang || item.mesin_merk || item.mesin_type">
                                • <span x-text="item.mesin_nama_barang ? (item.mesin_nama_barang + ' • ') : ''"></span><span x-text="(item.mesin_merk || '') + ' ' + (item.mesin_type || '')"></span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Qty: <strong class="text-indigo-300" x-text="(item.mesin_jumlah_barang || 1) + ' ' + (item.mesin_satuan || 'Unit')"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getMesinSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Barang -->
                        <button type="button" 
                                x-show="formData.mesin_items.length > 1" 
                                @click="removeMesinItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Barang Ini</span>
                        </button>
                    </div>

                    <!-- Grid Form Pengisian Spesifikasi Peralatan dan Mesin -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- 1. Spesifikasi Fisik (Nama, Merk, Type, Ukuran & Bahan) -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>⚙️ Identitas Fisik, Merk &amp; Tipe</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Fisik Aset</span>
                            </div>

                            <!-- Nama Spesifik Barang Item Ini -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Spesifik Barang / Alat</label>
                                <input type="text" x-model="item.mesin_nama_barang"
                                    placeholder="Contoh: USG 4D Mindray DC-70 / Mobil Ambulance Toyota Hiace"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>

                            <!-- Merk & Type -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Merk / Pabrikan</label>
                                    <input type="text" x-model="item.mesin_merk"
                                        placeholder="Toyota, Philips, GE, Sharp..."
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Type / Model</label>
                                    <input type="text" x-model="item.mesin_type"
                                        placeholder="DC-70 / 2.5 Manual / Pro..."
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Ukuran & Bahan -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / CC / Dimensi</label>
                                    <input type="text" x-model="item.mesin_ukuran"
                                        placeholder="2.500 CC / 120 x 80 cm"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Material</label>
                                    <input type="text" x-model="item.mesin_bahan"
                                        placeholder="Besi, Stainless, Plastik, Aluminium..."
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Nomor Legalitas & Identifikasi Pabrik/Kendaraan -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                <span class="text-xs font-bold text-indigo-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🔢 Nomor Pabrik, Rangka, Mesin &amp; Polisi</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">Identitas Unik</span>
                            </div>

                            <!-- No Pabrik / Serial Number -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">No. Pabrik / Serial Number (SN)</label>
                                <input type="text" x-model="item.mesin_no_pabrik"
                                    placeholder="SN: 882941-XYZ-2024"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>

                            <!-- No Rangka & No Mesin (Jika Kendaraan Bermotor) -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">No. Rangka / Chassis</label>
                                    <input type="text" x-model="item.mesin_no_rangka"
                                        placeholder="MHF11..."
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">No. Mesin / Engine</label>
                                    <input type="text" x-model="item.mesin_no_mesin"
                                        placeholder="2KD-FTV..."
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- No Polisi / BPKB & Kuantitas/Nilai Satuan -->
                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-1">
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">No. Polisi</label>
                                    <input type="text" x-model="item.mesin_no_polisi"
                                        placeholder="P 1234 AP"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none uppercase">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Volume (Qty)</label>
                                    <input type="number" min="1" x-model.number="item.mesin_jumlah_barang" @input="syncTotalsFromItems()"
                                        placeholder="1"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono font-bold text-center text-white focus:outline-none">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                    <input type="text" x-model="item.mesin_satuan"
                                        placeholder="Unit"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-center text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Nilai Satuan BMD dari SKPD Pengirim -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nilai Perolehan BMD Satuan (Rp)</span>
                                    <span class="text-emerald-400 font-mono text-[9px]">Sesuai Berita Acara</span>
                                </label>
                                <div class="flex items-center rounded-xl bg-slate-950 border border-slate-700 focus-within:border-indigo-400 focus-within:ring-1 focus-within:ring-indigo-400/30 overflow-hidden transition-all">
                                    <span class="px-3 py-2 bg-slate-900 border-r border-slate-800 text-slate-400 text-xs font-bold font-mono select-none flex items-center justify-center">
                                        Rp
                                    </span>
                                    <input type="text"
                                        :value="item.mesin_nilai_satuan ? Number(item.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            item.mesin_nilai_satuan = raw ? parseInt(raw, 10) : 0;
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

        <!-- Tombol Tambah Barang Mesin -->
        <div class="pt-2 flex justify-start">
            <button type="button" @click="addMesinItem()"
                class="px-4 py-2.5 rounded-2xl bg-indigo-500/15 hover:bg-indigo-500/25 text-indigo-300 border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-2 shadow-lg shadow-indigo-950/40 cursor-pointer">
                <span class="text-base">➕</span>
                <span>Tambah Rincian Peralatan / Mesin Pelimpahan Lainnya</span>
            </button>
        </div>

    </div>
</div>
