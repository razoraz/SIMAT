<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: GEDUNG & BANGUNAN (KIB C / AKUN 1.5.2.01.01.xx.003)    -->
<!-- MULTI-ITEM REPEATER (MODEL PERSIS KIB B PERALATAN & MESIN)                 -->
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
                            KIB C
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
                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 hover:border-blue-500/50 transition-all space-y-4 shadow-lg relative">
                    
                    <!-- Header Kartu Gedung -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-blue-500/20 text-blue-300 font-mono font-extrabold text-xs border border-blue-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🏢 Gedung / Bangunan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.gedung_nama_barang" x-text="item.gedung_nama_barang"></span>
                            <span class="text-[10.5px] text-slate-400 font-mono" x-show="item.gedung_luas_lantai">
                                • Luas: <strong class="text-cyan-300" x-text="(item.gedung_luas_lantai || 0) + ' m²'"></strong>
                            </span>
                            <span class="text-[10.5px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Gedung -->
                        <button type="button"
                            x-show="formData.gedung_items.length > 1"
                            @click="removeGedungItem(idx)"
                            class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer self-end sm:self-auto">
                            <span>🗑️ Hapus Gedung</span>
                        </button>
                    </div>

                    <!-- 1. Pilihan Jenis & Nama Barang PMDN 108 (Satu Input Filter & Ketik Langsung) -->
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
                                       placeholder="Ketik untuk memfilter jenis PMDN 108 atau tulis rincian gedung / ruangan..."
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-blue-500 focus:border-blue-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all pl-9">
                                <div class="absolute left-3 top-3 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <button type="button" 
                                        x-show="item.gedung_nama_barang" 
                                        @click="item.gedung_nama_barang = ''; item.gedung_kode_barang = ''; item.isFilterOpen = true; syncTotalsFromItems();" 
                                        class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-300 text-xs cursor-pointer">✕</button>
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

                    <!-- Grid Form Spesifikasi Gedung -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- 1. Kondisi & Spesifikasi Bangunan -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🏗️ Kondisi &amp; Konstruksi:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Struktur Fisik</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Luas Total Lantai (m²) -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Luas Lantai Gedung (m²) <span class="text-rose-400">*</span>
                                    </label>
                                    <div class="flex items-center rounded-xl bg-slate-900 border border-slate-700 focus-within:border-amber-500 overflow-hidden transition-all">
                                        <input type="number" step="0.01" min="0" x-model.number="item.gedung_luas_lantai"
                                            placeholder="850"
                                            class="w-full bg-transparent px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none">
                                        <span class="px-2.5 py-2 text-[10px] font-mono font-bold text-slate-400 bg-slate-800/80 border-l border-slate-700 shrink-0">m²</span>
                                    </div>
                                </div>

                                <!-- Kondisi Bangunan -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Kondisi Bangunan <span class="text-rose-400">*</span>
                                    </label>
                                    <select x-model="item.gedung_kondisi"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Baik">🟢 Baik (B) &mdash; Siap Digunakan</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Bertingkat / Tidak -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Konstruksi Bertingkat
                                    </label>
                                    <select x-model="item.gedung_bertingkat"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Tidak">Tidak Bertingkat (1 Lantai)</option>
                                        <option value="Bertingkat">Bertingkat (2 Lantai atau Lebih)</option>
                                    </select>
                                </div>

                                <!-- Beton / Tidak -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Konstruksi Beton / Rangka
                                    </label>
                                    <select x-model="item.gedung_beton"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Beton Bertulang">Beton Bertulang (Permanen)</option>
                                        <option value="Rangka Baja">Rangka Baja / Pre-cast</option>
                                        <option value="Semi Permanen">Semi Permanen</option>
                                        <option value="Kayu / Lainnya">Kayu / Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Status Tanah & Dokumen PBG/IMB -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📜 Dokumen PBG/IMB &amp; Status:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Legalitas</span>
                            </div>

                            <!-- Status Tanah Tempat Berdiri -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Status Penguasaan Tanah Tempat Berdiri <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="item.gedung_status_tanah"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-cyan-500 focus:outline-none transition-all">
                                    <option value="Tanah Milik RSUD">Tanah Hak Pakai Milik RSUD</option>
                                    <option value="Tanah Milik Pemkab">Tanah Milik Pemerintah Kabupaten</option>
                                    <option value="Tanah Sewa Mitra">Tanah Milik Pihak Ketiga (Sewa)</option>
                                    <option value="Tanah Hak Pengelolaan">Tanah Hak Pengelolaan (HPL)</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Nomor PBG / IMB -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Nomor PBG / IMB / SLF
                                    </label>
                                    <input type="text" x-model="item.gedung_dokumen_no"
                                        placeholder="PBG-3511/RSUD/2026"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                </div>

                                <!-- Tanggal PBG / IMB -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Tanggal Terbit PBG / IMB
                                    </label>
                                    <input type="text" x-datepicker x-model="item.gedung_dokumen_tgl"
                                        placeholder="dd/mm/yyyy"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 3. Keterangan / Catatan Khusus Bangunan (Diletakkan di Atas Lokasi Fisik) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 space-y-1.5 shadow-md transition-all">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <label class="block text-slate-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📝 KETERANGAN / CATATAN KHUSUS BANGUNAN:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-bold">Catatan Tambahan</span>
                        </div>
                        <input type="text" x-model="item.gedung_fungsi"
                            placeholder="Contoh: Gedung Rawat Inap Kelas VVIP / Kantin &amp; Pujasera Kemitraan / Spesifikasi Bangunan"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-cyan-500 rounded-xl px-3.5 py-2 text-xs text-white font-medium focus:outline-none transition-all">
                    </div>

                    <!-- 4. Letak / Alamat Lokasi Fisik Bangunan -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/40 space-y-1.5 shadow-md">
                        <div class="flex items-center justify-between border-b border-emerald-500/30 pb-1.5">
                            <label class="block text-emerald-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📍 Letak / Alamat Lokasi Fisik Bangunan:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik</span>
                        </div>
                        <input type="text" x-model="item.gedung_alamat" placeholder="Contoh: Jl. Piere Tendean No. 3 Bondowoso (Kompleks RSUD Dr. H. Koesnandi - Blok Paviliun Melati)"
                               class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500 transition-all">
                    </div>

                    <!-- 5. Volume & Taksiran Nilai Wajar Bangunan -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-3 shadow-md">
                        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Volume &amp; Taksiran Nilai Wajar Bangunan (Rp):</span>
                            </span>
                            <div class="flex items-center space-x-2 bg-emerald-950/70 border border-emerald-500/40 px-3 py-1 rounded-xl shadow-sm">
                                <span class="text-[10px] text-slate-300 font-semibold">Sub Total Gedung #<span x-text="idx + 1"></span>:</span>
                                <span class="text-xs font-black text-emerald-300 font-mono tracking-tight" x-text="'Rp ' + Number(getGedungSubtotal(item)).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            {{-- Baris 1: Jumlah Volume & Satuan --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1.5 font-semibold">Jumlah Volume / Bangunan <span class="text-rose-400">*</span></label>
                                    <input type="number" min="1" x-model.number="item.gedung_jumlah_bangunan" @input="syncTotalsFromItems()" placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-500 rounded-xl px-3.5 py-2 text-xs text-white font-mono font-bold focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1.5 font-semibold">Satuan <span class="text-rose-400">*</span></label>
                                    <input type="text" x-model="item.gedung_satuan" @input="syncTotalsFromItems()" placeholder="Gedung / Unit"
                                           class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-500 rounded-xl px-3.5 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                            </div>
                            {{-- Baris 2: Taksiran Nilai (full width) --}}
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                    <label class="text-slate-400 text-[10px] font-semibold">
                                        Taksiran Total Nilai Wajar Gedung (Rp) <span class="text-rose-400">*</span>
                                    </label>
                                    <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/25 font-mono whitespace-nowrap">
                                        Lump-sum • Nilai Keseluruhan
                                    </span>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-emerald-500 text-xs font-mono font-extrabold select-none">Rp</span>
                                    <input type="text" 
                                           :value="item.gedung_nilai_satuan ? Number(item.gedung_nilai_satuan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.gedung_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncTotalsFromItems();
                                           "
                                           placeholder="Contoh: 1.500.000.000"
                                           class="w-full bg-slate-950 border border-emerald-500/40 hover:border-emerald-500/60 focus:border-emerald-400 text-emerald-300 rounded-xl pl-10 pr-3.5 py-2 text-xs font-mono font-bold focus:outline-none transition-all shadow-inner">
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center gap-2 text-[10.5px] text-slate-400 border-t border-slate-800/60">
                            <span class="text-xs">💡</span>
                            <span><strong class="text-slate-300">Catatan:</strong> Taksiran nilai gedung adalah nilai appraisal total keseluruhan untuk Gedung #<span x-text="idx + 1"></span> (lump-sum, tidak dikalikan jumlah bangunan).</span>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Gedung / Bangunan Baru -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addGedungItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Bangunan / Gedung Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Gedung:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
            </div>
        </div>

    </div>

</div>
