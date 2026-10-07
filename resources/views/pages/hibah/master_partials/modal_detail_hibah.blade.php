<!-- ========================================================================= -->
<!-- MODAL DETAIL ASET HIBAH & RINCIAN REGISTER NIBAR (BESPOKE DARK LUXURY)    -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showModalDetail" x-cloak @click.self="showModalDetail = false"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 9000;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    
    <div class="border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[92vh] space-y-5 my-auto"
         style="background-color: #0f172a;">
        
        <!-- 1. Modal Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="space-y-1.5 min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Kategori KIB Badge -->
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                        :class="{
                            'bg-amber-500/20 text-amber-300 border-amber-500/30': getEffectiveKibCategory(selectedDetail) === 'KIB A',
                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     getEffectiveKibCategory(selectedDetail) === 'KIB B',
                            'bg-purple-500/20 text-purple-300 border-purple-500/30': getEffectiveKibCategory(selectedDetail) === 'KIB C',
                            'bg-teal-500/20 text-teal-300 border-teal-500/30':     getEffectiveKibCategory(selectedDetail) === 'KIB D',
                            'bg-orange-500/20 text-orange-300 border-orange-500/30': getEffectiveKibCategory(selectedDetail) === 'KIB E',
                            'bg-blue-500/20 text-blue-300 border-blue-500/30':     getEffectiveKibCategory(selectedDetail) === 'KIB F' || getEffectiveKibCategory(selectedDetail) === 'ATB'
                        }"
                        x-text="getEffectiveKibCategory(selectedDetail)"></span>

                    <!-- Badge Tipe Hibah -->
                    <template x-if="selectedDetail?.tipe_hibah === 'masuk'">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0 bg-amber-400/20 text-amber-300 border-amber-400/40">
                            🎁 HIBAH MASUK (BERTAMBAH)
                        </span>
                    </template>
                    <template x-if="selectedDetail?.tipe_hibah === 'keluar'">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0 bg-rose-500/20 text-rose-300 border-rose-500/40">
                            📤 HIBAH KELUAR (PENGURANGAN AT)
                        </span>
                    </template>

                    <!-- Kode 108 -->
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full"
                        x-text="'Kode: ' + (selectedDetail?.astap?.kode_108 || selectedDetail?.kode_108 || '-')"></span>

                    <!-- Tanggal BAST -->
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                        <span class="text-slate-400">📅 Tanggal BAST:</span>
                        <span class="text-amber-300 font-bold" x-text="formatTanggalIndo(selectedDetail?.tanggal_bast)"></span>
                    </span>

                    <!-- Status Kondisi Keseluruhan -->
                    <template x-if="selectedDetail">
                        <span class="px-2.5 py-0.5 rounded-lg border text-[11px] font-semibold flex items-center space-x-1.5 shrink-0"
                              :class="getKondisiStats(selectedDetail).badge_class">
                            <span class="w-1.5 h-1.5 rounded-full" :class="getKondisiStats(selectedDetail).dot_class"></span>
                            <span x-text="'Kondisi: ' + getKondisiStats(selectedDetail).text"></span>
                        </span>
                    </template>
                </div>

                <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words" 
                    x-text="selectedDetail ? (selectedDetail.astap ? selectedDetail.astap.nama_barang : (selectedDetail.nama_barang || 'Aset Hibah')) : ''"></h3>
            </div>

            <!-- Tombol Close -->
            <button type="button" @click="showModalDetail = false" 
                class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <template x-if="selectedDetail">
            <div class="space-y-5 text-xs text-slate-300">

                <!-- 2. Top 4 Metric KPI Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏷️ Jenis PMDN 108</span>
                        <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate" 
                              :title="selectedDetail.astap?.jenis_astap?.nama_jenis || selectedDetail.astap?.category || 'Aset Hibah'" 
                              x-text="selectedDetail.astap?.jenis_astap?.nama_jenis || selectedDetail.astap?.category || 'ASET BMD'"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Tahun &amp; Triwulan</span>
                        <span class="text-amber-300 font-extrabold font-mono text-xs sm:text-sm block" 
                              x-text="'TA ' + selectedDetail.tahun + ' • ' + selectedDetail.triwulan"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📏 Volume / Satuan</span>
                        <span class="text-teal-300 font-extrabold font-mono text-xs sm:text-sm block truncate" 
                              x-text="(selectedDetail.jumlah_volume || 1) + ' ' + (selectedDetail.satuan || 'Unit')"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Total Nilai Aset</span>
                        <span class="font-extrabold font-mono text-xs sm:text-sm block truncate"
                              :class="selectedDetail.tipe_hibah === 'masuk' ? 'text-amber-400' : 'text-rose-400'"
                              x-text="'Rp ' + formatRupiah(selectedDetail.nilai_aset)"></span>
                    </div>
                </div>

                <!-- 3. Tabbed Navigation inside Modal -->
                <div class="flex items-center space-x-2 border-b border-slate-800 pb-2">
                    <button type="button" @click="detailActiveTab = 'bast'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer"
                        :class="detailActiveTab === 'bast' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                        <span>📜 Dokumen BAST &amp; Legalitas</span>
                    </button>
                    <button type="button" @click="detailActiveTab = 'spek'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer"
                        :class="detailActiveTab === 'spek' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                        <span>⚙️ Klasifikasi &amp; Spesifikasi KIB</span>
                    </button>
                    <button type="button" @click="detailActiveTab = 'registers'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer"
                        :class="detailActiveTab === 'registers' ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'">
                        <span>🏷️ Unit Register NIBAR</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                            :class="detailActiveTab === 'registers' ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-300'"
                            x-text="getRegistersList(selectedDetail).length"></span>
                    </button>
                </div>

                <!-- TAB 1: BAST & LEGALITAS -->
                <div x-show="detailActiveTab === 'bast'" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Pihak Terlibat -->
                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                            <span class="text-amber-400 text-[10px] font-bold uppercase tracking-wider block">🏛️ Pihak Terlibat Serah Terima</span>
                            <div>
                                <span class="text-slate-400 text-[10px] block" x-text="selectedDetail.tipe_hibah === 'masuk' ? 'Instansi / Lembaga Pemberi Hibah:' : 'Penerima Hibah:'"></span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetail.pihak_hibah || '-'"></div>
                            </div>
                            <template x-if="selectedDetail.astap?.spesifikasi_json?.hibah_pimpinan || selectedDetail.astap?.spesifikasi_json?.pimpinan_pemberi">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Pimpinan / Pejabat Pemberi:</span>
                                    <div class="font-bold text-slate-200" x-text="selectedDetail.astap?.spesifikasi_json?.hibah_pimpinan || selectedDetail.astap?.spesifikasi_json?.pimpinan_pemberi"></div>
                                </div>
                            </template>
                            <template x-if="selectedDetail.astap?.spesifikasi_json?.hibah_alamat_pemberi || selectedDetail.astap?.spesifikasi_json?.alamat_pemberi">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Alamat Domisili Pemberi:</span>
                                    <div class="text-slate-300 text-xs" x-text="selectedDetail.astap?.spesifikasi_json?.hibah_alamat_pemberi || selectedDetail.astap?.spesifikasi_json?.alamat_pemberi"></div>
                                </div>
                            </template>
                        </div>

                        <!-- Dokumen BAST Resmi -->
                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                            <span class="text-cyan-400 text-[10px] font-bold uppercase tracking-wider block">📜 Legalitas Berita Acara (BAST)</span>
                            <div>
                                <span class="text-slate-400 text-[10px] block">Nomor BAST:</span>
                                <div class="font-mono font-extrabold text-white text-sm" x-text="selectedDetail.nomor_bast || '-'"></div>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10px] block">Tanggal Penandatanganan BAST:</span>
                                <div class="font-medium text-slate-200" x-text="formatTanggalIndo(selectedDetail.tanggal_bast)"></div>
                            </div>
                            
                            <!-- Berkas Upload Link / Status -->
                            <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                                <span class="text-slate-400 text-[10px]">Berkas Dokumen BAST:</span>
                                <template x-if="selectedDetail.astap?.spesifikasi_json?.dokumen_path">
                                    <a :href="'/storage/' + selectedDetail.astap.spesifikasi_json.dokumen_path" target="_blank"
                                       class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 font-bold text-[11px] transition-all">
                                        <span>📄</span><span>Unduh Berkas BAST</span>
                                    </a>
                                </template>
                                <template x-if="!selectedDetail.astap?.spesifikasi_json?.dokumen_path">
                                    <span class="text-slate-500 italic text-[11px]">Berkas digital belum diunggah</span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- PPK & Alamat Penempatan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                            <span class="text-emerald-400 text-[10px] font-bold uppercase tracking-wider block">✍️ Pejabat Pembuat Komitmen (PPK)</span>
                            <div>
                                <div class="font-extrabold text-white" x-text="selectedDetail.astap?.ppk_nama || selectedDetail.astap?.spesifikasi_json?.ppk_nama || 'dr. YUS PRIYATNA, Sp.P'"></div>
                                <div class="font-mono text-[11px] text-slate-400" x-text="'NIP. ' + (selectedDetail.astap?.ppk_nip || selectedDetail.astap?.spesifikasi_json?.ppk_nip || '19760815 200501 1 009')"></div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                            <span class="text-purple-400 text-[10px] font-bold uppercase tracking-wider block">📍 Lokasi Penempatan Barang</span>
                            <div>
                                <div class="font-bold text-white" x-text="selectedDetail.astap?.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1'"></div>
                                <div class="text-[11px] text-slate-400" x-text="'Ruangan Induk: ' + (selectedDetail.astap?.unit?.nama || 'Gudang Aset / Seluruh Ruangan')"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <template x-if="selectedDetail.keterangan || selectedDetail.astap?.keterangan_tambahan">
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan / Keterangan:</span>
                            <p class="text-slate-200 text-xs italic" x-text="selectedDetail.keterangan || selectedDetail.astap?.keterangan_tambahan"></p>
                        </div>
                    </template>
                </div>

                <!-- TAB 2: KLASIFIKASI 108 & SPESIFIKASI KIB -->
                <div x-show="detailActiveTab === 'spek'" class="space-y-4">
                    <!-- Kodefikasi Permendagri 108 -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <span class="text-cyan-400 text-[10px] font-bold uppercase tracking-wider block">📑 Klasifikasi Kode Barang Permendagri 108</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 text-[10px] block">Kode Akun 108:</span>
                                <span class="font-mono font-bold text-cyan-300" x-text="selectedDetail.astap?.kode_108 || selectedDetail.kode_108 || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10px] block">Klasifikasi Akun:</span>
                                <span class="font-bold text-white" x-text="selectedDetail.astap?.jenis_astap?.nama_jenis || 'Aset Tetap (Akun 1.3)'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10px] block">Sub Rincian Objek:</span>
                                <span class="text-slate-200" x-text="selectedDetail.astap?.jenis_astap?.nama_sub_rincian_objek || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10px] block">Sub-Sub Rincian Objek:</span>
                                <span class="text-slate-200" x-text="selectedDetail.astap?.jenis_astap?.nama_sub_sub_rincian_objek || selectedDetail.astap?.nama_barang || '-'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Spesifikasi Teknis Fisik KIB -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <span class="text-amber-400 text-[10px] font-bold uppercase tracking-wider block">⚙️ Spesifikasi Fisik Barang</span>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                            <!-- KIB B Peralatan & Mesin -->
                            <template x-if="getSpecDetail(selectedDetail).merk">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Merk / Pabrik:</span>
                                    <strong class="text-white" x-text="getSpecDetail(selectedDetail).merk"></strong>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).type">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Tipe / Model:</span>
                                    <span class="text-slate-200 font-mono" x-text="getSpecDetail(selectedDetail).type"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).ukuran">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Ukuran / Dimensi:</span>
                                    <span class="text-slate-200" x-text="getSpecDetail(selectedDetail).ukuran"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).bahan">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Bahan / Material:</span>
                                    <span class="text-slate-200" x-text="getSpecDetail(selectedDetail).bahan"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_pabrik">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Nomor Pabrik:</span>
                                    <span class="text-slate-200 font-mono" x-text="getSpecDetail(selectedDetail).no_pabrik"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_rangka">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Nomor Rangka:</span>
                                    <span class="text-slate-200 font-mono" x-text="getSpecDetail(selectedDetail).no_rangka"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_mesin">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Nomor Mesin:</span>
                                    <span class="text-slate-200 font-mono" x-text="getSpecDetail(selectedDetail).no_mesin"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_polisi">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Nomor Polisi:</span>
                                    <span class="text-amber-300 font-mono font-bold" x-text="getSpecDetail(selectedDetail).no_polisi"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_bpkb">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Nomor BPKB:</span>
                                    <span class="text-slate-200 font-mono" x-text="getSpecDetail(selectedDetail).no_bpkb"></span>
                                </div>
                            </template>

                            <!-- KIB A Tanah -->
                            <template x-if="getSpecDetail(selectedDetail).tanah_luas_m2 || getSpecDetail(selectedDetail).luas_m2">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Luas Tanah:</span>
                                    <strong class="text-emerald-400 font-mono" x-text="(getSpecDetail(selectedDetail).tanah_luas_m2 || getSpecDetail(selectedDetail).luas_m2) + ' m²'"></strong>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).tanah_sertifikat_no || getSpecDetail(selectedDetail).sertifikat_no">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Sertifikat / Hak:</span>
                                    <span class="text-slate-200 font-mono" x-text="getSpecDetail(selectedDetail).tanah_sertifikat_no || getSpecDetail(selectedDetail).sertifikat_no"></span>
                                </div>
                            </template>

                            <!-- KIB C Gedung -->
                            <template x-if="getSpecDetail(selectedDetail).gedung_luas_m2">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Luas Lantai Gedung:</span>
                                    <strong class="text-purple-400 font-mono" x-text="getSpecDetail(selectedDetail).gedung_luas_m2 + ' m²'"></strong>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).gedung_bertingkat">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">Konstruksi Gedung:</span>
                                    <span class="text-slate-200" x-text="getSpecDetail(selectedDetail).gedung_bertingkat + ' • ' + (getSpecDetail(selectedDetail).gedung_beton || 'Beton')"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: UNIT INVENTARIS & NIBAR -->
                <div x-show="detailActiveTab === 'registers'" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white">Daftar Unit Register Aset (NIBAR)</span>
                        <span class="text-[10px] text-slate-400 font-mono" x-text="getRegistersList(selectedDetail).length + ' Unit Terdaftar'"></span>
                    </div>

                    <div class="rounded-2xl border border-slate-800 overflow-hidden bg-slate-950/60 max-h-[360px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950 text-slate-400 text-[10px] font-bold uppercase tracking-wider border-b border-slate-800 sticky top-0 z-10">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-10">No</th>
                                    <th class="px-4 py-2.5">No. Register / NIBAR</th>
                                    <th class="px-4 py-2.5">Ruangan Pemegang</th>
                                    <th class="px-3 py-2.5 text-center">Kondisi</th>
                                    <th class="px-3 py-2.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                <template x-for="(reg, rIdx) in getRegistersList(selectedDetail)" :key="reg.id || rIdx">
                                    <tr class="hover:bg-slate-900/60 transition-colors">
                                        <td class="px-3 py-2 text-center text-slate-500 font-mono" x-text="rIdx + 1"></td>
                                        <td class="px-4 py-2">
                                            <div class="font-mono font-bold text-cyan-300" x-text="reg.nibar || reg.no_register || ('REG-' + String(rIdx+1).padStart(4,'0'))"></div>
                                            <div class="text-[9.5px] text-slate-500 font-mono" x-text="'No Reg: ' + (reg.no_register || (rIdx + 1))"></div>
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="font-semibold text-slate-200" x-text="reg.ruang_pemegang || reg.unit?.nama || (selectedDetail.astap?.unit?.nama || 'Gudang Aset')"></div>
                                        </td>
                                        <td class="px-3 py-2 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold border"
                                                :class="{
                                                    'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': (reg.kondisi || 'Baik') === 'Baik' || reg.kondisi === 'B',
                                                    'bg-amber-500/15 text-amber-300 border-amber-500/30': reg.kondisi === 'Kurang Baik' || reg.kondisi === 'KB' || reg.kondisi === 'Rusak Ringan',
                                                    'bg-rose-500/15 text-rose-300 border-rose-500/30': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'RB' || reg.kondisi === 'Rusak'
                                                }"
                                                x-text="reg.kondisi || 'Baik'"></span>
                                        </td>
                                        <td class="px-3 py-2 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold"
                                                :class="selectedDetail.tipe_hibah === 'keluar' ? 'bg-rose-500/15 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30'"
                                                x-text="selectedDetail.tipe_hibah === 'keluar' ? 'Dihibahkan' : (reg.status || 'Tersedia')"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Audit info -->
                <div class="text-[10px] text-slate-500 flex items-center justify-between pt-2 border-t border-slate-800/80">
                    <span>Operator Input: <strong class="text-slate-400" x-text="selectedDetail.user ? selectedDetail.user.name : 'Administrator'"></strong></span>
                    <span>Dicatat Sistem: <strong class="text-slate-400" x-text="formatTanggalIndo(selectedDetail.created_at)"></strong></span>
                </div>
            </div>
        </template>

        <!-- 4. Modal Footer Actions -->
        <div class="pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <template x-if="selectedDetail">
                <div class="flex items-center space-x-2">
                    <!-- Cetak BAST -->
                    <button type="button" @click="showModalDetail = false; openPrintBast(selectedDetail);"
                        class="px-4 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>🖨️ Cetak Lembar BAST Resmi</span>
                    </button>

                    <!-- Ubah Data (Khusus Hibah Masuk yang memiliki astap_id) -->
                    <template x-if="selectedDetail.tipe_hibah === 'masuk' && selectedDetail.astap_id">
                        <a :href="'/astap/' + selectedDetail.astap_id + '/edit-hibah'"
                            class="px-4 py-2 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>✏️ Ubah Data Aset</span>
                        </a>
                    </template>
                </div>
            </template>

            <button type="button" @click="showModalDetail = false"
                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-colors ml-auto cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</template>
