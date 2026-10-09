<!-- ========================================================================= -->
<!-- MODAL DETAIL ASET KEMITRAAN & RINCIAN REGISTER NIBAR (BESPOKE ASTAP STYLE) -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showDetailModal" x-cloak @click.self="showDetailModal = false"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 9000;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    
    <div class="border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto"
         style="background-color: #0f172a;">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="space-y-1.5 min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Kategori KIB Badge -->
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                        :class="{
                            'bg-amber-500/20 text-amber-300 border-amber-500/30': getEffectiveKibCategory(selectedAstapDetail) === 'KIB A',
                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     getEffectiveKibCategory(selectedAstapDetail) === 'KIB B',
                            'bg-purple-500/20 text-purple-300 border-purple-500/30': getEffectiveKibCategory(selectedAstapDetail) === 'KIB C',
                            'bg-teal-500/20 text-teal-300 border-teal-500/30':     getEffectiveKibCategory(selectedAstapDetail) === 'KIB D',
                            'bg-orange-500/20 text-orange-300 border-orange-500/30': getEffectiveKibCategory(selectedAstapDetail) === 'KIB E'
                        }"
                        x-text="getEffectiveKibCategory(selectedAstapDetail)"></span>

                    <!-- Badge Kemitraan Akun 1.5.2 -->
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0 bg-cyan-500/20 text-cyan-300 border-cyan-500/30">
                        🤝 KEMITRAAN AKUN 1.5.2
                    </span>

                    <!-- Badge Reklasifikasi jika ada riwayat reklas -->
                    <template x-if="selectedAstapDetail?.has_reklas || selectedAstapDetail?.is_reklas || (selectedAstapDetail?.reklas_count && selectedAstapDetail?.reklas_count > 0)">
                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0 bg-indigo-500/20 text-indigo-300 border-indigo-500/30 shadow-sm"
                            :title="'Aset ini memiliki riwayat Reklasifikasi' + (selectedAstapDetail?.jenis_reklas ? ' (' + selectedAstapDetail.jenis_reklas.replace(/_/g, ' ') + ')' : '')">
                            <svg class="w-3 h-3 shrink-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>REKLASIFIKASI</span>
                        </span>
                    </template>

                    <!-- Badge Aset Ditambahkan Mitra jika ada item ditambahkan -->
                    <template x-if="selectedAstapDetail?.is_dimanfaatkan && getAsetDitambahkanMitraForDetail(selectedAstapDetail).length > 0">
                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0 bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm shadow-emerald-500/10"
                            :title="'Objek kerjasama ini memuat ' + getAsetDitambahkanMitraForDetail(selectedAstapDetail).length + ' aset yang ditambahkan/didatangkan oleh mitra rekanan'">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span x-text="'📦 +' + getAsetDitambahkanMitraForDetail(selectedAstapDetail).length + ' ASET MITRA'"></span>
                        </span>
                    </template>

                    <!-- Kode 108 / NIBAR -->
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full"
                        x-text="'Kode: ' + (selectedAstapDetail?.kode_barang || '-')"></span>

                    <!-- Tanggal PKS -->
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                        <span class="text-slate-400">📅 Tanggal PKS:</span>
                        <span class="text-cyan-300 font-bold" x-text="formatTanggalIndo(selectedAstapDetail?.tanggal_pks || selectedAstapDetail?.created_at)"></span>
                    </span>

                    <!-- Status Kondisi Keseluruhan -->
                    <template x-if="selectedAstapDetail">
                        <span class="px-2.5 py-0.5 rounded-lg border text-[11px] font-semibold flex items-center space-x-1.5 shrink-0"
                              :class="getKondisiStats(selectedAstapDetail).badge_class">
                            <span class="w-1.5 h-1.5 rounded-full" :class="getKondisiStats(selectedAstapDetail).dot_class"></span>
                            <span x-text="'Kondisi: ' + getKondisiStats(selectedAstapDetail).text"></span>
                        </span>
                    </template>
                </div>
                <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></h3>
            </div>

            <!-- Tombol Close -->
            <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <template x-if="selectedAstapDetail">
            <div class="space-y-4 text-xs text-slate-300">

                <!-- Top 4 Metric KPI Cards (Fully Responsive Grid) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏷️ Jenis PMDN 108</span>
                        <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate" :title="selectedAstapDetail.jenis_aset_nama" x-text="selectedAstapDetail.jenis_aset_nama"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Tahun &amp; Triwulan</span>
                        <span class="text-cyan-300 font-extrabold font-mono text-xs sm:text-sm block" x-text="selectedAstapDetail.tahun_perolehan + ' • ' + selectedAstapDetail.triwulan"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📏 Volume / Satuan</span>
                        <span class="text-teal-300 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAstapDetail.volume_satuan"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Taksiran Nilai Wajar</span>
                        <span class="text-emerald-400 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAstapDetail.jumlah_realisasi"></span>
                    </div>
                </div>

                <!-- DOKUMEN LEGALITAS PKS & KONSESI KERJASAMA -->
                <div class="p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-cyan-500/20 pb-2">
                        <div class="flex items-center space-x-2 text-cyan-300 font-extrabold text-xs uppercase tracking-wider">
                            <span>📜 Dokumen Perjanjian Kerja Sama (PKS) &amp; Masa Konsesi</span>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <template x-if="selectedAstapDetail && (selectedAstapDetail.kemitraan_id || selectedAstapDetail.id)">
                                <button type="button" @click="showDetailModal = false; openPrintBast(selectedAstapDetail, selectedAstapDetail.is_dimanfaatkan)"
                                   class="px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold bg-gradient-to-r from-purple-600/30 to-indigo-600/30 hover:from-purple-600/50 hover:to-indigo-600/50 text-purple-200 border border-purple-500/40 flex items-center gap-1.5 transition-all active:scale-95 shadow-sm cursor-pointer"
                                   title="Buka Dokumen Resmi Berita Acara Serah Terima (BAST Kemitraan Format A4)">
                                    <span>🖨️</span>
                                    <span>Cetak Draf BAST</span>
                                </button>
                            </template>

                            <span class="px-2.5 py-0.5 rounded-lg text-[10.5px] font-bold border"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': selectedAstapDetail.status_konsesi === 'Aktif',
                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': selectedAstapDetail.status_konsesi === 'Konsesi Berakhir',
                                      'bg-blue-500/20 text-blue-300 border-blue-500/30': selectedAstapDetail.status_konsesi === 'Selesai' || selectedAstapDetail.status_konsesi === 'Selesai / Reklasifikasi',
                                      'bg-rose-500/20 text-rose-300 border-rose-500/30': selectedAstapDetail.status_konsesi === 'Dihentikan'
                                  }"
                                  x-text="'Status: ' + ((selectedAstapDetail.status_konsesi === 'Selesai' || selectedAstapDetail.status_konsesi === 'Selesai / Reklasifikasi') ? 'Selesai' : selectedAstapDetail.status_konsesi)"></span>

                            <template x-if="selectedAstapDetail.sisa_hari_konsesi !== null && selectedAstapDetail.sisa_hari_konsesi !== undefined">
                                <span class="px-2 py-0.5 rounded-lg text-[10.5px] font-mono font-bold"
                                      :class="{
                                          'bg-emerald-500/15 text-emerald-300': selectedAstapDetail.sisa_hari_konsesi > 60,
                                          'bg-amber-500/15 text-amber-300': selectedAstapDetail.sisa_hari_konsesi > 0 && selectedAstapDetail.sisa_hari_konsesi <= 60,
                                          'bg-rose-500/15 text-rose-300': selectedAstapDetail.sisa_hari_konsesi <= 0
                                      }"
                                      x-text="selectedAstapDetail.sisa_hari_konsesi > 0 ? ('⏱️ Sisa ' + selectedAstapDetail.sisa_hari_konsesi + ' Hari') : '🛑 Konsesi Berakhir'"></span>
                            </template>
                        </div>
                    </div>

                    <!-- 4 Kolom Rincian PKS -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs pt-1">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Nomor PKS:</span>
                            <span class="font-mono text-cyan-300 font-bold text-sm block truncate" :title="selectedAstapDetail.nomor_pks" x-text="selectedAstapDetail.nomor_pks || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal PKS:</span>
                            <span class="text-white font-semibold block" x-text="formatTanggalIndo(selectedAstapDetail.tanggal_pks)"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Skema Kemitraan:</span>
                            <span class="font-extrabold text-cyan-300 block" x-text="selectedAstapDetail.skema_kemitraan || 'KSO'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Masa Konsesi / Kerjasama:</span>
                            <span class="text-slate-200 font-medium block" x-text="(formatTanggalIndo(selectedAstapDetail.tanggal_mulai) || '?') + ' s.d. ' + (formatTanggalIndo(selectedAstapDetail.tanggal_selesai) || '?')"></span>
                        </div>
                    </div>


                    <!-- KARTU DOKUMEN BAST & KONTRAK KERJASAMA (PRATINJAU, GANTI & HAPUS) - FULL WIDTH -->
                    <div class="w-full pt-3 border-t border-cyan-500/20" style="width: 100%;">
                        <!-- Input File Tersembunyi untuk Ubah / Upload Dokumen BAST -->
                        <input type="file" id="fileInputDokumenDetail" @change="handleUploadDokumenDetail" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" class="hidden">

                        <div class="w-full p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border border-cyan-500/30 shadow-md">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3.5">
                                <div class="flex items-center space-x-3.5 min-w-0 flex-1">
                                    <!-- Thumbnail / Ikon Dokumen -->
                                    <div class="relative shrink-0">
                                        <template x-if="isDokumenImage(selectedAstapDetail?.dokumen_path)">
                                            <div @click="openDokumenPreview(selectedAstapDetail.dokumen_path, selectedAstapDetail.nama_barang)"
                                                 class="w-12 h-12 rounded-xl border border-cyan-500/40 bg-slate-900 overflow-hidden cursor-pointer group shadow-sm flex items-center justify-center relative hover:ring-2 hover:ring-cyan-400/50 transition-all">
                                                <img :src="'/storage/' + selectedAstapDetail.dokumen_path" alt="Thumbnail BAST" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/10 flex items-center justify-center transition-colors">
                                                    <span class="text-xs">🔍</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!isDokumenImage(selectedAstapDetail?.dokumen_path) && selectedAstapDetail?.dokumen_path">
                                            <div class="w-12 h-12 rounded-xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-300 flex items-center justify-center text-xl shadow-inner">
                                                📄
                                            </div>
                                        </template>
                                        <template x-if="!selectedAstapDetail?.dokumen_path">
                                            <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center text-xl">
                                                📁
                                            </div>
                                        </template>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center flex-wrap gap-2">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-cyan-400">
                                                Arsip Scan Dokumen Sah (BAST / PKS Bertanda Tangan)
                                            </span>
                                            <template x-if="selectedAstapDetail?.dokumen_path">
                                                <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1 shadow-sm">
                                                    <span>✓</span> Dokumen Sah Terlampir
                                                </span>
                                            </template>
                                            <template x-if="!selectedAstapDetail?.dokumen_path">
                                                <span class="px-2 py-0.5 rounded text-[9.5px] font-semibold bg-slate-800 text-slate-400 border border-slate-700/80">
                                                    Belum Ada Arsip Scan (Menyusul)
                                                </span>
                                            </template>
                                        </div>
                                        <div class="text-xs font-bold text-white truncate mt-1"
                                             :title="selectedAstapDetail?.dokumen_path ? selectedAstapDetail.dokumen_path.split('/').pop() : ''"
                                             x-text="selectedAstapDetail?.dokumen_path ? selectedAstapDetail.dokumen_path.split('/').pop() : 'Unggah hasil scan berkas setelah selesai ditandatangani basah & distempel oleh Direktur RSUD dan Pihak Mitra.'">
                                        </div>
                                        <div class="text-[10.5px] text-slate-400 mt-0.5">
                                            <template x-if="selectedAstapDetail?.dokumen_path">
                                                <span>Format: <strong class="font-mono text-cyan-300 uppercase" x-text="selectedAstapDetail.dokumen_path.split('.').pop()"></strong> · Maks. 10 MB (Arsip Legalitas Resmi)</span>
                                            </template>
                                            <template x-if="!selectedAstapDetail?.dokumen_path">
                                                <span class="text-slate-400">Format: PDF atau Gambar Scan JPG/PNG (Maks 10 MB). Sifatnya opsional dan dapat dilengkapi kapan saja.</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Aksi Dokumen: Tampilkan, Ganti, Hapus -->
                                <div class="flex items-center flex-wrap gap-2 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-800/80">
                                    <!-- Indikator Loading -->
                                    <template x-if="isUploadingDokumen || isDeletingDokumen">
                                        <span class="px-3 py-1.5 rounded-xl bg-cyan-950/80 text-cyan-300 text-xs font-bold border border-cyan-500/40 flex items-center gap-1.5 animate-pulse">
                                            <svg class="animate-spin h-3.5 w-3.5 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span x-text="isUploadingDokumen ? 'Mengunggah...' : 'Menghapus...'"></span>
                                        </span>
                                    </template>

                                    <template x-if="!isUploadingDokumen && !isDeletingDokumen && selectedAstapDetail?.dokumen_path">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <!-- 1. Tombol Tampilkan Berkas (Pratinjau) -->
                                            <button type="button" @click="openDokumenPreview(selectedAstapDetail.dokumen_path, selectedAstapDetail.nama_barang)"
                                                title="Tampilkan / Pratinjau Berkas BAST"
                                                class="px-3.5 py-1.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Tampilkan Berkas</span>
                                            </button>

                                            <!-- 2. Tombol Ganti Berkas -->
                                            <button type="button" @click="triggerUploadDokumenDetail()"
                                                title="Ganti Berkas BAST dengan Dokumen Baru"
                                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                <span>Ganti Berkas</span>
                                            </button>

                                            <!-- 3. Tombol Hapus Berkas -->
                                            <button type="button" @click="deleteDokumenDetail()"
                                                title="Hapus Berkas Dokumen BAST ini"
                                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Jika Belum Ada Berkas: Tombol Unggah -->
                                    <template x-if="!isUploadingDokumen && !isDeletingDokumen && !selectedAstapDetail?.dokumen_path">
                                        <button type="button" @click="triggerUploadDokumenDetail()"
                                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 hover:from-cyan-500 hover:to-teal-500 text-white font-extrabold text-xs transition-all flex items-center gap-1.5 shadow-md shadow-cyan-500/20 active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            <span>Unggah Scan Dokumen Sah</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- SECTION: INFORMASI & TABEL ASET YANG DITAMBAHKAN MITRA PADA OBJEK INI   -->
                <!-- ========================================================================= -->
                <template x-if="selectedAstapDetail && selectedAstapDetail.is_dimanfaatkan">
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-emerald-500/30 space-y-4 shadow-xl">
                        
                        <!-- Header Section -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-emerald-500/20 gap-3">
                            <div class="flex items-start sm:items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center text-lg shrink-0 shadow-inner">
                                    📦
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-emerald-400">
                                            Aset yang Ditambahkan / Didatangkan oleh Mitra Kerjasama
                                        </h4>
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 uppercase tracking-wider">
                                            Objek Kerjasama Ini
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Daftar peralatan medis, mesin, sarana, atau fasilitas yang disediakan pihak ketiga di atas objek pemanfaatan ini.
                                    </p>
                                </div>
                            </div>

                            <!-- Tombol Aksi Tambah & Ringkasan Nilai -->
                            <div class="flex items-center gap-2 flex-wrap shrink-0">
                                <template x-if="getAsetDitambahkanMitraForDetail(selectedAstapDetail).length > 0">
                                    <div class="hidden md:flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-bold bg-slate-900 border border-emerald-500/30 text-emerald-300"
                                              x-text="getAsetDitambahkanMitraForDetail(selectedAstapDetail).length + ' Item (' + getTotalVolumeAsetDitambahkanMitra(selectedAstapDetail) + ' Unit)'"></span>
                                        <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-extrabold bg-emerald-500/15 border border-emerald-500/30 text-emerald-400"
                                              x-text="'Rp ' + formatRupiah(getTotalNilaiAsetDitambahkanMitra(selectedAstapDetail))"></span>
                                    </div>
                                </template>

                                <a :href="getLinkTambahAsetMitra(selectedAstapDetail)"
                                   title="Catat barang/aset baru yang didatangkan oleh mitra rekanan untuk objek PKS ini"
                                   class="px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs transition-all shadow-md shadow-emerald-500/20 active:scale-95 flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Catat Aset Ditambahkan Mitra</span>
                                </a>
                            </div>
                        </div>

                        <!-- 1. Kondisi Ada Aset: Tampilkan Tabel Rincian -->
                        <template x-if="getAsetDitambahkanMitraForDetail(selectedAstapDetail).length > 0">
                            <div class="space-y-3">
                                <div class="rounded-xl border border-slate-800/90 overflow-hidden custom-scrollbar max-h-72 overflow-y-auto">
                                    <table class="w-full text-left text-[11px] text-slate-300 min-w-[700px]">
                                        <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px] border-b border-slate-800 sticky top-0 z-10 shadow-sm" style="background-color: #020617;">
                                            <tr>
                                                <th class="px-3 py-2.5 text-center w-12">#</th>
                                                <th class="px-3 py-2.5">Nama Barang &amp; Spesifikasi</th>
                                                <th class="px-3 py-2.5 text-center">Kode 108 &amp; NIBAR</th>
                                                <th class="px-3 py-2.5 text-center">Volume</th>
                                                <th class="px-3 py-2.5 text-center">Kondisi</th>
                                                <th class="px-3 py-2.5 text-right">Taksiran Nilai (Rp)</th>
                                                <th class="px-3 py-2.5 text-center w-24">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-800/80 bg-slate-900/40">
                                            <template x-for="(dItem, dIdx) in getAsetDitambahkanMitraForDetail(selectedAstapDetail)" :key="dItem.id || dIdx">
                                                <tr class="hover:bg-slate-800/50 transition-colors">
                                                    <!-- No & KIB -->
                                                    <td class="px-3 py-2 text-center">
                                                        <div class="flex flex-col items-center gap-1">
                                                            <span class="text-slate-400 font-mono text-[10px]" x-text="dIdx + 1"></span>
                                                            <span class="px-1.5 py-0.2 rounded text-[8.5px] font-black border uppercase"
                                                                  :class="{
                                                                      'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': dItem.category === 'KIB B',
                                                                      'bg-purple-500/20 text-purple-300 border-purple-500/30': dItem.category === 'KIB C',
                                                                      'bg-teal-500/20 text-teal-300 border-teal-500/30': dItem.category === 'KIB D',
                                                                      'bg-orange-500/20 text-orange-300 border-orange-500/30': dItem.category === 'KIB E',
                                                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': dItem.category === 'KIB A'
                                                                  }"
                                                                  x-text="dItem.category || 'KIB B'"></span>
                                                        </div>
                                                    </td>

                                                    <!-- Nama Barang & Spesifikasi -->
                                                    <td class="px-3 py-2 min-w-[200px]">
                                                        <div class="font-bold text-white text-xs" x-text="dItem.nama_barang"></div>
                                                        <div class="text-[10px] text-slate-400 truncate max-w-xs" x-show="dItem.merk_type" x-text="'Merk/Type: ' + dItem.merk_type"></div>
                                                        <div class="text-[10px] text-teal-400/90 truncate max-w-xs" x-show="dItem.ruang_pemegang && dItem.ruang_pemegang !== '-'" x-text="'📍 ' + dItem.ruang_pemegang"></div>
                                                    </td>

                                                    <!-- Kode 108 & NIBAR -->
                                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                                        <div class="font-mono text-cyan-400 font-bold text-[10.5px]" x-text="dItem.kode_barang || '-'"></div>
                                                        <div class="font-mono text-emerald-400 text-[10px]" x-text="dItem.nibar ? ('NIBAR: ' + dItem.nibar) : '-'"></div>
                                                    </td>

                                                    <!-- Volume -->
                                                    <td class="px-3 py-2 text-center whitespace-nowrap font-mono font-bold text-slate-200">
                                                        <span x-text="(dItem.jumlah_volume || 1) + ' ' + (dItem.satuan || 'Unit')"></span>
                                                    </td>

                                                    <!-- Kondisi -->
                                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border inline-block"
                                                              :class="{
                                                                  'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': ['Baik', 'B'].includes(dItem.kondisi),
                                                                  'bg-amber-500/20 text-amber-300 border-amber-500/30': ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR'].includes(dItem.kondisi),
                                                                  'bg-rose-500/20 text-rose-300 border-rose-500/30': ['Rusak Berat', 'RB', 'Rusak'].includes(dItem.kondisi)
                                                              }"
                                                              x-text="['B', 'Baik'].includes(dItem.kondisi) ? 'Baik' : (['KB', 'RR', 'Kurang Baik', 'Rusak Ringan'].includes(dItem.kondisi) ? 'Kurang Baik' : 'Rusak Berat')"></span>
                                                    </td>

                                                    <!-- Taksiran Nilai -->
                                                    <td class="px-3 py-2 text-right whitespace-nowrap font-mono font-bold text-emerald-400">
                                                        <span x-text="'Rp ' + formatRupiah(dItem.nilai_aset || 0)"></span>
                                                    </td>

                                                    <!-- Aksi -->
                                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                                        <button type="button" @click="openDetailFromDitambahkan(dItem)"
                                                                title="Buka detail lengkap aset tambahan ini"
                                                                class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-cyan-500/20 text-cyan-300 hover:text-cyan-200 border border-slate-700 hover:border-cyan-500/40 text-[10.5px] font-bold transition-all active:scale-95 cursor-pointer">
                                                            Detail &rarr;
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot class="bg-slate-950/90 border-t border-slate-800 text-[10.5px] font-bold font-mono">
                                            <tr>
                                                <td colspan="3" class="px-3 py-2 text-right uppercase tracking-wider text-slate-400">Total Investasi Aset Mitra:</td>
                                                <td class="px-3 py-2 text-center text-teal-300" x-text="getTotalVolumeAsetDitambahkanMitra(selectedAstapDetail) + ' Unit'"></td>
                                                <td></td>
                                                <td class="px-3 py-2 text-right text-emerald-400 font-extrabold" x-text="'Rp ' + formatRupiah(getTotalNilaiAsetDitambahkanMitra(selectedAstapDetail))"></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <!-- 2. Kondisi Kosong (Empty State): Box Edukatif & Tombol CTA -->
                        <template x-if="getAsetDitambahkanMitraForDetail(selectedAstapDetail).length === 0">
                            <div class="p-6 rounded-2xl border border-dashed border-emerald-500/30 bg-emerald-950/10 text-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl mx-auto shadow-inner">
                                    📦
                                </div>
                                <div class="max-w-md mx-auto space-y-1">
                                    <h5 class="text-xs font-bold text-white">
                                        Belum Ada Aset yang Dicatat Ditambahkan oleh Mitra pada Objek Ini
                                    </h5>
                                    <p class="text-[11px] text-slate-400 leading-relaxed">
                                        Jika mitra rekanan (misal sewa tanah/gedung ini) mendatangkan peralatan medis, fasilitas penunjang, atau mesin baru, Anda dapat mencatatnya agar terinventarisasi secara resmi dalam pembukuan kemitraan RSUD.
                                    </p>
                                </div>
                                <div class="pt-1">
                                    <a :href="getLinkTambahAsetMitra(selectedAstapDetail)"
                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-emerald-500/20 active:scale-95 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Catat Aset Pertama yang Ditambahkan Mitra</span>
                                    </a>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>

                <!-- ========================================================================= -->
                <!-- SHOWCASE CARD: INFORMASI OBJEK PEMANFAATAN BMD ASAL (BUKAN TABEL)        -->
                <!-- Ditampilkan khusus saat membuka detail aset yang ditambahkan mitra         -->
                <!-- ========================================================================= -->
                <template x-if="selectedAstapDetail && !selectedAstapDetail.is_dimanfaatkan">
                    <div class="rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-slate-900 via-slate-950 to-cyan-950/40 border border-cyan-500/40 shadow-xl space-y-3.5 relative overflow-hidden">
                        
                        <!-- Subtle Accent Glow -->
                        <div class="absolute -right-12 -top-12 w-40 h-40 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                        <!-- Header Card Showcase -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-cyan-500/20 gap-3 relative z-10">
                            <div class="flex items-start sm:items-center space-x-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 flex items-center justify-center text-xl shrink-0 shadow-inner">
                                    🏛️
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">
                                            OBJEK PEMANFAATAN BMD ASAL
                                        </span>
                                        <template x-if="getObjekPemanfaatanAsal(selectedAstapDetail)?.category">
                                            <span class="px-2 py-0.5 rounded-lg text-[9.5px] font-bold uppercase tracking-wider border"
                                                  :class="{
                                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': getObjekPemanfaatanAsal(selectedAstapDetail).category === 'KIB A',
                                                      'bg-purple-500/20 text-purple-300 border-purple-500/30': getObjekPemanfaatanAsal(selectedAstapDetail).category === 'KIB C',
                                                      'bg-teal-500/20 text-teal-300 border-teal-500/30': getObjekPemanfaatanAsal(selectedAstapDetail).category === 'KIB D'
                                                  }"
                                                  x-text="getObjekPemanfaatanAsal(selectedAstapDetail).category"></span>
                                        </template>
                                    </div>
                                    <h4 class="text-xs sm:text-sm font-extrabold text-white mt-1 leading-snug break-words"
                                        x-text="getObjekPemanfaatanAsal(selectedAstapDetail)?.nama_barang || (selectedAstapDetail.objek_nibar ? ('Objek Tanah / Gedung RSUD (NIBAR: ' + selectedAstapDetail.objek_nibar + ')') : 'Aset Fasilitas Tambahan Kerjasama Rekanan Mitra')">
                                    </h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Objek Barang Milik Daerah (BMD) milik RSUD Koesnandi tempat aset/fasilitas ini didatangkan dan dioperasikan.
                                    </p>
                                </div>
                            </div>

                            <!-- Tombol Aksi Buka Detail Objek Asal -->
                            <template x-if="getObjekPemanfaatanAsal(selectedAstapDetail) && (getObjekPemanfaatanAsal(selectedAstapDetail).raw_astap || getObjekPemanfaatanAsal(selectedAstapDetail).astap_id)">
                                <div class="shrink-0 flex items-center gap-2">
                                    <button type="button" @click="openDetailFromDimanfaatkan(getObjekPemanfaatanAsal(selectedAstapDetail))"
                                            title="Tinjau spesifikasi dan rincian lengkap objek BMD pemanfaatan ini"
                                            class="px-3.5 py-1.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-black text-xs transition-all shadow-md shadow-cyan-500/20 active:scale-95 flex items-center gap-1.5 cursor-pointer">
                                        <span>🔍</span>
                                        <span>Buka Detail Objek BMD</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <!-- Grid Showcase Informasi Objek Asal (Bukan Tabel) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-3 text-xs pt-1 relative z-10">
                            
                            <!-- Card 1: NIBAR & Identitas Register -->
                            <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">🏷️ NIBAR Objek BMD</span>
                                <div class="font-mono text-cyan-300 font-extrabold text-xs sm:text-sm truncate"
                                     x-text="getObjekPemanfaatanAsal(selectedAstapDetail)?.nibar || selectedAstapDetail.objek_nibar || '-'"></div>
                                <span class="text-[10px] text-slate-400 block truncate"
                                      x-text="'Kode 108: ' + (getObjekPemanfaatanAsal(selectedAstapDetail)?.kode_barang || '-')"></span>
                            </div>

                            <!-- Card 2: Lokasi / Alamat Objek -->
                            <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">📍 Lokasi Penempatan</span>
                                <div class="text-slate-200 font-bold text-xs truncate"
                                     :title="getObjekPemanfaatanAsal(selectedAstapDetail)?.alamat || selectedAstapDetail.alamat_barang"
                                     x-text="getObjekPemanfaatanAsal(selectedAstapDetail)?.alamat || selectedAstapDetail.alamat_barang || 'RSUD Dr. H. Koesnandi'"></div>
                                <span class="text-[10px] text-teal-400/90 block truncate"
                                      x-text="'Unit: ' + (selectedAstapDetail.penempatan || 'Pengelola Aset RSUD')"></span>
                            </div>

                            <!-- Card 3: Dimensi / Luas / Sertifikat -->
                            <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">📐 Luas / Dimensi Objek</span>
                                <div class="font-mono text-emerald-400 font-extrabold text-xs sm:text-sm"
                                     x-text="getObjekPemanfaatanAsal(selectedAstapDetail)?.luas ? (Number(getObjekPemanfaatanAsal(selectedAstapDetail).luas).toLocaleString('id-ID') + ' m²') : 'Sesuai Fisik Kerjasama'"></div>
                                <span class="text-[10px] text-slate-400 block truncate"
                                      x-text="getObjekPemanfaatanAsal(selectedAstapDetail)?.sertifikat ? ('Sertifikat: ' + getObjekPemanfaatanAsal(selectedAstapDetail).sertifikat) : 'Hak Pengelolaan RSUD'"></span>
                            </div>

                            <!-- Card 4: Dasar Kerjasama & Mitra -->
                            <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800/80 space-y-1">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">📜 Dasar Kerjasama</span>
                                <div class="font-mono text-amber-300 font-bold text-xs truncate"
                                     :title="selectedAstapDetail.nomor_pks"
                                     x-text="selectedAstapDetail.nomor_pks || '-'"></div>
                                <span class="text-[10px] text-slate-300 block truncate font-semibold"
                                      :title="selectedAstapDetail.penyedia_nama"
                                      x-text="'Mitra: ' + (selectedAstapDetail.penyedia_nama || '-')"></span>
                            </div>

                        </div>

                        <!-- Catatan Integrasi Operasional jika belum ada relasi spesifik -->
                        <template x-if="!getObjekPemanfaatanAsal(selectedAstapDetail) && !selectedAstapDetail.objek_nibar">
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-[11px] text-slate-400 flex items-center gap-2">
                                <span class="text-amber-400">💡</span>
                                <span>Aset ini tercatat sebagai pengadaan fasilitas operasional mitra berdasarkan Perjanjian Kerja Sama (PKS) terlampir.</span>
                            </div>
                        </template>

                    </div>
                </template>

                <!-- DYNAMIC SPESIFIKASI BERDASARKAN KATEGORI ASET (KIB A - E) -->
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider flex items-center space-x-1.5"
                            :class="{
                                'text-amber-400': getEffectiveKibCategory(selectedAstapDetail) === 'KIB A',
                                'text-cyan-400':  getEffectiveKibCategory(selectedAstapDetail) === 'KIB B',
                                'text-purple-400': getEffectiveKibCategory(selectedAstapDetail) === 'KIB C',
                                'text-teal-400':  getEffectiveKibCategory(selectedAstapDetail) === 'KIB D',
                                'text-orange-400': getEffectiveKibCategory(selectedAstapDetail) === 'KIB E'
                            }">
                            <span>🔍 Rincian Spesifikasi Teknis Aset (<span x-text="getEffectiveKibCategory(selectedAstapDetail)"></span>)</span>
                        </h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400" x-text="'Spesifikasi Khusus ' + getEffectiveKibCategory(selectedAstapDetail)"></span>
                    </div>

                    <!-- 1. KIB A (TANAH) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail) === 'KIB A'">
                        <div class="space-y-2.5">
                            <template x-if="getTanahItemsForDetail(selectedAstapDetail).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(tItem, tIdx) in getTanahItemsForDetail(selectedAstapDetail)" :key="tIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                        🌾 Bidang Tanah #<span x-text="tIdx + 1"></span>
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                          x-text="(tItem.tanah_jumlah_bidang || 1) + ' ' + (selectedAstapDetail.satuan || 'Bidang')"></span>
                                                    <template x-if="getRincianNibar(selectedAstapDetail, tIdx, 'tanah_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedAstapDetail, tIdx, 'tanah_items').tooltip"
                                                              x-text="getRincianNibar(selectedAstapDetail, tIdx, 'tanah_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Total Taksiran: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(getTanahItemNilai(tItem, selectedAstapDetail)).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📜 Hak &amp; Sertifikat</span>
                                                    <span class="text-amber-300 font-bold block" x-text="tItem.tanah_hak || 'Hak Pakai'"></span>
                                                    <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="tItem.tanah_sertifikat_no ? ('No: ' + tItem.tanah_sertifikat_no) : 'Tanpa No Sertifikat'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                                    <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(tItem.tanah_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                    <span class="font-semibold text-[10px] inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md border" :class="getRincianKondisiStats(selectedAstapDetail, tIdx, 'tanah_items').badge_class">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, tIdx, 'tanah_items').dot_class"></span>
                                                        <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, tIdx, 'tanah_items').text"></span>
                                                    </span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Taksiran Nilai Wajar</span>
                                                    <span class="text-emerald-400 font-mono font-bold block text-[10.5px]" x-text="'Rp ' + Number(getTanahItemNilai(tItem, selectedAstapDetail)).toLocaleString('id-ID')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Lokasi</span>
                                                    <span class="text-teal-300 font-medium block truncate" :title="tItem.tanah_alamat" x-text="tItem.tanah_alamat || selectedAstapDetail.alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback data tanah tunggal / legacy -->
                            <template x-if="getTanahItemsForDetail(selectedAstapDetail).length === 0">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                🌾 Bidang Tanah #1
                                            </span>
                                            <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                  x-text="selectedAstapDetail.volume_satuan"></span>
                                            <template x-if="getRincianNibar(selectedAstapDetail, 0)">
                                                <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                      :title="getRincianNibar(selectedAstapDetail, 0).tooltip"
                                                      x-text="getRincianNibar(selectedAstapDetail, 0).label"></span>
                                            </template>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Total Taksiran: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="selectedAstapDetail.jumlah_realisasi"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📜 Hak &amp; Sertifikat</span>
                                            <span class="text-amber-300 font-bold block" x-text="selectedAstapDetail.spesifikasi_json?.hak_tanah || 'Hak Pakai'"></span>
                                            <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="selectedAstapDetail.spesifikasi_json?.sertifikat_no ? ('No: ' + selectedAstapDetail.spesifikasi_json?.sertifikat_no) : 'Tanpa No Sertifikat'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(selectedAstapDetail.spesifikasi_json?.luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                            <span class="font-semibold text-[10px] inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md border" :class="getRincianKondisiStats(selectedAstapDetail, 0).badge_class">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, 0).dot_class"></span>
                                                <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, 0).text"></span>
                                            </span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Taksiran Nilai Wajar</span>
                                            <span class="text-emerald-400 font-mono font-bold block text-[10.5px]" x-text="selectedAstapDetail.jumlah_realisasi || ('Rp ' + Number(selectedAstapDetail.nilai_aset || selectedAstapDetail.total_realisasi || 0).toLocaleString('id-ID'))"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Lokasi</span>
                                            <span class="text-teal-300 font-medium block truncate" :title="selectedAstapDetail.alamat_barang" x-text="selectedAstapDetail.alamat_barang || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 2. KIB B (PERALATAN & MESIN) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail) === 'KIB B'">
                        <div class="space-y-3">
                            <!-- Multi-Item Repeater List jika ada mesin_items -->
                            <template x-if="getMesinItemsForDetail(selectedAstapDetail).length > 0">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                        <span class="flex items-center gap-1.5 text-cyan-400">
                                            📦 Rincian Barang Terdaftar (<span x-text="getMesinItemsForDetail(selectedAstapDetail).length"></span> Item)
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedAstapDetail.jumlah_volume || getMesinItemsForDetail(selectedAstapDetail).reduce((s, m) => s + (parseFloat(m.mesin_jumlah_barang) || 1), 0)) + ' ' + (selectedAstapDetail.satuan || 'Unit')"></span>
                                    </div>
                                    <template x-for="(mItem, mIdx) in getMesinItemsForDetail(selectedAstapDetail)" :key="mIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 space-y-2.5 shadow-sm hover:border-cyan-400/50 transition-all">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-mono font-bold text-[11px] border border-cyan-500/30"
                                                          x-text="'Item #' + (mIdx + 1)"></span>
                                                    <span class="text-white font-bold text-xs" x-text="(mItem.mesin_nama_barang ? (mItem.mesin_nama_barang + ' • ') : '') + ((mItem.mesin_merk || mItem.mesin_type) ? ((mItem.mesin_merk || '') + ' ' + (mItem.mesin_type || '')) : '-')"></span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                          x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || 'Unit')"></span>
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                                          :class="mItem.is_extracom ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                          x-text="mItem.is_extracom ? '📦 Extracom' : '⚙️ Reguler'"></span>
                                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                          :class="getRincianKondisiStats(selectedAstapDetail, mIdx, 'mesin_items').badge_class">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, mIdx, 'mesin_items').dot_class"></span>
                                                        <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, mIdx, 'mesin_items').text"></span>
                                                    </span>
                                                    <template x-if="getRincianNibar(selectedAstapDetail, mIdx, 'mesin_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedAstapDetail, mIdx, 'mesin_items').tooltip"
                                                              x-text="getRincianNibar(selectedAstapDetail, mIdx, 'mesin_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Subtotal: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(mItem.mesin_jumlah_barang) || 1) * (parseFloat(mItem.mesin_nilai_satuan) || 0)) + (parseFloat(mItem.mesin_administrasi_proyek) || 0)).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Merk / Type / Bahan</span>
                                                    <span class="text-cyan-300 font-bold block truncate" x-text="(mItem.mesin_merk || '-') + ' / ' + (mItem.mesin_type || '-')"></span>
                                                    <span class="text-slate-300 text-[10px]" x-text="'Bhn: ' + (mItem.mesin_bahan || '-') + ' • Uk: ' + (mItem.mesin_ukuran || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔢 Identitas / No. Seri</span>
                                                    <span class="text-cyan-300 font-mono font-semibold block truncate" x-text="'Pabrik: ' + (mItem.mesin_no_pabrik || '-')"></span>
                                                    <span class="text-slate-400 font-mono text-[9px] block truncate" x-text="'Rgk: ' + (mItem.mesin_no_rangka || '-') + ' • Msn: ' + (mItem.mesin_no_mesin || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Wajar Satuan</span>
                                                    <span class="text-emerald-300 font-medium block text-[10px]" x-text="'Rp ' + Number(mItem.mesin_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback jika single item / legacy -->
                            <template x-if="getMesinItemsForDetail(selectedAstapDetail).length === 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Merk / Brand</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.spesifikasi_json?.merk || selectedAstapDetail.merk || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Type / Model</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.spesifikasi_json?.type || selectedAstapDetail.type || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🧪 Bahan / Material</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.spesifikasi_json?.bahan || selectedAstapDetail.bahan || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🔢 No. Pabrik / Seri (SN)</span>
                                        <span class="text-cyan-300 font-mono font-bold" x-text="selectedAstapDetail.spesifikasi_json?.no_pabrik || selectedAstapDetail.no_pabrik || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🚗 No. Rangka / Mesin</span>
                                        <span class="text-slate-200 font-mono font-semibold" x-text="(selectedAstapDetail.spesifikasi_json?.no_rangka || '-') + ' / ' + (selectedAstapDetail.spesifikasi_json?.no_mesin || '-')"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 3. KIB C (GEDUNG & BANGUNAN) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail) === 'KIB C'">
                        <div class="space-y-3">
                            <template x-if="getGedungItemsForDetail(selectedAstapDetail).length > 0">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between px-1">
                                        <span class="text-[11px] font-bold text-purple-300 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>🏢 Rincian Gedung &amp; Bangunan:</span>
                                        </span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30" 
                                              x-text="getGedungItemsForDetail(selectedAstapDetail).length + ' Gedung Terdaftar'"></span>
                                    </div>
                                    <template x-for="(gItem, gIdx) in getGedungItemsForDetail(selectedAstapDetail)" :key="gIdx">
                                        <div class="p-3 rounded-2xl bg-slate-900/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-2.5">
                                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40" x-text="'Gedung #' + (gIdx + 1)"></span>
                                                    <span class="text-xs font-bold text-white" x-text="gItem.gedung_nama_barang || selectedAstapDetail.nama_barang"></span>
                                                    <template x-if="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items').tooltip"
                                                              x-text="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="flex items-center space-x-3 text-[10.5px] font-mono">
                                                    <span class="text-slate-400">Luas: <strong class="text-cyan-300" x-text="(gItem.gedung_luas_m2 || 0) + ' M²'"></strong></span>
                                                    <span class="text-emerald-400 font-bold" x-text="'Rp ' + Number(Number(gItem.gedung_nilai_perencanaan || 0) + Number(gItem.gedung_nilai_fisik || 0) + Number(gItem.gedung_nilai_pengawasan || 0)).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏢 Konstruksi</span>
                                                    <span class="text-purple-300 font-bold block" x-text="(gItem.gedung_bertingkat || 'Bertingkat') + ' • ' + (gItem.gedung_beton || 'Beton')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status Tanah</span>
                                                    <span class="text-teal-300 font-semibold block truncate" x-text="gItem.gedung_status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Komponen Nilai</span>
                                                    <span class="text-slate-300 block text-[9.5px]" x-text="'Fisik: Rp ' + Number(gItem.gedung_nilai_fisik || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Lokasi Fisik</span>
                                                    <span class="text-emerald-300 font-medium block truncate" :title="gItem.gedung_alamat" x-text="gItem.gedung_alamat || selectedAstapDetail.alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback data single gedung -->
                            <template x-if="getGedungItemsForDetail(selectedAstapDetail).length === 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏢 Tipe Konstruksi</span>
                                        <span class="text-purple-300 font-bold" x-text="(selectedAstapDetail.spesifikasi_json?.bertingkat || 'Bertingkat') + ' • ' + (selectedAstapDetail.spesifikasi_json?.beton || 'Beton')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Luas Lantai Gedung</span>
                                        <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.luas_lantai_m2 || selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.volume_satuan || '-') + ' m²'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🌱 Status Penguasaan Tanah</span>
                                        <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 4. KIB D (JALAN, IRIGASI DAN JARINGAN) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail) === 'KIB D'">
                        <div class="space-y-3">
                            <template x-for="(jItem, jIdx) in getJaringanItemsForDetail(selectedAstapDetail)" :key="jIdx">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-teal-500/30 space-y-2.5 shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-teal-500/20 text-teal-300 font-mono font-bold text-[11px] border border-teal-500/30"
                                                  x-text="'Ruas #' + (jIdx + 1)"></span>
                                            <span class="text-white font-bold text-xs" x-text="jItem.jaringan_nama_barang || selectedAstapDetail.nama_barang"></span>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Total Taksiran: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(jItem.jaringan_nilai_fisik || selectedAstapDetail.nilai_aset || 0).toLocaleString('id-ID')"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🛣️ Konstruksi</span>
                                            <span class="text-teal-300 font-bold block" x-text="jItem.jaringan_konstruksi || selectedAstapDetail.spesifikasi_json?.konstruksi || 'Aspal / Beton'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Dimensi / Luas</span>
                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(jItem.jaringan_luas_m2 || selectedAstapDetail.spesifikasi_json?.luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status Tanah</span>
                                            <span class="text-amber-300 font-semibold block truncate" x-text="jItem.jaringan_status_tanah || selectedAstapDetail.spesifikasi_json?.status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Lokasi</span>
                                            <span class="text-teal-300 font-medium block truncate" x-text="jItem.jaringan_alamat || selectedAstapDetail.alamat_barang || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 5. KIB E (ASET TETAP LAINNYA) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail) === 'KIB E'">
                        <div class="space-y-3">
                            <template x-for="(lItem, lIdx) in getLainnyaItemsForDetail(selectedAstapDetail)" :key="lIdx">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-purple-500/30 space-y-2.5 shadow-sm hover:border-purple-400/50 transition-all">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[11px] border border-purple-500/30"
                                                  x-text="'Item #' + (lIdx + 1)"></span>
                                            <span class="text-white font-bold text-xs" x-text="lItem.lainnya_nama_barang || lItem.lainnya_judul || selectedAstapDetail.nama_barang"></span>
                                            
                                            <!-- Badge Status Akuntansi: Extracom vs Reguler -->
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                                  :class="lItem.is_extracom ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                  x-text="lItem.is_extracom ? '📦 Extracom' : '⚙️ Reguler'"></span>

                                            <!-- Badge Kategori KIB E -->
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border"
                                                  :class="{
                                                      'bg-amber-500/15 text-amber-300 border-amber-500/30': (lItem.kib_e_type || 'buku') === 'buku',
                                                      'bg-purple-500/15 text-purple-300 border-purple-500/30': lItem.kib_e_type === 'kesenian',
                                                      'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': lItem.kib_e_type === 'hewan_tumbuhan'
                                                  }"
                                                  x-text="lItem.kib_e_type === 'kesenian' ? '🎨 Kesenian' : (lItem.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan/Tanaman' : '📚 Buku Pustaka')"></span>

                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                  x-text="(lItem.lainnya_jumlah || 1) + ' ' + (lItem.lainnya_satuan || 'Buah')"></span>

                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                  :class="getRincianKondisiStats(selectedAstapDetail, lIdx, 'lainnya_items').badge_class">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, lIdx, 'lainnya_items').dot_class"></span>
                                                <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, lIdx, 'lainnya_items').text"></span>
                                            </span>

                                            <template x-if="getRincianNibar(selectedAstapDetail, lIdx, 'lainnya_items')">
                                                <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                      :title="getRincianNibar(selectedAstapDetail, lIdx, 'lainnya_items').tooltip"
                                                      x-text="getRincianNibar(selectedAstapDetail, lIdx, 'lainnya_items').label"></span>
                                            </template>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Subtotal: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + (Number(lItem.lainnya_jumlah || 1) * Number(lItem.lainnya_nilai_satuan || 0)).toLocaleString('id-ID')"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Judul / Uraian Rincian</span>
                                            <span class="text-purple-300 font-bold block truncate" :title="lItem.lainnya_judul" x-text="lItem.lainnya_judul || selectedAstapDetail.nama_barang || '-'"></span>
                                            <span class="text-slate-400 text-[10px]" x-text="'Pencipta/Brand: ' + (lItem.lainnya_pencipta || '-')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🧪 Bahan &amp; Ukuran</span>
                                            <span class="text-cyan-300 font-semibold block truncate" x-text="'Bhn: ' + (lItem.lainnya_bahan || '-')"></span>
                                            <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Uk: ' + (lItem.lainnya_ukuran || '-') + (lItem.lainnya_tahun ? (' • Thn: ' + lItem.lainnya_tahun) : '')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Wajar Satuan</span>
                                            <span class="text-emerald-300 font-mono font-bold block text-[11px]" x-text="'Rp ' + Number(lItem.lainnya_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                            <span class="text-[9.5px]" :class="lItem.is_extracom ? 'text-amber-400 font-medium' : 'text-slate-400'" x-text="lItem.is_extracom ? 'Maksimal Rp 300.000' : 'Aset Tetap Intrakomptabel'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- PIHAK REKANAN MITRA & PEJABAT PEMBUAT KOMITMEN (PPK RSUD) -->
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <h4 class="text-xs font-extrabold text-teal-400 uppercase tracking-wider flex items-center space-x-1.5">
                            <span>🏢 Pihak Rekanan (Mitra) &amp; Pejabat Pembuat Komitmen (PPK RSUD)</span>
                        </h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400">Legalitas Para Pihak</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-[11px]">
                        <!-- Card Pihak Mitra Rekanan -->
                        <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-2.5">
                            <span class="text-[10.5px] font-extrabold text-cyan-400 uppercase tracking-wider block flex items-center space-x-1">
                                <span>🏢 Informasi Mitra Pihak Ketiga</span>
                            </span>
                            <div class="space-y-1.5 divide-y divide-slate-800/60">
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-slate-400">Nama Perusahaan / Rekanan:</span>
                                    <strong class="text-white font-bold truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_nama" x-text="selectedAstapDetail.penyedia_nama || '-'"></strong>
                                </div>
                                <div class="flex items-center justify-between pt-1.5">
                                    <span class="text-slate-400">Pejabat Mitra / Direktur:</span>
                                    <strong class="text-cyan-300 font-bold truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_pemilik" x-text="selectedAstapDetail.penyedia_pemilik || '-'"></strong>
                                </div>
                                <div class="flex items-center justify-between pt-1.5">
                                    <span class="text-slate-400">No. HP / Kontak Aktif:</span>
                                    <span class="text-amber-400 font-mono font-bold truncate max-w-[55%]" x-text="selectedAstapDetail.penyedia_telepon || '-'"></span>
                                </div>
                                <div class="flex items-start justify-between pt-1.5">
                                    <span class="text-slate-400 shrink-0">Alamat Domisili:</span>
                                    <span class="text-teal-300 font-medium text-right truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_alamat" x-text="selectedAstapDetail.penyedia_alamat || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Pejabat Pembuat Komitmen (Kolom 24 & Kolom 25) -->
                        <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-2.5">
                            <span class="text-[10.5px] font-extrabold text-amber-400 uppercase tracking-wider block flex items-center space-x-1">
                                <span>👔 Pejabat Pembuat Komitmen (PPK RSUD)</span>
                            </span>
                            <div class="space-y-1.5 divide-y divide-slate-800/60">
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-slate-400">Nama PPK (Kolom 24):</span>
                                    <strong class="text-white font-bold truncate max-w-[55%]" :title="selectedAstapDetail.ppk_nama" x-text="selectedAstapDetail.ppk_nama || '-'"></strong>
                                </div>
                                <div class="flex items-center justify-between pt-1.5">
                                    <span class="text-slate-400">NIP PPK (Kolom 25):</span>
                                    <span class="text-cyan-300 font-mono font-bold truncate max-w-[55%]" x-text="selectedAstapDetail.ppk_nip || '-'"></span>
                                </div>
                                <div class="flex items-start justify-between pt-1.5">
                                    <span class="text-slate-400 shrink-0">Keterangan / Catatan PKS:</span>
                                    <span class="text-slate-300 italic text-right truncate max-w-[55%]" :title="selectedAstapDetail.keterangan" x-text="selectedAstapDetail.keterangan || '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL RINCIAN REGISTER NIBAR PER-UNIT (FULLY RESPONSIVE SCROLL) -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-800 pb-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider">🏷️ RINCIAN NIBAR &amp; PENEMPATAN RUANGAN (REGISTER):</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Daftar unik kode NIBAR per-aset kemitraan beserta lokasi penempatannya.</p>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                            <button type="button" @click="resequenceSingleAstap(selectedAstapDetail)"
                                class="px-2.5 py-1 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-[10.5px] font-bold transition-all flex items-center space-x-1 cursor-pointer shadow-sm active:scale-95"
                                title="Urutkan ulang register NIBAR barang ini agar berurutan tanpa celah">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Rapikan NIBAR Barang Ini</span>
                            </button>
                            @endif
                            <span class="text-[10px] font-extrabold px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono shadow-sm"
                                  x-text="filteredRegisters.length + ' / ' + (selectedAstapDetail.registers ? selectedAstapDetail.registers.length : 0) + ' Aset'"></span>
                        </div>
                    </div>

                    <!-- Filter Bar Interaktif Rincian Modal -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 bg-slate-900/70 p-3 rounded-xl border border-slate-800">
                        <div>
                            <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Penempatan</label>
                            <select x-model="detailPenempatanFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                <option value="all">Semua Penempatan</option>
                                <option value="sudah">📍 Sudah Ditempatkan</option>
                                <option value="belum">⚠️ Belum Ditempatkan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Aset</label>
                            <select x-model="detailKondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                <option value="all">Semua Kondisi</option>
                                <option value="Baik">Baik (B)</option>
                                <option value="Kurang Baik">Kurang Baik (KB)</option>
                                <option value="Rusak Berat">Rusak Berat (RB)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari NIBAR / Ruangan</label>
                            <input type="text" x-model="detailSearchQuery" placeholder="Cari NIBAR / No Reg / Ruang..."
                                   class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <!-- Scrollable Table -->
                    <div class="rounded-xl border border-slate-800/80 custom-scrollbar" style="max-height: 285px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full text-left text-[11px] text-slate-300 min-w-[640px]">
                            <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                                <tr>
                                    <th class="px-3.5 py-2.5 text-center">NIBAR &amp; No. Register Resmi</th>
                                    <th class="px-3.5 py-2.5 text-center">Lokasi / Pengelolaan</th>
                                    <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                    <th class="px-3.5 py-2.5 text-center whitespace-nowrap">QR Code</th>
                                    <th class="px-3.5 py-2.5 text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                <template x-for="reg in filteredRegisters" :key="reg.id">
                                    <tr class="hover:bg-slate-800/60 transition-colors">
                                        <td class="px-3.5 py-2.5 font-mono font-bold text-emerald-400 whitespace-nowrap text-center" x-text="reg.nibar || reg.no_register"></td>
                                        <td class="px-3.5 py-2.5 text-center">
                                            <template x-if="reg.ruang_pemegang">
                                                <span class="inline-flex items-center space-x-1.5 text-slate-200 font-semibold justify-center">
                                                    <span class="text-teal-400 text-xs">📍</span>
                                                    <span x-text="reg.ruang_pemegang"></span>
                                                </span>
                                            </template>
                                            <template x-if="!reg.ruang_pemegang">
                                                <span class="inline-flex items-center space-x-1.5 text-cyan-400 font-bold bg-cyan-500/10 px-2.5 py-1 rounded-lg border border-cyan-500/30 text-[10px] justify-center">
                                                    <span>🤝</span>
                                                    <span x-text="selectedAstapDetail?.kemitraan?.mitra_nama ? ('Mitra: ' + selectedAstapDetail.kemitraan.mitra_nama) : 'Konsesi Kemitraan (Mitra)'"></span>
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-bold border inline-block shadow-sm"
                                                  :class="{
                                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': reg.kondisi === 'Baik' || reg.kondisi === 'B',
                                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': reg.kondisi === 'Kurang Baik' || reg.kondisi === 'KB' || reg.kondisi === 'Rusak Ringan' || reg.kondisi === 'RR',
                                                      'bg-rose-500/20 text-rose-300 border-rose-500/30': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'RB' || reg.kondisi === 'Rusak'
                                                  }" x-text="reg.kondisi === 'B' ? 'Baik' : ((reg.kondisi === 'KB' || reg.kondisi === 'RR' || reg.kondisi === 'Rusak Ringan') ? 'Kurang Baik' : ((reg.kondisi === 'RB' || reg.kondisi === 'Rusak') ? 'Rusak Berat' : (reg.kondisi || 'Baik')))"></span>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                            <button type="button" @click.stop="downloadQrCodeNibar(reg, selectedAstapDetail)"
                                                title="Pratinjau & Download QR NIBAR Aset Ini"
                                                class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/25 border border-cyan-500/30 hover:border-cyan-400 text-cyan-400 hover:text-cyan-300 font-bold text-[10.5px] transition-all shadow-sm active:scale-95 group cursor-pointer leading-none">
                                                <svg class="w-3.5 h-3.5 text-cyan-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                <span class="leading-none pt-0.5">Download QR</span>
                                            </button>
                                        </td>
                                        <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center space-x-1.5">
                                                <!-- 1. Tombol Cek Riwayat Aset (Mutasi) -->
                                                <button type="button" @click.stop="openRiwayatModal(reg)" title="Cek Riwayat Mutasi Aset Ini"
                                                        class="p-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:text-purple-300 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>

                                                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                                <!-- 2. Tombol Ubah Kondisi Barang (Modal Khusus) -->
                                                <button type="button" @click.stop="openEditKondisiModal(reg)" title="Ubah Kondisi Aset Ini"
                                                        class="p-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                </button>

                                                <!-- 3. Tombol Hapus Register -->
                                                <button type="button" @click.stop="deleteRegister(reg)" title="Hapus Aset Register Ini"
                                                        class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredRegisters.length === 0">
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-slate-500 italic text-xs">
                                            Tidak ditemukan rincian register NIBAR yang sesuai dengan filter pencarian.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- FORM PEMBARUAN STATUS KONSESI & MATRIKS REKLASIFIKASI -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-cyan-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-cyan-300 uppercase tracking-wider">
                            ⚙️ Perbarui Status Konsesi Kerja Sama
                        </span>
                        <span class="text-[10px] text-slate-400">Pilih status terkini untuk pembukuan neraca</span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                        <div class="flex-1">
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Pilih Status Konsesi:</label>
                            <select x-model="statusForm.status_konsesi"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-colors">
                                <option value="Aktif">🟢 Aktif</option>
                                <option value="Konsesi Berakhir">🟠 Konsesi Berakhir</option>
                                <option value="Selesai">🔵 Selesai</option>
                                <option value="Dihentikan">🔴 Dihentikan</option>
                            </select>
                        </div>

                        <div class="shrink-0 flex items-center">
                            <button type="button" @click="saveStatusUpdate()" :disabled="isUpdatingStatus"
                                class="w-full sm:w-auto px-5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition-all flex items-center justify-center gap-1.5 shadow-md shadow-cyan-500/20 disabled:opacity-50 cursor-pointer">
                                <span x-show="!isUpdatingStatus">Simpan Status</span>
                                <span x-show="isUpdatingStatus">Menyimpan...</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-1">
                        <a href="{{ route('master.reklasifikasi') }}"
                            class="text-[11px] text-cyan-400 hover:text-cyan-300 transition-colors inline-flex items-center gap-1">
                            <span>Lihat Matriks Reklasifikasi Neraca &rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Catatan / Keterangan Tambahan -->
                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1" x-show="selectedAstapDetail.keterangan && selectedAstapDetail.keterangan !== '-'">
                    <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider">💡 Keterangan &amp; Catatan Tambahan:</span>
                    <p class="text-slate-200 text-xs leading-relaxed" x-text="selectedAstapDetail.keterangan"></p>
                </div>

            </div>
        </template>

        <!-- Modal Footer -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-between flex-wrap gap-2.5">
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Tombol Cetak Dokumen Resmi BAST Pemanfaatan / Penambahan Kemitraan -->
                <template x-if="selectedAstapDetail && (selectedAstapDetail.kemitraan_id || selectedAstapDetail.id)">
                    <button type="button" @click="showDetailModal = false; openPrintBast(selectedAstapDetail, selectedAstapDetail.is_dimanfaatkan)"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black text-xs transition-all shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 hover:-translate-y-0.5 active:scale-95 flex items-center gap-2 cursor-pointer"
                        title="Buka Dokumen Resmi BAST Kemitraan (Format Kedinasan A4)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Resmi BAST</span>
                    </button>
                </template>

                <template x-if="selectedAstapDetail && selectedAstapDetail.dokumen_path">
                    <a :href="'/storage/' + selectedAstapDetail.dokumen_path" target="_blank"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-400 hover:to-teal-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-cyan-500/25 active:scale-95 flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Buka Berkas Scan BAST</span>
                        <span>↗</span>
                    </a>
                </template>
            </div>
            <div class="flex items-center space-x-2.5">
                <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-xs transition-all shadow-md active:scale-95 cursor-pointer">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</template>

