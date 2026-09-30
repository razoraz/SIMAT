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
                                x-show="formData.mesin_items.length > 1" 
                                @click="removeMesinItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Barang Ini</span>
                        </button>
                    </div>

                    <!-- 1. Pilihan Jenis & Nama Barang PMDN 108 (Satu Input Filter & Ketik Langsung) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-amber-500/40 space-y-2 shadow-inner">
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
                            
                            <div class="relative">
                                <input type="text"
                                       x-model="item.mesin_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; syncTotalsFromItems();"
                                       placeholder="Ketik untuk mencari jenis barang PMDN 108 atau tulis rincian unit alat..."
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all pl-9 pr-8">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <button type="button" 
                                        x-show="item.mesin_nama_barang" 
                                        @click="item.mesin_nama_barang = ''; item.mesin_kode_barang = ''; item.isFilterOpen = true; syncTotalsFromItems();" 
                                        class="absolute inset-y-0 right-2.5 flex items-center text-slate-400 hover:text-white transition-colors cursor-pointer"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
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
                                        <div class="text-[9px] text-slate-400">Nilai wajar satuan &gt; Rp 300.000</div>
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
                                        <div class="text-[9px] text-slate-400">Nilai wajar satuan ≤ Rp 300.000 (Non-Kendaraan)</div>
                                    </div>
                                </div>
                                <span x-show="item.is_extracom" class="text-cyan-400 font-bold text-xs">✓ Terpilih</span>
                            </button>
                        </div>
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

                            <!-- Tahun Pembuatan Pabrik (Dibatasi Maksimal Tahun Berjalan Sekarang) -->
                            <div>
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

                    <!-- 4. Ruang / Unit Pemegang (Penanggung Jawab & Lokasi) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative shadow-md" @click.away="item.isRuangOpen = false">
                        <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                            <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📍 Ruang / Unit Pemegang (Penanggung Jawab &amp; Lokasi):</span>
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
                            <input type="text" 
                                   :value="!item.isRuangOpen ? item.ruang_pemegang : item.searchRuang"
                                   @input="item.ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true; syncTotalsFromItems();"
                                   @focus="item.isRuangOpen = true"
                                   placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white font-semibold focus:outline-none transition-all">
                            <svg class="w-3.5 h-3.5 text-amber-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
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

                    <!-- 5. Volume & Nilai Taksiran Wajar Barang (Persis Belanja Modal) -->
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
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
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

    </div>

</div>

