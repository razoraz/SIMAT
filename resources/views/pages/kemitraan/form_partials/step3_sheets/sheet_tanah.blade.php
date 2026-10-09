<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: TANAH (KIB A / AKUN 1.5.2.01.01.xx.001)               -->
<!-- REPEATER MULTI-ITEM PERSIS LANGKAH 3 BELANJA MODAL (ASTAP)                -->
<!-- ========================================================================= -->
<div x-show="isTanah" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB A -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-emerald-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB A -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/30 shadow-inner">🌾</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik &amp; Legalitas Tanah
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/40">
                            KIB A
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bisa menambah beberapa bidang tanah dengan status hak, sertifikat, luas bidang, dan letak lokasi masing-masing.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-950/60 px-3 py-1.5 rounded-xl border border-emerald-500/30 shadow-sm">
                    Total: <span x-text="formData.tanah_items ? formData.tanah_items.length : 1"></span> Bidang Tanah
                </span>
            </div>
        </div>

        <!-- List Kartu Bidang Tanah (Repeater) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.tanah_nama_barang" x-text="item.tanah_nama_barang"></span>
                            <span class="text-[11px] text-slate-400 font-mono" x-show="item.tanah_luas_m2">
                                • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></strong>
                            </span>
                        </div>

                        <button type="button" 
                                x-show="formData.tanah_items.length > 1 && tipeKemitraan !== 'dimanfaatkan'" 
                                @click="removeTanahItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Bidang Ini</span>
                        </button>
                    </div>

                    <!-- 1. MODE DIMANFAATKAN: Kodefikasi 108 Terkunci ke Akun 1.5.2 -->
                    <div x-show="tipeKemitraan === 'dimanfaatkan'" class="p-4 rounded-2xl bg-cyan-950/30 border border-cyan-500/30 space-y-2.5 shadow-inner">
                        <div class="flex items-center justify-between mb-1 flex-wrap gap-2">
                            <label class="text-cyan-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                <span>🔒 Kodefikasi PMDN 108 Akun 1.5.2 Pemanfaatan BMD (Terkunci)</span>
                            </label>
                            <span class="text-[9.5px] px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold font-mono"
                                  x-text="'Kode 108: ' + (item.tanah_kode_barang || selectedSubSub?.kode || '1.5.2.01.01.01.001')">
                            </span>
                        </div>
                        <div>
                            <label class="block text-slate-300 text-[11px] mb-1 font-semibold">
                                Uraian / Nama Rincian Bidang Tanah yang Disewakan / Dimanfaatkan <span class="text-rose-400">*</span>
                            </label>
                            <input type="text"
                                   x-model="item.tanah_nama_barang"
                                   @input="syncTotalsFromItems()"
                                   placeholder="Contoh: Tanah Bangunan Rumah Negara Golongan I / Lahan Parkir Paviliun..."
                                   class="w-full bg-slate-950 border border-slate-700 hover:border-cyan-400 focus:border-cyan-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- 1. MODE DITAMBAHKAN: Pilihan Jenis & Nama Barang PMDN 108 Belanja Modal 1.3.1 -->
                    <div x-show="tipeKemitraan !== 'dimanfaatkan'" class="p-4 rounded-2xl bg-slate-900/90 border border-emerald-500/40 space-y-2 shadow-inner">
                        <div class="relative" @click.outside="item.isFilterOpen = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-emerald-300 text-[10.5px] font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🏷️ Pilih / Ketik Jenis Barang PMDN 108 (Tanah)</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold font-mono"
                                      x-show="item.tanah_kode_barang"
                                      x-text="'Kode 108: ' + item.tanah_kode_barang">
                                </span>
                            </div>
                            
                            <div class="relative">
                                <input type="text"
                                       x-model="item.tanah_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; syncTotalsFromItems();"
                                       placeholder="Ketik untuk memfilter jenis PMDN 108 atau tulis rincian bidang tanah..."
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-500 focus:border-emerald-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none transition-all pl-9 pr-8">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <button type="button" 
                                        x-show="item.tanah_nama_barang" 
                                        @click="item.tanah_nama_barang = ''; item.tanah_kode_barang = ''; item.isFilterOpen = true; syncTotalsFromItems();" 
                                        class="absolute inset-y-0 right-2.5 flex items-center text-slate-400 hover:text-white transition-colors cursor-pointer"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                            </div>

                            <!-- Dropdown Hasil Filter (Strict Max 5 Baris - Zero Lag) -->
                            <div x-show="item.isFilterOpen" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute z-50 left-0 right-0 mt-1 bg-slate-900 border border-emerald-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 text-[10px] text-slate-400 font-semibold flex items-center justify-between">
                                    <span>Pilihan Rekomendasi PMDN 108 (Maks. 5):</span>
                                    <span class="text-emerald-400 font-mono text-[9px]">PMDN 108 Tanah (1.3.1)</span>
                                </div>
                                <template x-for="opt in filterJenisAstap108('1.3.1', item.tanah_nama_barang, item.isFilterOpen)" :key="opt.id">
                                    <div @click="select108ForItem(item, opt, 'tanah')"
                                         class="px-3.5 py-2 hover:bg-emerald-500/20 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div class="flex-1 pr-2">
                                            <div class="text-xs font-bold text-white group-hover:text-emerald-300" x-text="opt.nama"></div>
                                        </div>
                                        <span class="font-mono text-[10px] text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-500/30 shrink-0" x-text="opt.kode"></span>
                                    </div>
                                </template>
                                <div x-show="filterJenisAstap108('1.3.1', item.tanah_nama_barang, item.isFilterOpen).length === 0" 
                                     class="px-3.5 py-2.5 text-center text-xs text-slate-400 italic">
                                    <span>Gunakan nama yang Anda ketik jika tidak ada dalam daftar PMDN 108 di atas.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 items-stretch">
                        <!-- 1. Status Tanah & Sertifikat (Legalitas Lahan) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-slate-700/80 shadow-lg flex flex-col justify-between gap-3.5 transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                                    <span class="text-sm">📜</span>
                                    <span>Status Tanah &amp; Sertifikat:</span>
                                </span>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/25 font-semibold">
                                    Legalitas Lahan
                                </span>
                            </div>

                            <div class="space-y-3.5">
                                <div>
                                    <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                        Hak / Status Penguasaan Tanah <span class="text-rose-400">*</span>
                                    </label>
                                    <select x-model="item.tanah_hak"
                                        class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 font-medium focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none transition-all">
                                        <option value="Hak Pakai">Hak Pakai (Pemda / RSUD)</option>
                                        <option value="Hak Pengelolaan">Hak Pengelolaan (HPL)</option>
                                        <option value="Hak Milik Pemda">Hak Milik Pemerintah Kabupaten</option>
                                        <option value="Tanah Adat / Ulayat">Tanah Adat / Ulayat</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                            Nomor Sertifikat Lahan
                                        </label>
                                        <input type="text" x-model="item.tanah_sertifikat_no"
                                            placeholder="HP-108/Bondowoso/1998"
                                            class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3 py-2.5 text-xs text-slate-100 font-mono focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between gap-1 mb-1.5">
                                            <label class="block text-slate-300 text-[11px] font-semibold truncate">
                                                Tanggal Terbit Sertifikat
                                            </label>
                                            <span class="text-[9.5px] font-mono text-cyan-400 bg-cyan-950/60 px-2 py-0.5 rounded border border-cyan-800/50 shrink-0">
                                                Maks: Hari Ini
                                            </span>
                                        </div>
                                        <input type="text" x-datepicker="{ maxDate: 'today' }" x-model="item.tanah_sertifikat_tgl"
                                            placeholder="dd/mm/yyyy"
                                            class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3 py-2.5 text-xs text-slate-100 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Kondisi, Penggunaan & Luas Bidang (Fisik & Dimensi) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-slate-700/80 shadow-lg flex flex-col justify-between gap-3.5 transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-2">
                                    <span class="text-sm">📐</span>
                                    <span>Kondisi, Penggunaan &amp; Luas:</span>
                                </span>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-semibold">
                                    Fisik &amp; Dimensi
                                </span>
                            </div>

                            <div class="space-y-3.5">
                                <div>
                                    <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                        Kondisi Lahan Tanah <span class="text-rose-400">*</span>
                                    </label>
                                    <select x-model="item.tanah_kondisi"
                                        class="w-full bg-slate-950 border border-slate-700/90 rounded-xl px-3.5 py-2.5 text-xs text-slate-100 font-semibold focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 focus:outline-none transition-all">
                                        <option value="Baik">🟢 Baik (Siap Digunakan)</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (Perlu Pematangan / Pengurukan)</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (Rawa / Kontur Ekstrem)</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-3.5">
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                            Luas Tanah (m²) <span class="text-rose-400">*</span>
                                        </label>
                                        <div class="flex items-center rounded-xl bg-slate-950 border border-emerald-500/40 focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/20 overflow-hidden transition-all">
                                            <input type="number" step="0.01" min="0" x-model.number="item.tanah_luas_m2" @input="syncTotalsFromItems()"
                                                placeholder="Contoh: 1500"
                                                class="w-full bg-transparent px-3 py-2.5 text-xs sm:text-sm text-emerald-300 font-mono font-bold focus:outline-none">
                                            <span class="px-3 py-2.5 text-xs font-mono font-bold text-emerald-400/90 bg-slate-900 border-l border-slate-800 shrink-0">m²</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold">
                                            Status Kesiapan Fisik
                                        </label>
                                        <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center gap-2 h-[42px]">
                                            <template x-if="item.tanah_kondisi === 'Baik' || !item.tanah_kondisi">
                                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-300">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                                    <span>Siap Bangun &amp; Operasional</span>
                                                </span>
                                            </template>
                                            <template x-if="item.tanah_kondisi === 'Kurang Baik'">
                                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-300">
                                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                                    <span>Perlu Pematangan / Cut &amp; Fill</span>
                                                </span>
                                            </template>
                                            <template x-if="item.tanah_kondisi === 'Rusak Berat'">
                                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-rose-300">
                                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                                    <span>Perlu Reklamasi / Kajian Khusus</span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Keterangan / Catatan Khusus Tanah -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700/80 space-y-1.5 shadow-md transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <label class="block text-slate-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📝 Keterangan / Catatan Khusus Tanah:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-bold">Catatan Tambahan</span>
                            </div>
                            <input type="text" x-model="item.tanah_penggunaan"
                                placeholder="Contoh: Area Parkir Terpadu / Gedung Paviliun / Keterangan Legalitas Lahan"
                                class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-emerald-500 rounded-xl px-3.5 py-2 text-xs text-white font-medium focus:outline-none transition-all">
                        </div>

                        <!-- 4. Lokasi Fisik Tanah -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/40 space-y-1.5 shadow-md">
                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-1.5">
                                <label class="block text-emerald-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📍 Letak / Alamat Tanah &amp; Lokasi Fisik:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik</span>
                            </div>
                            <input type="text" x-model="item.tanah_alamat"
                                placeholder="Contoh: Kompleks RSUD Dr. H. Koesnandi, Jl. Piere Tendean No. 1, Bondowoso"
                                class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-500 rounded-xl px-3.5 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500 transition-all">
                        </div>

                        <!-- 5. Volume & Taksiran Nilai Wajar Bidang Tanah -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-emerald-500/50 space-y-3.5 shadow-lg shadow-emerald-500/5">
                            <div class="flex flex-wrap items-center justify-between border-b border-emerald-500/30 pb-2.5 gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-bold border border-emerald-500/30">💰</span>
                                    <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider">
                                        Volume &amp; Taksiran Nilai Wajar Bidang Tanah
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2 bg-emerald-950/80 border border-emerald-500/40 px-3 py-1 rounded-xl shadow-sm">
                                    <span class="text-[10px] text-slate-300 font-semibold">Sub Total Bidang #<span x-text="idx + 1"></span>:</span>
                                    <span class="text-xs font-black text-emerald-300 font-mono tracking-tight" x-text="'Rp ' + Number(getTanahSubtotal(item)).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                                {{-- Input 1: Jumlah Volume / Bidang --}}
                                <div>
                                    <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold flex items-center justify-between">
                                        <span>Jumlah Volume / Bidang <span class="text-rose-400">*</span></span>
                                        <span class="text-[9.5px] font-mono text-emerald-400 bg-emerald-950/50 px-1.5 py-0.5 rounded border border-emerald-800/40">Kuantitas</span>
                                    </label>
                                    <input type="number" min="1" x-model.number="item.tanah_jumlah_barang" @input="syncTotalsFromItems()" placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700/90 hover:border-emerald-500 focus:border-emerald-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none transition-all">
                                </div>

                                {{-- Input 2: Satuan --}}
                                <div>
                                    <label class="block text-slate-300 text-[11px] mb-1.5 font-semibold flex items-center justify-between">
                                        <span>Satuan <span class="text-rose-400">*</span></span>
                                        <span class="text-[9.5px] font-mono text-slate-400">Unit Ukur</span>
                                    </label>
                                    <input type="text" x-model="item.tanah_satuan" @input="syncTotalsFromItems()" placeholder="Bidang"
                                           class="w-full bg-slate-950 border border-slate-700/90 hover:border-emerald-500 focus:border-emerald-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>

                                {{-- Input 3: Taksiran Total Nilai Wajar Bidang (Rp) --}}
                                <div>
                                    <div class="flex items-center justify-between gap-1 mb-1.5">
                                        <label class="text-slate-300 text-[11px] font-semibold truncate">
                                            Taksiran Nilai Wajar (Rp) <span class="text-rose-400">*</span>
                                        </label>
                                        <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/25 font-mono shrink-0">
                                            Lump-Sum Bidang
                                        </span>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span class="absolute left-3.5 text-emerald-400 text-xs font-mono font-extrabold select-none">Rp</span>
                                        <input type="text" 
                                               :value="item.tanah_nilai_satuan ? Number(item.tanah_nilai_satuan).toLocaleString('id-ID') : ''"
                                               @input="
                                                   let raw = $event.target.value.replace(/\D/g, '');
                                                   item.tanah_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                                   $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                   syncTotalsFromItems();
                                               "
                                               placeholder="Contoh: 500.000.000"
                                               class="w-full bg-slate-950 border border-emerald-500/50 hover:border-emerald-400 focus:border-emerald-400 text-emerald-300 font-mono font-bold rounded-xl pl-10 pr-3.5 py-2.5 text-xs sm:text-sm focus:outline-none transition-all shadow-inner">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 flex items-center gap-2 text-[10.5px] text-slate-400 border-t border-slate-800/60">
                                <span class="text-xs">💡</span>
                                <span><strong class="text-slate-300">Catatan Nilai Tanah:</strong> Taksiran nilai tanah adalah nilai appraisal total keseluruhan untuk Bidang #<span x-text="idx + 1"></span> (lump-sum penilaian wajar, otomatis mengakumulasi total realisasi di SIMAT-RK).</span>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

        <!-- Tombol Tambah Bidang Tanah Baru -->
        <div x-show="tipeKemitraan !== 'dimanfaatkan'" class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addTanahItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Bidang Tanah Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Tanah:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiTanah)"></span>
            </div>
        </div>
        <!-- Total taksiran selalu tampil pada mode dimanfaatkan -->
        <div x-show="tipeKemitraan === 'dimanfaatkan'" class="flex items-center justify-end pt-2 border-t border-slate-800">
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Tanah:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiTanah)"></span>
            </div>
        </div>

    </div>

</div>

