<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: GEDUNG & BANGUNAN (KIB C) UNTUK HIBAH                   -->
<!-- REPEATER MULTI-ITEM — MIRROR KEMITRAAN DENGAN PALET AMBER                  -->
<!-- ========================================================================= -->
<div x-show="isGedung" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB C -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB C -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg border border-amber-500/30 shadow-inner">🏢</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik Gedung &amp; Bangunan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold text-[10px] border border-amber-500/40">
                            KIB C
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian konstruksi gedung hibah, luas lantai, status penguasaan tanah, serta keterangan operasional.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-amber-400 bg-amber-950/60 px-3 py-1.5 rounded-xl border border-amber-500/30 shadow-sm">
                    Total: <span x-text="formData.gedung_items ? formData.gedung_items.length : 1"></span> Bangunan
                </span>
            </div>
        </div>

        <!-- REPEATER DAFTAR GEDUNG & BANGUNAN -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.gedung_items" :key="idx">
                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 hover:border-amber-500/50 transition-all space-y-4 shadow-lg relative">
                    
                    <!-- Header Kartu Gedung -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-amber-500/20 text-amber-300 font-mono font-extrabold text-xs border border-amber-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🏢 Gedung / Bangunan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.gedung_nama_barang" x-text="item.gedung_nama_barang"></span>
                            <span class="text-[10.5px] text-slate-400 font-mono" x-show="item.gedung_luas_m2 || item.gedung_luas_lantai">
                                • Luas: <strong class="text-amber-300" x-text="(item.gedung_luas_m2 || item.gedung_luas_lantai || 0) + ' m²'"></strong>
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

                    <!-- Pilihan Jenis & Nama Barang PMDN 108 KIB C -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-amber-500/40 space-y-2 shadow-inner">
                        <div class="relative" @click.outside="item.isFilterOpen = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-amber-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🏢 Pilih / Ketik Jenis Barang PMDN 108 (Gedung &amp; Bangunan)</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold font-mono"
                                      x-show="item.gedung_kode_barang"
                                      x-text="'Kode 108: ' + item.gedung_kode_barang">
                                </span>
                            </div>
                            
                            <div class="relative">
                                <input type="text"
                                       x-model="item.gedung_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; formData.nama_barang = item.gedung_nama_barang; syncActiveKibTotals();"
                                       placeholder="Ketik untuk memfilter jenis PMDN 108 atau tulis rincian gedung / ruangan hibah..."
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all pl-9 pr-11 shadow-inner">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <button type="button" 
                                        x-show="Boolean(item.gedung_nama_barang)" 
                                        @click.stop.prevent="clearItem108(item, 'gedung'); item.gedung_nama_barang = ''; item.gedung_kode_barang = ''; item.isFilterOpen = true;" 
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
                                 class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-amber-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 text-[10px] text-slate-400 font-semibold flex items-center justify-between">
                                    <span>Pilihan Rekomendasi PMDN 108 (Maks. 5):</span>
                                    <span class="text-amber-400 font-mono text-[9px]">PMDN 108 Gedung &amp; Bangunan (1.3.3)</span>
                                </div>
                                <template x-for="opt in filterJenisAstap108('1.3.3', item.gedung_nama_barang, item.isFilterOpen)" :key="opt.id">
                                    <div @click="select108ForItem(item, opt, 'gedung')"
                                         class="px-3.5 py-2 hover:bg-amber-500/20 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div class="flex-1 pr-2">
                                            <div class="text-xs font-bold text-white group-hover:text-amber-300" x-text="opt.nama"></div>
                                        </div>
                                        <span class="font-mono text-[10px] text-amber-400 bg-amber-950/60 px-2 py-0.5 rounded border border-amber-500/30 shrink-0" x-text="opt.kode"></span>
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
                                <!-- Luas Lantai (m²) -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Luas Lantai Gedung (m²) <span class="text-rose-400">*</span>
                                    </label>
                                    <div class="flex items-center rounded-xl bg-slate-900 border border-slate-700 focus-within:border-amber-500 overflow-hidden transition-all">
                                        <input type="number" step="0.01" min="0" x-model.number="item.gedung_luas_m2"
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
                                        <option value="Tidak">Tidak Bertingkat</option>
                                        <option value="Bertingkat">Bertingkat</option>
                                    </select>
                                </div>

                                <!-- Beton / Tidak -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Konstruksi Beton
                                    </label>
                                    <select x-model="item.gedung_beton"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Beton">Beton</option>
                                        <option value="Bukan Beton">Bukan Beton</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Status Fisik Bangunan (Baru / Lama) -->
                            <div class="pt-1 border-t border-slate-800/60">
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Status Fisik Bangunan</span>
                                    <span class="text-[9.5px] text-amber-400 font-bold" x-text="item.gedung_is_baru === 'Lama' ? '🏛️ Bangunan Lama (Eksisting)' : '✨ Bangunan Baru'"></span>
                                </label>
                                <select x-model="item.gedung_is_baru"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                    <option value="Lama">🏛️ Lama &mdash; Bangunan Eksisting / Sebelum Hibah</option>
                                    <option value="Baru">✨ Baru &mdash; Dibangun Baru dari Hibah</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. Status Tanah & Dokumen Kepemilikan -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📜 Status Tanah Tapak:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Legalitas Lahan</span>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Status Penguasaan Tanah Tapak <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="item.gedung_status_tanah"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                    <option value="Tanah Hak Pakai RSUD">Tanah Hak Pakai RSUD Dr. H. Koesnandi</option>
                                    <option value="Tanah Milik Pemkab Bondowoso">Tanah Milik Pemkab Bondowoso</option>
                                    <option value="Tanah Negara / Lainnya">Tanah Negara / Pihak Ketiga</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Kode Aset Tanah Tapak
                                </label>
                                <input type="text" x-model="item.gedung_kode_aset_tanah"
                                    placeholder="1.3.1.01.01.02.013"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                            </div>
                        </div>

                    </div>

                    <!-- 3. Letak / Alamat Lokasi Fisik Bangunan -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-1.5 shadow-md">
                        <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                            <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📍 Letak / Alamat Lokasi Fisik Bangunan:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik</span>
                        </div>
                        <input type="text" x-model="item.gedung_alamat" placeholder="Contoh: Jl. Piere Tendean No. 1 Bondowoso (Kompleks RSUD Dr. H. Koesnandi - Blok Paviliun Melati)"
                               class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-amber-500 transition-all">
                    </div>

                    <!-- 4. Volume & Taksiran Nilai Wajar Bangunan Hibah -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-3 shadow-md">
                        <div class="flex items-center justify-between border-b border-amber-500/20 pb-2">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Volume &amp; Taksiran Nilai Wajar Bangunan Hibah (Rp):</span>
                            </span>
                            <div class="flex items-center space-x-2 bg-amber-950/70 border border-amber-500/40 px-3 py-1 rounded-xl shadow-sm">
                                <span class="text-[10px] text-slate-300 font-semibold">Subtotal Gedung #<span x-text="idx + 1"></span>:</span>
                                <span class="text-xs font-black text-amber-300 font-mono tracking-tight" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1.5 font-semibold">Jumlah Volume / Bangunan <span class="text-rose-400">*</span></label>
                                    <input type="number" min="1"
                                        x-model.number="item.gedung_jumlah_bangunan"
                                        @keydown="if(['-', '+', 'e', 'E', '.'].includes($event.key)) $event.preventDefault();"
                                        @input="
                                            let raw = String($event.target.value).replace(/\D/g, '');
                                            if (raw.startsWith('0')) raw = raw.replace(/^0+/, '');
                                            item.gedung_jumlah_bangunan = raw ? parseInt(raw, 10) : '';
                                            syncActiveKibTotals();
                                        "
                                        @blur="
                                            if (!item.gedung_jumlah_bangunan || Number(item.gedung_jumlah_bangunan) < 1) {
                                                item.gedung_jumlah_bangunan = 1;
                                                syncActiveKibTotals();
                                            }
                                        "
                                        :class="(!item.gedung_jumlah_bangunan || Number(item.gedung_jumlah_bangunan) < 1) ? 'border-rose-500 ring-1 ring-rose-500 text-rose-300' : 'border-slate-700 text-white focus:border-amber-500'"
                                        placeholder="1"
                                        class="w-full bg-slate-950 border rounded-xl px-3.5 py-2 text-xs font-mono font-bold focus:outline-none transition-colors">
                                    <template x-if="!item.gedung_jumlah_bangunan || Number(item.gedung_jumlah_bangunan) < 1">
                                        <span class="text-[9.5px] font-bold text-rose-400 block mt-1">⚠️ Minimal 1 bangunan.</span>
                                    </template>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1.5 font-semibold">Satuan <span class="text-rose-400">*</span></label>
                                    <input type="text" x-model="item.gedung_satuan" @input="syncActiveKibTotals()" placeholder="Gedung / Unit"
                                           class="w-full bg-slate-950 border border-slate-700 focus:border-amber-500 rounded-xl px-3.5 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                    <label class="text-slate-400 text-[10px] font-semibold">
                                        Taksiran Total Nilai Wajar Gedung Hibah (Rp) <span class="text-rose-400">*</span>
                                    </label>
                                    <span class="text-[9px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/25 font-mono whitespace-nowrap">
                                        Lump-sum • Nilai Keseluruhan
                                    </span>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-amber-500 text-xs font-mono font-extrabold select-none">Rp</span>
                                    <input type="text" 
                                           :value="item.gedung_nilai_fisik ? Number(item.gedung_nilai_fisik).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.gedung_nilai_fisik = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncActiveKibTotals();
                                           "
                                           placeholder="Contoh: 1.500.000.000"
                                           class="w-full bg-slate-950 border border-amber-500/40 hover:border-amber-500/60 focus:border-amber-400 text-amber-300 rounded-xl pl-10 pr-3.5 py-2 text-xs font-mono font-bold focus:outline-none transition-all shadow-inner">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Gedung -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addGedungItem()"
                class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-lg shadow-amber-500/20 flex items-center space-x-2 cursor-pointer">
                <span>➕ Tambah Bangunan / Gedung Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Gedung:</span>
                <span class="font-mono font-extrabold text-amber-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
            </div>
        </div>

    </div>

</div>
