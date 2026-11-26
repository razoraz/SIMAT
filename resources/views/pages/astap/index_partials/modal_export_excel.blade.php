<!-- ========================================================================= -->
<!-- MODAL PILIH SUMBER PEROLEHAN, TAHUN & TRIWULAN UNTUK EKSPOR EXCEL         -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showExportModal" x-cloak
         class="fixed inset-0 z-[99999] w-screen h-screen flex items-center justify-center p-3 sm:p-4 bg-slate-950/85 backdrop-blur-md overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.away="showExportModal = false"
             class="bg-slate-900 border border-slate-700/80 rounded-2xl sm:rounded-3xl p-4 sm:p-5 max-w-xl w-full shadow-2xl flex flex-col max-h-[88vh] my-auto relative"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between shrink-0 pb-3 border-b border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <div class="p-2.5 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-lg shadow-inner">
                        📊
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white leading-tight">Ekspor Laporan SIMAT-RK</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pilih Sumber Perolehan, Tahun &amp; Triwulan untuk format Excel resmi.</p>
                    </div>
                </div>
                <button type="button" @click="showExportModal = false" class="text-slate-400 hover:text-white p-1 rounded-xl hover:bg-slate-800 text-xl font-bold leading-none transition-all">&times;</button>
            </div>

            <!-- Form Filter Periode Ekspor (Scrollable) -->
            <div class="space-y-3.5 overflow-y-auto pr-1.5 -mr-1 custom-scrollbar py-3 flex-1 min-h-0">
                
                <!-- 1. FORMAT LAPORAN EXCEL (TARUH PALING ATAS SENDIRI) -->
                <div>
                    <label for="export_format_type_select" class="block text-slate-300 font-bold text-xs mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span>📑 FORMAT LAPORAN EXCEL</span>
                            <span class="text-rose-400">*</span>
                        </span>
                        <span class="text-[10px] text-slate-400">Pilih Jenis Format Dokumen</span>
                    </label>
                    <select id="export_format_type_select"
                            x-model="exportFormatType"
                            @change="if (exportFormatType !== 'sipenerbang') exportSumberDana = 'belanja_modal'"
                            :class="{
                                'border-emerald-500/50 text-emerald-200 focus:border-emerald-400': exportFormatType === 'sipenerbang',
                                'border-purple-500/50 text-purple-200 focus:border-purple-400': exportFormatType === 'rekap_triwulan',
                                'border-sky-500/50 text-sky-200 focus:border-sky-400': exportFormatType === 'lampiran_aset_tetap',
                                'border-amber-500/50 text-amber-200 focus:border-amber-400': exportFormatType === 'lampiran_extracom',
                                'border-indigo-500/50 text-indigo-200 focus:border-indigo-400': exportFormatType === 'lampiran_atb'
                            }"
                            class="w-full bg-slate-950 border rounded-xl px-3 py-2 text-xs font-bold focus:outline-none cursor-pointer transition-colors">
                        <option value="sipenerbang" class="bg-slate-950 text-emerald-200">📘 SIPENERBANG — Buku Induk Aset Tetap (9 Sheet / Per KIB)</option>
                        <option value="rekap_triwulan" class="bg-slate-950 text-purple-200">📑 Rekap Triwulan — Paket 4 Sheet: Daftar AT, Pengurangan, Reklas &amp; RMB</option>
                        <option value="lampiran_aset_tetap" class="bg-slate-950 text-sky-200"
                                x-text="'📋 Format Lampiran Aset Tetap_' + (exportTriwulan === 'all' ? 'Tahunan' : exportTriwulan) + '_RSDK_' + (exportYear === 'all' ? new Date().getFullYear() : exportYear)"></option>
                        <option value="lampiran_extracom" class="bg-slate-950 text-amber-200"
                                x-text="'🏷️ Lampiran Extracom RSUD-' + (exportYear === 'all' ? new Date().getFullYear() : exportYear) + ' _RSDK_' + (exportYear === 'all' ? new Date().getFullYear() : exportYear)"></option>
                        <option value="lampiran_atb" class="bg-slate-950 text-indigo-200"
                                x-text="'💾 Lampiran ATB_' + (exportTriwulan === 'all' ? 'Tahunan' : exportTriwulan.replace(/\s+/g, '')) + '-RSDK-' + (exportYear === 'all' ? new Date().getFullYear() : exportYear)"></option>
                    </select>
                    <p x-show="exportFormatType === 'lampiran_extracom'"
                       class="text-[10.5px] text-amber-300/90 mt-1.5 leading-snug">
                        Format 2 Sheet siap ekspor (Penambahan Reklas ke Extracom &amp; Pengurangan Extracom).
                    </p>
                    <p x-show="['lampiran_aset_tetap', 'lampiran_atb'].includes(exportFormatType)"
                       class="text-[10.5px] text-slate-400 mt-1.5 leading-snug">
                        Opsi format telah ditambahkan (mencakup semua sumber perolehan; struktur isi sheet Excel menyusul).
                    </p>
                </div>

                <!-- 2. PILIHAN SUMBER PEROLEHAN / KELUARAN (Tampil ketika format SIPENERBANG dipilih) -->
                <div x-show="exportFormatType === 'sipenerbang'">
                    <label class="block text-slate-300 font-bold text-xs mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <span>🏷️ SUMBER PEROLEHAN ASET (SIPENERBANG)</span>
                            <span class="text-rose-400">*</span>
                        </span>
                        <span class="text-[10px] text-slate-400">Pilih Jenis Pengadaan</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <!-- Tab 1: Belanja Modal -->
                        <button type="button" @click="exportSumberDana = 'belanja_modal'"
                            class="p-2 rounded-xl border text-left transition-all flex flex-col justify-between cursor-pointer"
                            :class="exportSumberDana === 'belanja_modal' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/60 shadow-md ring-1 ring-emerald-500/30' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950 hover:text-slate-200'">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs font-bold flex items-center gap-1">🛍️ Modal</span>
                                <span x-show="exportSumberDana === 'belanja_modal'" class="text-emerald-400 font-black text-xs">✓</span>
                            </div>
                            <span class="text-[9.5px] text-slate-400 mt-1 leading-tight">Pengadaan APBD</span>
                        </button>

                        <!-- Tab 2: Kemitraan (Akun 1.5.2) -->
                        <button type="button" @click="exportSumberDana = 'kemitraan'"
                            class="p-2 rounded-xl border text-left transition-all flex flex-col justify-between cursor-pointer"
                            :class="exportSumberDana === 'kemitraan' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/60 shadow-md ring-1 ring-cyan-500/30' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950 hover:text-slate-200'">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs font-bold flex items-center gap-1">🤝 Kemitraan</span>
                                <span x-show="exportSumberDana === 'kemitraan'" class="text-cyan-400 font-black text-xs">✓</span>
                            </div>
                            <span class="text-[9.5px] text-cyan-400/80 mt-1 leading-tight">Akun 1.5.2 PKS</span>
                        </button>

                        <!-- Tab 3: Hibah -->
                        <button type="button" @click="exportSumberDana = 'hibah'"
                            class="p-2 rounded-xl border text-left transition-all flex flex-col justify-between cursor-pointer"
                            :class="exportSumberDana === 'hibah' ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 shadow-md ring-1 ring-amber-500/30' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950 hover:text-slate-200'">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs font-bold flex items-center gap-1">🎁 Hibah</span>
                                <span x-show="exportSumberDana === 'hibah'" class="text-amber-400 font-black text-xs">✓</span>
                            </div>
                            <span class="text-[9.5px] text-slate-400 mt-1 leading-tight">BAST Masuk</span>
                        </button>

                        <!-- Tab 4: Mutasi Masuk / Antar-OPD -->
                        <button type="button" @click="exportSumberDana = 'mutasi_eksternal'"
                            class="p-2 rounded-xl border text-left transition-all flex flex-col justify-between cursor-pointer"
                            :class="(exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan') ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/60 shadow-md ring-1 ring-indigo-500/30' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950 hover:text-slate-200'">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs font-bold flex items-center gap-1">🔄 Mutasi Masuk</span>
                                <span x-show="exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan'" class="text-indigo-400 font-black text-xs">✓</span>
                            </div>
                            <span class="text-[9.5px] text-indigo-300/80 mt-1 leading-tight">Antar-OPD</span>
                        </button>

                        <!-- Tab 5: Belanja Barang (Operasional) -->
                        <button type="button" @click="exportSumberDana = 'belanja_barang'"
                            class="p-2 rounded-xl border text-left transition-all flex flex-col justify-between cursor-pointer"
                            :class="exportSumberDana === 'belanja_barang' ? 'bg-teal-500/20 text-teal-300 border-teal-500/60 shadow-md ring-1 ring-teal-500/30' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950 hover:text-slate-200'">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs font-bold flex items-center gap-1">📦 Barang</span>
                                <span x-show="exportSumberDana === 'belanja_barang'" class="text-teal-400 font-black text-xs">✓</span>
                            </div>
                            <span class="text-[9.5px] text-teal-300/80 mt-1 leading-tight">Operasional</span>
                        </button>
                    </div>
                </div>

                <!-- 3. KHUSUS KEMITRAAN: PILIHAN SKEMA KERJA SAMA (Akun 1.5.2 Berdasarkan PMDN 108) -->
                <div x-show="exportFormatType === 'sipenerbang' && exportSumberDana === 'kemitraan'" class="space-y-2 p-3 rounded-2xl bg-cyan-950/20 border border-cyan-500/30">
                    <label class="block text-cyan-300 font-bold text-xs flex items-center justify-between">
                        <span>🤝 SKEMA KERJA SAMA (AKUN 1.5.2.01.01)</span>
                        <span class="text-[10px] text-cyan-400/80 font-mono">Permendagri 108</span>
                    </label>
                    <select x-model="exportKemitraanSkema"
                            class="w-full bg-slate-950 border border-cyan-500/40 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-cyan-400">
                        <option value="all">🌐 Semua Skema Kemitraan (Akun 1.5.2 Lengkap)</option>
                        <option value="sewa">🏢 Sewa (Akun 1.5.2.01.01.01)</option>
                        <option value="ksp">🤝 Kerja Sama Pemanfaatan / KSP (Akun 1.5.2.01.01.02)</option>
                        <option value="bgs_bsg">🏗️ Bangun Guna Serah / BSG (Akun 1.5.2.01.01.03)</option>
                        <option value="kso">⚡ Kerja Sama Penyediaan Infrastruktur / KSO (Akun 1.5.2.01.01.04)</option>
                    </select>
                    <p class="text-[10.5px] text-cyan-300/80 leading-relaxed">
                        Data akan diekspor dalam format resmi berstandar Permendagri 108 lengkap dengan rincian PKS dan masa konsesi.
                    </p>
                </div>

                <!-- 4A. KHUSUS HIBAH: INFO & AKSI HIBAH -->
                <div x-show="exportFormatType === 'sipenerbang' && exportSumberDana === 'hibah'" class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                    <div class="flex items-center gap-2 text-amber-300 font-bold text-xs">
                        <span>🎁 Laporan Aset Perolehan Hibah</span>
                    </div>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        Dokumen ekspor Excel perolehan hibah menggunakan format BAST Serah Terima Aset Hibah dari Kemenkes, Dinkes, atau Mitra Donatur.
                    </p>
                    <a href="{{ route('master.hibah') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-[11px] font-bold transition-all">
                        <span>Buka Modul Kelola Hibah ↗</span>
                    </a>
                </div>

                <!-- 4B. KHUSUS MUTASI MASUK: INFO MUTASI EKSTERNAL -->
                <div x-show="exportFormatType === 'sipenerbang' && (exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan')" class="p-3 rounded-2xl bg-indigo-950/30 border border-indigo-500/40 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="text-lg">🔄</span>
                        <div>
                            <div class="text-xs font-bold text-indigo-300">Laporan Aset Mutasi Masuk (Antar-OPD)</div>
                            <div class="text-[10.5px] text-indigo-200/70">Format Buku Inventaris 7 Sheet BPKAD / SIMAT-RK (PMDN 108)</div>
                        </div>
                    </div>
                    <a href="{{ route('mutasi.eksternal') }}" target="_blank"
                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 text-[10px] font-semibold border border-indigo-500/30 transition-all">
                        <span>Buka Modul ↗</span>
                    </a>
                </div>

                <!-- 4C. KHUSUS BELANJA BARANG: INFO BELANJA BARANG & JASA (OPERASIONAL) -->
                <div x-show="exportFormatType === 'sipenerbang' && exportSumberDana === 'belanja_barang'" class="p-3.5 rounded-2xl bg-teal-950/20 border border-teal-500/30 space-y-2">
                    <div class="flex items-center gap-2 text-teal-300 font-bold text-xs">
                        <span>📦 Laporan Aset Belanja Barang &amp; Jasa (Operasional / Akun 5.1.02)</span>
                    </div>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        Dokumen ekspor Excel perolehan belanja barang operasional / perbekalan ruangan berstandar Permendagri 108 (9 Sheet SIPENERBANG: Rekap, KIB A–F, ATB &amp; Extracom).
                    </p>
                    <a href="{{ route('master.belanja_barang') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 text-[11px] font-bold transition-all">
                        <span>Buka Modul Kelola Belanja Barang ↗</span>
                    </a>
                </div>

                <!-- 3. PILIHAN TAHUN ANGGARAN (Berlaku untuk Modal & Kemitraan) -->
                <div>
                    <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📅 TAHUN ANGGARAN / PEROLEHAN</span>
                        <span class="text-[10px] text-slate-400">Periode Pelaporan</span>
                    </label>
                    <select x-model="exportYear"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-emerald-500">
                        <option value="all">Semua Tahun (Seluruh Riwayat Aset 1980 - Sekarang)</option>
                        <template x-for="yr in availableYears" :key="yr">
                            <option :value="yr" x-text="'Tahun Anggaran ' + yr"></option>
                        </template>
                    </select>
                </div>

                <!-- 4. PILIHAN TRIWULAN (TW I - IV / SEMUA) -->
                <div>
                    <label class="block text-cyan-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span x-text="exportSumberDana === 'kemitraan' ? '📊 TRIWULAN PKS / KONSESI' : ((exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan') ? '📊 TRIWULAN MUTASI MASUK (BAMB)' : (exportSumberDana === 'belanja_barang' ? '📊 TRIWULAN FAKTUR BELANJA' : '📊 TRIWULAN PENGADAAN (BAST)'))"></span>
                        <span class="text-[10px] text-cyan-400/80 font-mono">TW I - IV</span>
                    </label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <button type="button" @click="exportTriwulan = 'all'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportTriwulan === 'all' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>📑 Semua (Tahunan)</span>
                            <span x-show="exportTriwulan === 'all'" class="text-cyan-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportTriwulan = 'TW I'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportTriwulan === 'TW I' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>🌱 Triwulan I (TW I)</span>
                            <span x-show="exportTriwulan === 'TW I'" class="text-emerald-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportTriwulan = 'TW II'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportTriwulan === 'TW II' ? 'bg-blue-500/20 text-blue-300 border-blue-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>☀️ Triwulan II (TW II)</span>
                            <span x-show="exportTriwulan === 'TW II'" class="text-blue-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportTriwulan = 'TW III'"
                            class="p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportTriwulan === 'TW III' ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>🍂 Triwulan III (TW III)</span>
                            <span x-show="exportTriwulan === 'TW III'" class="text-amber-400 font-black">✓</span>
                        </button>
                        <button type="button" @click="exportTriwulan = 'TW IV'"
                            class="col-span-2 p-1.5 sm:p-2 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between cursor-pointer"
                            :class="exportTriwulan === 'TW IV' ? 'bg-purple-500/20 text-purple-300 border-purple-500/60 shadow-md' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                            <span>❄️ Triwulan IV (TW IV - Akhir Tahun)</span>
                            <span x-show="exportTriwulan === 'TW IV'" class="text-purple-400 font-black">✓</span>
                        </button>
                    </div>
                </div>

                <!-- 5A. PILIHAN KATEGORI KIB KHUSUS FORMAT SIPENERBANG BELANJA MODAL -->
                <div x-show="exportSumberDana === 'belanja_modal' && exportFormatType === 'sipenerbang'">
                    <label class="block text-slate-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📦 KLASIFIKASI KIB BELANJA MODAL</span>
                        <span class="text-[10px] text-slate-400">Sheet Excel</span>
                    </label>
                    <select x-model="exportCategory"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500">
                        <option value="all">Semua KIB (Buku Aset Lengkap 9 Sheet)</option>
                        <option value="REKAP">Lembar Rekapitulasi Realisasi (Sheet 1)</option>
                        <option value="KIB A">KIB A - Tanah</option>
                        <option value="KIB B">KIB B - Peralatan &amp; Mesin</option>
                        <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                        <option value="KIB D">KIB D - Jalan &amp; Jaringan</option>
                        <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                        <option value="KIB F">KIB F - Konstruksi KDP</option>
                        <option value="ATB">ATB - Aset Tidak Berwujud</option>
                        <option value="EXTRACOM">Extracom</option>
                        <option value="ASET LAIN">Aset Lain-Lain (1.5.4)</option>
                    </select>
                </div>

                <!-- 5B. PILIHAN SHEET KHUSUS REKAP TRIWULAN BELANJA MODAL -->
                <div x-show="exportSumberDana === 'belanja_modal' && exportFormatType === 'rekap_triwulan'">
                    <label class="block text-purple-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📑 PILIHAN SHEET REKAPITULASI</span>
                        <span class="text-[10px] text-purple-400/80 font-mono">Sheet Excel</span>
                    </label>
                    <select x-model="exportRekapSheet"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-purple-500">
                        <option value="all">Semua Sheet (Paket Lengkap Rekapitulasi)</option>
                        <option value="sheet1" x-text="'1. Daftar AT ' + (exportTriwulan === 'all' ? 'Tahunan' : exportTriwulan)"></option>
                        <option value="sheet2">2. Daftar Pengurangan AT RSDK</option>
                        <option value="sheet3">3. Reklas RSDK</option>
                        <option value="sheet4">4. RMB (excel) RSDK</option>
                        <option value="sheet5_hibah">5. Hibah RSDK</option>
                    </select>
                </div>

                <!-- 5C. PILIHAN KLASIFIKASI SHEET KHUSUS KEMITRAAN (Akun 1.5.2) -->
                <div x-show="exportFormatType === 'sipenerbang' && exportSumberDana === 'kemitraan'">
                    <label class="block text-cyan-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📦 KLASIFIKASI KIB KEMITRAAN (AKUN 1.5.2)</span>
                        <span class="text-[10px] text-cyan-400 font-mono">Sheet Excel</span>
                    </label>
                    <select x-model="exportKemitraanCategory"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-cyan-400">
                        <option value="all">Semua KIB (Buku Aset Kemitraan Lengkap 7 Sheet: Rekap, KIB A s/d E &amp; Extracom)</option>
                        <option value="REKAP">Lembar Rekapitulasi Realisasi Kemitraan (Sheet 1)</option>
                        <option value="KIB A">KIB A - Tanah (Akun 1.5.2.01.01.xx.001)</option>
                        <option value="KIB B">KIB B - Peralatan &amp; Mesin / KSO Alkes (Akun 1.5.2.01.01.xx.002)</option>
                        <option value="KIB C">KIB C - Gedung &amp; Bangunan / BGS (Akun 1.5.2.01.01.xx.003)</option>
                        <option value="KIB D">KIB D - Jalan, Irigasi &amp; Jaringan (Akun 1.5.2.01.01.xx.004)</option>
                        <option value="KIB E">KIB E - Aset Tetap Lainnya (Akun 1.5.2.01.01.xx.005)</option>
                        <option value="EXTRACOM">Extracom - Barang Ekstrakomtabel (&lt; Rp 300.000)</option>
                    </select>
                </div>

                <!-- 5D. PILIHAN KLASIFIKASI SHEET KHUSUS MUTASI MASUK (PMDN 108) -->
                <div x-show="exportFormatType === 'sipenerbang' && (exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan')">
                    <label class="block text-indigo-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📦 KLASIFIKASI LEMBAR SHEET MUTASI (PMDN 108)</span>
                        <span class="text-[10px] text-indigo-400 font-mono">Sheet Excel</span>
                    </label>
                    <select x-model="exportMutasiCategory"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-indigo-400">
                        <option value="all">Semua KIB (Buku Aset Mutasi Masuk Lengkap 7 Sheet: Rekap, KIB A s/d E &amp; Extracom)</option>
                        <option value="REKAP">Lembar Rekapitulasi Realisasi Mutasi Masuk (Sheet 1)</option>
                        <option value="KIB A">KIB A - Tanah</option>
                        <option value="KIB B">KIB B - Peralatan &amp; Mesin / Alkes Medis</option>
                        <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                        <option value="KIB D">KIB D - Jalan, Irigasi &amp; Jaringan</option>
                        <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                        <option value="EXTRACOM">Extracom - Barang Ekstrakomtabel (&lt; Rp 300.000)</option>
                    </select>
                </div>

                <!-- 5E. PILIHAN KLASIFIKASI SHEET KHUSUS BELANJA BARANG (AKUN 5.1.02) -->
                <div x-show="exportFormatType === 'sipenerbang' && exportSumberDana === 'belanja_barang'">
                    <label class="block text-teal-300 font-bold text-xs mb-1 flex items-center justify-between">
                        <span>📦 FORMAT LEMBAR SHEET BELANJA BARANG (AKUN 5.1.02)</span>
                        <span class="text-[10px] text-teal-400 font-mono">Sheet Excel</span>
                    </label>
                    <select x-model="exportBarangCategory"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:border-teal-400">
                        <option value="all">Semua Lembar (Paket Lengkap: Rekap Faktur &amp; Rincian Barang)</option>
                        <option value="REKAP">Lembar 1 - Rekapitulasi Pembelian Faktur (Per Toko Rekanan)</option>
                        <option value="RINCIAN">Lembar 2 - Rincian Lengkap Seluruh Barang Belanja</option>
                        <option value="KIB B">Lembar 3 - KIB B (Peralatan &amp; Mesin / Perbekalan Operasional)</option>
                        <option value="EXTRACOM">Lembar 4 - Extracom (Barang Ekstrakomtabel &le; Rp 300.000)</option>
                        <option value="KIB E">Lembar 5 - KIB E (Aset Tetap Lainnya)</option>
                    </select>
                </div>

                <!-- Info Ringkasan Data Siap Ekspor -->
                <div class="p-2.5 rounded-xl border flex items-center justify-between"
                     :class="exportSumberDana === 'kemitraan' ? 'bg-slate-950/80 border-cyan-500/30' : (exportSumberDana === 'hibah' ? 'bg-slate-950/80 border-amber-500/30' : ((exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan') ? 'bg-slate-950/80 border-indigo-500/30' : (exportSumberDana === 'belanja_barang' ? 'bg-slate-950/80 border-teal-500/30' : 'bg-slate-950/80 border-emerald-500/30')))">
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="text-base" x-text="exportSumberDana === 'kemitraan' ? '🤝' : (exportSumberDana === 'hibah' ? '🎁' : ((exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan') ? '🔄' : (exportSumberDana === 'belanja_barang' ? '📦' : '📋')))"></span>
                        <span class="text-slate-300 font-medium">Aset siap diekspor:</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-lg font-mono font-bold text-xs border"
                          :class="exportSumberDana === 'kemitraan' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : (exportSumberDana === 'hibah' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : ((exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan') ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40' : (exportSumberDana === 'belanja_barang' ? 'bg-teal-500/20 text-teal-300 border-teal-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40')))"
                          x-text="exportFilteredCount + ' Item Data'"></span>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" @click="showExportModal = false"
                    class="px-4 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="submitExport()"
                    :disabled="isSubmittingExport"
                    class="px-5 py-2 rounded-xl font-black text-xs shadow-lg transition-all flex items-center space-x-2 cursor-pointer active:scale-95 disabled:opacity-50"
                    :class="exportSumberDana === 'kemitraan' ? 'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/20' : (exportSumberDana === 'hibah' ? 'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20' : ((exportSumberDana === 'mutasi_eksternal' || exportSumberDana === 'pelimpahan') ? 'bg-indigo-500 hover:bg-indigo-400 text-slate-950 shadow-indigo-500/20' : (exportSumberDana === 'belanja_barang' ? 'bg-teal-500 hover:bg-teal-400 text-slate-950 shadow-teal-500/20' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/20')))">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-text="isSubmittingExport ? 'Mengekspor...' : (exportSumberDana === 'hibah' ? 'Buka Modul Hibah' : 'Unduh File Excel')"></span>
                </button>
            </div>
        </div>
    </div>
</template>
