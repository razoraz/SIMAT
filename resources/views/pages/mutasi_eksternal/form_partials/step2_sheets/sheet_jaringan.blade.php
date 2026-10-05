<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: JALAN, IRIGASI & JARINGAN (KIB D / AKUN 1.3.4)         -->
<!-- REPEATER MULTI-ITEM BESPOKE SERAGAM DENGAN KIB A, KIB B, KIB C, KIB E      -->
<!-- ========================================================================= -->
<div x-show="isJaringan" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB D Multi-Item Repeater -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-teal-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB D -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg border border-teal-500/30 shadow-inner">🛣️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik Jalan, Irigasi &amp; Jaringan Pelimpahan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 font-mono font-bold text-[10px] border border-teal-500/40">
                            KIB D · Akun 1.3.4
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Dapat mencatat jalan lingkungan, jembatan, instalasi pemipaan air, instalasi kabel listrik, atau jaringan IT pelimpahan SKPD lengkap dengan nilai BMD.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-teal-400 bg-teal-950/60 px-3 py-1.5 rounded-xl border border-teal-500/30 shadow-sm">
                    Total: <span x-text="formData.jaringan_items ? formData.jaringan_items.length : 1"></span> Ruas / Titik
                </span>
            </div>
        </div>

        <!-- List Kartu Jaringan (Repeater Multi-Item) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.jaringan_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-teal-500/30 hover:border-teal-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <!-- Header Kartu Tiap Ruas -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-teal-500/20 text-teal-300 font-mono font-extrabold text-xs border border-teal-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🛣️ Ruas / Jaringan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.jaringan_nama_barang" x-text="item.jaringan_nama_barang"></span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg border font-mono"
                                :class="(item.jaringan_kondisi === 'Baik' || !item.jaringan_kondisi) ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : (item.jaringan_kondisi === 'Kurang Baik' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40')"
                                x-text="'• Kondisi: ' + (item.jaringan_kondisi || 'Baik')">
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono" x-show="item.jaringan_luas_m2">
                                • Luas: <strong class="text-indigo-300" x-text="(item.jaringan_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Ruas (Muncul jika > 1 item) -->
                        <button type="button" 
                                x-show="formData.jaringan_items.length > 1" 
                                @click="removeJaringanItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Ruas Ini</span>
                        </button>
                    </div>

                    <!-- 1. Pilihan Jenis & Nama Barang PMDN 108 (Satu Input Filter & Ketik Langsung) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-teal-500/40 space-y-2 shadow-inner">
                        <div class="relative" @click.outside="item.isFilterOpen = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-teal-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🛣️ Pilih / Ketik Jenis Barang PMDN 108 (Jalan, Irigasi &amp; Jaringan)</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] px-2 py-0.5 rounded-md bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold font-mono"
                                      x-show="item.jaringan_kode_barang"
                                      x-text="'Kode 108: ' + item.jaringan_kode_barang">
                                </span>
                            </div>
                            
                            <div class="relative flex items-center" style="position: relative;">
                                <svg class="w-4 h-4 text-teal-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text"
                                       x-model="item.jaringan_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; syncTotalsFromItems();"
                                       placeholder="Ketik untuk memfilter jenis PMDN 108 atau tulis rincian ruas jalan/irigasi/jaringan..."
                                       style="padding-left: 38px; padding-right: 36px;"
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-teal-500 focus:border-teal-500 rounded-xl py-2.5 text-xs text-white font-bold focus:outline-none transition-all shadow-inner">
                                <button type="button" 
                                        x-show="item.jaringan_nama_barang" 
                                        @click="item.jaringan_nama_barang = ''; item.jaringan_kode_barang = ''; item.isFilterOpen = true; syncTotalsFromItems();" 
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
                                 class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-teal-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 text-[10px] text-slate-400 font-semibold flex items-center justify-between">
                                    <span>Pilihan Rekomendasi PMDN 108 (Maks. 5):</span>
                                    <span class="text-teal-400 font-mono text-[9px]">PMDN 108 Jaringan (1.3.4)</span>
                                </div>
                                <template x-for="opt in filterJenisAstap108('1.3.4', item.jaringan_nama_barang, item.isFilterOpen)" :key="opt.id">
                                    <div @click="select108ForItem(item, opt, 'jaringan')"
                                         class="px-3.5 py-2 hover:bg-teal-500/20 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div class="flex-1 pr-2">
                                            <div class="text-xs font-bold text-white group-hover:text-teal-300" x-text="opt.nama"></div>
                                        </div>
                                        <span class="font-mono text-[10px] text-teal-400 bg-teal-950/60 px-2 py-0.5 rounded border border-teal-500/30 shrink-0" x-text="opt.kode"></span>
                                    </div>
                                </template>
                                <div x-show="filterJenisAstap108('1.3.4', item.jaringan_nama_barang, item.isFilterOpen).length === 0" 
                                     class="px-3.5 py-2.5 text-center text-xs text-slate-400 italic">
                                    <span>Gunakan nama yang Anda ketik jika tidak ada dalam daftar PMDN 108 di atas.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Grid Spesifikasi Legalitas & Fisik Jaringan (2 Kolom Seragam) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Kolom Kiri: Konstruksi & Dimensi Jaringan -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-teal-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🏗️ Konstruksi &amp; Dimensi Jaringan:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-teal-500/10 text-teal-300 border border-teal-500/20 font-bold">Fisik &amp; Dimensi</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas Jaringan (m²)</label>
                                    <div class="flex items-center rounded-xl bg-slate-950 border border-teal-500/40 focus-within:border-teal-400 overflow-hidden transition-all">
                                        <input type="number" step="0.01" min="0" x-model.number="item.jaringan_luas_m2" @input="syncTotalsFromItems()"
                                            placeholder="Contoh: 300"
                                            class="w-full bg-transparent px-3 py-2 text-xs text-indigo-300 font-mono font-bold focus:outline-none">
                                        <span class="px-2.5 py-2 text-[10px] font-mono font-bold text-teal-400/80 bg-slate-900 border-l border-slate-800 shrink-0">m²</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tipe Jalur / Konstruksi</label>
                                    <select x-model="item.jaringan_bertingkat"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-teal-500 focus:outline-none transition-all">
                                        <option value="Tidak">Permukaan Tanah (Standar)</option>
                                        <option value="Bawah Tanah">Bawah Tanah / Terpendam</option>
                                        <option value="Bertingkat 2 Lantai">Konstruksi Layang / Jembatan</option>
                                        <option value="Bertingkat 3 Lantai">Tower / Jaringan Udara</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Jaringan <span class="text-rose-400">*</span></label>
                                    <select x-model="item.jaringan_kondisi" @change="syncTotalsFromItems()"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-teal-500 focus:outline-none transition-all cursor-pointer">
                                        <option value="Baik">🟢 Baik (Berfungsi Normal)</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (Perlu Perbaikan)</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (Macet/Putus)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jenis Konstruksi / Bahan</label>
                                    <select x-model="item.jaringan_beton"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-teal-500 focus:outline-none transition-all">
                                        <option value="Beton">Beton Bertulang</option>
                                        <option value="Aspal">Aspal Hotmix</option>
                                        <option value="Pipa / HDPE">Pipa HDPE / Besi Air Medis</option>
                                        <option value="Kabel / FO">Kabel Listrik / Fiber Optik IT</option>
                                        <option value="Bukan Beton">Bukan Beton / Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Status Tanah & Dokumen Kontrak/Izin -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📜 Dokumen Kontrak &amp; Status Lahan:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Legalitas Kontrak</span>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Penguasaan Tanah</label>
                                <input type="text" x-model="item.jaringan_status_tanah"
                                    placeholder="Contoh: Tanah Pemkab Bondowoso / Hak Pakai RSUD"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nomor Dokumen Kontrak / BAST</label>
                                    <input type="text" x-model="item.jaringan_dokumen_no"
                                        placeholder="Contoh: 600/45/SPK-JAR/2022"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-slate-400 text-[10px] font-semibold">Tanggal Dokumen Kontrak</label>
                                        <span class="text-[9px] font-mono text-cyan-400/80 bg-cyan-950/40 px-1.5 py-0.5 rounded border border-cyan-800/40">Maks: Hari Ini</span>
                                    </div>
                                    <input type="text" x-datepicker="{ maxDate: 'today' }" x-model="item.jaringan_dokumen_tgl"
                                        placeholder="dd/mm/yyyy"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 3. Keterangan / Spesifikasi Teknis Jaringan -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 space-y-1.5 shadow-md transition-all">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <label class="block text-slate-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📝 KETERANGAN / SPESIFIKASI TEKNIS JARINGAN:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-bold">Catatan Tambahan</span>
                        </div>
                        <input type="text" x-model="item.jaringan_keterangan"
                            placeholder="Contoh: Pipa Distribusi Air Bersih Diameter 4 Inch dari Reservoir ke Gedung Rawat Inap"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-teal-500 rounded-xl px-3.5 py-2 text-xs text-white font-medium focus:outline-none transition-all">
                    </div>

                    <!-- 4. Lokasi Fisik / Alamat Letak Jaringan (Persis KIB A) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/40 space-y-1.5 shadow-md">
                        <div class="flex items-center justify-between border-b border-emerald-500/30 pb-1.5">
                            <label class="block text-emerald-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📍 LETAK / ALAMAT JARINGAN &amp; LOKASI FISIK:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik</span>
                        </div>
                        <input type="text" x-model="item.jaringan_alamat"
                            placeholder="Contoh: Kompleks RSUD Dr. H. Koesnandi, Jl. Piere Tendean No. 1, Bondowoso"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-500 rounded-xl px-3.5 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500 transition-all">
                    </div>

                    <!-- 5. Kalkulator Volume & Nilai Perolehan BMD Jaringan (Format Seragam KIB B/E/A/C) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-3 shadow-md">
                        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Volume &amp; Nilai Perolehan BMD Jaringan (Rp):</span>
                            </span>
                            <div class="flex items-center space-x-2 bg-emerald-950/70 border border-emerald-500/40 px-3 py-1 rounded-xl shadow-sm">
                                <span class="text-[10px] text-slate-300 font-semibold">Sub Total Ruas #<span x-text="idx + 1"></span>:</span>
                                <span class="text-xs font-black text-emerald-300 font-mono tracking-tight" x-text="'Rp ' + Number(getJaringanSubtotal(item)).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume) <span class="text-rose-400">*</span></label>
                                <input type="number" min="1" x-model.number="item.jaringan_jumlah" @input="syncTotalsFromItems()" placeholder="1"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:border-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan <span class="text-rose-400">*</span></label>
                                <input type="text" x-model="item.jaringan_satuan" @input="syncTotalsFromItems()" placeholder="Ruas / Titik / Meter"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                    <span>Nilai Perolehan Satuan (Rp) *</span>
                                    <span class="text-[9px] font-bold text-emerald-400">Sesuai BAMB</span>
                                </label>
                                <input type="text" 
                                       :value="item.jaringan_nilai_satuan ? Number(item.jaringan_nilai_satuan).toLocaleString('id-ID') : ''"
                                       @input="
                                           let raw = $event.target.value.replace(/\D/g, '');
                                           item.jaringan_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                           $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                           syncTotalsFromItems();
                                       "
                                       placeholder="Contoh: 150.000.000"
                                       class="w-full bg-slate-950 border border-slate-700 text-emerald-300 focus:border-emerald-500 rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-emerald-400 text-[10px] mb-1 font-bold">Sub Total Item #<span x-text="idx + 1"></span> (Rp)</label>
                                <div class="w-full bg-slate-950/90 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-black flex items-center justify-between shadow-inner">
                                    <span class="text-emerald-500 text-[10px]">Rp</span>
                                    <span x-text="Number(getJaringanSubtotal(item)).toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Ruas Jaringan Baru (Desain Pill Seragam) -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addJaringanItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer active:scale-95">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Ruas / Jaringan Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Nilai Jaringan:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiJaringan)"></span>
            </div>
        </div>

    </div>

</div>
