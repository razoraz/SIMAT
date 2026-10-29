<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: PERALATAN & MESIN (KIB B) UNTUK HIBAH                   -->
<!-- REPEATER MULTI-ITEM — MIRROR KEMITRAAN DENGAN PALET AMBER                  -->
<!-- ========================================================================= -->
<div x-show="isMesin" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB B Multi-Item Repeater -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB B -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg border border-amber-500/30 shadow-inner">⚙️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Rincian Peralatan &amp; Mesin / Alkes Medis
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold text-[10px] border border-amber-500/40">
                            KIB B
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bisa menambah 2 atau lebih barang hibah dengan spesifikasi beda (Merk, Type, Ukuran, No Pabrik/SN, Bahan, Kondisi, Volume &amp; Taksiran Nilai).</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-amber-400 bg-amber-950/60 px-3 py-1.5 rounded-xl border border-amber-500/30 shadow-sm">
                    Total: <span x-text="formData.mesin_items ? formData.mesin_items.length : 1"></span> Barang / Unit
                </span>
            </div>
        </div>

        <!-- List Kartu Barang Peralatan & Mesin (Repeater Multi-Item) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.mesin_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-amber-500/30 hover:border-amber-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <!-- Header Kartu Tiap Barang -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-amber-500/20 text-amber-300 font-mono font-extrabold text-xs border border-amber-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>⚙️ Barang / Unit #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border transition-all"
                                  :class="(item.is_extracom || formData.is_extracomtable) ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                  x-text="(item.is_extracom || formData.is_extracomtable) ? '📦 Ekstrakomtabel (≤ 300rb)' : '⚙️ Aset Tetap Reguler'">
                            </span>
                            <span class="text-[11px] text-slate-200 font-semibold" x-show="item.mesin_nama_barang || item.mesin_merk || item.mesin_type">
                                • <span x-text="item.mesin_nama_barang ? (item.mesin_nama_barang + ' • ') : ''"></span><span x-text="(item.mesin_merk || '') + ' ' + (item.mesin_type || '')"></span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Qty: <strong class="text-amber-300" x-text="(item.mesin_jumlah_barang || 1) + ' ' + (item.mesin_satuan || 'Unit')"></strong>
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

                    <!-- Pilihan Jenis & Nama Barang PMDN 108 KIB B -->
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
                            
                            <div class="relative flex items-center" style="position: relative;">
                                <svg class="w-4 h-4 text-amber-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                     style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 10;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text"
                                       x-model="item.mesin_nama_barang"
                                       @focus="item.isFilterOpen = true"
                                       @click="item.isFilterOpen = true"
                                       @input="item.isFilterOpen = true; formData.nama_barang = item.mesin_nama_barang; syncActiveKibTotals();"
                                       placeholder="Ketik untuk mencari jenis barang PMDN 108 atau tulis rincian unit alat hibah..."
                                       style="padding-left: 38px; padding-right: 36px;"
                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl py-2.5 text-xs text-white font-bold focus:outline-none transition-all shadow-inner">
                                <button type="button" 
                                        x-show="Boolean(item.mesin_nama_barang)" 
                                        @click.stop.prevent="clearItem108(item, 'mesin'); item.mesin_nama_barang = ''; item.mesin_kode_barang = ''; item.isFilterOpen = true;" 
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

                    <!-- Spesifikasi Fisik & Identitas Mesin (Single Cohesive Card) -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-md">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span x-text="(item.is_extracom || formData.is_extracomtable) ? '📦 SPESIFIKASI & IDENTITAS FISIK (EKSTRAKOMTABEL):' : '🏷️ SPESIFIKASI FISIK & IDENTITAS MESIN / ALKES'"></span>
                            </span>
                            <span class="text-[9px] px-2 py-0.5 rounded-md font-bold border transition-all"
                                  :class="(item.is_extracom || formData.is_extracomtable) ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-amber-500/10 text-amber-300 border-amber-500/20'"
                                  x-text="(item.is_extracom || formData.is_extracomtable) ? 'Ekstrakomtabel (≤ 300rb)' : 'Teknis Fisik'">
                            </span>
                        </div>

                        <!-- Nama Barang (PMDN 108) Khusus Mode Extracom -->
                        <div x-show="item.is_extracom || formData.is_extracomtable">
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

                        <!-- Baris 1: Merk, Type & Ukuran (3 Kolom Seimbang) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    <span x-text="(item.is_extracom || formData.is_extracomtable) ? 'Merk Barang' : 'Merk / Brand'"></span> <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.mesin_merk" @input="syncActiveKibTotals()"
                                       :placeholder="(item.is_extracom || formData.is_extracomtable) ? 'Contoh: Olympic / Lion / Krisbow / Kenko' : 'Contoh: GE / Philips / Honda'"
                                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-400 focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tipe / Model <span class="text-rose-400">*</span></label>
                                <input type="text" x-model="item.mesin_type" @input="syncActiveKibTotals()"
                                       :placeholder="(item.is_extracom || formData.is_extracomtable) ? 'Contoh: Standard / Meja / Rak' : 'Contoh: V-800 / CX-50 / 150cc'"
                                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-400 focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Kapasitas</label>
                                <input type="text" x-model="item.mesin_ukuran"
                                       :placeholder="(item.is_extracom || formData.is_extracomtable) ? 'Contoh: 120x60 cm / Sedang' : 'Contoh: 120x80 cm / 500 KVA'"
                                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-400 focus:outline-none transition-all">
                            </div>
                        </div>

                        <!-- Baris 2: No Pabrik/SN, Bahan, Kondisi (3 Kolom Seimbang) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    <span x-text="(item.is_extracom || formData.is_extracomtable) ? 'No Pabrik / SN' : 'Nomor Pabrik / Serial Number (SN)'"></span>
                                </label>
                                <input type="text" x-model="item.mesin_no_pabrik"
                                       :placeholder="(item.is_extracom || formData.is_extracomtable) ? 'SN-EXT-2026-001' : 'SN-89472894172 / Serial Unit'"
                                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-400 focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Material</label>
                                <input type="text" x-model="item.mesin_bahan"
                                       :placeholder="(item.is_extracom || formData.is_extracomtable) ? 'Kayu / Besi / Plastik' : 'Contoh: Stainless Steel / Besi'"
                                       class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-400 focus:outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    <span x-text="(item.is_extracom || formData.is_extracomtable) ? 'Kondisi Barang' : 'Kondisi Fisik Barang'"></span> <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="item.mesin_kondisi" @change="syncActiveKibTotals()"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-amber-400 focus:outline-none transition-all">
                                    <option value="Baik" x-text="(item.is_extracom || formData.is_extracomtable) ? 'Baik (B)' : '🟢 Baik (B) — Siap Digunakan'"></option>
                                    <option value="Kurang Baik" x-text="(item.is_extracom || formData.is_extracomtable) ? 'Kurang Baik (KB)' : '🟡 Kurang Baik (KB) — Perlu Servis'"></option>
                                    <option value="Rusak Berat" x-text="(item.is_extracom || formData.is_extracomtable) ? 'Rusak Berat (RB)' : '🔴 Rusak Berat (RB)'"></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Legalitas Kendaraan Operasional RSUD (Opsional / Jika Ada) -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-md"
                         x-show="!item.is_extracom && !formData.is_extracomtable">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🚗 Legalitas Kendaraan Operasional (Ambulance / Mobil Jenazah / Operasional)</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">Khusus Kendaraan</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Rangka</label>
                                <input type="text" x-model="item.mesin_no_rangka" placeholder="MH3..."
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Mesin</label>
                                <input type="text" x-model="item.mesin_no_mesin" placeholder="J15E..."
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Polisi</label>
                                <input type="text" x-model="item.mesin_no_polisi" placeholder="P 1234 RS"
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor BPKB</label>
                                <input type="text" x-model="item.mesin_no_bpkb" placeholder="N-1234567"
                                    class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-400 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Volume, Satuan & Nilai Taksiran Wajar Perolehan -->
                    <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900/95 via-amber-950/20 to-slate-900/95 border border-amber-500/40 space-y-4 shadow-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-amber-500/30 pb-2.5 gap-2">
                            <span class="text-xs font-bold text-amber-300 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Volume, Satuan &amp; Taksiran Nilai Wajar Perolehan Hibah</span>
                            </span>
                            <span class="text-xs font-mono font-black text-amber-300"
                                  x-text="'Subtotal: Rp ' + formatRupiah(getMesinSubtotal(item))"></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-300 text-[10.5px] mb-1 font-semibold">Jumlah Kuantitas <span class="text-rose-400">*</span></label>
                                <input type="number" min="1"
                                    x-model.number="item.mesin_jumlah_barang"
                                    @keydown="if(['-', '+', 'e', 'E', '.'].includes($event.key)) $event.preventDefault();"
                                    @input="
                                        let raw = String($event.target.value).replace(/\D/g, '');
                                        if (raw.startsWith('0')) raw = raw.replace(/^0+/, '');
                                        item.mesin_jumlah_barang = raw ? parseInt(raw, 10) : '';
                                        syncActiveKibTotals();
                                    "
                                    @blur="
                                        if (!item.mesin_jumlah_barang || Number(item.mesin_jumlah_barang) < 1) {
                                            item.mesin_jumlah_barang = 1;
                                            syncActiveKibTotals();
                                        }
                                    "
                                    :class="(!item.mesin_jumlah_barang || Number(item.mesin_jumlah_barang) < 1) ? 'border-rose-500 ring-1 ring-rose-500 text-rose-300' : 'border-slate-700 text-white focus:border-amber-400'"
                                    class="w-full bg-slate-950 border rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none transition-colors">
                                <template x-if="!item.mesin_jumlah_barang || Number(item.mesin_jumlah_barang) < 1">
                                    <span class="text-[9.5px] font-bold text-rose-400 block mt-1">⚠️ Kuantitas minimal 1 unit (tidak boleh 0 atau minus).</span>
                                </template>
                            </div>
                            <div>
                                <label class="block text-slate-300 text-[10.5px] mb-1 font-semibold">Satuan <span class="text-rose-400">*</span></label>
                                <input type="text" x-model="item.mesin_satuan" placeholder="Unit / Set / Buah"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold text-center focus:border-amber-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10.5px] mb-1 font-semibold flex items-center justify-between gap-1.5">
                                    <span class="text-slate-300 whitespace-nowrap flex items-center">
                                        <span x-show="item.is_extracom || formData.is_extracomtable">Nilai Satuan Extracom</span>
                                        <span x-show="!item.is_extracom && !formData.is_extracomtable">Taksiran Nilai Satuan</span>
                                        <span class="text-rose-400 ml-0.5">*</span>
                                    </span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded whitespace-nowrap shrink-0 transition-colors"
                                          :class="(item.is_extracom || formData.is_extracomtable) 
                                              ? (Number(item.mesin_nilai_satuan || 0) > 300000 ? 'text-rose-400 bg-rose-950/60 border border-rose-500/40 animate-pulse' : 'text-amber-400 bg-amber-950/40 border border-amber-500/30') 
                                              : (Number(item.mesin_nilai_satuan || 0) > 0 && Number(item.mesin_nilai_satuan || 0) <= 300000 ? 'text-rose-400 bg-rose-950/60 border border-rose-500/40 animate-pulse' : (Number(item.mesin_nilai_satuan || 0) > 300000 ? 'text-emerald-400 bg-emerald-950/40 border border-emerald-500/30' : 'text-slate-400 bg-slate-900 border border-slate-700/60'))"
                                          x-text="(item.is_extracom || formData.is_extracomtable) 
                                              ? (Number(item.mesin_nilai_satuan || 0) > 300000 ? 'Lewat Maksimal!' : 'Maks. Rp 300rb') 
                                              : (Number(item.mesin_nilai_satuan || 0) > 0 && Number(item.mesin_nilai_satuan || 0) <= 300000 ? 'Wajib > Rp 300rb' : (Number(item.mesin_nilai_satuan || 0) > 300000 ? '✓ Harga Wajar' : '> Rp 300rb'))">
                                    </span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-slate-400 text-xs font-bold font-mono">Rp</span>
                                    <input type="text"
                                        :value="item.mesin_nilai_satuan ? Number(item.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            let val = raw ? parseInt(raw, 10) : 0;
                                            item.mesin_nilai_satuan = val;
                                            $event.target.value = val ? Number(val).toLocaleString('id-ID') : '';
                                            syncActiveKibTotals();
                                        "
                                        placeholder="0"
                                        :class="((item.is_extracom || formData.is_extracomtable) && Number(item.mesin_nilai_satuan || 0) > 300000) || (!(item.is_extracom || formData.is_extracomtable) && Number(item.mesin_nilai_satuan || 0) > 0 && Number(item.mesin_nilai_satuan || 0) <= 300000) ? 'border-rose-500 ring-1 ring-rose-500 text-rose-300' : 'border-slate-700 text-amber-300 focus:border-amber-400'"
                                        class="w-full bg-slate-950 border rounded-xl px-3 py-2 pl-9 text-xs font-mono font-bold focus:outline-none transition-colors">
                                </div>
                                
                                <template x-if="(item.is_extracom || formData.is_extracomtable) && Number(item.mesin_nilai_satuan || 0) > 300000">
                                    <span class="text-[9.5px] font-bold text-rose-400 block mt-1">
                                        ⚠️ Nilai satuan Extracom tidak boleh &gt; Rp 300.000!
                                    </span>
                                </template>
                                <template x-if="!(item.is_extracom || formData.is_extracomtable) && Number(item.mesin_nilai_satuan || 0) > 0 && Number(item.mesin_nilai_satuan || 0) <= 300000">
                                    <div class="mt-1.5 p-2 rounded-lg bg-rose-950/40 border border-rose-500/30 text-[9.5px] text-rose-300 space-y-1">
                                        <div class="font-bold flex items-center gap-1">
                                            <span>⚠️</span>
                                            <span>Aset Reguler wajib memiliki nilai satuan &gt; Rp 300.000!</span>
                                        </div>
                                        <p class="text-slate-400 leading-tight">
                                            Barang bernilai ≤ Rp 300.000 harus dicatat sebagai <strong>Ekstrakomtabel</strong>.
                                        </p>
                                        <button type="button" @click="setGlobalExtracom(true)"
                                            class="mt-1 px-2.5 py-1 rounded-md bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold transition-all flex items-center gap-1 cursor-pointer">
                                            <span>📦</span>
                                            <span>Beralih ke Ekstrakomtabel Sekarang</span>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Barang Mesin -->
        <div class="pt-2 flex justify-end">
            <button type="button" @click="addMesinItem()"
                    class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-lg shadow-amber-500/20 flex items-center space-x-2 cursor-pointer">
                <span>➕ Tambah Barang / Unit Baru</span>
            </button>
        </div>

    </div>

</div>
