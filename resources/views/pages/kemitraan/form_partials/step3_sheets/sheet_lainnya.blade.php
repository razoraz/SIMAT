<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: ASET TETAP LAINNYA (KIB E / AKUN 1.5.2.xx.005)         -->
<!-- MULTI-ITEM REPEATER (MODEL PERSIS KIB B PERALATAN & MESIN)                 -->
<!-- ========================================================================= -->
<div x-show="isLainnya" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB E -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-purple-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB E -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg border border-purple-500/30 shadow-inner">📦</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Aset Tetap Lainnya
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40">
                            KIB E
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Buku Pustaka Medis, Kesenian &amp; Ornamen Kebudayaan, atau Hewan &amp; Tanaman Kemitraan RSUD.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-purple-400 bg-purple-950/60 px-3 py-1.5 rounded-xl border border-purple-500/30 shadow-sm">
                    Total: <span x-text="formData.lainnya_items ? formData.lainnya_items.length : 1"></span> Item
                </span>
            </div>
        </div>

        <!-- REPEATER DAFTAR ASET TETAP LAINNYA -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.lainnya_items" :key="idx">
                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 hover:border-purple-500/50 transition-all space-y-4 shadow-lg relative">
                    
                    <!-- Header Kartu Aset Lainnya -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-purple-500/20 text-purple-300 font-mono font-extrabold text-xs border border-purple-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>📦 Item #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                :class="{
                                    'bg-amber-500/20 text-amber-300 border-amber-500/40': (item.kib_e_type || 'buku') === 'buku',
                                    'bg-purple-500/20 text-purple-300 border-purple-500/40': item.kib_e_type === 'kesenian',
                                    'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': item.kib_e_type === 'hewan_tumbuhan'
                                }"
                                x-text="item.kib_e_type === 'kesenian' ? '🎨 Kesenian' : (item.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan/Tanaman' : '📚 Buku Pustaka')">
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.lainnya_judul" x-text="item.lainnya_judul"></span>
                            <span class="text-[10.5px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Item -->
                        <button type="button"
                            x-show="formData.lainnya_items.length > 1"
                            @click="removeLainnyaItem(idx)"
                            class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer self-end sm:self-auto">
                            <span>🗑️ Hapus Item</span>
                        </button>
                    </div>

                    <!-- Pilihan Kategori KIB E untuk Item Ini -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div @click="item.kib_e_type = 'buku'"
                            :class="(item.kib_e_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100'"
                            class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold text-amber-300">
                            <span>📚 Buku Perpustakaan</span>
                            <span x-show="(item.kib_e_type || 'buku') === 'buku'">✓</span>
                        </div>
                        <div @click="item.kib_e_type = 'kesenian'"
                            :class="item.kib_e_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100'"
                            class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold text-purple-300">
                            <span>🎨 Kesenian &amp; Budaya</span>
                            <span x-show="item.kib_e_type === 'kesenian'">✓</span>
                        </div>
                        <div @click="item.kib_e_type = 'hewan_tumbuhan'"
                            :class="item.kib_e_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100'"
                            class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold text-emerald-300">
                            <span>🌿 Hewan &amp; Tumbuhan</span>
                            <span x-show="item.kib_e_type === 'hewan_tumbuhan'">✓</span>
                        </div>
                    </div>

                    <!-- 1. Form Spesifik: Buku Perpustakaan -->
                    <div x-show="(item.kib_e_type || 'buku') === 'buku'" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Judul Buku / Pustaka Medis <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.lainnya_judul"
                                    placeholder="Contoh: Atlas Anatomi Manusia Sobotta Edisi 24"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pengarang / Pencipta</label>
                                <input type="text" x-model="item.lainnya_pencipta"
                                    placeholder="Prof. Dr. Friedrich Paulsen"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Penerbit / Spesifikasi</label>
                                <input type="text" x-model="item.lainnya_spesifikasi"
                                    placeholder="EGC Penerbit Buku Kedokteran"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tahun Terbit</label>
                                <input type="number" min="1950" max="2100" x-model.number="item.lainnya_tahun"
                                    placeholder="2024"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Halaman / Dimensi</label>
                                <input type="text" x-model="item.lainnya_ukuran"
                                    placeholder="840 Halaman / 21 x 29.7 cm"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- 2. Form Spesifik: Kesenian & Budaya -->
                    <div x-show="item.kib_e_type === 'kesenian'" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Judul / Nama Benda Seni <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.lainnya_judul"
                                    placeholder="Contoh: Lukisan Sejarah Pelayanan RSUD Bondowoso"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-purple-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Asal Daerah / Wilayah</label>
                                <input type="text" x-model="item.lainnya_asal_daerah"
                                    placeholder="Bondowoso, Jawa Timur"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Seniman</label>
                                <input type="text" x-model="item.lainnya_pencipta"
                                    placeholder="Sanggar Seni Rupa Bondowoso"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                <input type="text" x-model="item.lainnya_bahan"
                                    placeholder="Kanvas Cat Minyak / Perunggu / Kayu Jati"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Dimensi</label>
                                <input type="text" x-model="item.lainnya_ukuran"
                                    placeholder="150 x 200 cm"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Form Spesifik: Hewan & Tumbuhan -->
                    <div x-show="item.kib_e_type === 'hewan_tumbuhan'" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Jenis Hewan / Tanaman <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.lainnya_judul"
                                    placeholder="Contoh: Pohon Ketapang Kencana / Palem Raja"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500 focus:outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Tinggi</label>
                                <input type="text" x-model="item.lainnya_ukuran"
                                    placeholder="Tinggi 3.5 Meter"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500 focus:outline-none transition-all">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Keterangan / Spesifikasi Khusus</label>
                                <input type="text" x-model="item.lainnya_spesifikasi"
                                    placeholder="Penghijauan Area Taman Rawat Inap Barat"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500 focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Kondisi, Volume, Satuan & Taksiran Nilai -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-purple-500/30 space-y-2.5 shadow-md">
                        <div class="flex items-center justify-between border-b border-purple-500/20 pb-1.5">
                            <span class="text-xs font-bold text-purple-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Kondisi, Volume &amp; Taksiran Nilai Wajar Aset Lainnya (Rp):</span>
                            </span>
                            <div class="flex items-center space-x-1.5 bg-purple-950/60 border border-purple-500/30 px-2.5 py-0.5 rounded-lg">
                                <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi <span class="text-rose-400">*</span></label>
                                <select x-model="item.lainnya_kondisi"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-purple-500 focus:outline-none">
                                    <option value="Baik">🟢 Baik (B)</option>
                                    <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                    <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Volume <span class="text-rose-400">*</span></label>
                                <input type="number" min="1" x-model.number="item.lainnya_jumlah" @input="syncTotalsFromItems()" placeholder="1"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-purple-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan <span class="text-rose-400">*</span></label>
                                <input type="text" x-model="item.lainnya_satuan" @input="syncTotalsFromItems()" placeholder="Eks / Buah / Unit / Pohon"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-purple-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Taksiran Nilai Wajar (Rp) <span class="text-rose-400">*</span></span>
                                    <span class="text-[9px] font-bold text-emerald-400">Harga Wajar</span>
                                </label>
                                <input type="text"
                                    :value="item.lainnya_nilai_satuan ? Number(item.lainnya_nilai_satuan).toLocaleString('id-ID') : ''"
                                    @input="
                                        let raw = $event.target.value.replace(/\D/g, '');
                                        item.lainnya_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                        $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                        syncTotalsFromItems();
                                    "
                                    placeholder="Contoh: 1.500.000"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-bold focus:border-purple-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Item Aset Lainnya Baru -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addLainnyaItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Item Aset Lainnya Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Aset Lainnya:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiLainnya)"></span>
            </div>
        </div>

    </div>

</div>
