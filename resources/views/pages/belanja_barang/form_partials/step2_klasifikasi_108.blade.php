<!-- ========================================================================= -->
<!-- LANGKAH 2: KERANJANG RINCIAN BARANG MULTI-ITEM (PERMENDAGRI 108)           -->
<!-- ========================================================================= -->
<div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
                <span>Langkah 2 dari 3</span>
            </div>
            <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                <span>📦 Keranjang Rincian Barang Pembelian (Multi-Item)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Tambahkan satu atau lebih macam barang yang tercantum dalam faktur toko ini dan tentukan klasifikasi Kode 108 masing-masing.
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
                        <span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Klasifikasi Kode Barang (Permendagri 108) <span class="text-rose-400">*</span></span>
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
                            Nama Barang Spesifik <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="item.nama_barang" required
                            placeholder="Contoh: Tensimeter Digital Omron, Kursi Roda Standar..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none font-bold">
                    </div>

                    <!-- Volume -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Volume <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" x-model.number="item.jumlah_volume" min="1" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none text-center">
                    </div>

                    <!-- Satuan -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">
                            Satuan <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="item.satuan" required
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
                            <span>Harga Satuan (Rp) <span class="text-rose-400">*</span></span>
                        </label>
                        <input type="number" x-model.number="item.harga_satuan" min="0" step="any" required
                            placeholder="0"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-cyan-300 font-mono focus:outline-none font-bold text-right">
                    </div>
                </div>

                <!-- Subtotal Row & Capitalization Guard Alert -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 border-t border-slate-900">
                    <!-- Capitalization Guard Alert -->
                    <div>
                        <template x-if="item.harga_satuan >= 1000000">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/25 text-amber-300 text-[10px] font-bold">
                                <span>⚠️</span>
                                <span>Harga Satuan &ge; Rp 1.000.000 (Ekstrakomptabel - Dipantau di KIR tanpa kapitalisasi Belanja Modal APBD).</span>
                            </div>
                        </template>
                    </div>

                    <!-- Subtotal Item -->
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
                        5. Sub-Sub Rincian Objek (Level Akhir) <span class="text-rose-400">*</span>
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
