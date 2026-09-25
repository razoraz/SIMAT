<!-- ===================================================================== -->
<!-- KONDISI E: RINCIAN ASET TETAP LAINNYA (KIB E) UNTUK HIBAH            -->
<!-- ===================================================================== -->
<template x-if="isAsetLainnya">
    <div class="space-y-6">

        <!-- 1. PILIH KATEGORI ASET TETAP LAINNYA -->
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-orange-500/40 space-y-3 shadow-lg">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-orange-400 block uppercase tracking-wider">KATEGORI ASET TETAP LAINNYA:</span>
                    <span class="text-[10px] text-slate-400 font-medium">Kategori ini berlaku sebagai default untuk setiap item barang hibah baru</span>
                </div>
                <!-- Badge kategori terpilih -->
                <span class="text-[10px] px-2.5 py-1 rounded-full font-bold border"
                      :class="{
                          'bg-amber-500/20 text-amber-300 border-amber-500/40': (formData.kib_e_default_type || 'buku') === 'buku',
                          'bg-purple-500/20 text-purple-300 border-purple-500/40': formData.kib_e_default_type === 'kesenian',
                          'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': formData.kib_e_default_type === 'hewan_tumbuhan'
                      }"
                      x-text="formData.kib_e_default_type === 'kesenian' ? '🎨 Kesenian & Budaya Terpilih' : (formData.kib_e_default_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan Terpilih' : '📚 Buku Perpustakaan Terpilih')">
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                <!-- Opsi 1: Buku Perpustakaan -->
                <div @click="formData.kib_e_default_type = 'buku'; syncLainnyaFieldsToMain();"
                     :class="(formData.kib_e_default_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500 shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-amber-500/50'"
                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <input type="radio" name="kib_e_default_type_radio" value="buku"
                                   :checked="(formData.kib_e_default_type || 'buku') === 'buku'"
                                   @change="formData.kib_e_default_type = 'buku'; syncLainnyaFieldsToMain();"
                                   class="text-amber-500 focus:ring-amber-500">
                            <span class="text-sm font-extrabold text-amber-400">📚 Buku Perpustakaan</span>
                        </div>
                        <span x-show="(formData.kib_e_default_type || 'buku') === 'buku'"
                              class="text-[9px] px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Buku, jurnal ilmiah kedokteran, literatur medis, dan arsip pustaka rumah sakit dari pemberi hibah.</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">📖 Judul Buku</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">✍️ Pencipta/Penulis</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">📋 Spesifikasi</span>
                    </div>
                </div>

                <!-- Opsi 2: Kesenian & Kebudayaan -->
                <div @click="formData.kib_e_default_type = 'kesenian'; syncLainnyaFieldsToMain();"
                     :class="formData.kib_e_default_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 shadow-lg shadow-purple-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-purple-500/50'"
                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <input type="radio" name="kib_e_default_type_radio" value="kesenian"
                                   :checked="formData.kib_e_default_type === 'kesenian'"
                                   @change="formData.kib_e_default_type = 'kesenian'; syncLainnyaFieldsToMain();"
                                   class="text-purple-500 focus:ring-purple-500">
                            <span class="text-sm font-extrabold text-purple-400">🎨 Kesenian & Kebudayaan</span>
                        </div>
                        <span x-show="formData.kib_e_default_type === 'kesenian'"
                              class="text-[9px] px-2 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Lukisan, patung, ornamen dekoratif, atau benda seni dari pihak pemberi hibah.</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🗺️ Asal Daerah</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🎭 Seniman</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🧱 Bahan</span>
                    </div>
                </div>

                <!-- Opsi 3: Hewan & Tumbuhan -->
                <div @click="formData.kib_e_default_type = 'hewan_tumbuhan'; syncLainnyaFieldsToMain();"
                     :class="formData.kib_e_default_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500 shadow-lg shadow-emerald-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-emerald-500/50'"
                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <input type="radio" name="kib_e_default_type_radio" value="hewan_tumbuhan"
                                   :checked="formData.kib_e_default_type === 'hewan_tumbuhan'"
                                   @change="formData.kib_e_default_type = 'hewan_tumbuhan'; syncLainnyaFieldsToMain();"
                                   class="text-emerald-500 focus:ring-emerald-500">
                            <span class="text-sm font-extrabold text-emerald-400">🌿 Hewan & Tumbuhan</span>
                        </div>
                        <span x-show="formData.kib_e_default_type === 'hewan_tumbuhan'"
                              class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Tanaman peneduh, lanskap taman rumah sakit, atau hewan yang dihibahkan ke RSUD.</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-950/60 text-emerald-400 border border-emerald-700/30">🌳 Jenis Spesies</span>
                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-950/60 text-emerald-400 border border-emerald-700/30">📋 Spesifikasi</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- PEMBUNGKUS ASET TETAP LAINNYA MULTI-ITEM REPEATER (KIB E)                  -->
        <!-- ========================================================================= -->
        <div class="space-y-4">
            
            <!-- Header Pembungkus Aset Tetap Lainnya Multi-Item -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-orange-950/30 border border-orange-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-orange-500/20 text-orange-400 text-sm">📚</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            RINCIAN BARANG ASET TETAP LAINNYA
                            (<span class="text-orange-400" x-text="formData.lainnya_items.length"></span> Item Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Setiap item aset memiliki kategori, spesifikasi detail, volume, nilai taksiran satuan, dan ruangan/lokasi penempatan penanggung jawab masing-masing.
                    </p>
                </div>
                <button type="button" @click="addLainnyaItem()" 
                        class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-orange-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Item Aset Baru</span>
                </button>
            </div>

            <!-- List Kartu Item Aset Tetap Lainnya (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.lainnya_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-orange-500/30 hover:border-orange-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <!-- Header Kartu Tiap Item -->
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-orange-500/20 text-orange-300 font-mono font-extrabold text-xs border border-orange-500/40 flex items-center space-x-1.5">
                                    <span>📚 Item #<span x-text="idx + 1"></span></span>
                                </span>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold"
                                      :class="{
                                          'bg-amber-500/20 text-amber-300 border border-amber-500/40': (item.kib_e_sub_type || 'buku') === 'buku',
                                          'bg-purple-500/20 text-purple-300 border border-purple-500/40': item.kib_e_sub_type === 'kesenian',
                                          'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40': item.kib_e_sub_type === 'hewan_tumbuhan'
                                      }"
                                      x-text="(item.kib_e_sub_type === 'kesenian' ? '🎨 Kesenian & Budaya' : (item.kib_e_sub_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan' : '📚 Buku Perpustakaan'))">
                                </span>
                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.lainnya_buku_judul || item.lainnya_kesenian_asal || item.lainnya_hewan_jenis">
                                    • <span x-text="item.kib_e_sub_type === 'buku' ? item.lainnya_buku_judul : (item.kib_e_sub_type === 'kesenian' ? item.lainnya_kesenian_asal : (item.lainnya_hewan_jenis || item.lainnya_hewan_judul))"></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Qty: <strong class="text-cyan-300" x-text="(item.lainnya_jumlah_barang || 1) + ' ' + (item.lainnya_satuan || 'Eksemplar')"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(item))"></strong>
                                </span>
                            </div>

                            <!-- Tombol Hapus Item (Muncul jika > 1 item) -->
                            <button type="button" 
                                    x-show="formData.lainnya_items.length > 1" 
                                    @click="removeLainnyaItem(idx)" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>🗑️ Hapus Item Ini</span>
                            </button>
                        </div>

                        <!-- Sync kategori dari Section global ke tiap item -->
                        <template x-effect="item.kib_e_sub_type = formData.kib_e_default_type || 'buku'"></template>

                        <!-- Grid Form Pengisian Spesifikasi Aset Tetap Lainnya -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Sisi Kiri: Nama & Identitas Kategori -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span x-text="(item.kib_e_sub_type === 'kesenian' ? '🎨 Identitas Karya Seni' : (item.kib_e_sub_type === 'hewan_tumbuhan' ? '🌿 Identitas Hewan & Tumbuhan' : '📚 Identitas Buku Perpustakaan'))"></span>
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                        <span>Nama Barang (PMDN 108)</span>
                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                            <span>🔒</span>
                                            <span>Otomatis dari Langkah 2</span>
                                        </span>
                                    </label>
                                    <input type="text" 
                                           :value="item.lainnya_nama_barang || formData.lainnya_nama_barang || formData.sub_rincian_nama || 'Aset Tetap Lainnya'"
                                           readonly
                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                </div>

                                <!-- Form Spesifik: Buku -->
                                <template x-if="(item.kib_e_sub_type || 'buku') === 'buku'">
                                    <div class="space-y-2.5">
                                        <div>
                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul Buku / Literatur Medis</label>
                                            <input type="text" x-model="item.lainnya_buku_judul" placeholder="Pedoman Standar Pelayanan Klinis Kedokteran"
                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                        </div>
                                        <div>
                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Penulis / Penerbit</label>
                                            <input type="text" x-model="item.lainnya_buku_pencipta" placeholder="Kementerian Kesehatan RI / Penerbit Salemba Medika"
                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: Kesenian -->
                                <template x-if="item.kib_e_sub_type === 'kesenian'">
                                    <div class="space-y-2.5">
                                        <div>
                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Asal Daerah / Wilayah Budaya</label>
                                            <input type="text" x-model="item.lainnya_kesenian_asal" placeholder="Jawa Timur / Bondowoso"
                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-purple-500">
                                        </div>
                                        <div>
                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Seniman</label>
                                            <input type="text" x-model="item.lainnya_kesenian_pencipta" placeholder="Sanggar Seni Budaya"
                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: Hewan & Tumbuhan -->
                                <template x-if="item.kib_e_sub_type === 'hewan_tumbuhan'">
                                    <div class="space-y-2.5">
                                        <div>
                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Jenis Hewan & Tumbuhan</label>
                                            <input type="text" x-model="item.lainnya_hewan_jenis" @input="item.lainnya_hewan_judul = $event.target.value" placeholder="Pohon Tabebuya Emas / Tanaman Hias Lanskap"
                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500">
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Sisi Kanan: Spesifikasi Fisik, Bahan, Ukuran & Kondisi -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🔍 Spesifikasi Fisik & Kondisi:</span>
                                    </span>
                                </div>

                                <!-- Spesifikasi Buku -->
                                <template x-if="(item.kib_e_sub_type || 'buku') === 'buku'">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Buku / Literatur</label>
                                        <input type="text" x-model="item.lainnya_buku_spesifikasi" placeholder="Edisi Revisi / Hardcover Lux / 600 Halaman"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 focus:border-amber-500">
                                    </div>
                                </template>

                                <!-- Spesifikasi Kesenian -->
                                <template x-if="item.kib_e_sub_type === 'kesenian'">
                                    <div class="space-y-2.5">
                                        <div>
                                            <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Karya Seni</label>
                                            <input type="text" x-model="item.lainnya_kesenian_spesifikasi" placeholder="Lukisan Lanskap Alam / Patung Lambang Kesehatan"
                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-purple-300 focus:border-purple-500">
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Seni</label>
                                                <input type="text" x-model="item.lainnya_kesenian_bahan" placeholder="Kanvas & Kayu Jati"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-purple-500">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-medium">Ukuran (m/cm)</label>
                                                <input type="text" x-model="item.lainnya_kesenian_ukuran" placeholder="150 x 100 cm"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-purple-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Spesifikasi Hewan & Tumbuhan -->
                                <template x-if="item.kib_e_sub_type === 'hewan_tumbuhan'">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Tanaman / Hewan</label>
                                        <input type="text" x-model="item.lainnya_hewan_spesifikasi" placeholder="Bibit Unggul Tinggi 3 Meter Siap Tanam"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-300 focus:border-emerald-500">
                                    </div>
                                </template>

                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Barang Hibah</label>
                                    <select x-model="item.lainnya_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                        <option value="Baik">Baik (B)</option>
                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Volume & Taksiran Nilai Hibah Satuan Barang -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-1.5">
                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>💰 Volume & Nilai Taksiran Hibah Satuan (Rp):</span>
                                </span>
                                <div class="flex items-center space-x-1.5 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-0.5 rounded-lg">
                                    <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                    <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume)</label>
                                    <input type="number" min="1" x-model.number="item.lainnya_jumlah_barang" placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                    <input type="text" x-model="item.lainnya_satuan" placeholder="Eksemplar / Buah / Batang / Unit"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nilai Satuan (Rp)</label>
                                    <input type="text" 
                                           :value="item.lainnya_nilai_satuan ? Number(item.lainnya_nilai_satuan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.lainnya_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           "
                                           placeholder="450.000"
                                           class="w-full bg-slate-950 border border-slate-700 text-emerald-300 focus:border-emerald-500 rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Biaya Tambahan/Admin (Rp)</label>
                                    <input type="text" 
                                           :value="item.lainnya_administrasi_proyek ? Number(item.lainnya_administrasi_proyek).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.lainnya_administrasi_proyek = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           "
                                           placeholder="0"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block text-emerald-400 text-[10px] mb-1 font-bold">Sub Total (Rp)</label>
                                    <div class="w-full bg-slate-950/90 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-black flex items-center justify-between shadow-inner">
                                        <span class="text-emerald-500 text-[10px]">Rp</span>
                                        <span x-text="Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ruang / Pemegang Aset -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative" @click.away="item.isRuangOpen = false">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                                <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📍 Ruang / Unit Pemegang (Penanggung Jawab & Lokasi):</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                        <span>🏥</span>
                                        <span>Unit & Paviliun</span>
                                    </span>
                                    <button type="button" 
                                            x-show="item.ruang_pemegang || item.ruang_pemegang_lainnya" 
                                            @click="item.ruang_pemegang = ''; item.ruang_pemegang_lainnya = ''; item.searchRuang = ''; item.isRuangOpen = true; syncLainnyaFieldsToMain();" 
                                            class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                        ✕ Reset
                                    </button>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <input type="text" 
                                       :value="!item.isRuangOpen ? (item.ruang_pemegang || item.ruang_pemegang_lainnya) : item.searchRuang"
                                       @input="item.ruang_pemegang = $event.target.value; item.ruang_pemegang_lainnya = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true; syncLainnyaFieldsToMain();"
                                       @focus="item.isRuangOpen = true"
                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white font-semibold focus:outline-none transition-all">
                                <svg class="w-3.5 h-3.5 text-amber-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>

                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 180px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                <div class="px-2.5 py-1 bg-slate-950/80 rounded-lg text-[9.5px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                    <span class="text-slate-400 font-mono text-[9px]" x-text="filterUnitsForLainnyaItem(item).length + ' Unit/Ruangan'"></span>
                                </div>
                                <template x-for="u in filterUnitsForLainnyaItem(item)" :key="u.id">
                                    <div @click="selectUnitForLainnyaItem(item, u)" class="p-2 rounded-xl bg-slate-950/50 hover:bg-amber-500/15 border border-slate-800/60 hover:border-amber-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold text-white group-hover:text-amber-300 truncate" x-text="u.nama"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                            </div>
                                            <p class="text-[9.5px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-lg bg-slate-900 text-amber-300 border border-amber-500/30 text-[9.5px] font-bold shrink-0">Pilih →</span>
                                    </div>
                                </template>
                                <template x-if="filterUnitsForLainnyaItem(item).length === 0">
                                    <div class="p-2.5 text-center text-xs text-slate-400">
                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Tombol Tambah Item Baru -->
            <button type="button" @click="addLainnyaItem()" 
                    class="w-full py-3.5 border-2 border-dashed border-orange-500/50 hover:border-orange-400 bg-orange-950/20 hover:bg-orange-950/40 text-orange-300 hover:text-orange-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Item Aset Tetap Lainnya Baru</span>
            </button>

            <!-- Ringkasan Akumulasi KIB E Hibah -->
            <div class="p-4 rounded-2xl bg-slate-950/90 border border-orange-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                    <div>
                        <span class="text-[10px] text-orange-400 font-semibold block uppercase tracking-wider">📦 Total Volume / Item:</span>
                        <span class="text-sm font-black text-orange-300 font-mono" x-text="totalVolumeAsetLainnya + ' Item/Barang'"></span>
                    </div>
                </div>
                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                    <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">Total Taksiran Nilai Hibah:</span>
                    <span class="text-base font-extrabold text-emerald-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAsetLainnya)"></span>
                </div>
            </div>

        </div>

    </div>
</template>