<!-- ========================================================================= -->
<!-- MODAL LIGHTBOX PRATINJAU GAMBAR DOKUMEN BAST                             -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showDokumenImageModal" x-cloak @click.self="showDokumenImageModal = false"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-6 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.92); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="relative bg-slate-900 border border-cyan-500/40 rounded-3xl max-w-4xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[92vh] my-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <!-- Header Lightbox -->
            <div class="px-5 py-3.5 bg-slate-950/80 border-b border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-300 flex items-center justify-center text-sm border border-cyan-500/30">
                        🖼️
                    </span>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 block">
                            Pratinjau Berkas BAST / PKS
                        </span>
                        <h4 class="text-xs font-extrabold text-white truncate" x-text="previewDokumenNama"></h4>
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <!-- Tombol Buka Penuh / Tab Baru -->
                    <a :href="previewDokumenUrl" target="_blank"
                       class="px-2.5 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all flex items-center gap-1 border border-slate-700">
                        <span>Buka Penuh</span>
                        <span>↗</span>
                    </a>
                    <!-- Tombol Tutup -->
                    <button type="button" @click="showDokumenImageModal = false"
                            class="w-7 h-7 rounded-full bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Body Gambar Penuh -->
            <div class="flex-1 overflow-auto p-4 flex items-center justify-center bg-slate-950/60 custom-scrollbar min-h-[300px]">
                <img :src="previewDokumenUrl" :alt="previewDokumenNama"
                     class="max-w-full max-h-[72vh] object-contain rounded-xl shadow-2xl border border-slate-800/80">
            </div>

            <!-- Footer Lightbox -->
            <div class="px-5 py-3 bg-slate-950/80 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                <span class="text-[11px] font-mono text-cyan-400 truncate" x-text="previewDokumenNama"></span>
                <button type="button" @click="showDokumenImageModal = false"
                        class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-all cursor-pointer">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>
</template>
