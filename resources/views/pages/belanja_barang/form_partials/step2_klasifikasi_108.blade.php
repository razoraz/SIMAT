<!-- ========================================================================= -->
<!-- LANGKAH 2: KERANJANG BARANG MULTI-ITEM, KLASIFIKASI 108 & PENEMPATAN       -->
<!-- ========================================================================= -->
<div x-show="step === 2 || currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
                <span>Langkah 2 dari 3</span>
            </div>
            <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                <span>📦 Rincian Barang Pembelian (Multi-Item) &amp; Klasifikasi 108</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Tambahkan barang perbekalan yang tercantum dalam faktur, tentukan klasifikasi Kode 108, spesifikasi fisik, dan jalur penempatan ruangan (KIR).
            </p>
        </div>

        <button type="button" @click="addItem()"
            class="px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-extrabold shadow-lg shadow-cyan-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0 self-start sm:self-auto active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            <span>+ Tambah Barang ke Faktur</span>
        </button>
    </div>

    <!-- Quick Shortcuts Kategori KIB yang Relevan untuk Belanja Perbekalan -->
    <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2.5">
        <div class="flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                <span>⚡ KATEGORI ASET UMUM PERBEKALAN RSUD</span>
            </span>
            <span class="text-[10px] text-slate-500">Klik untuk filter cepat pencarian</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button type="button" @click="filterByCategory('1.3.2')"
                class="p-3 rounded-xl border text-left transition-all flex items-center justify-between bg-slate-900/80 border-slate-800 hover:border-cyan-500/50 hover:bg-slate-850 cursor-pointer group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-xl">🩺</span>
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-white block group-hover:text-cyan-300 truncate">KIB B: Peralatan, Mesin &amp; Alkes</span>
                        <span class="text-[10px] text-slate-400 block truncate">Tensimeter, timbangan pasien, kursi roda, nebulizer, dll.</span>
                    </div>
                </div>
                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">1.3.2</span>
            </button>

            <button type="button" @click="filterByCategory('1.3.5')"
                class="p-3 rounded-xl border text-left transition-all flex items-center justify-between bg-slate-900/80 border-slate-800 hover:border-purple-500/50 hover:bg-slate-850 cursor-pointer group">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-xl">📦</span>
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-white block group-hover:text-purple-300 truncate">KIB E: Aset Tetap Lainnya &amp; Perabot</span>
                        <span class="text-[10px] text-slate-400 block truncate">Perabot ruangan, lemari arsip, buku medis perbekalan, dll.</span>
                    </div>
                </div>
                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20">1.3.5</span>
            </button>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- ALUR PENEMPATAN FLEKSIBEL (OPSI A: GUDANG VS RUANGAN KIR)            -->
    <!-- ===================================================================== -->
    <div class="p-5 rounded-2xl bg-slate-950/80 border border-indigo-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📍 ALUR PENEMPATAN BARANG BELANJA (OPSI A)</span>
            </span>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-900 px-2.5 py-0.5 rounded-full border border-slate-800">
                Pilih Jalur Distribusi Fisik
            </span>
        </div>

        <!-- Pilihan Mode Alokasi (Radio Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Card 1: Simpan di Gudang Perbekalan (Transit Stok) -->
            <div @click="setGlobalAlokasiMode('gudang')"
                class="p-4 rounded-2xl border cursor-pointer transition-all relative flex flex-col justify-between"
                :class="globalAlokasiMode === 'gudang' ? 'bg-indigo-600/15 border-indigo-500 shadow-lg shadow-indigo-500/10 ring-1 ring-indigo-500' : 'bg-slate-900/80 border-slate-800 hover:border-slate-700'">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">📦</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="globalAlokasiMode === 'gudang' ? 'border-indigo-400 bg-indigo-500' : 'border-slate-700'">
                            <div x-show="globalAlokasiMode === 'gudang'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-white block">Simpan di Gudang Perbekalan (Transit Stok)</span>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Barang dicatat sebagai buffer stok di <strong class="text-indigo-300">Gudang Perbekalan</strong>. Register NIBAR berstatus <strong class="text-emerald-400">Tersedia</strong> dan siap didistribusikan bertahap ke ruangan menggunakan dokumen BAST Distribusi.
                    </p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[10px]">
                    <span class="text-slate-400">Status Register:</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold">Tersedia di Gudang</span>
                </div>
            </div>

            <!-- Card 2: Langsung Ditempatkan ke Ruangan Perawatan / Poli (KIR Langsung Aktif) -->
            <div @click="setGlobalAlokasiMode('ruangan')"
                class="p-4 rounded-2xl border cursor-pointer transition-all relative flex flex-col justify-between"
                :class="globalAlokasiMode === 'ruangan' ? 'bg-indigo-600/15 border-indigo-500 shadow-lg shadow-indigo-500/10 ring-1 ring-indigo-500' : 'bg-slate-900/80 border-slate-800 hover:border-slate-700'">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">🏢</span>
                        <div class="w-4 h-4 rounded-full border flex items-center justify-center"
                            :class="globalAlokasiMode === 'ruangan' ? 'border-indigo-400 bg-indigo-500' : 'border-slate-700'">
                            <div x-show="globalAlokasiMode === 'ruangan'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold text-white block">Langsung ke Ruangan Perawatan / Poli (KIR)</span>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Untuk barang yang dibeli khusus unit tertentu (contoh: ICU, IGD, Poli Gigi). Register langsung berstatus <strong class="text-indigo-300">Tidak Tersedia</strong> dan <strong class="text-indigo-300">langsung aktif muncul di Kartu Inventaris Ruangan (KIR)</strong>.
                    </p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[10px]">
                    <span class="text-slate-400">Integrasi KIR:</span>
                    <span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">Langsung Masuk KIR</span>
                </div>
            </div>

        </div>

        <!-- Form Alokasi jika Mode = Ruangan (Global) -->
        <template x-if="globalAlokasiMode === 'ruangan'">
            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
                <span class="text-xs font-bold text-indigo-300 flex items-center gap-1.5">
                    <span>🏢 Tentukan Unit / Ruangan Tujuan Seluruh Item</span>
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Pilih Unit / Ruangan / Poli
                        </label>
                        <select x-model.number="globalUnitId" @change="applyGlobalUnit()"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none">
                            <option value="">-- Pilih Unit / Ruangan Pemegang Aset --</option>
                            @foreach($dbUnits ?? [] as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama }} ({{ $unit->kode ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Kondisi Fisik Barang
                        </label>
                        <select x-model="globalKondisi" @change="applyGlobalKondisi()"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none">
                            <option value="Baik">Baik (Bagus / Baru)</option>
                            <option value="Kurang Baik">Kurang Baik (Dapat Berfungsi)</option>
                            <option value="Rusak Berat">Rusak Berat (Tidak Berfungsi)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-300 mb-1">
                        Detail Lokasi Fisik Spesifik
                    </label>
                    <input type="text" x-model="globalAlamatBarang" @input="applyGlobalAlamat()"
                        placeholder="Contoh: Gedung Rawat Inap Melati Lt. 2, Ruang Isolasi 204..."
                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none">
                </div>
            </div>
        </template>
    </div>

    <!-- DAFTAR ITEM BARANG DINAMIS (REPEATER CARDS) -->
    <div class="space-y-4">
        <template x-for="(item, index) in formData.items" :key="item._id || index">
            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 hover:border-slate-700/80 transition-all space-y-4 relative shadow-lg">
                
                <!-- Item Header Bar -->
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-xs font-bold font-mono" x-text="index + 1"></span>
                        <span class="text-xs font-extrabold text-white" x-text="item.nama_barang ? item.nama_barang : 'Barang #' + (index + 1)"></span>
                        <template x-if="item.kode_108">
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20" x-text="item.kode_108"></span>
                        </template>
                    </div>

                    <!-- Tombol Aksi Item -->
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="duplicateItem(index)" title="Duplikat baris barang ini"
                            class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-cyan-300 border border-slate-800 text-[11px] font-semibold transition-all">
                            📋 Salin
                        </button>
                        <template x-if="formData.items.length > 1">
                            <button type="button" @click="removeItem(index)" title="Hapus baris barang ini"
                                class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 border border-rose-500/20 text-[11px] font-bold transition-all">
                                🗑️ Hapus
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Bagian Pemilihan Kode 108 Barang Ini -->
                <div class="p-3.5 rounded-xl bg-slate-900 border border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="min-w-0">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Klasifikasi Kode Barang (Permendagri 108)</span>
                        <template x-if="item.jenis_astap_id && item.kode_108">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-extrabold text-emerald-400" x-text="item.kode_108"></span>
                                <span class="text-xs text-slate-200 truncate" x-text="item.nama_108"></span>
                            </div>
                        </template>
                        <template x-if="!item.jenis_astap_id">
                            <span class="text-xs text-amber-400 font-medium">⚠️ Kode 108 belum dipilih. Klik tombol untuk mencari kode klasifikasi.</span>
                        </template>
                    </div>

                    <button type="button" @click="openPicker108(index)"
                        class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 hover:border-indigo-500 text-xs font-bold transition-all flex items-center justify-center gap-1.5 shrink-0 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span x-text="item.jenis_astap_id ? 'Ganti Kode 108' : 'Pilih Kode 108'"></span>
                    </button>
                </div>

                <!-- Input Nama, Volume, Satuan & Harga -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
                    <!-- Nama Barang Spesifik -->
                    <div class="sm:col-span-5">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Nama Barang Spesifik
                        </label>
                        <input type="text" x-model="item.nama_barang"
                            placeholder="Contoh: Tensimeter Digital Omron, Kursi Roda Standar..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none font-bold">
                    </div>

                    <!-- Volume -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Volume
                        </label>
                        <input type="number" x-model.number="item.jumlah_volume" min="1"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none text-center">
                    </div>

                    <!-- Satuan -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Satuan
                        </label>
                        <select x-model="item.satuan"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-2.5 py-2 text-xs text-white focus:outline-none">
                            <option value="Unit">Unit</option>
                            <option value="Buah">Buah</option>
                            <option value="Set">Set</option>
                            <option value="Box">Box</option>
                            <option value="Pak">Pak</option>
                            <option value="Roll">Roll</option>
                            <option value="Botol">Botol</option>
                            <option value="Lembar">Lembar</option>
                        </select>
                    </div>

                    <!-- Harga Satuan -->
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1 flex items-center justify-between">
                            <span>Harga Satuan (Rp)</span>
                        </label>
                        <input type="number" x-model.number="item.harga_satuan" min="0" step="any"
                            placeholder="0"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-cyan-300 font-mono focus:outline-none font-bold text-right">
                    </div>
                </div>

                <!-- Spesifikasi Teknis KIB Per Item (Merk, Tipe, Bahan, SN) -->
                <div class="pt-2 border-t border-slate-900/90">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-2">Spesifikasi Teknis KIB (Opsional):</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <div>
                            <input type="text" x-model="item.spesifikasi_json.merk" placeholder="Merk / Brand"
                                class="w-full bg-slate-900/90 border border-slate-800 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <input type="text" x-model="item.spesifikasi_json.type" placeholder="Tipe / Model"
                                class="w-full bg-slate-900/90 border border-slate-800 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <input type="text" x-model="item.spesifikasi_json.bahan" placeholder="Bahan / Material"
                                class="w-full bg-slate-900/90 border border-slate-800 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-xs text-white">
                        </div>
                        <div>
                            <input type="text" x-model="item.spesifikasi_json.no_pabrik" placeholder="Nomor Seri (SN)"
                                class="w-full bg-slate-900/90 border border-slate-800 focus:border-indigo-400 rounded-xl px-3 py-1.5 text-xs text-white font-mono">
                        </div>
                    </div>
                </div>

                <!-- Subtotal Row & Capitalization Guard Alert -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-900">
                    <div>
                        <template x-if="item.harga_satuan >= 1000000">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/25 text-amber-300 text-[10px] font-bold">
                                <span>⚠️</span>
                                <span>Harga Satuan &ge; Rp 1.000.000 (Ekstrakomptabel - Dipantau di KIR tanpa kapitalisasi Belanja Modal APBD).</span>
                            </div>
                        </template>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold mr-1">Subtotal Item:</span>
                        <span class="text-xs font-mono font-black text-cyan-400" x-text="formatRupiah((item.jumlah_volume || 0) * (item.harga_satuan || 0))"></span>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- Tombol Tambah Item Bawah -->
    <div class="pt-2 flex justify-center">
        <button type="button" @click="addItem()"
            class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-cyan-400 hover:text-cyan-300 border border-cyan-500/30 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-sm active:scale-95">
            <span>➕ Tambah Item Barang Lagi</span>
        </button>
    </div>

    <!-- REKAPITULASI TOTAL FAKTUR STICKY BAR -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border border-indigo-500/40 shadow-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[10px] text-indigo-400 uppercase font-black tracking-wider block">Ringkasan Faktur Pembelian</span>
            <div class="flex items-center gap-3 mt-1">
                <span class="text-xs text-slate-300">
                    Total Macam Barang: <strong class="text-white font-mono" x-text="formData.items.length + ' Item'"></strong>
                </span>
                <span class="text-slate-600">•</span>
                <span class="text-xs text-slate-300">
                    Total Volume Fisik: <strong class="text-white font-mono" x-text="totalVolumeFaktur + ' Unit'"></strong>
                </span>
            </div>
        </div>

        <div class="text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-800">
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Nilai Faktur (Rp)</span>
            <span class="text-xl sm:text-2xl font-black font-mono text-emerald-400" x-text="formatRupiah(totalNilaiFaktur)"></span>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL / DRAWER PICKER KODE 108 PERMENDAGRI                                -->
    <!-- ========================================================================= -->
    <div x-show="isPicker108Open" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
        
        <div @click.away="closePicker108()" 
            class="w-full max-w-3xl bg-slate-900 border border-cyan-500/40 rounded-3xl p-6 shadow-2xl space-y-4 max-h-[90vh] flex flex-col">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span>🔍 Pilih Klasifikasi Kode Barang 108</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Item: <strong class="text-cyan-300" x-text="activeItemIndex !== null ? (formData.items[activeItemIndex]?.nama_barang || 'Barang #' + (activeItemIndex + 1)) : ''"></strong>
                    </p>
                </div>
                <button type="button" @click="closePicker108()"
                    class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center font-bold text-sm transition-colors">
                    ✕
                </button>
            </div>

            <!-- Search Filter Cepat -->
            <div class="relative">
                <input type="text"
                    x-model="search108"
                    @input="performSearch108()"
                    placeholder="Ketik kata kunci atau kode barang (contoh: Tensimeter, Kursi Roda, USG, Bed Pasien, Timbangan, AC, Lemari)..."
                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button type="button" x-show="search108" @click="search108 = ''; searchResults108 = []"
                    class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-white">✕</button>
            </div>

            <!-- Hasil Pencarian Langsung -->
            <div x-show="searchResults108.length > 0" class="max-h-56 overflow-y-auto rounded-xl border border-cyan-500/30 bg-slate-950 divide-y divide-slate-800 custom-scrollbar">
                <template x-for="item in searchResults108" :key="item.id">
                    <div @click="selectSearchResult108(item)"
                        class="p-3 hover:bg-cyan-500/10 cursor-pointer transition-colors flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-white block" x-text="item.nama"></span>
                            <span class="text-cyan-400 font-mono text-[11px] block mt-0.5" x-text="item.kode"></span>
                        </div>
                        <span class="text-[10px] px-2.5 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 font-bold shrink-0">Pilih ↵</span>
                    </div>
                </template>
            </div>

            <!-- Dropdown Cascading 5-Level (Akun -> Jenis -> Objek -> Rincian -> Sub-Sub) -->
            <div class="overflow-y-auto flex-1 space-y-3 custom-scrollbar pr-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">1. Kelompok Aset (Akun)</label>
                        <select x-model="selectedAkunKode" @change="onAkunChange()"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            <option value="">-- Pilih Kelompok --</option>
                            <template x-for="item in master108" :key="item.kode">
                                <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">2. Jenis Aset</label>
                        <select x-model="selectedJenisKode" @change="onJenisChange()" :disabled="!jenisList.length"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                            <option value="">-- Pilih Jenis --</option>
                            <template x-for="item in jenisList" :key="item.kode">
                                <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">3. Objek Aset</label>
                        <select x-model="selectedObjekKode" @change="onObjekChange()" :disabled="!objekList.length"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                            <option value="">-- Pilih Objek --</option>
                            <template x-for="item in objekList" :key="item.kode">
                                <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 mb-1">4. Rincian Objek</label>
                        <select x-model="selectedRincianKode" @change="onRincianChange()" :disabled="!rincianList.length"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                            <option value="">-- Pilih Rincian --</option>
                            <template x-for="item in rincianList" :key="item.kode">
                                <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Level 5: Sub-Sub Rincian Objek -->
                <div>
                    <label class="block text-xs font-bold text-emerald-400 mb-1">
                        5. Sub-Sub Rincian Objek (Level Akhir)
                    </label>
                    <select x-model="selectedSubSubId" @change="onSubSubChange()" :disabled="!subSubList.length"
                        class="w-full bg-slate-950 border border-emerald-500/40 focus:border-emerald-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none disabled:opacity-50 font-bold">
                        <option value="">-- Pilih Sub-Sub Rincian Akhir --</option>
                        <template x-for="item in subSubList" :key="item.id">
                            <option :value="item.id" x-text="item.kode + ' - ' + item.nama"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="border-t border-slate-800 pt-3 flex items-center justify-end gap-2">
                <button type="button" @click="closePicker108()"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-colors">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
