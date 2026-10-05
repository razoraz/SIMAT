<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: ASET TETAP LAINNYA (KIB E / AKUN 1.3.5 / PELIMPAHAN)   -->
<!-- REPEATER MULTI-ITEM LENGKAP PERSIS MODUL KEMITRAAN & BELANJA MODAL        -->
<!-- ========================================================================= -->
<div x-show="isLainnya" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB E Multi-Item Repeater -->
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
                            Spesifikasi Aset Tetap Lainnya Pelimpahan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40">
                            KIB E · Akun 1.3.5
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Buku Pustaka Medis, Kesenian &amp; Ornamen Kebudayaan, atau Hewan &amp; Tanaman Pelimpahan BMD ke RSUD.</p>
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
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <!-- Header Kartu Aset Lainnya -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800/80 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-purple-500/20 text-purple-300 font-mono font-extrabold text-xs border border-purple-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>📦 Item #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border transition-all"
                                  :class="item.is_extracom ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                  x-text="item.is_extracom ? '📦 Extracom (≤ 300rb)' : '⚙️ Reguler'">
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                :class="{
                                    'bg-amber-500/20 text-amber-300 border-amber-500/40': (item.kib_e_type || 'buku') === 'buku',
                                    'bg-purple-500/20 text-purple-300 border-purple-500/40': item.kib_e_type === 'kesenian',
                                    'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': item.kib_e_type === 'hewan_tumbuhan'
                                }"
                                x-text="item.kib_e_type === 'kesenian' ? '🎨 Kesenian' : (item.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan/Tanaman' : '📚 Buku Pustaka')">
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.lainnya_nama_barang || item.lainnya_judul" x-text="item.lainnya_nama_barang || item.lainnya_judul"></span>
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

                    <!-- Pilihan Kategori KIB E untuk Item Ini (Menentukan Klasifikasi & Akun PMDN 108 Awal) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10.5px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📑 Kategori Asal PMDN 108:</span>
                            </span>
                            <span class="text-[9.5px] text-cyan-400 font-medium" x-show="item.is_extracom">
                                ✨ Tab menentukan kode 108 asal &bull; Area isian bawah otomatis form Extracom baku
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div @click="item.kib_e_type = 'buku'"
                                :class="(item.kib_e_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-amber-300/80'"
                                class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                <div class="flex items-center space-x-2">
                                    <span>📚</span>
                                    <div class="text-left">
                                        <div class="text-xs">Buku Perpustakaan</div>
                                        <div class="text-[9px] text-slate-400 font-mono font-normal">Kode 108: 1.3.5.01</div>
                                    </div>
                                </div>
                                <span x-show="(item.kib_e_type || 'buku') === 'buku'" class="text-amber-400 font-bold">✓</span>
                            </div>
                            <div @click="item.kib_e_type = 'kesenian'"
                                :class="item.kib_e_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-purple-300/80'"
                                class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                <div class="flex items-center space-x-2">
                                    <span>🎨</span>
                                    <div class="text-left">
                                        <div class="text-xs">Kesenian &amp; Budaya</div>
                                        <div class="text-[9px] text-slate-400 font-mono font-normal">Kode 108: 1.3.5.02</div>
                                    </div>
                                </div>
                                <span x-show="item.kib_e_type === 'kesenian'" class="text-purple-400 font-bold">✓</span>
                            </div>
                            <div @click="item.kib_e_type = 'hewan_tumbuhan'"
                                :class="item.kib_e_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-emerald-300/80'"
                                class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                <div class="flex items-center space-x-2">
                                    <span>🌿</span>
                                    <div class="text-left">
                                        <div class="text-xs">Hewan &amp; Tumbuhan</div>
                                        <div class="text-[9px] text-slate-400 font-mono font-normal">Kode 108: 1.3.5.03</div>
                                    </div>
                                </div>
                                <span x-show="item.kib_e_type === 'hewan_tumbuhan'" class="text-emerald-400 font-bold">✓</span>
                            </div>
                        </div>
                    </div>

                    <!-- 1. Pilihan Jenis & Nama Barang PMDN 108 (Satu Input Filter & Ketik Langsung) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-purple-500/40 space-y-2 shadow-inner">
                        <div class="relative" @click.outside="item.isFilterOpen = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-purple-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🏷️ Pilih / Ketik Jenis Barang PMDN 108 (Aset Tetap Lainnya)</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] px-2 py-0.5 rounded-md bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold font-mono"
                                      x-show="item.lainnya_kode_barang"
                                      x-text="'Kode 108: ' + item.lainnya_kode_barang">
                                </span>
                            </div>
                            
                            <div class="relative">
                                <svg class="w-4 h-4 text-purple-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text"
                                       x-model="item.lainnya_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; syncTotalsFromItems();"
                                       placeholder="Ketik untuk mencari jenis barang PMDN 108 atau tulis rincian aset lainnya..."
                                       style="padding-left: 38px; padding-right: 36px;"
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-purple-500 focus:border-purple-500 rounded-xl py-2.5 text-xs text-white font-bold focus:outline-none transition-all shadow-inner">
                                <button type="button" 
                                        x-show="item.lainnya_nama_barang" 
                                        @click="item.lainnya_nama_barang = ''; item.lainnya_kode_barang = ''; item.isFilterOpen = true; syncTotalsFromItems();" 
                                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); z-index: 10;"
                                        class="flex items-center justify-center text-slate-400 hover:text-white transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Dropdown Hasil Filter (Strict Max 5 Baris - Zero Lag) -->
                            <div x-show="item.isFilterOpen" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-purple-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 text-[10px] text-slate-400 font-semibold flex items-center justify-between">
                                    <span>Pilihan Rekomendasi PMDN 108 (Maks. 5):</span>
                                    <span class="text-purple-400 font-mono text-[9px]" x-text="'Prefix: ' + getKibEPrefix(item)"></span>
                                </div>
                                <template x-for="opt in filterJenisAstap108(getKibEPrefix(item), item.lainnya_nama_barang, item.isFilterOpen)" :key="opt.id">
                                    <div @click="select108ForItem(item, opt, 'lainnya')"
                                         class="px-3.5 py-2 hover:bg-purple-500/20 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div class="flex-1 pr-2">
                                            <div class="text-xs font-bold text-white group-hover:text-purple-300" x-text="opt.nama"></div>
                                        </div>
                                        <span class="font-mono text-[10px] text-purple-400 bg-purple-950/60 px-2 py-0.5 rounded border border-purple-500/30 shrink-0" x-text="opt.kode"></span>
                                    </div>
                                </template>
                                <div x-show="filterJenisAstap108(getKibEPrefix(item), item.lainnya_nama_barang, item.isFilterOpen).length === 0" 
                                     class="px-3.5 py-2.5 text-center text-xs text-slate-400 italic">
                                    <span>Gunakan nama yang Anda ketik jika tidak ada dalam daftar PMDN 108 di atas.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Pilihan Status Akuntansi: Aset Tetap Reguler vs Ekstrakomtabel (Extracom) -->
                    <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold uppercase tracking-wider flex items-center space-x-2"
                                   :class="item.is_extracom ? 'text-cyan-300' : 'text-purple-300'">
                                <span x-text="item.is_extracom ? '📦 Status Akuntansi: Ekstrakomtabel (Extracom)' : '⚙️ Status Akuntansi: Aset Tetap Reguler (Intrakomptabel)'"></span>
                            </label>
                            <span class="text-[9.5px] px-2 py-0.5 rounded-md font-bold font-mono border"
                                  :class="item.is_extracom ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                  x-text="item.is_extracom ? '≤ Rp 300.000' : '> Rp 300.000'">
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- Aset Tetap Reguler -->
                            <button type="button" 
                                    @click="item.is_extracom = false; syncTotalsFromItems();"
                                    :class="!item.is_extracom ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 text-white font-extrabold' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                                    class="p-2.5 rounded-xl border transition-all flex items-center justify-between text-xs cursor-pointer">
                                <div class="flex items-center space-x-2">
                                    <span class="text-base">⚙️</span>
                                    <div class="text-left">
                                        <div class="text-[11px] font-bold">Aset Tetap Reguler</div>
                                        <div class="text-[9px] text-slate-400">Nilai perolehan satuan &gt; Rp 300.000</div>
                                    </div>
                                </div>
                                <span x-show="!item.is_extracom" class="text-purple-400 font-bold text-xs">✓ Terpilih</span>
                            </button>

                            <!-- Ekstrakomtabel -->
                            <button type="button" 
                                    @click="item.is_extracom = true; syncTotalsFromItems();"
                                    :class="item.is_extracom ? 'border-cyan-500 bg-cyan-950/40 ring-1 ring-cyan-500 text-white font-extrabold' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                                    class="p-2.5 rounded-xl border transition-all flex items-center justify-between text-xs cursor-pointer">
                                <div class="flex items-center space-x-2">
                                    <span class="text-base">📦</span>
                                    <div class="text-left">
                                        <div class="text-[11px] font-bold">Ekstrakomtabel (Extracom)</div>
                                        <div class="text-[9px] text-slate-400">Nilai perolehan satuan ≤ Rp 300.000</div>
                                    </div>
                                </div>
                                <span x-show="item.is_extracom" class="text-cyan-400 font-bold text-xs">✓ Terpilih</span>
                            </button>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- AREA FORM ISIAN SPESIFIKASI                                               -->
                    <!-- ========================================================================= -->

                    <!-- 1. Form Spesifik: Buku Perpustakaan (Hanya Tampil Saat Non-Extracom) -->
                    <div x-show="!item.is_extracom && (item.kib_e_type || 'buku') === 'buku'" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📚 Atribut Buku Perpustakaan &amp; Pustaka Medis:</span>
                            </span>
                            <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">KIB E Reguler</span>
                        </div>
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
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-slate-400 text-[10px] font-semibold">Tahun Terbit</label>
                                    <span class="text-[9px] text-slate-500 font-mono">Maks: {{ date('Y') }}</span>
                                </div>
                                <input type="number" min="1950" :max="new Date().getFullYear()" x-model.number="item.lainnya_tahun"
                                    @input="if(item.lainnya_tahun > {{ date('Y') }}) item.lainnya_tahun = {{ date('Y') }};"
                                    placeholder="Contoh: {{ date('Y') }}"
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

                    <!-- 2. Form Spesifik: Kesenian & Budaya (Hanya Tampil Saat Non-Extracom) -->
                    <div x-show="!item.is_extracom && item.kib_e_type === 'kesenian'" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="text-xs font-bold text-purple-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🎨 Atribut Fisik Benda Seni / Budaya:</span>
                            </span>
                            <span class="text-[9px] px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20 font-bold">KIB E Reguler</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                            <div class="sm:col-span-2">
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Karya Seni / Ornamen</label>
                                <input type="text" x-model="item.lainnya_spesifikasi"
                                    placeholder="Lukisan dinding sejarah pelayanan RSUD Bondowoso"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-purple-300 focus:border-purple-500 focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Form Spesifik: Hewan & Tumbuhan (Hanya Tampil Saat Non-Extracom) -->
                    <div x-show="!item.is_extracom && item.kib_e_type === 'hewan_tumbuhan'" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🌿 Atribut Fisik Hewan &amp; Tumbuhan:</span>
                            </span>
                            <span class="text-[9px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-bold">KIB E Reguler</span>
                        </div>
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

                    <!-- 4. Form Spesifikasi Ekstrakomtabel Baku (Universal untuk Semua Kategori KIB E saat Extracom) -->
                    <div x-show="item.is_extracom" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Spesifikasi Fisik (Merk, Type, Ukuran & Nama) -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>⚙️ MERK, TYPE &amp; UKURAN:</span>
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                        <span>Nama Barang (PMDN 108)</span>
                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                            <span>🔒</span>
                                            <span>Otomatis</span>
                                        </span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               :value="item.lainnya_nama_barang || formData.nama_barang || (selected108Item?.nama || 'Aset Tetap Lainnya')"
                                               readonly
                                               class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Merk Barang</label>
                                    <input type="text" x-model="item.lainnya_pencipta" placeholder="Contoh: Olympic / Lion / Krisbow / Kenko"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Type / Model</label>
                                        <input type="text" x-model="item.lainnya_spesifikasi" placeholder="Contoh: Standard / Meja / Rak"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Ukuran / Kapasitas</label>
                                        <input type="text" x-model="item.lainnya_ukuran" placeholder="Contoh: 120x60 cm / Sedang"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Spesifikasi No Pabrik, Bahan & Kondisi -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🏷️ NO PABRIK, BAHAN &amp; KONDISI:</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">No Pabrik / SN</label>
                                        <input type="text" x-model="item.lainnya_no_pabrik" placeholder="SN-EXT-2026-001"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Pembuatan</label>
                                        <input type="text" x-model="item.lainnya_bahan" placeholder="Kayu / Besi / Plastik"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Fisik Barang</label>
                                    <select x-model="item.lainnya_kondisi" @change="syncTotalsFromItems()" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                        <option value="Baik">🟢 Baik (B)</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Keterangan / Catatan Khusus (Diletakkan di Atas Ruang Pemegang) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 space-y-1.5 shadow-md transition-all">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <label class="block text-slate-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📝 KETERANGAN / CATATAN KHUSUS:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-bold">Catatan Pelimpahan</span>
                        </div>
                        <input type="text" x-model="item.lainnya_keterangan"
                            placeholder="Contoh: Koleksi literatur medis pelimpahan SKPD / Aset tetap lainnya / Spesifikasi khusus"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-purple-500 rounded-xl px-3.5 py-2 text-xs text-white font-medium focus:outline-none transition-all">
                    </div>

                    <!-- Ruang / Unit Pemegang (Penanggung Jawab & Lokasi Aset Lainnya) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative shadow-md" @click.away="item.isRuangOpen = false">
                        <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                            <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📍 Ruang / Unit Pemegang &amp; Penempatan Aset:</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                    <span>🏥</span>
                                    <span>Unit &amp; Paviliun RSUD</span>
                                </span>
                                <button type="button" 
                                        x-show="item.ruang_pemegang" 
                                        @click="item.ruang_pemegang = ''; item.searchRuang = ''; item.isRuangOpen = true; syncTotalsFromItems();" 
                                        class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                    ✕ Reset
                                </button>
                            </div>
                        </div>
                        
                        <div class="relative">
                            <svg class="w-3.5 h-3.5 text-amber-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <input type="text" 
                                   :value="!item.isRuangOpen ? item.ruang_pemegang : item.searchRuang"
                                   @input="item.ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true; syncTotalsFromItems();"
                                   @focus="item.isRuangOpen = true"
                                   placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                   style="padding-left: 38px;"
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl py-2.5 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                        </div>

                        <!-- Dropdown List Pilihan Unit & Paviliun -->
                        <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 180px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                            <div class="px-2.5 py-1 bg-slate-950/80 rounded-lg text-[9.5px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                                <span>PILIH DARI DATA UNIT &amp; PAVILIUN RSUD:</span>
                                <span class="text-slate-400 font-mono text-[9px]" x-text="filterUnitsForItem(item).length + ' Unit/Ruangan'"></span>
                            </div>
                            <template x-for="u in filterUnitsForItem(item)" :key="u.id || u.nama">
                                <div @click="selectUnitForItem(item, u)" class="p-2 rounded-xl bg-slate-950/50 hover:bg-amber-500/15 border border-slate-800/60 hover:border-amber-500/40 cursor-pointer transition-all flex items-center justify-between group">
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
                            <template x-if="filterUnitsForItem(item).length === 0">
                                <div class="p-2.5 text-center text-xs text-slate-400">
                                    <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Kondisi, Volume, Satuan & Nilai Perolehan BMD Satuan -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-purple-500/30 space-y-2.5 shadow-md">
                        <div class="flex items-center justify-between border-b border-purple-500/20 pb-1.5">
                            <span class="text-xs font-bold text-purple-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Kondisi, Volume &amp; Nilai Perolehan BMD Satuan (Rp):</span>
                            </span>
                            <div class="flex items-center space-x-1.5 bg-purple-950/60 border border-purple-500/30 px-2.5 py-0.5 rounded-lg">
                                <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Fisik <span class="text-rose-400">*</span></label>
                                <select x-model="item.lainnya_kondisi" @change="syncTotalsFromItems()"
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
                                    <span x-text="item.is_extracom ? 'Nilai Satuan (Rp) *' : 'Nilai Perolehan Satuan (Rp) *'"></span>
                                    <span class="text-[9px] font-bold"
                                          :class="item.is_extracom ? 'text-amber-400 font-mono' : 'text-emerald-400'"
                                          x-text="item.is_extracom ? 'Maks. Rp 300.000' : 'Sesuai BAMB'">
                                    </span>
                                </label>
                                <input type="text"
                                    :value="item.lainnya_nilai_satuan ? Number(item.lainnya_nilai_satuan).toLocaleString('id-ID') : ''"
                                    @input="
                                        let raw = $event.target.value.replace(/\D/g, '');
                                        item.lainnya_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                        $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                        syncTotalsFromItems();
                                    "
                                    :placeholder="item.is_extracom ? 'Maks. 300.000' : 'Contoh: 1.500.000'"
                                    :class="item.is_extracom && item.lainnya_nilai_satuan > 300000 ? 'border-rose-500 ring-1 ring-rose-500 text-rose-300' : 'border-slate-700 text-emerald-400 focus:border-purple-500'"
                                    class="w-full bg-slate-950 border rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none transition-colors">
                                <span x-show="item.is_extracom && item.lainnya_nilai_satuan > 300000" class="text-[9px] font-bold text-rose-400 block mt-1">
                                    ⚠️ Nilai satuan Extracom tidak boleh > Rp 300.000!
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Item Aset Lainnya Baru -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addLainnyaItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer active:scale-95">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Item Aset Lainnya Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Nilai Aset Tetap Lainnya:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiLainnya)"></span>
            </div>
        </div>

    </div>

</div>
