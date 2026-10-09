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
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border transition-all"
                                  :class="item.is_extracom ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                  x-text="item.is_extracom ? '📦 Ekstrakomtabel (≤ 300rb)' : '⚙️ Aset Tetap Reguler'">
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
                                x-show="formData.mesin_items.length > 1 && tipeKemitraan !== 'dimanfaatkan'" 
                                @click="removeMesinItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Barang Ini</span>
                        </button>
                    </div>

                    <!-- 1. MODE DIMANFAATKAN: Kodefikasi 108 Terkunci ke Akun 1.5.2 -->
                    <div x-show="tipeKemitraan === 'dimanfaatkan'" class="p-4 rounded-2xl bg-cyan-950/30 border border-cyan-500/30 space-y-2.5 shadow-inner">
                        <div class="flex items-center justify-between mb-1 flex-wrap gap-2">
                            <label class="text-cyan-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                <span>🔒 Kodefikasi PMDN 108 Akun 1.5.2 Pemanfaatan BMD (Terkunci)</span>
                            </label>
                            <span class="text-[9.5px] px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold font-mono"
                                  x-text="'Kode 108: ' + (item.mesin_kode_barang || selectedSubSub?.kode || '1.5.2.01.01.01.002')">
                            </span>
                        </div>
                        <div>
                            <label class="block text-slate-300 text-[11px] mb-1 font-semibold">
                                Uraian / Nama Rincian Peralatan &amp; Mesin yang Disewakan / Dimanfaatkan <span class="text-rose-400">*</span>
                            </label>
                            <input type="text"
                                   x-model="item.mesin_nama_barang"
                                   @input="syncTotalsFromItems()"
                                   placeholder="Contoh: Mesin Analyzer Laboratorium Kimia Klinik / Peralatan Radiologi..."
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-cyan-400 focus:border-cyan-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- 1. MODE DITAMBAHKAN: Pilihan Jenis & Nama Barang PMDN 108 Belanja Modal 1.3.2 -->
                    <div x-show="tipeKemitraan !== 'dimanfaatkan'" class="p-4 rounded-2xl bg-slate-900/90 border border-amber-500/40 space-y-2 shadow-inner">
                        <div class="relative" @click.outside="item.isFilterOpen = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-amber-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <span>⚙️ Pilih / Ketik Jenis Barang PMDN 108 (Peralatan &amp; Mesin)</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold font-mono"
                                      x-show="item.mesin_kode_barang"
                                      x-text="'Kode 108: ' + item.mesin_kode_barang">
                                </span>
                            </div>
                            
                            <div class="relative flex items-center" style="position: relative;">
                                <svg class="w-4 h-4 text-amber-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text"
                                       x-model="item.mesin_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; syncTotalsFromItems();"
                                       placeholder="Ketik untuk mencari jenis barang PMDN 108 atau tulis rincian unit alat..."
                                       style="padding-left: 38px; padding-right: 36px;"
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl py-2.5 text-xs text-white font-bold focus:outline-none transition-all shadow-inner">
                                <button type="button" 
                                        x-show="item.mesin_nama_barang" 
                                        @click="item.mesin_nama_barang = ''; item.mesin_kode_barang = ''; item.isFilterOpen = true; syncTotalsFromItems();" 
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
                                 class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-amber-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 text-[10px] text-slate-400 font-semibold flex items-center justify-between">
                                    <span>Pilihan Rekomendasi PMDN 108 (Maks. 5):</span>
                                    <span class="text-amber-400 font-mono text-[9px]">PMDN 108 Peralatan &amp; Mesin (1.3.2)</span>
                                </div>
                                <template x-for="opt in filterJenisAstap108('1.3.2', item.mesin_nama_barang, item.isFilterOpen)" :key="opt.id">
                                    <div @click="select108ForItem(item, opt, 'mesin')"
                                         class="px-3.5 py-2 hover:bg-amber-500/20 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div class="flex-1 pr-2">
                                            <div class="text-xs font-bold text-white group-hover:text-amber-300" x-text="opt.nama"></div>
                                        </div>
                                        <span class="font-mono text-[10px] text-amber-400 bg-amber-950/60 px-2 py-0.5 rounded border border-amber-500/30 shrink-0" x-text="opt.kode"></span>
                                    </div>
                                </template>
                                <div x-show="filterJenisAstap108('1.3.2', item.mesin_nama_barang, item.isFilterOpen).length === 0" 
                                     class="px-3.5 py-2.5 text-center text-xs text-slate-400 italic">
                                    <span>Gunakan nama yang Anda ketik jika tidak ada dalam daftar PMDN 108 di atas.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Form Pengisian Spesifikasi Peralatan dan Mesin -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- 1. Spesifikasi Fisik (Nama, Merk, Type, Ukuran & Tahun) -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span x-text="item.is_extracom ? '⚙️ MERK, TYPE & UKURAN:' : '⚙️ Merk, Type & Ukuran:'"></span>
                                </span>
                                <span x-show="!item.is_extracom" class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Identitas Fisik</span>
                            </div>

                            <!-- Nama Barang (PMDN 108) Khusus Mode Extracom (Persis Template Baku Belanja Modal) -->
                            <div x-show="item.is_extracom">
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nama Barang (PMDN 108)</span>
                                    <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                        <span>🔒</span>
                                        <span>Otomatis dari Langkah 2</span>
                                    </span>
                                </label>
                                <input type="text" 
                                       :value="item.mesin_nama_barang || formData.nama_barang || (selectedSubSub?.nama || 'Barang Ekstrakomtabel')"
                                       readonly
                                       class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                            </div>

                            <!-- Merk Barang -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    <span x-text="item.is_extracom ? 'Merk Barang' : 'Merk / Brand Pabrikan'"></span> <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.mesin_merk" @input="syncTotalsFromItems()"
                                       :placeholder="item.is_extracom ? 'Contoh: Olympic / Lion / Krisbow / Kenko' : 'Contoh: Siemens / Mindray / Roche / Sysmex / Fresenius / GE'"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Type / Model <span class="text-rose-400">*</span></label>
                                    <input type="text" x-model="item.mesin_type" @input="syncTotalsFromItems()"
                                           :placeholder="item.is_extracom ? 'Contoh: Standard / Meja / Rak' : 'Contoh: SOMATOM go.Now / DC-70 / 4008S'"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Ukuran / Kapasitas</label>
                                    <input type="text" x-model="item.mesin_ukuran"
                                           :placeholder="item.is_extracom ? 'Contoh: 120x60 cm / Sedang' : 'Contoh: 128 Slice / 300 Test/Jam'"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                            </div>

                            <!-- Tahun Pembuatan Pabrik (Khusus Reguler, disembunyikan saat Extracom) -->
                            <div x-show="!item.is_extracom">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-slate-400 text-[10px] font-semibold">Tahun Pembuatan Pabrik</label>
                                    <span class="text-[9px] text-slate-500 font-mono">Maks: {{ date('Y') }}</span>
                                </div>
                                <input type="number" min="1970" :max="new Date().getFullYear()" x-model.number="item.mesin_tahun_pembuatan"
                                       @input="if(item.mesin_tahun_pembuatan > {{ date('Y') }}) item.mesin_tahun_pembuatan = {{ date('Y') }};"
                                       placeholder="Contoh: {{ date('Y') }}"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                        </div>

                        <!-- 2. Spesifikasi No Pabrik, Bahan, Kondisi & Kendaraan -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                    <span x-text="item.is_extracom ? '🏷️ NO PABRIK, BAHAN & KONDISI:' : '🏷️ No Pabrik, Kendaraan, Bahan & Kondisi:'"></span>
                                </span>
                                <span x-show="!item.is_extracom" class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Serial &amp; Spesifikasi</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        <span x-text="item.is_extracom ? 'No Pabrik / SN' : 'No Pabrik / Serial Number (SN)'"></span>
                                    </label>
                                    <input type="text" x-model="item.mesin_no_pabrik"
                                           :placeholder="item.is_extracom ? 'SN-EXT-2026-001' : 'SN-RAD-2026-88192'"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                    <input type="text" x-model="item.mesin_bahan"
                                           :placeholder="item.is_extracom ? 'Kayu / Besi / Plastik' : 'Logam & Elektronik / Stainless'"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    <span x-text="item.is_extracom ? 'Kondisi Barang' : 'Kondisi Fisik Barang'"></span> <span class="text-rose-400" x-show="!item.is_extracom">*</span>
                                </label>
                                <select x-model="item.mesin_kondisi" @change="syncTotalsFromItems()"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                                    <option value="Baik" x-text="item.is_extracom ? 'Baik (B)' : '🟢 Baik (B) — Siap Operasional'"></option>
                                    <option value="Kurang Baik" x-text="item.is_extracom ? 'Kurang Baik (KB)' : '🟡 Kurang Baik (KB) — Perlu Kalibrasi / Setting'"></option>
                                    <option value="Rusak Berat" x-text="item.is_extracom ? 'Rusak Berat (RB)' : '🔴 Rusak Berat (RB)'"></option>
                                </select>
                            </div>

                            <!-- Detail Kendaraan Bermotor (Khusus Ambulans / Kendaraan Operasional Kemitraan Non-Extracom) -->
                            <div x-show="!item.is_extracom" class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-1.5 transition-all">
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

                    <!-- 3. Keterangan / Catatan Khusus (Diletakkan di Atas Ruang Pemegang) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 space-y-1.5 shadow-md transition-all">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <label class="block text-slate-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📝 KETERANGAN / CATATAN KHUSUS:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-bold">Catatan Tambahan</span>
                        </div>
                        <input type="text" x-model="item.mesin_keterangan"
                            placeholder="Contoh: Penempatan alat uji laboratorium KSO / Garansi vendor 3 tahun / Keterangan operasional"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-white font-medium focus:outline-none transition-all">
                    </div>

                    <!-- 4. Volume & Nilai Taksiran Wajar Barang (Persis Belanja Modal) -->
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
                                    <span x-text="item.is_extracom ? 'Nilai Satuan (Rp) *' : 'Taksiran Nilai Wajar (Rp) *'"></span>
                                    <span class="text-[9px] font-bold"
                                          :class="item.is_extracom ? 'text-amber-400 font-mono' : 'text-emerald-400'"
                                          x-text="item.is_extracom ? 'Maks. Rp 300.000' : 'Harga Wajar'">
                                    </span>
                                </label>
                                <input type="text" 
                                       :value="item.mesin_nilai_satuan ? Number(item.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                       @input="
                                           let raw = $event.target.value.replace(/\D/g, '');
                                           item.mesin_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                           $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           syncTotalsFromItems();
                                       "
                                       :placeholder="item.is_extracom ? 'Maks. 300.000' : 'Contoh: 185.000.000'"
                                       :class="item.is_extracom && item.mesin_nilai_satuan > 300000 ? 'border-rose-500 ring-1 ring-rose-500 text-rose-300' : 'border-slate-700 text-emerald-300 focus:border-emerald-500'"
                                       class="w-full bg-slate-950 border rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none transition-colors">
                                <span x-show="item.is_extracom && item.mesin_nilai_satuan > 300000" class="text-[9px] font-bold text-rose-400 block mt-1">
                                    ⚠️ Nilai satuan Extracom tidak boleh > Rp 300.000!
                                </span>
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
        <div x-show="tipeKemitraan !== 'dimanfaatkan'" class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addMesinItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Barang / Unit Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Mesin / Alkes:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
            </div>
        </div>
        <div x-show="tipeKemitraan === 'dimanfaatkan'" class="flex items-center justify-end pt-2 border-t border-slate-800">
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Mesin / Alkes:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
            </div>
        </div>

    </div>

</div>

