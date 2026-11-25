            <!-- 3. Klasifikasi KIB Tujuan & Rekening PMDN 108 -->
            <div x-show="formData.jenis_reklas === 'KOREKSI_REKENING' || formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'"
                 x-transition.duration.200ms
                 class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                    <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span>🎯</span> 3. Klasifikasi &amp; Rekening Tujuan PMDN 108
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        Hierarki 108
                    </span>
                </div>

                <!-- Tingkat 1: KIB Tujuan -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                        Tingkat 1: Pilih Kelompok / KIB Tujuan <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.tujuan_kib" @change="onTujuanKibChange()"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-bold focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih KIB / Kelompok Tujuan --</option>
                        <option value="KIB A">KIB A - Tanah (1.3.1)</option>
                        <option value="KIB B">KIB B - Peralatan &amp; Mesin (1.3.2)</option>
                        <option value="KIB C">KIB C - Gedung &amp; Bangunan (1.3.3)</option>
                        <option value="KIB D">KIB D - Jalan, Jaringan &amp; Irigasi (1.3.4)</option>
                        <option value="KIB E">KIB E - Aset Tetap Lainnya (1.3.5)</option>
                        <option value="ATB">ATB - Aset Tidak Berwujud (1.5.3)</option>
                        <option value="KEMITRAAN">Kemitraan Pihak Ketiga (1.5.2)</option>
                        <option value="ASET LAIN">Aset Lain-Lain (1.5.4)</option>
                    </select>
                </div>

                <!-- Tingkat 2: Sub-Rincian Rekening 108 -->
                <div class="space-y-1.5 relative" @click.away="isReklasSubRincianOpen = false">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[9px] font-black flex items-center justify-center">2</span>
                            <span>Sub-Rincian Objek PMDN 108:</span>
                        </label>
                        <div class="shrink-0 flex items-center gap-1.5" x-show="reklasSubRincianKode">
                            <button type="button" @click="clearSubRincian()" class="text-[10px] text-amber-300 hover:underline">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <input type="text"
                               :value="(!isReklasSubRincianOpen && reklasSubRincianKode) ? (reklasSubRincianKode + ' - ' + reklasSubRincianNama) : searchReklasSubRincian"
                               @input="searchReklasSubRincian = $event.target.value; isReklasSubRincianOpen = true"
                               @focus="isReklasSubRincianOpen = true"
                               :placeholder="reklasSubRincianKode ? (reklasSubRincianKode + ' - ' + reklasSubRincianNama) : 'Ketik untuk memfilter sub-rincian 108...'"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border text-white text-xs font-medium focus:outline-none shadow-inner"
                               :class="reklasSubRincianKode && !isReklasSubRincianOpen ? 'border-emerald-500/60 text-emerald-200' : 'border-slate-700 focus:border-emerald-400'">
                    </div>

                    <!-- Dropdown List Sub-Rincian -->
                    <div x-show="isReklasSubRincianOpen" x-transition x-cloak
                         class="absolute z-30 mt-1.5 w-full max-h-48 overflow-y-auto space-y-1 p-2 bg-slate-900/95 border border-emerald-500/50 rounded-xl shadow-2xl backdrop-blur-xl custom-scrollbar">
                        <template x-for="s in filteredSubRincian108" :key="s.kode">
                            <div @click="selectSubRincian(s)"
                                 class="p-2 rounded-lg bg-slate-950 border border-slate-800 hover:border-emerald-500/50 hover:bg-slate-900 cursor-pointer flex items-center justify-between text-xs">
                                <span class="font-bold text-white truncate mr-2" x-text="s.kode + ' - ' + s.nama"></span>
                                <span class="shrink-0 text-[10px] text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded">Pilih</span>
                            </div>
                        </template>
                        <template x-if="isReklasSubRincianOpen && filteredSubRincian108.length === 0">
                            <div class="p-2.5 text-center text-xs text-slate-400 italic">Tidak ada sub-rincian cocok.</div>
                        </template>
                    </div>
                </div>

                <!-- Tingkat 3: Sub-Sub Rincian / Identitas Barang 108 -->
                <div class="space-y-1.5 relative" @click.away="isReklasSubSubRincianOpen = false">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[9px] font-black flex items-center justify-center">3</span>
                            <span>Identitas Barang / Kode 108 (Sub-Sub):</span>
                        </label>
                        <div class="shrink-0 flex items-center gap-1.5" x-show="reklasSubSubRincianKode">
                            <button type="button" @click="clearSubSubRincian()" class="text-[10px] text-amber-300 hover:underline">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <input type="text"
                               :value="(!isReklasSubSubRincianOpen && reklasSubSubRincianKode) ? (reklasSubSubRincianKode + ' - ' + reklasSubSubRincianNama) : searchReklasSubSubRincian"
                               @input="searchReklasSubSubRincian = $event.target.value; isReklasSubSubRincianOpen = true"
                               @focus="isReklasSubSubRincianOpen = true"
                               :placeholder="reklasSubSubRincianKode ? (reklasSubSubRincianKode + ' - ' + reklasSubSubRincianNama) : 'Ketik nama / kode barang spesifik...'"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border text-white text-xs font-medium focus:outline-none shadow-inner"
                               :class="reklasSubSubRincianKode && !isReklasSubSubRincianOpen ? 'border-purple-500/60 text-purple-200' : 'border-slate-700 focus:border-purple-400'">
                    </div>

                    <!-- Dropdown List Sub-Sub Rincian -->
                    <div x-show="isReklasSubSubRincianOpen" x-transition x-cloak
                         class="absolute z-30 mt-1.5 w-full max-h-48 overflow-y-auto space-y-1 p-2 bg-slate-900/95 border border-purple-500/50 rounded-xl shadow-2xl backdrop-blur-xl custom-scrollbar">
                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                            <div @click="selectSubSubRincian(item)"
                                 class="p-2 rounded-lg bg-slate-950 border hover:border-purple-500/50 hover:bg-slate-900 cursor-pointer flex items-center justify-between text-xs transition-all"
                                 :class="item.isRecommended ? 'border-emerald-500/50 bg-emerald-950/25 shadow-sm' : 'border-slate-800'">
                                <div class="flex items-center gap-2 min-w-0 mr-2">
                                    <span class="font-bold text-white truncate" x-text="item.kode + ' - ' + item.nama"></span>
                                    <span x-show="item.isRecommended" class="shrink-0 text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">
                                        ✨ Sesuai Aset Terpilih
                                    </span>
                                </div>
                                <span class="shrink-0 text-[10px] text-purple-300 bg-purple-500/20 px-2 py-0.5 rounded">Pilih</span>
                            </div>
                        </template>
                        <template x-if="isReklasSubSubRincianOpen && filteredSubSubRincian108.length === 0">
                            <div class="p-2.5 text-center text-xs text-slate-400 italic">Tidak ada rincian barang cocok.</div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Panduan Interaktif: Gating Rekening PMDN 108 Sebelum Membuka Spesifikasi -->
            <div x-show="formData.tujuan_kib && !isReklasRekeningLengkap() && (formData.jenis_reklas === 'KOREKSI_REKENING' || formData.jenis_reklas === 'KAPITALISASI_INTRAKOM')"
                 x-transition.duration.300ms
                 class="p-4 rounded-2xl bg-slate-950/90 border border-amber-500/30 flex items-start gap-3.5 shadow-lg shadow-amber-950/20">
                <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center shrink-0 text-amber-300 font-extrabold text-sm mt-0.5 shadow-inner">
                    🔒
                </div>
                <div class="space-y-1 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-amber-200 text-xs uppercase tracking-wider">
                            Lengkapi Rekening PMDN 108 Terlebih Dahulu
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Wajib Tingkat 2 &amp; 3
                        </span>
                    </div>
                    <p class="text-slate-400 text-[11px] leading-relaxed">
                        Silakan pilih <strong class="text-white">Sub-Rincian Objek PMDN 108 (Tingkat 2)</strong> dan <strong class="text-white">Identitas Barang 108 (Tingkat 3)</strong> pada bagian di atas terlebih dahulu untuk membuka formulir spesifikasi fisik baru (<span class="text-amber-300 font-semibold" x-text="formData.tujuan_kib"></span>).
                    </p>
                </div>
            </div>

            <!-- 4. Form Spesifikasi Fisik Baru Dinamis Sesuai KIB Tujuan -->
            <div x-show="isReklasRekeningLengkap() && (formData.jenis_reklas === 'KOREKSI_REKENING' || formData.jenis_reklas === 'KAPITALISASI_INTRAKOM')"
                 x-transition.duration.300ms
                 class="p-4 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-3.5 shadow-lg">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <div class="flex items-center space-x-2">
                        <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center shrink-0">4</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            📝 Spesifikasi Fisik Baru (<span class="text-cyan-300 font-extrabold" x-text="formData.tujuan_kib === 'KEMITRAAN' ? ('Fisik Objek ' + (getPhysicalTypeLabel(getKemitraanTargetSubGroup()) || 'Kemitraan')) : formData.tujuan_kib"></span>):
                        </span>
                    </div>
                    <span class="text-[10px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/30 font-semibold">
                        Format Spek Dinamis
                    </span>
                </div>

                <!-- Banner Umpan Balik Cerdas: Deteksi Kesamaan Kelompok Fisik -->
                <template x-if="isReklasSamePhysicalType()">
                    <div class="p-3.5 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 text-emerald-200 text-xs leading-relaxed flex items-start gap-3 shadow-lg shadow-emerald-950/30">
                        <div class="w-7 h-7 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0 text-emerald-300 font-extrabold text-sm mt-0.5 shadow-inner">
                            ✓
                        </div>
                        <div class="space-y-1 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-extrabold text-white text-xs uppercase tracking-wide">
                                    Wujud Fisik Sama (<span class="text-emerald-300 font-mono" x-text="getPhysicalTypeLabel(getTargetPhysicalType()) || formData.tujuan_kib"></span>)
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm animate-pulse">
                                    ✨ Spesifikasi Otomatis Dipertahankan (Tidak Perlu Isi Ulang)
                                </span>
                            </div>
                            <p class="text-emerald-200/90 text-[11px] leading-relaxed">
                                Aset tetap berada dalam kategori wujud fisik yang sama. Seluruh rincian spesifikasi sebelumnya (luas, nomor &amp; tanggal dokumen legalitas, jenis konstruksi, status tanah, serta lokasi) telah <strong>disalin otomatis</strong> ke formulir di bawah. Anda tidak perlu repot mengetik ulang, silakan langsung lanjutkan atau sesuaikan jika ada perubahan kecil.
                            </p>
                        </div>
                    </div>
                </template>

                <template x-if="!isReklasSamePhysicalType()">
                    <div class="p-3.5 rounded-2xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs leading-relaxed flex items-start gap-3 shadow-lg shadow-amber-950/30">
                        <div class="w-7 h-7 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0 text-amber-300 font-extrabold text-sm mt-0.5 shadow-inner">
                            ⚠️
                        </div>
                        <div class="space-y-1 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-extrabold text-white text-xs uppercase tracking-wide">
                                    Perubahan Kelompok Fisik (<span class="text-slate-300" x-text="getPhysicalTypeLabel(getSourcePhysicalType()) || 'Asal'"></span> ➔ <span class="text-amber-300 font-mono" x-text="getPhysicalTypeLabel(getTargetPhysicalType()) || formData.tujuan_kib"></span>)
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm">
                                    Wajib Isi Spesifikasi Baru
                                </span>
                            </div>
                            <p class="text-amber-200/90 text-[11px] leading-relaxed">
                                Aset dialihkan ke kelompok fisik yang berbeda, sehingga atribut fisik lama diarsipkan ke riwayat audit. Silakan lengkapi formulir spesifikasi teknis baru di bawah ini agar pencatatan buku inventaris &amp; KIR terbit sesuai format fisik tujuan.
                            </p>
                        </div>
                    </div>
                </template>

                <!-- 2 PILIHAN MODE SPESIFIKASI: KOLEKTIF vs PER UNIT (Hanya Tampil Jika > 1 Unit Terpilih) -->
                <template x-if="formData.selected_register_ids && formData.selected_register_ids.length > 1">
                    <div class="space-y-3.5 p-4 rounded-2xl bg-slate-950/90 border border-indigo-500/35 shadow-xl shadow-indigo-950/30">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-2.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-base">⚙️</span>
                                <span class="text-xs font-black uppercase tracking-wider text-white">Mode Pengisian Spesifikasi Fisik</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/35"
                                      x-text="formData.selected_register_ids.length + ' Unit NIBAR Terpilih'"></span>
                            </div>
                            <span class="text-[10.5px] text-slate-400">Pilih bagaimana data teknis &amp; legalitas diterapkan</span>
                        </div>

                        <!-- 2 Pilihan Kartu Interaktif (Segmented Cards) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Pilihan 1: Spesifikasi Kolektif (Seragam) -->
                            <button type="button" @click="setSpekMode('kolektif')"
                                    :class="formData.spek_mode === 'kolektif' 
                                        ? 'bg-indigo-950/70 border-indigo-500 ring-2 ring-indigo-500/40 text-white shadow-lg shadow-indigo-950/60' 
                                        : 'bg-slate-900/60 border-slate-800 text-slate-400 hover:border-slate-700 hover:text-slate-200'"
                                    class="p-3.5 rounded-2xl border text-left transition-all flex items-start gap-3 cursor-pointer group">
                                <div :class="formData.spek_mode === 'kolektif' ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/30' : 'bg-slate-800 text-slate-400 group-hover:text-white'"
                                     class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm shadow-inner transition-colors">
                                    📦
                                </div>
                                <div class="space-y-1 flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-extrabold" :class="formData.spek_mode === 'kolektif' ? 'text-indigo-300' : 'text-slate-200'">
                                            1. Seragam (Kolektif)
                                        </span>
                                        <template x-if="formData.spek_mode === 'kolektif'">
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/25 text-indigo-300 border border-indigo-500/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                                                <span>Aktif</span>
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-[10.5px] text-slate-400 leading-snug">
                                        Satu formulir diterapkan sama ke seluruh unit terpilih. Nomor pabrik / seri dapat dipisahkan koma.
                                    </p>
                                </div>
                            </button>

                            <!-- Pilihan 2: Spesifikasi Per Unit (Individual) -->
                            <button type="button" @click="setSpekMode('individual')"
                                    :class="formData.spek_mode === 'individual' 
                                        ? 'bg-cyan-950/70 border-cyan-500 ring-2 ring-cyan-500/40 text-white shadow-lg shadow-cyan-950/60' 
                                        : 'bg-slate-900/60 border-slate-800 text-slate-400 hover:border-slate-700 hover:text-slate-200'"
                                    class="p-3.5 rounded-2xl border text-left transition-all flex items-start gap-3 cursor-pointer group">
                                <div :class="formData.spek_mode === 'individual' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/30' : 'bg-slate-800 text-slate-400 group-hover:text-white'"
                                     class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 font-bold text-sm shadow-inner transition-colors">
                                    📑
                                </div>
                                <div class="space-y-1 flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-extrabold" :class="formData.spek_mode === 'individual' ? 'text-cyan-300' : 'text-slate-200'">
                                            2. Beda Tiap Unit (Individual)
                                        </span>
                                        <template x-if="formData.spek_mode === 'individual'">
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-0.5 rounded-full bg-cyan-500/25 text-cyan-300 border border-cyan-500/40">
                                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                                                <span>Aktif</span>
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-[10.5px] text-slate-400 leading-snug">
                                        Formulir spesifikasi terpisah per unit/NIBAR. Cocok jika luas tanah, sertifikat, atau nomor pabriknya berbeda.
                                    </p>
                                </div>
                            </button>
                        </div>

                        <!-- TAB NAVIGASI PER UNIT (Hanya Tampil Jika Mode Individual Dipilih) -->
                        <div x-show="formData.spek_mode === 'individual'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="pt-3 border-t border-slate-800/80 space-y-2.5">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none flex-1">
                                    <span class="text-[11px] font-bold text-slate-400 shrink-0 mr-1 flex items-center gap-1">
                                        <span>🏷️</span>
                                        <span>Unit:</span>
                                    </span>
                                    <template x-for="(unit, uIdx) in getSelectedUnitsList()" :key="unit.id || uIdx">
                                        <button type="button" @click="switchSpekUnitTab(uIdx)"
                                                :class="formData.spek_unit_active_tab === uIdx 
                                                    ? 'bg-cyan-500 text-slate-950 font-black shadow-md shadow-cyan-500/25 ring-2 ring-cyan-400/50' 
                                                    : 'bg-slate-900 text-slate-300 border border-slate-700/80 hover:bg-slate-800 hover:text-white'"
                                                class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0">
                                            <span class="text-[10px] opacity-80" x-text="'#' + (uIdx + 1)"></span>
                                            <span class="font-mono font-bold" x-text="unit.nibar ? ('NIBAR: ' + unit.nibar) : ('Unit ' + (uIdx + 1))"></span>
                                            <template x-if="unit.no_register">
                                                <span class="text-[9.5px] opacity-75 font-mono" x-text="'(Reg ' + unit.no_register + ')'"></span>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Indikator Banner Unit Aktif -->
                            <div class="px-3 py-1.5 rounded-xl bg-slate-900/90 border border-cyan-500/25 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                    <span class="text-slate-300 text-[11px]">
                                        Sedang mengisi spesifikasi untuk: <strong class="text-cyan-300 font-mono" x-text="'Unit #' + ((formData.spek_unit_active_tab || 0) + 1) + ' (' + (getSelectedUnitsList()[formData.spek_unit_active_tab]?.nibar || '-') + ')'"></strong>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-500 italic hidden sm:inline">Data tersimpan otomatis saat berpindah tab</span>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB A - Tanah -->
                <template x-if="formData.tujuan_kib === 'KIB A'">
                    <div class="space-y-3.5 p-4 rounded-2xl bg-slate-950/60 border border-slate-800 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🌾</span>
                                <span>Spesifikasi &amp; Legalitas Fisik Tanah (KIB A):</span>
                            </span>
                            <span class="text-[9.5px] px-2 py-0.5 rounded-md font-bold border bg-amber-500/10 text-amber-300 border-amber-500/25">
                                KIB A &bull; Akun 1.3.1
                            </span>
                        </div>

                        <!-- Baris 1: Luas Tanah & Status Hak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    <span>📐</span>
                                    <span>Luas Bidang Tanah (M²)</span>
                                    <span class="text-rose-400 font-bold">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" x-model="formData.spekBaru.tanah_luas_m2" placeholder="Contoh: 1500"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs font-mono text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    <span>📜</span>
                                    <span>Status Hak Tanah</span>
                                    <span class="text-rose-400 font-bold">*</span>
                                </label>
                                <select x-model="formData.spekBaru.tanah_hak"
                                        class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white focus:outline-none transition-all shadow-inner cursor-pointer font-medium">
                                    <option value="Hak Pakai">Hak Pakai</option>
                                    <option value="Hak Milik">Hak Milik</option>
                                    <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                    <option value="Hak Guna Bangunan">Hak Guna Bangunan (HGB)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Baris 2: Nomor & Tanggal Sertifikat -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    <span>📋</span>
                                    <span>Nomor Sertifikat Tanah</span>
                                </label>
                                <input type="text" x-model="formData.spekBaru.tanah_sertifikat_no" placeholder="Contoh: No. 12.04.05.001..."
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    <span>📅</span>
                                    <span>Tanggal Dokumen Sertifikat</span>
                                </label>
                                <input type="date" x-model="formData.spekBaru.tanah_sertifikat_tgl"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs font-mono text-white focus:outline-none transition-all shadow-inner">
                            </div>
                        </div>

                        <!-- Baris 3: Penggunaan Bidang Tanah -->
                        <div class="space-y-1 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                <span>🏢</span>
                                <span>Penggunaan Bidang Tanah</span>
                            </label>
                            <input type="text" x-model="formData.spekBaru.tanah_penggunaan" placeholder="Contoh: Gedung Instalasi Farmasi &amp; Rawat Inap RSUD"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner font-medium">
                        </div>

                        <!-- Baris 4: Alamat / Lokasi Fisik Tanah -->
                        <div class="space-y-1 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                <span>📍</span>
                                <span>Alamat / Lokasi Fisik Bidang Tanah</span>
                            </label>
                            <input type="text" x-model="formData.spekBaru.tanah_alamat" placeholder="Contoh: Jl. Kapten Piere Tendean No. 1 Bondowoso"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB B - Peralatan & Mesin (Sesuai Gambar 3) -->
                <template x-if="formData.tujuan_kib === 'KIB B'">
                    <div class="space-y-3.5 p-4 rounded-2xl bg-slate-950/60 border border-slate-800 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>⚙️</span>
                                <span>Spesifikasi &amp; Identitas Fisik Peralatan &amp; Mesin:</span>
                            </span>
                            <span class="text-[9.5px] px-2 py-0.5 rounded-md font-bold border bg-amber-500/10 text-amber-300 border-amber-500/25">
                                KIB B &bull; Akun 1.3.2
                            </span>
                        </div>

                        <!-- Baris 1: Merk/Brand Pabrikan, Type/Model, Ukuran/Kapasitas (3 Kolom Sesuai Gambar 3) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            <div class="space-y-1 min-w-0">
                                <label class="block text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    Merk / Brand Pabrikan <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="formData.spekBaru.mesin_merk"
                                       placeholder="Contoh: GE Healthcare / Philips / Siemens..."
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs text-white font-bold placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    Type / Model <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="formData.spekBaru.mesin_type"
                                       placeholder="Contoh: Brivo XR575 / Veradius..."
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs text-white font-mono placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    Ukuran / Kapasitas
                                </label>
                                <input type="text" x-model="formData.spekBaru.mesin_ukuran_cc"
                                       placeholder="Contoh: 500 mA / 2000 VA / 150 cc..."
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>
                        </div>

                        <!-- Baris 2: No Pabrik/SN, Bahan Pembuatan, Kondisi Fisik Barang (3 Kolom Sesuai Gambar 3) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            <div class="space-y-1 min-w-0">
                                <label class="block text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    No Pabrik / Serial Number (SN)
                                </label>
                                <input type="text" x-model="formData.spekBaru.mesin_no_pabrik"
                                       :placeholder="(formData.selected_register_ids && formData.selected_register_ids.length > 1) ? 'Contoh: SN-001, SN-002 (pisahkan tanda koma)' : 'SN-RAD-2026-88192...'"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white font-mono placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    Bahan Pembuatan
                                </label>
                                <input type="text" x-model="formData.spekBaru.mesin_bahan"
                                       placeholder="Logam &amp; Elektronik / Stainless..."
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    Kondisi Fisik Barang <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="formData.spekBaru.mesin_kondisi"
                                        class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white font-bold focus:outline-none transition-all shadow-inner cursor-pointer">
                                    <option value="Baik">🟢 Baik (B)</option>
                                    <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                    <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Baris 3: Legality Kendaraan (Khusus Ambulans / Kendaraan Operasional) Sesuai Gambar 3 -->
                        <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2.5 transition-all">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🚗</span>
                                    <span>LEGALITY KENDARAAN (KHUSUS AMBULANS / KENDARAAN OPERASIONAL):</span>
                                </span>
                                <span class="text-[9.5px] text-slate-400 italic">Opsional jika bukan kendaraan bermotor</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                                <div class="space-y-1 min-w-0">
                                    <label class="block text-slate-400 text-[9.5px] font-semibold">No Rangka</label>
                                    <input type="text" x-model="formData.spekBaru.mesin_no_rangka" placeholder="MH1JM..."
                                           class="w-full h-9 bg-slate-900 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-lg px-2.5 text-xs text-white font-mono placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                                </div>
                                <div class="space-y-1 min-w-0">
                                    <label class="block text-slate-400 text-[9.5px] font-semibold">No Mesin</label>
                                    <input type="text" x-model="formData.spekBaru.mesin_no_mesin" placeholder="JM51E..."
                                           class="w-full h-9 bg-slate-900 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-lg px-2.5 text-xs text-white font-mono placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                                </div>
                                <div class="space-y-1 min-w-0">
                                    <label class="block text-slate-400 text-[9.5px] font-semibold">No BPKB</label>
                                    <input type="text" x-model="formData.spekBaru.mesin_no_bpkb" placeholder="BPKB-88..."
                                           class="w-full h-9 bg-slate-900 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-lg px-2.5 text-xs text-white font-mono placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                                </div>
                                <div class="space-y-1 min-w-0">
                                    <label class="block text-slate-400 text-[9.5px] font-semibold">No POLISI / Plat</label>
                                    <input type="text" x-model="formData.spekBaru.mesin_no_polisi" placeholder="P 1080 RS"
                                           class="w-full h-9 bg-slate-900 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-lg px-2.5 text-xs text-amber-300 font-mono font-bold placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB C - Gedung & Bangunan (Sesuai Gambar 1 yang Dirapikan) -->
                <template x-if="formData.tujuan_kib === 'KIB C'">
                    <div class="space-y-3.5 p-4 rounded-2xl bg-slate-950/60 border border-slate-800 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🏛️</span>
                                <span>Spesifikasi &amp; Legalitas Gedung / Bangunan (KIB C):</span>
                            </span>
                            <span class="text-[9.5px] px-2 py-0.5 rounded-md font-bold border bg-amber-500/10 text-amber-300 border-amber-500/25">
                                KIB C &bull; Akun 1.3.3
                            </span>
                        </div>

                        <!-- Baris 1: Status Bangunan & Konstruksi Bertingkat -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1 min-w-0">
                                <label class="flex items-center justify-between text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    <span>Status Bangunan:</span>
                                    <span class="text-amber-400 font-mono text-[9.5px]" x-text="formData.spekBaru.gedung_is_baru === 'Lama' ? '🏛️ Bangunan Lama' : '✨ Bangunan Baru'"></span>
                                </label>
                                <select x-model="formData.spekBaru.gedung_is_baru"
                                        class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white focus:outline-none transition-all shadow-inner cursor-pointer font-medium">
                                    <option value="Lama">🏛️ Bangunan Lama (Eksisting / Pasca-Kemitraan)</option>
                                    <option value="Baru">✨ Bangunan Baru (Pembangunan Perdana)</option>
                                </select>
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Konstruksi Bertingkat:
                                </label>
                                <select x-model="formData.spekBaru.gedung_konstruksi_bertingkat"
                                        class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white focus:outline-none transition-all shadow-inner cursor-pointer font-medium">
                                    <option value="Bertingkat">Bertingkat (2 Lantai atau lebih)</option>
                                    <option value="Tidak Bertingkat">Tidak Bertingkat (1 Lantai)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Baris 2: Konstruksi Beton & Luas Lantai -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1 min-w-0">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Konstruksi Beton:
                                </label>
                                <select x-model="formData.spekBaru.gedung_konstruksi_beton"
                                        class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white focus:outline-none transition-all shadow-inner cursor-pointer font-medium">
                                    <option value="Beton">Beton</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Luas Lantai Gedung (M²):
                                </label>
                                <input type="number" step="0.01" min="0" x-model="formData.spekBaru.gedung_luas_lantai_m2" placeholder="Contoh: 450.5"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs font-mono text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>
                        </div>

                        <!-- Baris 3: Status Kepemilikan Tanah Gedung (Lebar Penuh) -->
                        <div class="space-y-1 min-w-0">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                Status Kepemilikan Tanah Gedung:
                            </label>
                            <select x-model="formData.spekBaru.gedung_status_tanah"
                                    class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white focus:outline-none transition-all shadow-inner cursor-pointer font-medium">
                                <option value="Tanah Pemda">Tanah Milik Pemda / RSUD</option>
                                <option value="Hak Pakai">Hak Pakai</option>
                                <option value="Sewa">Sewa / Pinjam Pakai</option>
                            </select>
                        </div>

                        <!-- Baris 4: Nomor Dokumen PBG/IMB & Tanggal Dokumen IMB (Pasangan Sejajar Simetris) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1 min-w-0">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Nomor Dokumen PBG / IMB:
                                </label>
                                <input type="text" x-model="formData.spekBaru.gedung_dokumen_nomor" placeholder="Contoh: 640/IMB/DPMPTSP/2026"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Tanggal Dokumen IMB:
                                </label>
                                <input type="date" x-model="formData.spekBaru.gedung_dokumen_tgl"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs font-mono text-white focus:outline-none transition-all shadow-inner">
                            </div>
                        </div>

                        <!-- Baris 5: Alamat / Letak Gedung di RSUD -->
                        <div class="space-y-1 min-w-0">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                Alamat / Letak Gedung di RSUD:
                            </label>
                            <input type="text" x-model="formData.spekBaru.gedung_alamat" placeholder="Contoh: Kompleks RSUD Dr. H. Koesnandi"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB D - Jalan, Jaringan & Irigasi Sesuai Gambar 2 -->
                <template x-if="formData.tujuan_kib === 'KIB D'">
                    <div class="space-y-4">
                        <!-- Stack 2 Card Vertikal (Full Width Tanpa Kanan-Kiri Agar Tidak Tergencet) -->
                        <div class="space-y-4">

                            <!-- 1. Kondisi & Spesifikasi Jaringan -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-inner">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🏗️</span>
                                        <span>KONDISI &amp; SPESIFIKASI:</span>
                                    </span>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Jaringan / Ruas (Spesifik / Custom)</label>
                                    <input type="text" x-model="formData.spekBaru.jaringan_nama_barang"
                                           placeholder="Contoh: Jaringan Pipa Gas Oksigen Sentral Medis / Ruas Jalan Paviliun"
                                           class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Panjang (M)</label>
                                        <input type="number" min="0" step="any" x-model.number="formData.spekBaru.jaringan_panjang_m"
                                               @input="
                                                   if (formData.spekBaru.jaringan_panjang_m < 0) formData.spekBaru.jaringan_panjang_m = 0;
                                                   if (formData.spekBaru.jaringan_panjang_m !== null && formData.spekBaru.jaringan_panjang_m !== undefined && formData.spekBaru.jaringan_lebar_m !== null && formData.spekBaru.jaringan_lebar_m !== undefined) {
                                                       formData.spekBaru.jaringan_luas_m2 = parseFloat(((parseFloat(formData.spekBaru.jaringan_panjang_m) || 0) * (parseFloat(formData.spekBaru.jaringan_lebar_m) || 0)).toFixed(2));
                                                   }
                                               "
                                               placeholder="0"
                                               class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2 py-2 text-xs text-white font-mono font-bold focus:outline-none transition-all shadow-inner">
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Lebar (M)</label>
                                        <input type="number" min="0" step="any" x-model.number="formData.spekBaru.jaringan_lebar_m"
                                               @input="
                                                   if (formData.spekBaru.jaringan_lebar_m < 0) formData.spekBaru.jaringan_lebar_m = 0;
                                                   if (formData.spekBaru.jaringan_panjang_m !== null && formData.spekBaru.jaringan_panjang_m !== undefined && formData.spekBaru.jaringan_lebar_m !== null && formData.spekBaru.jaringan_lebar_m !== undefined) {
                                                       formData.spekBaru.jaringan_luas_m2 = parseFloat(((parseFloat(formData.spekBaru.jaringan_panjang_m) || 0) * (parseFloat(formData.spekBaru.jaringan_lebar_m) || 0)).toFixed(2));
                                                   }
                                               "
                                               placeholder="0"
                                               class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2 py-2 text-xs text-white font-mono focus:outline-none transition-all shadow-inner">
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                            <span>Luas (M2/Lt)</span>
                                            <span class="text-[9px] text-amber-400 font-mono">(P × L)</span>
                                        </label>
                                        <input type="number" min="0" step="any" x-model.number="formData.spekBaru.jaringan_luas_m2"
                                               @input="if (formData.spekBaru.jaringan_luas_m2 < 0) formData.spekBaru.jaringan_luas_m2 = 0;"
                                               placeholder="0"
                                               class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2 py-2 text-xs text-cyan-300 font-mono font-bold focus:outline-none transition-all shadow-inner">
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi (B/KB/RB)</label>
                                        <select x-model="formData.spekBaru.jaringan_kondisi"
                                                class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:outline-none transition-all shadow-inner cursor-pointer">
                                            <option value="B">🟢 B (Baik)</option>
                                            <option value="KB">🟡 KB (Kurang Baik)</option>
                                            <option value="RB">🔴 RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat / Tidak</label>
                                        <select x-model="formData.spekBaru.jaringan_bertingkat"
                                                class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner cursor-pointer">
                                            <option value="Bertingkat">Bertingkat</option>
                                            <option value="Tidak">Tidak Bertingkat</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Beton / Tidak</label>
                                        <select x-model="formData.spekBaru.jaringan_beton"
                                                class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner cursor-pointer">
                                            <option value="Beton">Beton</option>
                                            <option value="Tidak">Bukan Beton</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Status Tanah & Kapitalisasi Jaringan -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-inner">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📜</span>
                                        <span>STATUS TANAH &amp; KAPITALISASI:</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Tanah</label>
                                        <input type="text" x-model="formData.spekBaru.jaringan_status_tanah" placeholder="Tanah Hak Pakai RSUD"
                                               class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all shadow-inner">
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah</label>
                                        <input type="text" x-model="formData.spekBaru.jaringan_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                               class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-semibold focus:outline-none transition-all shadow-inner">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jaringan Baru</label>
                                        <select x-model="formData.spekBaru.jaringan_is_baru"
                                                @change="if (formData.spekBaru.jaringan_is_baru === 'Baru') { formData.spekBaru.jaringan_kapitalisasi_tahun_induk = ''; formData.spekBaru.jaringan_kapitalisasi_nilai_induk = 0; }"
                                                class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all shadow-inner cursor-pointer font-medium">
                                            <option value="Baru">Baru</option>
                                            <option value="Lama">Lama</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tahun Induk</label>
                                        <input type="text" x-model="formData.spekBaru.jaringan_kapitalisasi_tahun_induk"
                                               :disabled="formData.spekBaru.jaringan_is_baru === 'Baru'"
                                               :class="formData.spekBaru.jaringan_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700/80 focus:border-cyan-400 text-white'"
                                               placeholder="2021"
                                               class="w-full border rounded-xl px-3 py-2 text-xs font-mono transition-all shadow-inner">
                                    </div>

                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nilai Induk</label>
                                        <input type="text"
                                               :disabled="formData.spekBaru.jaringan_is_baru === 'Baru'"
                                               :class="formData.spekBaru.jaringan_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700/80 focus:border-cyan-400 text-amber-300 font-bold'"
                                               :value="formData.spekBaru.jaringan_is_baru === 'Baru' ? '' : (formData.spekBaru.jaringan_kapitalisasi_nilai_induk ? Number(formData.spekBaru.jaringan_kapitalisasi_nilai_induk).toLocaleString('id-ID') : '')"
                                               @input="
                                                   let raw = $event.target.value.replace(/\D/g, '');
                                                   formData.spekBaru.jaringan_kapitalisasi_nilai_induk = raw ? parseInt(raw, 10) : 0;
                                                   $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               "
                                               placeholder="850.000.000"
                                               class="w-full border rounded-xl px-3 py-2 text-xs font-mono transition-all shadow-inner">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Baris 3: Alamat / Letak Jaringan di RSUD -->
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                Alamat / Letak Ruas Jaringan di Kompleks RSUD:
                            </label>
                            <input type="text" x-model="formData.spekBaru.jaringan_alamat" placeholder="Contoh: Sayap Timur Kompleks RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB E - Aset Tetap Lainnya -->
                <template x-if="formData.tujuan_kib === 'KIB E'">
                    <div class="space-y-4">
                        <!-- Header Card KIB E -->
                        <div class="flex items-center justify-between pb-1 border-b border-slate-800/80">
                            <div class="flex items-center space-x-2">
                                <span class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-300 flex items-center justify-center text-sm border border-purple-500/40">📦</span>
                                <div>
                                    <span class="text-[11px] font-extrabold text-purple-300 uppercase tracking-wider">
                                        Spesifikasi Aset Tetap Lainnya (KIB E):
                                    </span>
                                    <p class="text-[10px] text-slate-400">Buku Perpustakaan Medis, Kesenian/Budaya &amp; Ornamen, atau Hewan &amp; Tanaman RSUD.</p>
                                </div>
                            </div>
                            <span class="text-[9.5px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold font-mono">
                                KIB E &bull; Akun 1.3.5
                            </span>
                        </div>

                        <!-- Selector Sub-Kategori KIB E (Interactive Pill Buttons) -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider flex items-center justify-between">
                                <span>PILIH SUB-KATEGORI KIB E:</span>
                                <span class="text-[9px] text-purple-400 font-mono" x-text="'Aktif: ' + ((formData.spekBaru.kib_e_type === 'kesenian') ? 'Kesenian (1.3.5.02)' : ((formData.spekBaru.kib_e_type === 'hewan_tumbuhan') ? 'Hewan/Tanaman (1.3.5.03)' : 'Buku Perpustakaan (1.3.5.01)'))"></span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <button type="button" @click="formData.spekBaru.kib_e_type = 'buku'"
                                        :class="(formData.spekBaru.kib_e_type || 'buku') === 'buku' ? 'border-amber-500/80 bg-amber-950/40 text-amber-200 ring-1 ring-amber-500/50 shadow-sm' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200'"
                                        class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">📚</span>
                                        <div>
                                            <div class="text-xs font-bold">Buku Perpustakaan</div>
                                            <div class="text-[9px] font-mono text-slate-400">Kode 1.3.5.01</div>
                                        </div>
                                    </div>
                                    <span x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="text-amber-400 font-bold text-xs">✓</span>
                                </button>
                                <button type="button" @click="formData.spekBaru.kib_e_type = 'kesenian'"
                                        :class="formData.spekBaru.kib_e_type === 'kesenian' ? 'border-purple-500/80 bg-purple-950/40 text-purple-200 ring-1 ring-purple-500/50 shadow-sm' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200'"
                                        class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🎨</span>
                                        <div>
                                            <div class="text-xs font-bold">Kesenian &amp; Budaya</div>
                                            <div class="text-[9px] font-mono text-slate-400">Kode 1.3.5.02</div>
                                        </div>
                                    </div>
                                    <span x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="text-purple-400 font-bold text-xs">✓</span>
                                </button>
                                <button type="button" @click="formData.spekBaru.kib_e_type = 'hewan_tumbuhan'"
                                        :class="formData.spekBaru.kib_e_type === 'hewan_tumbuhan' ? 'border-emerald-500/80 bg-emerald-950/40 text-emerald-200 ring-1 ring-emerald-500/50 shadow-sm' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200'"
                                        class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🌿</span>
                                        <div>
                                            <div class="text-xs font-bold">Hewan &amp; Tanaman</div>
                                            <div class="text-[9px] font-mono text-slate-400">Kode 1.3.5.03</div>
                                        </div>
                                    </div>
                                    <span x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="text-emerald-400 font-bold text-xs">✓</span>
                                </button>
                            </div>
                        </div>

                        <!-- 1. Form Sub-Kategori: Buku Perpustakaan / Literatur Medis -->
                        <div x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="p-3.5 rounded-2xl bg-slate-950/70 border border-amber-500/30 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <span class="text-[10.5px] font-extrabold text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>📚</span>
                                    <span>Atribut Buku Perpustakaan / Literatur Medis:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">KIB E Buku</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                        Judul Buku / Nama Literatur <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_judul"
                                           @input="formData.spekBaru.lainnya_nama_barang = formData.spekBaru.lainnya_judul; formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Pedoman Standar Pelayanan Klinis & Formularium RSUD..."
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Pengarang / Pencipta / Penerbit:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_pencipta"
                                           @input="formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Tim Komite Medik RSUD / Penerbit EGC"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Spesifikasi / Edisi / Cetakan:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi"
                                           placeholder="Contoh: Edisi Revisi 2026 / Cetakan Lux Hardcover"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan Pembuatan / Cover:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_bahan"
                                           placeholder="Contoh: Kertas Art Paper / Hardcover / HVS"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Buku:</label>
                                    <select x-model="formData.spekBaru.kondisi"
                                            @change="if (formData.spekBaru) { formData.spekBaru.lainnya_kondisi = formData.spekBaru.kondisi; }"
                                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none transition-all cursor-pointer">
                                        <option value="Baik">🟢 Baik (B)</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Form Sub-Kategori: Kesenian & Budaya / Ornamen -->
                        <div x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="p-3.5 rounded-2xl bg-slate-950/70 border border-purple-500/30 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <span class="text-[10.5px] font-extrabold text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🎨</span>
                                    <span>Atribut Benda Seni, Budaya &amp; Ornamen:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20 font-bold">KIB E Kesenian</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                        Judul Karya Seni / Nama Barang <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_judul"
                                           @input="formData.spekBaru.lainnya_nama_barang = formData.spekBaru.lainnya_judul; formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Lukisan Sejarah Pelayanan RSUD Bondowoso / Patung Ornamen Medis"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Pencipta / Seniman:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_pencipta"
                                           @input="formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Sanggar Seni Rupa Bondowoso"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Asal Daerah / Wilayah:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_asal_daerah"
                                           placeholder="Contoh: Bondowoso, Jawa Timur"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <!-- Baris 3 Kolom Seimbang: Bahan, Ukuran & Kondisi Fisik -->
                                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan Pembuatan:</label>
                                        <input type="text" x-model="formData.spekBaru.lainnya_bahan"
                                               placeholder="Contoh: Kanvas Cat Minyak / Logam Perunggu / Kayu Jati"
                                               class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Ukuran / Dimensi:</label>
                                        <input type="text" x-model="formData.spekBaru.lainnya_ukuran"
                                               placeholder="Contoh: 150 x 200 cm / Tinggi 1.8 Meter"
                                               class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Barang:</label>
                                        <select x-model="formData.spekBaru.kondisi"
                                                @change="if (formData.spekBaru) { formData.spekBaru.lainnya_kondisi = formData.spekBaru.kondisi; }"
                                                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none transition-all cursor-pointer">
                                            <option value="Baik">🟢 Baik (B)</option>
                                            <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                            <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Spesifikasi / Deskripsi Karya:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi"
                                           placeholder="Contoh: Pigura ukir kayu mahoni gold, terpajang di lobi utama RSUD"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Form Sub-Kategori: Hewan & Tanaman -->
                        <div x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="p-3.5 rounded-2xl bg-slate-950/70 border border-emerald-500/30 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <span class="text-[10.5px] font-extrabold text-emerald-300 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🌿</span>
                                    <span>Atribut Hewan, Ternak &amp; Tanaman:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-bold">KIB E Flora/Fauna</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                        Jenis Hewan / Tanaman <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_judul"
                                           @input="formData.spekBaru.lainnya_nama_barang = formData.spekBaru.lainnya_judul; formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Pohon Ketapang Kencana / Palem Raja / Tanaman Hias Lanskap"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                                <!-- Baris 3 Kolom Seimbang: Ukuran, Asal Usul & Kondisi Fisik -->
                                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Ukuran / Tinggi / Usia:</label>
                                        <input type="text" x-model="formData.spekBaru.lainnya_ukuran"
                                               placeholder="Contoh: Tinggi 3.5 Meter / Usia 2 Tahun"
                                               class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Asal Usul / Bibit:</label>
                                        <input type="text" x-model="formData.spekBaru.lainnya_asal_daerah"
                                               placeholder="Contoh: Balai Pembibitan Pertanian Jawa Timur"
                                               class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Hewan / Tanaman:</label>
                                        <select x-model="formData.spekBaru.kondisi"
                                                @change="if (formData.spekBaru) { formData.spekBaru.lainnya_kondisi = formData.spekBaru.kondisi; }"
                                                class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none transition-all cursor-pointer">
                                            <option value="Baik">🟢 Baik (B)</option>
                                            <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                            <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Keterangan / Lokasi Penghijauan:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi"
                                           placeholder="Contoh: Penghijauan Taman Lobi Rawat Jalan RSUD Dr. H. Koesnandi"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: ATB - Aset Tak Berwujud -->
                <template x-if="formData.tujuan_kib === 'ATB'">
                    <div class="space-y-3">
                        <!-- Judul / Nama Software & Lisensi -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Nama Software &amp; Lisensi</label>
                            <input type="text" 
                                   x-model="formData.spekBaru.atb_judul_nama" 
                                   @input="formData.spekBaru.atb_nama_software = formData.spekBaru.atb_judul_nama;"
                                   placeholder="Aplikasi SIMAT-RK (Sistem Informasi Manajemen Aset)..."
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- Pencipta / Vendor / Pengembang -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Vendor / Pengembang</label>
                            <input type="text" 
                                   x-model="formData.spekBaru.atb_pencipta" 
                                   @input="formData.spekBaru.atb_pengembang = formData.spekBaru.atb_pencipta;"
                                   placeholder="Tim IT SIMRS RSUD & Pengembang Sistem"
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- Spesifikasi Software / Hak Cipta -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Software / Hak Cipta</label>
                            <textarea rows="2" 
                                      x-model="formData.spekBaru.atb_spesifikasi" 
                                      placeholder="Web-Based, Multi-Role Access, Integrasi SatuSehat & RME..."
                                      class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-cyan-300 focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                        </div>
                    </div>
                </template>


                <!-- Form Spesifik: KEMITRAAN - Kemitraan Pihak Ketiga (Akun 1.5.2 Sesuai Form Kemitraan Langkah 1) -->
                <template x-if="formData.tujuan_kib === 'KEMITRAAN'">
                    <div class="space-y-4 pt-1">

                        <!-- 2. Nama Perusahaan Mitra / Rekanan Pihak Ketiga (Ketik Filter seperti Form 1 Kemitraan) -->
                        <div class="relative" @click.away="isReklasMitraDropdownOpen = false">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Nama Perusahaan Mitra / Rekanan: <span class="text-rose-400">*</span>
                                </label>
                                <div class="flex items-center gap-1.5">
                                    <template x-if="formData.spekBaru.kemitraan_mitra && filteredReklasMitraList.length === 0">
                                        <span class="text-[9.5px] text-emerald-400 font-mono font-bold flex items-center gap-1 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                            <span>➕</span>
                                            <span>Mitra Baru</span>
                                        </span>
                                    </template>
                                    <template x-if="masterMitraList && masterMitraList.length > 0">
                                        <span class="text-[9.5px] text-cyan-400 font-mono font-normal flex items-center gap-1 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/20">
                                            <span>⚡</span>
                                            <span>Riwayat Tersimpan</span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Input Box dengan Ikon dan Tombol Clear -->
                            <div class="relative">
                                <input type="text" 
                                    x-model="formData.spekBaru.kemitraan_mitra"
                                    @focus="isReklasMitraDropdownOpen = true"
                                    @input="isReklasMitraDropdownOpen = true; onReklasMitraInput($event.target.value)"
                                    @change="onReklasMitraInput($event.target.value)"
                                    @keydown.escape="isReklasMitraDropdownOpen = false"
                                    @keydown.enter.prevent="isReklasMitraDropdownOpen = false"
                                    autocomplete="off"
                                    placeholder="Ketik atau pilih mitra rekanan (contoh: PT. Kimia Farma, PT. Roche...)"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3.5 py-2.5 pl-9 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold shadow-inner transition-all">

                                <!-- Ikon Mitra Perusahaan -->
                                <svg class="w-4 h-4 text-cyan-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>

                                <!-- Tombol Kosongkan Input (Clear Button) -->
                                <button type="button" 
                                    x-show="formData.spekBaru.kemitraan_mitra"
                                    @click="formData.spekBaru.kemitraan_mitra = ''; isReklasMitraDropdownOpen = true" 
                                    title="Kosongkan nama mitra"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                                    class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                                    ✕
                                </button>
                            </div>

                            <!-- Floating Dropdown Saran / Filter Mitra (Hanya Tampil Jika Ada Saran yang Cocok) -->
                            <div x-show="isReklasMitraDropdownOpen && filteredReklasMitraList.length > 0" 
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                style="max-height: 220px !important; overflow-y: auto !important;"
                                class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                                
                                <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                    <span>Pilih Riwayat / Ketik Mitra Baru</span>
                                    <span class="font-mono text-slate-400" x-text="filteredReklasMitraList.length + ' saran'"></span>
                                </div>

                                <template x-for="(mitra, mIdx) in filteredReklasMitraList" :key="mIdx">
                                    <div @click="selectReklasMitra(mitra)"
                                        class="px-3.5 py-2 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                                        :class="formData.spekBaru.kemitraan_mitra === (mitra.nama || mitra) ? 'bg-cyan-500/20 text-cyan-200' : 'text-slate-200'">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="text-xs text-cyan-400/80">🤝</span>
                                            <div class="min-w-0">
                                                <span class="text-xs font-bold group-hover:text-cyan-300 truncate block" x-text="mitra.nama || mitra"></span>
                                                <template x-if="mitra.pimpinan || mitra.alamat">
                                                    <span class="text-[10px] text-slate-400 truncate block font-normal mt-0.5" x-text="(mitra.pimpinan ? 'Pimpinan: ' + mitra.pimpinan : '') + (mitra.pimpinan && mitra.alamat ? ' • ' : '') + (mitra.alamat ? 'Alamat: ' + mitra.alamat : '')"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0 group-hover:bg-cyan-500/25">
                                            Pilih &amp; Auto-fill ↵
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 3. Pejabat Mitra & Alamat Domisili -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Pejabat Mitra / Direktur Rekanan: <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="formData.spekBaru.kemitraan_pimpinan"
                                    placeholder="Nama Direktur / Penanggung Jawab Rekanan"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none placeholder-slate-500 shadow-inner">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Alamat Kantor Domisili Mitra:
                                </label>
                                <input type="text" x-model="formData.spekBaru.kemitraan_alamat"
                                    placeholder="Alamat kantor / domisili rekanan mitra"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none placeholder-slate-500 shadow-inner">
                            </div>
                        </div>

                        <!-- 3. Skema Pemanfaatan Kemitraan (Otomatis Terkunci dari Rekening 108) -->
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Bentuk Skema Pemanfaatan (Permendagri 108): <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] font-mono font-bold flex items-center gap-1 text-cyan-400 bg-cyan-500/10 border border-cyan-500/25 px-2 py-0.5 rounded-md">
                                    <span>🔒</span>
                                    <span>Otomatis Sesuai Akun 108</span>
                                </span>
                            </div>
                            <input type="hidden" x-model="formData.spekBaru.kemitraan_skema">
                            <div class="w-full bg-slate-950/90 border border-cyan-500/30 rounded-xl p-2.5 flex items-center justify-between gap-3 shadow-inner">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-sm shrink-0"
                                         x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? '💰' : (formData.spekBaru.kemitraan_skema === 'BGS/BSG' ? '🏗️' : (formData.spekBaru.kemitraan_skema === 'KSPI' ? '⚡' : '🤝'))">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-extrabold text-white truncate" 
                                             x-text="formData.spekBaru.kemitraan_skema || 'Sewa'"></div>
                                        <div class="text-[10px] text-cyan-300/80 font-mono truncate"
                                             x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? 'Akun 1.5.2.01.01.01 — Sewa BMD' : (formData.spekBaru.kemitraan_skema === 'BGS/BSG' ? 'Akun 1.5.2.01.01.03 — Bangun Guna Serah / BSG' : (formData.spekBaru.kemitraan_skema === 'KSPI' ? 'Akun 1.5.2.01.01.04 — KSPI' : 'Akun 1.5.2.01.01.02 — Kerja Sama Pemanfaatan (KP)'))">
                                        </div>
                                    </div>
                                </div>
                                <span class="shrink-0 text-[10px] font-bold text-slate-400 bg-slate-900 border border-slate-800 px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <span>✓</span>
                                    <span>Terpilih di Atas</span>
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-500 mt-1">
                                Skema pemanfaatan otomatis disinkronkan dan terkunci dari Sub-Rincian Rekening 108 yang Anda pilih pada langkah di atas.
                            </p>
                        </div>

                        <!-- 4. Nomor PKS & Tanggal PKS -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Nomor Dokumen Perjanjian (PKS / MoU): <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="formData.spekBaru.kemitraan_perjanjian_no"
                                    placeholder="Contoh: 000.2.3.2/PKS-KSO/430.10.7/2026"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none shadow-inner">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Tanggal Penandatanganan PKS: <span class="text-rose-400">*</span>
                                </label>
                                <input type="date" x-model="formData.spekBaru.kemitraan_tanggal_pks"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none shadow-inner">
                            </div>
                        </div>

                        <!-- 5. Masa Berlaku Kerjasama (Tanggal Mulai & Tanggal Selesai) -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/90 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-cyan-300 flex items-center gap-1.5">
                                    <span>🗓️</span>
                                    <span>Masa Berlaku Kerja Sama (Konsesi Pemanfaatan)</span>
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                        Tanggal Mulai Berlaku: <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="formData.spekBaru.kemitraan_tanggal_mulai"
                                        @input="onReklasKemitraanTanggalMulaiChange()"
                                        @change="onReklasKemitraanTanggalMulaiChange()"
                                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                        Tanggal Berakhir (Konsesi Selesai): <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="formData.spekBaru.kemitraan_tanggal_selesai"
                                        :min="formData.spekBaru.kemitraan_tanggal_mulai"
                                        @input="calcReklasKemitraanDurasi()"
                                        @change="calcReklasKemitraanDurasi()"
                                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[10px] font-semibold text-slate-400">
                                        Estimasi Jangka Waktu / Durasi:
                                    </label>
                                    <span class="text-[9.5px] text-cyan-400 font-mono">⚡ Ketik 2 Tahun dkk, tanggal berakhir otomatis menyesuaikan</span>
                                </div>
                                <input type="text" x-model="formData.spekBaru.kemitraan_jangka_waktu"
                                    @input="onReklasKemitraanDurasiInput()"
                                    @change="onReklasKemitraanDurasiInput()"
                                    placeholder="Contoh: 2 Tahun / 6 Bulan / 90 Hari"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-medium">
                                <!-- Shortcut Preset Buttons -->
                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                    <span class="text-[9.5px] text-slate-500 font-medium mr-0.5">⚡ Pilihan Cepat:</span>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(6, 'bulan')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +6 Bulan
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(1, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +1 Tahun
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(2, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +2 Tahun
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(3, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +3 Tahun
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(5, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +5 Tahun
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Tarif / Nilai Finansial Sewa & Kontribusi Pemanfaatan -->
                        <div class="p-3.5 rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 border border-cyan-500/30 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-[11px] font-bold text-cyan-300 flex items-center gap-1.5">
                                    <span x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? '💰' : '🤝'"></span>
                                    <span x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? 'Tarif / Nilai Harga Sewa Pemanfaatan BMD' : 'Nilai Kontribusi / Kerja Sama Pemanfaatan'"></span>
                                </span>
                                <span class="text-[9.5px] font-mono px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-300 border border-cyan-500/20"
                                      x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? 'Pendapatan Sewa BMD' : 'Bagi Hasil / Kontribusi'"></span>
                            </div>

                            <!-- KONDISI A: JIKA SKEMA SEWA (S) -->
                            <div x-show="formData.spekBaru.kemitraan_skema === 'Sewa'" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Harga / Tarif Sewa (Rp): <span class="text-rose-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs font-bold text-cyan-400">Rp</span>
                                            <input type="number" min="0" step="1000" x-model="formData.spekBaru.kemitraan_harga_sewa"
                                                placeholder="Contoh: 50000000"
                                                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl pl-9 pr-3 py-2 text-xs font-mono font-bold text-white focus:outline-none shadow-inner">
                                        </div>
                                        <template x-if="formData.spekBaru.kemitraan_harga_sewa > 0">
                                            <p class="text-[10px] text-cyan-400 font-mono font-semibold mt-1"
                                               x-text="'Terbaca: Rp ' + Number(formData.spekBaru.kemitraan_harga_sewa).toLocaleString('id-ID')"></p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Periode / Satuan Waktu Sewa: <span class="text-rose-400">*</span>
                                        </label>
                                        <select x-model="formData.spekBaru.kemitraan_periode_sewa"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-semibold">
                                            <option value="Per Tahun">Per Tahun (Tahunan)</option>
                                            <option value="Per Bulan">Per Bulan (Bulanan)</option>
                                            <option value="Total Selama Kontrak">Total Selama Masa Kontrak</option>
                                        </select>
                                        <p class="text-[9.5px] text-slate-500 mt-1">Dasar perhitungan tagihan penerimaan sewa daerah.</p>
                                    </div>
                                </div>

                                <!-- Highlight Estimasi Total Penerimaan Kontrak Sewa -->
                                <template x-if="getReklasEstimasiTotalSewa() > 0">
                                    <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">📈</span>
                                            <div>
                                                <span class="text-[10px] text-emerald-300 font-bold uppercase tracking-wider block">Estimasi Total Kontrak Sewa:</span>
                                                <span class="text-slate-400 text-[10px]" x-text="'Durasi: ' + (formData.spekBaru.kemitraan_jangka_waktu || '-') + ' • Tarif ' + (formData.spekBaru.kemitraan_periode_sewa || 'Per Tahun')"></span>
                                            </div>
                                        </div>
                                        <span class="font-mono font-extrabold text-sm text-emerald-300"
                                              x-text="'Rp ' + Number(getReklasEstimasiTotalSewa()).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- KONDISI B: JIKA SKEMA NON-SEWA (KP, BGS/BSG, KSPI) -->
                            <div x-show="formData.spekBaru.kemitraan_skema !== 'Sewa'" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Nilai Kontribusi Tetap / Bagi Hasil (Rp):
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs font-bold text-cyan-400">Rp</span>
                                            <input type="number" min="0" step="1000" x-model="formData.spekBaru.kemitraan_nilai_kontribusi"
                                                placeholder="Contoh: 75000000"
                                                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl pl-9 pr-3 py-2 text-xs font-mono font-bold text-white focus:outline-none shadow-inner">
                                        </div>
                                        <template x-if="formData.spekBaru.kemitraan_nilai_kontribusi > 0">
                                            <p class="text-[10px] text-cyan-400 font-mono font-semibold mt-1"
                                               x-text="'Terbaca: Rp ' + Number(formData.spekBaru.kemitraan_nilai_kontribusi).toLocaleString('id-ID')"></p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Keterangan Pola Pembagian / Kontribusi:
                                        </label>
                                        <input type="text" x-model="formData.spekBaru.kemitraan_keterangan_kontribusi"
                                            placeholder="Contoh: Kontribusi tetap per tahun + 15% laba operasional"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none shadow-inner">
                                    </div>
                                </div>
                            </div>

                            <div class="text-[9.5px] text-slate-400 bg-slate-950/60 p-2 rounded-lg border border-slate-800 flex items-center gap-1.5">
                                <span>ℹ️</span>
                                <span><strong>Catatan:</strong> Nilai buku aset BMD (tanah/gedung) yang direklasifikasi tetap dicatat terpisah sebesar nilai perolehan/revaluasinya. Nilai di atas adalah nilai kontrak finansial pemanfaatan.</span>
                            </div>
                        </div>

                        <!-- 7. SPESIFIKASI FISIK OBJEK BMD YANG DIKEMITRAKAN (OTOMATIS TERKUNCI SESUAI ASET ASAL & PMDN 108) -->
                        <div class="space-y-4">
                            <!-- Header Sub-Kelompok Fisik Kemitraan (Bersih & Terkunci Otomatis Sesuai Gambar 1) -->
                            <div class="flex items-center justify-between pb-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs" x-text="getKemitraanTargetSubGroup() === 'tanah' ? '🌾' : (getKemitraanTargetSubGroup() === 'gedung' ? '🏢' : (getKemitraanTargetSubGroup() === 'jaringan' ? '🛣️' : (getKemitraanTargetSubGroup() === 'lainnya' ? '📦' : '⚙️')))"></span>
                                    <span class="text-[11px] font-extrabold text-cyan-300 uppercase tracking-wider"
                                          x-text="'Spesifikasi Fisik ' + (getKemitraanTargetSubGroup() === 'tanah' ? 'Tanah' : (getKemitraanTargetSubGroup() === 'gedung' ? 'Gedung & Bangunan' : (getKemitraanTargetSubGroup() === 'jaringan' ? 'Jalan & Jaringan' : (getKemitraanTargetSubGroup() === 'lainnya' ? 'Aset Tetap Lainnya' : 'Peralatan & Mesin')))) + ' Kemitraan:'">
                                    </span>
                                </div>
                                <span class="text-[9.5px] px-2.5 py-0.5 rounded-md font-bold border bg-cyan-500/15 text-cyan-300 border-cyan-500/30 flex items-center gap-1">
                                    <span>🤝</span>
                                    <span>Objek Fisik Kemitraan</span>
                                </span>
                            </div>
                            
                            <!-- SUB-TIPE 1: PERALATAN DAN MESIN (KIB B / 1.5.2.xx.002) -->
                            <template x-if="getKemitraanTargetSubGroup() === 'mesin'">
                                <div class="space-y-4">
                                    <!-- Single Cohesive Card: Spesifikasi Fisik Mesin Reguler (Format Lengkap) -->
                                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-md">
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                <span>⚙️ Spesifikasi & Identitas Fisik Peralatan & Mesin:</span>
                                            </span>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Identitas Fisik & Spesifikasi</span>
                                        </div>

                                        <!-- Baris 1: Merk, Type, Ukuran & Tahun Pembuatan (4 Kolom Seimbang) -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                    Merk / Brand Pabrikan <span class="text-rose-400">*</span>
                                                </label>
                                                <input type="text" x-model="formData.spekBaru.mesin_merk"
                                                       placeholder="Contoh: Siemens / Mindray / Roche / Sysmex / Fresenius / GE"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Type / Model <span class="text-rose-400">*</span></label>
                                                <input type="text" x-model="formData.spekBaru.mesin_type"
                                                       placeholder="Contoh: SOMATOM go.Now / DC-70 / 4008S"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Kapasitas</label>
                                                <input type="text" x-model="formData.spekBaru.mesin_ukuran_cc"
                                                       placeholder="Contoh: 128 Slice / 300 Test/Jam"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tahun Pabrik</label>
                                                <input type="number" min="1970" :max="new Date().getFullYear()" x-model.number="formData.spekBaru.mesin_tahun_pembuatan"
                                                       placeholder="Contoh: {{ date('Y') }}"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                            </div>
                                        </div>

                                        <!-- Baris 2: No Pabrik/SN, Bahan, Kondisi (3 Kolom Seimbang) -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                    No Pabrik / Serial Number (SN)
                                                </label>
                                                <input type="text" x-model="formData.spekBaru.mesin_no_pabrik"
                                                       :placeholder="(formData.selected_register_ids && formData.selected_register_ids.length > 1) ? 'Contoh: SN-001, SN-002 (pisahkan tanda koma)' : 'SN-RAD-2026-88192'"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                                <input type="text" x-model="formData.spekBaru.mesin_bahan"
                                                       placeholder="Logam & Elektronik / Stainless"
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                    Kondisi Fisik Barang <span class="text-rose-400">*</span>
                                                </label>
                                                <select x-model="formData.spekBaru.mesin_kondisi"
                                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                                                    <option value="Baik">🟢 Baik (B)</option>
                                                    <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                                    <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Baris 3: Legality Kendaraan (Khusus Ambulans / Kendaraan Operasional) -->
                                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2 transition-all">
                                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1">
                                                <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                                    <span>🚗</span>
                                                    <span>Legality Kendaraan (Khusus Ambulans / Kendaraan Operasional):</span>
                                                </span>
                                                <span class="text-[9px] text-slate-500 italic">Opsional jika bukan kendaraan bermotor</span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                                                <div>
                                                    <label class="block text-slate-500 text-[9px] mb-1">No Rangka</label>
                                                    <input type="text" x-model="formData.spekBaru.mesin_no_rangka" placeholder="MH1JM..."
                                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-500 text-[9px] mb-1">No Mesin</label>
                                                    <input type="text" x-model="formData.spekBaru.mesin_no_mesin" placeholder="JM51E..."
                                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-500 text-[9px] mb-1">No BPKB</label>
                                                    <input type="text" x-model="formData.spekBaru.mesin_no_bpkb" placeholder="BPKB-88..."
                                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-500 text-[9px] mb-1">No POLISI / Plat</label>
                                                    <input type="text" x-model="formData.spekBaru.mesin_no_polisi" placeholder="P 1080 RS"
                                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono font-bold focus:border-cyan-500 focus:outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Keterangan / Catatan Khusus Mesin -->
                                    <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1 shadow-md">
                                        <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                            📝 Keterangan / Catatan Khusus Peralatan:
                                        </label>
                                        <input type="text" x-model="formData.spekBaru.mesin_keterangan"
                                               placeholder="Contoh: Penempatan alat uji laboratorium KSO / Garansi vendor 3 tahun / Keterangan operasional"
                                               class="w-full bg-slate-950 border border-slate-700 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                    </div>
                                </div>
                            </template>

                            <!-- SUB-TIPE 2: ASET TETAP LAINNYA (KIB E / 1.5.2.xx.005) -->
                            <template x-if="getKemitraanTargetSubGroup() === 'lainnya'">
                                <div class="space-y-4">
                                    <div class="space-y-3">
                                            <!-- Tab Kategori KIB E (Buku, Kesenian, Hewan/Tumbuhan) -->
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                                <div @click="formData.spekBaru.kib_e_type = 'buku'"
                                                     :class="(formData.spekBaru.kib_e_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-amber-300/80'"
                                                     class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                                    <div class="flex items-center space-x-2">
                                                        <span>📚</span>
                                                        <div class="text-left">
                                                            <div class="text-xs">Buku Perpustakaan</div>
                                                            <div class="text-[9px] text-slate-400 font-mono font-normal">Pustaka Medis</div>
                                                        </div>
                                                    </div>
                                                    <span x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="text-amber-400 font-bold">✓</span>
                                                </div>
                                                <div @click="formData.spekBaru.kemitraan_kib_e_type = 'kesenian'; formData.spekBaru.kib_e_type = 'kesenian'"
                                                     :class="formData.spekBaru.kib_e_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-purple-300/80'"
                                                     class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                                    <div class="flex items-center space-x-2">
                                                        <span>🎨</span>
                                                        <div class="text-left">
                                                            <div class="text-xs">Kesenian &amp; Budaya</div>
                                                            <div class="text-[9px] text-slate-400 font-mono font-normal">Karya Seni / Ornamen</div>
                                                        </div>
                                                    </div>
                                                    <span x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="text-purple-400 font-bold">✓</span>
                                                </div>
                                                <div @click="formData.spekBaru.kemitraan_kib_e_type = 'hewan_tumbuhan'; formData.spekBaru.kib_e_type = 'hewan_tumbuhan'"
                                                     :class="formData.spekBaru.kib_e_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-emerald-300/80'"
                                                     class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                                    <div class="flex items-center space-x-2">
                                                        <span>🌿</span>
                                                        <div class="text-left">
                                                            <div class="text-xs">Hewan &amp; Tumbuhan</div>
                                                            <div class="text-[9px] text-slate-400 font-mono font-normal">Penghijauan / Hewan Lab</div>
                                                        </div>
                                                    </div>
                                                    <span x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="text-emerald-400 font-bold">✓</span>
                                                </div>
                                            </div>

                                            <!-- 1. Form Buku Perpustakaan -->
                                            <div x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul Buku / Pustaka Medis <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_judul" placeholder="Contoh: Atlas Anatomi Manusia Sobotta Edisi 24"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pengarang / Penerbit / Pencipta</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_pencipta" placeholder="Contoh: EGC Penerbit Buku Kedokteran"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi / Edisi</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi" placeholder="Contoh: Edisi Revisi 2026 / Hardcover Lux"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. Form Benda Seni & Budaya -->
                                            <div x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Asal Daerah / Wilayah</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_asal_daerah" placeholder="Bondowoso, Jawa Timur"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Seniman</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_pencipta" placeholder="Sanggar Seni Rupa Bondowoso"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_bahan" placeholder="Kanvas Cat Minyak / Perunggu / Kayu Jati"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Dimensi</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_ukuran" placeholder="150 x 200 cm"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Karya Seni / Ornamen</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi" placeholder="Lukisan dinding sejarah pelayanan RSUD"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 3. Form Hewan & Tumbuhan -->
                                            <div x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jenis Hewan / Tanaman <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_judul" placeholder="Contoh: Pohon Ketapang Kencana / Palem Raja"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Tinggi</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_ukuran" placeholder="Tinggi 3.5 Meter"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Keterangan / Spesifikasi Khusus</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi" placeholder="Penghijauan Area Taman Rawat Inap Barat"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kondisi Fisik KIB E Reguler -->
                                            <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between gap-3">
                                                <label class="text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">Kondisi Fisik Barang:</label>
                                                <select x-model="formData.spekBaru.lainnya_kondisi" 
                                                        @change="if (formData.spekBaru) { formData.spekBaru.kondisi = formData.spekBaru.lainnya_kondisi; }"
                                                        class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-bold focus:border-cyan-500 cursor-pointer">
                                                    <option value="Baik">🟢 Baik (B)</option>
                                                    <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                                    <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- SUB-TIPE 3: GEDUNG DAN BANGUNAN (KIB C / 1.5.2.xx.003) -->
                            <template x-if="getKemitraanTargetSubGroup() === 'gedung'">
                                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Bertingkat:</label>
                                            <select x-model="formData.spekBaru.gedung_konstruksi_bertingkat" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Bertingkat">Bertingkat</option>
                                                <option value="Tidak Bertingkat">Tidak Bertingkat</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Beton:</label>
                                            <select x-model="formData.spekBaru.gedung_konstruksi_beton" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Beton">Beton</option>
                                                <option value="Tidak">Bukan Beton / Semi Permanen</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Lantai (M2):</label>
                                            <input type="number" step="0.01" min="0" x-model="formData.spekBaru.gedung_luas_lantai_m2" placeholder="Contoh: 450.5" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Status Hak Tanah:</label>
                                            <select x-model="formData.spekBaru.gedung_status_tanah" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Tanah Pemda">Tanah Milik Pemda</option>
                                                <option value="Tanah Negara">Tanah Negara</option>
                                                <option value="Hak Pengelolaan">Hak Pengelolaan (HPL)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Dokumen Bangunan (IMB/PBG):</label>
                                            <input type="text" x-model="formData.spekBaru.gedung_dokumen_nomor" placeholder="Contoh: 640/IMB/DPMPTSP/2026" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Lokasi Alamat Bangunan:</label>
                                            <input type="text" x-model="formData.spekBaru.gedung_alamat" placeholder="Sayap Barat RSUD Dr. H. Koesnandi" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- SUB-TIPE 4: TANAH (KIB A / 1.5.2.xx.001) -->
                            <template x-if="getKemitraanTargetSubGroup() === 'tanah'">
                                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Tanah (M2):</label>
                                            <input type="number" step="0.01" min="0" x-model="formData.spekBaru.tanah_luas_m2" placeholder="Contoh: 1500" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Hak Tanah:</label>
                                            <select x-model="formData.spekBaru.tanah_hak" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Hak Pakai">Hak Pakai</option>
                                                <option value="Hak Milik">Hak Milik</option>
                                                <option value="Hak Pengelolaan">Hak Pengelolaan (HPL)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Sertifikat:</label>
                                            <input type="text" x-model="formData.spekBaru.tanah_sertifikat_no" placeholder="No. 12.04.05..." class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Penggunaan Tanah:</label>
                                            <input type="text" x-model="formData.spekBaru.tanah_penggunaan" placeholder="Area Parkir / Lahan Kemitraan" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Alamat / Lokasi:</label>
                                            <input type="text" x-model="formData.spekBaru.tanah_alamat" placeholder="Jl. Kapten Piere Tendean No. 1 Bondowoso" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- SUB-TIPE 5: JALAN, JARINGAN & IRIGASI (KIB D / 1.5.2.xx.004) -->
                            <template x-if="getKemitraanTargetSubGroup() === 'jaringan'">
                                <div class="space-y-4">
                                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-inner">
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                <span>🏗️</span>
                                                <span>KONDISI &amp; SPESIFIKASI JARINGAN KEMITRAAN:</span>
                                            </span>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">KIB D</span>
                                        </div>

                                        <div>
                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Jaringan / Ruas (Spesifik / Custom)</label>
                                            <input type="text" x-model="formData.spekBaru.jaringan_nama_barang"
                                                   placeholder="Contoh: Jaringan Pipa Gas Oksigen Sentral Medis / Ruas Jalan Paviliun"
                                                   class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                                        </div>

                                        <div class="space-y-3">
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Panjang (M)</label>
                                                    <input type="number" min="0" step="any" x-model.number="formData.spekBaru.jaringan_panjang_m"
                                                           placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2 py-2 text-xs text-white font-mono font-bold focus:outline-none transition-all shadow-inner">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Lebar (M)</label>
                                                    <input type="number" min="0" step="any" x-model.number="formData.spekBaru.jaringan_lebar_m"
                                                           placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2 py-2 text-xs text-white font-mono font-bold focus:outline-none transition-all shadow-inner">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas (M2/Lt)</label>
                                                    <input type="number" min="0" step="any" x-model.number="formData.spekBaru.jaringan_luas_m2"
                                                           placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2 py-2 text-xs text-white font-mono font-bold focus:outline-none transition-all shadow-inner">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Fisik</label>
                                                    <select x-model="formData.spekBaru.jaringan_kondisi"
                                                            class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-2 py-2 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                                                        <option value="Baik">🟢 Baik (B)</option>
                                                        <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                                        <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Bertingkat</label>
                                                    <select x-model="formData.spekBaru.jaringan_konstruksi_bertingkat"
                                                            class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                                                        <option value="Tidak">Tidak Bertingkat</option>
                                                        <option value="Bertingkat">Bertingkat</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Beton</label>
                                                    <select x-model="formData.spekBaru.jaringan_konstruksi_beton"
                                                            class="w-full bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all shadow-inner">
                                                        <option value="Beton">Beton</option>
                                                        <option value="Tidak">Bukan Beton</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: ASET LAIN - Aset Lain-Lain (1.5.4) -->
                <template x-if="formData.tujuan_kib === 'ASET LAIN' || formData.tujuan_kib === 'ASET LAIN-LAIN'">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Barang:</label>
                            <select x-model="formData.spekBaru.aset_lain_kondisi"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <option value="Rusak Berat (Menunggu Penghapusan)">Rusak Berat (Menunggu Usulan Penghapusan)</option>
                                <option value="Tidak Dapat Digunakan Lagi">Tidak Dapat Digunakan Lagi / Usang</option>
                                <option value="Hilang / Tidak Ditemukan">Hilang / Tidak Ditemukan</option>
                                <option value="Sengketa / Status Hukum">Sengketa / Status Hukum</option>
                            </select>
                        </div>
                    </div>
                </template>
            </div>
