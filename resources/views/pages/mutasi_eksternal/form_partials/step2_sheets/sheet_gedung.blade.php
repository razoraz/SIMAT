<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: GEDUNG & BANGUNAN (KIB C / AKUN 1.3.3 / MUTASI MASUK)   -->
<!-- REPEATER MULTI-ITEM PERSIS DENGAN MODUL KEMITRAAN & KIB A                   -->
<!-- ========================================================================= -->
<div x-show="isGedung" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB C -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-blue-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB C -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg border border-blue-500/30 shadow-inner">🏢</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik Gedung &amp; Bangunan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 font-mono font-bold text-[10px] border border-blue-500/40">
                            KIB C · Akun 1.3.3
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian konstruksi gedung, luas lantai, izin PBG/IMB, status penguasaan tanah, serta keterangan operasional.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-blue-400 bg-blue-950/60 px-3 py-1.5 rounded-xl border border-blue-500/30 shadow-sm">
                    Total: <span x-text="formData.gedung_items ? formData.gedung_items.length : 1"></span> Bangunan
                </span>
            </div>
        </div>

        <!-- REPEATER DAFTAR GEDUNG & BANGUNAN -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.gedung_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-blue-500/30 hover:border-blue-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <!-- Header Kartu Gedung -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-blue-500/20 text-blue-300 font-mono font-extrabold text-xs border border-blue-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🏢 Gedung / Bangunan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.gedung_nama_barang" x-text="item.gedung_nama_barang"></span>
                            <span class="text-[11px] text-slate-400 font-mono" x-show="item.gedung_luas_m2 || item.gedung_luas_lantai">
                                • Luas: <strong class="text-cyan-300" x-text="(item.gedung_luas_m2 || item.gedung_luas_lantai || 0).toLocaleString('id-ID') + ' m²'"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Gedung -->
                        <button type="button" 
                                x-show="formData.gedung_items.length > 1" 
                                @click="removeGedungItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Gedung Ini</span>
                        </button>
                    </div>

                    <!-- 1. Pilihan Jenis & Nama Barang PMDN 108 Gedung 1.3.3 -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-blue-500/40 space-y-2 shadow-inner">
                        <div class="relative" @click.outside="item.isFilterOpen = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-blue-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🏢 Pilih / Ketik Jenis Barang PMDN 108 (Gedung &amp; Bangunan)</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30 font-bold font-mono"
                                      x-show="item.gedung_kode_barang"
                                      x-text="'Kode 108: ' + item.gedung_kode_barang">
                                </span>
                            </div>
                            
                            <div class="relative">
                                <input type="text"
                                       x-model="item.gedung_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; syncTotalsFromItems();"
                                       placeholder="Ketik untuk memfilter jenis PMDN 108 atau tulis rincian unit gedung/bangunan..."
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-blue-500 focus:border-blue-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all pl-9 pr-11">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <button type="button" 
                                        x-show="Boolean(item.gedung_nama_barang)" 
                                        @click.stop.prevent="clearItem108(item, 'gedung'); item.gedung_kode_barang = ''; item.gedung_nama_barang = ''; item.gedung_nama_bangunan = ''; item.searchFilter = ''; formData.nama_barang = ''; item.isFilterOpen = true;" 
                                        @mousedown.stop.prevent="clearItem108(item, 'gedung'); item.gedung_kode_barang = ''; item.gedung_nama_barang = ''; item.gedung_nama_bangunan = ''; item.searchFilter = ''; formData.nama_barang = ''; item.isFilterOpen = true;" 
                                        class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-slate-400 hover:text-white transition-colors cursor-pointer z-30"
                                        title="Hapus / Kosongkan Pilihan">
                                    <span class="w-6 h-6 rounded-full bg-slate-800/90 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all border border-slate-700 hover:border-rose-500/50 shadow-sm">
                                        <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                </button>
                            </div>

                            <!-- Dropdown Hasil Filter (Strict Max 5 Baris - Zero Lag) -->
                            <div x-show="item.isFilterOpen" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-blue-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 text-[10px] text-slate-400 font-semibold flex items-center justify-between">
                                    <span>Pilihan Rekomendasi PMDN 108 (Maks. 5):</span>
                                    <span class="text-blue-400 font-mono text-[9px]">PMDN 108 Gedung &amp; Bangunan (1.3.3)</span>
                                </div>
                                <template x-for="opt in filterJenisAstap108('1.3.3', item.gedung_nama_barang, item.isFilterOpen)" :key="opt.id">
                                    <div @click="select108ForItem(item, opt, 'gedung')"
                                         class="px-3.5 py-2 hover:bg-blue-500/20 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div class="flex-1 pr-2">
                                            <div class="text-xs font-bold text-white group-hover:text-blue-300" x-text="opt.nama"></div>
                                        </div>
                                        <span class="font-mono text-[10px] text-blue-400 bg-blue-950/60 px-2 py-0.5 rounded border border-blue-500/30 shrink-0" x-text="opt.kode"></span>
                                    </div>
                                </template>
                                <div x-show="filterJenisAstap108('1.3.3', item.gedung_nama_barang, item.isFilterOpen).length === 0" 
                                     class="px-3.5 py-2.5 text-center text-xs text-slate-400 italic">
                                    <span>Gunakan nama yang Anda ketik jika tidak ada dalam daftar PMDN 108 di atas.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Grid Spesifikasi Legalitas & Fisik Gedung (2 Kolom Seragam) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 items-stretch">
                        
                        <!-- 1. Kondisi & Spesifikasi Bangunan (Struktur Fisik) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-slate-700/80 shadow-lg flex flex-col justify-between gap-3.5 transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                                    <span class="text-sm">🏗️</span>
                                    <span>Kondisi &amp; Konstruksi Bangunan:</span>
                                </span>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/25 font-semibold">
                                    Struktur Fisik
                                </span>
                            </div>

                            <div class="space-y-3.5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
                                    <!-- Luas Lantai Gedung (m²) -->
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                            Luas Lantai Gedung (m²) <span class="text-rose-400">*</span>
                                        </label>
                                        <div class="flex items-center rounded-xl bg-slate-950 border border-blue-500/40 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-500/20 overflow-hidden transition-all">
                                            <input type="number" step="0.01" min="0" x-model.number="item.gedung_luas_m2" @input="syncTotalsFromItems()"
                                                placeholder="Contoh: 450"
                                                class="w-full bg-transparent px-3 py-2.5 text-xs sm:text-sm text-cyan-300 font-mono font-bold focus:outline-none">
                                            <span class="px-3 py-2.5 text-xs font-mono font-bold text-cyan-400/90 bg-slate-900 border-l border-slate-800 shrink-0">m²</span>
                                        </div>
                                    </div>

                                    <!-- Kondisi Bangunan -->
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                            Kondisi Bangunan <span class="text-rose-400">*</span>
                                        </label>
                                        <select x-model="item.gedung_kondisi" @change="syncTotalsFromItems()"
                                            class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 font-bold focus:border-blue-400 focus:ring-2 focus:ring-blue-400/20 focus:outline-none transition-all cursor-pointer">
                                            <option value="Baik">🟢 Baik (Siap Digunakan)</option>
                                            <option value="Kurang Baik">🟡 Kurang Baik (Perlu Renovasi)</option>
                                            <option value="Rusak Berat">🔴 Rusak Berat (Tidak Layak)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
                                    <!-- Konstruksi Bertingkat -->
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                            Konstruksi Bertingkat
                                        </label>
                                        <select x-model="item.gedung_bertingkat"
                                            class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 font-semibold focus:border-blue-400 focus:ring-2 focus:ring-blue-400/20 focus:outline-none transition-all">
                                            <option value="Tidak">Tidak Bertingkat</option>
                                            <option value="Bertingkat">Bertingkat</option>
                                        </select>
                                    </div>

                                    <!-- Konstruksi Beton -->
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                            Konstruksi Beton
                                        </label>
                                        <select x-model="item.gedung_beton"
                                            class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 font-semibold focus:border-blue-400 focus:ring-2 focus:ring-blue-400/20 focus:outline-none transition-all">
                                            <option value="Beton">Beton</option>
                                            <option value="Bukan Beton">Bukan Beton</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Status Tanah & Dokumen PBG/IMB (Legalitas Izin) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-slate-700/80 shadow-lg flex flex-col justify-between gap-3.5 transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-2">
                                    <span class="text-sm">📜</span>
                                    <span>Dokumen Izin PBG/IMB &amp; Status Lahan:</span>
                                </span>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-semibold">
                                    Legalitas Izin
                                </span>
                            </div>

                            <div class="space-y-3.5">
                                <!-- Status Penguasaan Tanah -->
                                <div>
                                    <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                        Status Penguasaan Tanah Tempat Berdiri <span class="text-rose-400">*</span>
                                    </label>
                                    <select x-model="item.gedung_status_tanah"
                                        class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 font-medium focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">
                                        <option value="Tanah Pemkab Bondowoso">Tanah Pemkab Bondowoso</option>
                                        <option value="Tanah Milik RSUD">Tanah Hak Pakai Milik RSUD</option>
                                        <option value="Tanah Milik Pemkab">Tanah Milik Pemerintah Kabupaten</option>
                                        <option value="Tanah Hak Pengelolaan">Tanah Hak Pengelolaan (HPL)</option>
                                        <option value="Tanah Sewa / Pihak Ketiga">Tanah Milik Pihak Ketiga (Sewa / Pinjam Pakai)</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5 items-start">
                                    <!-- Nomor PBG / IMB -->
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold truncate" title="Nomor PBG / IMB">
                                            Nomor Izin Gedung (PBG / IMB)
                                        </label>
                                        <input type="text" x-model="item.gedung_dokumen_no"
                                            placeholder="Contoh: 640/123/IMB/2021"
                                            class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3 py-2.5 text-xs text-slate-100 font-mono focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">
                                    </div>

                                    <!-- Tanggal Dokumen Izin -->
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="block text-slate-300 text-[11px] font-semibold truncate" title="Tanggal Dokumen Izin">
                                                Tanggal Dokumen Izin
                                            </label>
                                            <span class="text-[9.5px] font-mono text-cyan-400/80 bg-cyan-950/40 px-1.5 py-0.5 rounded border border-cyan-800/40">Maks: Hari Ini</span>
                                        </div>
                                        <input type="text" x-datepicker="{ maxDate: 'today' }" x-model="item.gedung_dokumen_tgl"
                                            placeholder="dd/mm/yyyy"
                                            :class="isFutureDate(item.gedung_dokumen_tgl) ? 'border-rose-500 focus:border-rose-400 bg-rose-950/20 text-rose-300' : 'border-slate-700/90 focus:border-cyan-400 bg-slate-950 text-slate-100'"
                                            class="w-full border rounded-xl px-3 py-2.5 text-xs font-mono focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">
                                        <div x-show="isFutureDate(item.gedung_dokumen_tgl)" class="mt-1 flex items-center gap-1 text-[10px] text-rose-400 font-semibold animate-pulse">
                                            <span>⚠️ Tanggal dokumen izin tidak boleh melebihi hari ini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Keterangan / Fungsi Peruntukan Gedung (Catatan Tambahan) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-slate-700/80 space-y-2 shadow-lg flex flex-col justify-between transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <label class="block text-slate-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span class="text-sm">📝</span>
                                    <span>Keterangan / Fungsi Peruntukan Gedung:</span>
                                </label>
                                <span class="text-[9.5px] px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-bold">Catatan Tambahan</span>
                            </div>
                            <div>
                                <input type="text" x-model="item.gedung_fungsi"
                                    placeholder="Contoh: Gedung Rawat Inap Paviliun Mawar / Laboratorium Sentral &amp; Instalasi Farmasi"
                                    class="w-full bg-slate-950 border border-slate-700/90 hover:border-blue-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/20 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 font-medium focus:outline-none transition-all shadow-inner">
                                <span class="text-[10px] text-slate-400 mt-1 block">Rincian fungsi operasional, peruntukan ruangan, atau catatan teknis bangunan.</span>
                            </div>
                        </div>

                        <!-- 4. Letak / Alamat Lokasi Fisik Gedung -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-emerald-500/40 hover:border-emerald-500/70 space-y-2 shadow-lg flex flex-col justify-between transition-all">
                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                <label class="block text-emerald-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span class="text-sm">📍</span>
                                    <span>Letak / Alamat Gedung &amp; Lokasi Fisik:</span>
                                </label>
                                <span class="text-[9.5px] px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik</span>
                            </div>
                            <div>
                                <input type="text" x-model="item.gedung_alamat"
                                    placeholder="Contoh: Kompleks RSUD Dr. H. Koesnandi, Jl. Piere Tendean No. 1, Bondowoso"
                                    class="w-full bg-slate-950 border border-slate-700/90 hover:border-emerald-500 focus:border-emerald-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                                <span class="text-[10px] text-slate-400 mt-1 block">Alamat lengkap letak persil/blok gedung di lingkungan RSUD.</span>
                            </div>
                        </div>

                        <!-- 5. Volume & Nilai Perolehan BMD Gedung (Full Width) -->
                        <div class="col-span-1 lg:col-span-2 p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-slate-900/95 via-blue-950/20 to-slate-900/95 border border-blue-500/50 space-y-4 shadow-xl shadow-blue-500/5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-blue-500/30 pb-3 gap-2.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-sm font-bold border border-blue-500/30 shadow-inner">💰</span>
                                    <div>
                                        <span class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider block">
                                            Volume &amp; Nilai Perolehan BMD Gedung (Rp)
                                        </span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">Nilai perolehan BMD per unit gedung sesuai Berita Acara (BAMB/BAST)</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 bg-blue-950/80 border border-blue-500/40 px-3.5 py-1.5 rounded-xl shadow-sm self-start sm:self-auto">
                                    <span class="text-[11px] text-slate-300 font-semibold">Sub Total Gedung #<span x-text="idx + 1"></span>:</span>
                                    <span class="text-xs sm:text-sm font-black text-emerald-300 font-mono tracking-tight" x-text="'Rp ' + Number(getGedungSubtotal(item)).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                                {{-- Input 1: Jumlah Volume / Gedung --}}
                                <div>
                                    <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold flex items-center justify-between">
                                        <span>Jumlah Volume / Bangunan <span class="text-rose-400">*</span></span>
                                        <span class="text-[9.5px] font-mono text-blue-400 bg-blue-950/50 px-2 py-0.5 rounded border border-blue-800/40 font-bold">Kuantitas</span>
                                    </label>
                                    <input type="number" min="1" x-model.number="item.gedung_jumlah_bangunan"
                                           @keydown="handleVolumeKeydown($event)"
                                           @input="if (item.gedung_jumlah_bangunan !== '' && item.gedung_jumlah_bangunan !== null && parseInt(item.gedung_jumlah_bangunan) < 1) item.gedung_jumlah_bangunan = 1; syncTotalsFromItems();"
                                           @blur="normalizeVolume(item, 'gedung_jumlah_bangunan')"
                                           @change="normalizeVolume(item, 'gedung_jumlah_bangunan')"
                                           placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700/90 hover:border-blue-500 focus:border-blue-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none transition-all shadow-inner">
                                </div>

                                {{-- Input 2: Satuan --}}
                                <div>
                                    <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold flex items-center justify-between">
                                        <span>Satuan <span class="text-rose-400">*</span></span>
                                        <span class="text-[9.5px] font-mono text-slate-400 font-bold">Unit Ukur</span>
                                    </label>
                                    <input type="text" x-model="item.gedung_satuan" @input="syncTotalsFromItems()" placeholder="Gedung / Unit"
                                           class="w-full bg-slate-950 border border-slate-700/90 hover:border-blue-500 focus:border-blue-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                                </div>

                                {{-- Input 3: Nilai Perolehan Satuan (Rp) --}}
                                <div>
                                    <div class="flex items-center justify-between gap-1 mb-1.5">
                                        <label class="text-slate-300 text-[11px] font-semibold">
                                            Nilai Perolehan Satuan (Rp) <span class="text-rose-400">*</span>
                                        </label>
                                        <span class="text-[9.5px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/25 font-mono shrink-0">
                                            Sesuai Dokumen
                                        </span>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span class="absolute left-3.5 text-emerald-400 text-xs font-mono font-extrabold select-none">Rp</span>
                                        <input type="text" 
                                               :value="item.gedung_nilai_satuan ? Number(item.gedung_nilai_satuan).toLocaleString('id-ID') : ''"
                                               @input="
                                                   let raw = $event.target.value.replace(/\D/g, '');
                                                   item.gedung_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                                   item.gedung_nilai_fisik = item.gedung_nilai_satuan;
                                                   $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                   syncTotalsFromItems();
                                               "
                                               placeholder="Contoh: 1.250.000.000"
                                               class="w-full bg-slate-950 border border-blue-500/50 hover:border-blue-400 focus:border-blue-400 text-emerald-300 font-mono font-bold rounded-xl pl-11 pr-3.5 py-2.5 text-xs sm:text-sm focus:outline-none transition-all shadow-inner">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 flex items-center gap-2 text-[10.5px] text-slate-400 border-t border-slate-800/60">
                                <span class="text-xs">💡</span>
                                <span><strong class="text-slate-300">Catatan Nilai Gedung:</strong> Nilai perolehan gedung adalah nilai total perolehan keseluruhan untuk Bangunan Gedung #<span x-text="idx + 1"></span> (lump-sum perolehan per unit, otomatis mengakumulasi total realisasi di SIMAT-RK).</span>
                            </div>
                        </div>

                    </div>
                </div>
            </template>
        </div>

        <!-- Tombol Tambah Gedung Baru (Desain Pill Seragam) -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addGedungItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer active:scale-95">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Bangunan Gedung Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Nilai Gedung:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
            </div>
        </div>

    </div>

</div>
