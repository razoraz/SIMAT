<!-- ========================================================================= -->
<!-- MODAL DETAIL ASET HIBAH & RINCIAN REGISTER NIBAR (BESPOKE AMBER LUXURY)     -->
<!-- Identik dengan layout Kemitraan dengan Tema Warm Amber / Gold Hibah       -->
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
    
    <div class="border border-slate-800 rounded-3xl max-w-5xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto bg-slate-900">
        
        <!-- ===================================================================== -->
        <!-- 1. MODAL HEADER                                                       -->
        <!-- ===================================================================== -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="space-y-2 min-w-0 flex-1">
                <!-- Badges & Chips Row -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Kategori KIB Badge -->
                    <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0"
                        :class="{
                            'bg-amber-500/20 text-amber-300 border-amber-500/30': getEffectiveKibCategory(selectedDetail) === 'KIB A',
                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     getEffectiveKibCategory(selectedDetail) === 'KIB B',
                            'bg-purple-500/20 text-purple-300 border-purple-500/30': getEffectiveKibCategory(selectedDetail) === 'KIB C',
                            'bg-teal-500/20 text-teal-300 border-teal-500/30':     getEffectiveKibCategory(selectedDetail) === 'KIB D',
                            'bg-orange-500/20 text-orange-300 border-orange-500/30': getEffectiveKibCategory(selectedDetail) === 'KIB E',
                            'bg-blue-500/20 text-blue-300 border-blue-500/30':     getEffectiveKibCategory(selectedDetail) === 'KIB F' || getEffectiveKibCategory(selectedDetail) === 'ATB'
                        }"
                        x-text="getEffectiveKibCategory(selectedDetail)"></span>

                    <!-- Badge Tipe Hibah Masuk vs Keluar -->
                    <template x-if="selectedDetail?.tipe_hibah === 'masuk'">
                        <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0 bg-amber-500/20 text-amber-300 border-amber-500/40 shadow-sm flex items-center gap-1.5">
                            <span>🎁</span>
                            <span>HIBAH MASUK (BERTAMBAH)</span>
                        </span>
                    </template>
                    <template x-if="selectedDetail?.tipe_hibah === 'keluar'">
                        <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0 bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-sm flex items-center gap-1.5">
                            <span>📤</span>
                            <span>HIBAH KELUAR (PENGURANGAN AT)</span>
                        </span>
                    </template>

                    <!-- Kode 108 -->
                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full shadow-inner"
                        x-text="'Kode: ' + (selectedDetail?.astap?.kode_108 || selectedDetail?.kode_108 || '-')"></span>

                    <!-- Tanggal BAST -->
                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0 shadow-inner">
                        <span class="text-slate-400">📅 BAST:</span>
                        <span class="text-amber-300 font-bold" x-text="formatTanggalIndo(selectedDetail?.tanggal_bast || selectedDetail?.created_at)"></span>
                    </span>

                    <!-- Status Kondisi Keseluruhan -->
                    <template x-if="selectedDetail">
                        <span class="px-2.5 py-1 rounded-lg border text-[11px] font-bold flex items-center space-x-1.5 shrink-0 shadow-sm"
                              :class="getKondisiStats(selectedDetail).badge_class">
                            <span class="w-1.5 h-1.5 rounded-full" :class="getKondisiStats(selectedDetail).dot_class"></span>
                            <span x-text="'Kondisi: ' + getKondisiStats(selectedDetail).text"></span>
                        </span>
                    </template>
                </div>

                <!-- Title & Meta Subtitle -->
                <div class="pt-1 space-y-1">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-black text-white leading-tight tracking-tight break-words" 
                        x-text="selectedDetail ? (selectedDetail.astap ? selectedDetail.astap.nama_barang : (selectedDetail.nama_barang || 'Aset Hibah')) : ''"></h3>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                        <span class="text-amber-400 font-semibold" 
                              x-text="selectedDetail?.astap?.jenis_astap?.nama_jenis || selectedDetail?.astap?.category || 'Aset Hibah (Non-Belanja Modal)'"></span>
                        
                        <div class="flex items-center gap-1 text-slate-300">
                            <span class="text-slate-600">•</span>
                            <span class="text-teal-400">📍</span>
                            <span class="truncate max-w-md" x-text="selectedDetail?.astap?.unit?.nama || getRuangLabel(selectedDetail)"></span>
                        </div>

                        <template x-if="selectedDetail?.pihak_hibah">
                            <div class="flex items-center gap-1 text-slate-300">
                                <span class="text-slate-600">•</span>
                                <span class="text-slate-400" x-text="selectedDetail.tipe_hibah === 'masuk' ? 'Pemberi:' : 'Penerima:'"></span>
                                <span class="text-amber-300 font-bold" x-text="selectedDetail.pihak_hibah"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tombol Close -->
            <button type="button" @click="showModalDetail = false" 
                class="w-9 h-9 rounded-2xl bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-slate-700/60 hover:border-rose-500/30 flex items-center justify-center transition-all shrink-0 cursor-pointer shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <template x-if="selectedDetail">
            <div class="space-y-4 text-xs text-slate-300">

                <!-- ===================================================================== -->
                <!-- 2. TOP 4 METRIC KPI CARDS (Fully Responsive Grid)                     -->
                <!-- ===================================================================== -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1">🏷️ Klasifikasi PMDN 108</span>
                        <span class="text-white font-bold text-xs sm:text-[13px] leading-snug break-words line-clamp-2 block" 
                              :title="selectedDetail.astap?.jenis_astap?.nama_jenis || selectedDetail.astap?.category || 'Aset BMD'" 
                              x-text="selectedDetail.astap?.jenis_astap?.nama_jenis || selectedDetail.astap?.category || 'ASET BMD'"></span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1">📅 Periode Pembukuan</span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-amber-300 font-black font-mono text-sm sm:text-base tracking-tight" x-text="selectedDetail.tahun"></span>
                            <span class="text-slate-500 font-mono text-xs">•</span>
                            <span class="px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/30 font-mono font-bold text-[11px]" 
                                  x-text="selectedDetail.triwulan || 'TW I'"></span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1">📏 Volume Fisik</span>
                        <span class="text-teal-300 font-black font-mono text-sm sm:text-base block truncate tracking-tight" 
                              x-text="(selectedDetail.jumlah_volume || 1) + ' ' + (selectedDetail.satuan || 'Unit')"></span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1">💰 Taksiran Nilai Wajar</span>
                        <span class="font-black font-mono text-sm sm:text-base block truncate tracking-tight drop-shadow-sm" 
                              :class="selectedDetail.tipe_hibah === 'masuk' ? 'text-amber-400' : 'text-rose-400'"
                              x-text="'Rp ' + formatRupiah(selectedDetail.nilai_aset)"></span>
                    </div>
                </div>

                <!-- ===================================================================== -->
                <!-- 3. DOKUMEN LEGALITAS BAST & LEMBAR PERIKATAN HIBAH (Full-Width Card) -->
                <!-- ===================================================================== -->
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-amber-950/25 border border-amber-500/30 space-y-4 shadow-xl">
                    <!-- Header Section BAST -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-amber-500/20 gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center justify-center text-lg shrink-0 shadow-inner">
                                📜
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-black uppercase tracking-wider text-amber-300">
                                    Dokumen Berita Acara Serah Terima (BAST) &amp; Legalitas Hibah
                                </h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Dasar hukum legalitas mutasi perolehan aset hibah dan naskah serah terima resmi.
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge & Tombol Cetak BAST Cepat -->
                        <div class="flex items-center gap-2 flex-wrap shrink-0">
                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold border inline-flex items-center gap-1.5 shadow-sm"
                                  :class="selectedDetail.tipe_hibah === 'masuk' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40'">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="selectedDetail.tipe_hibah === 'masuk' ? 'bg-amber-400 animate-pulse' : 'bg-rose-400'"></span>
                                <span x-text="selectedDetail.tipe_hibah === 'masuk' ? 'Hibah Masuk (Non-APBD)' : 'Hibah Keluar (Pengurangan)'"></span>
                            </span>

                            <button type="button" @click="showModalDetail = false; openPrintBast(selectedDetail);"
                                class="px-3 py-1.5 rounded-xl text-[10.5px] font-extrabold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 border border-amber-400/40 flex items-center gap-1.5 transition-all active:scale-95 shadow-md shadow-amber-500/20 cursor-pointer"
                                title="Cetak Format Lembar BAST Resmi">
                                <span>🖨️</span>
                                <span>Cetak BAST</span>
                            </button>
                        </div>
                    </div>

                    <!-- 4 Kolom Rincian BAST (Grid Cards) -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1">Nomor BAST / NPHD:</span>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-amber-300 font-bold text-xs sm:text-sm block truncate select-all" 
                                      :title="selectedDetail.nomor_bast" 
                                      x-text="selectedDetail.nomor_bast || '-'"></span>
                                <template x-if="selectedDetail.nomor_bast && selectedDetail.nomor_bast !== '-'">
                                    <button type="button" @click.stop="navigator.clipboard.writeText(selectedDetail.nomor_bast); showToast('Nomor BAST berhasil disalin!', 'success')"
                                            class="p-1 rounded text-slate-500 hover:text-amber-300 hover:bg-slate-800 transition-colors cursor-pointer" title="Salin Nomor BAST">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1">Tanggal BAST:</span>
                            <span class="text-white font-bold text-xs sm:text-sm block" x-text="formatTanggalIndo(selectedDetail.tanggal_bast) || '-'"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1" x-text="selectedDetail.tipe_hibah === 'masuk' ? 'Instansi Pemberi:' : 'Pihak Penerima:'"></span>
                            <span class="text-white font-bold text-xs sm:text-sm block truncate" :title="selectedDetail.pihak_hibah" x-text="selectedDetail.pihak_hibah || '-'"></span>
                            <template x-if="getSpecDetail(selectedDetail).hibah_pimpinan">
                                <span class="text-[10px] text-amber-300/80 block truncate mt-0.5" :title="getSpecDetail(selectedDetail).hibah_pimpinan" x-text="getSpecDetail(selectedDetail).hibah_pimpinan"></span>
                            </template>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1">Alamat Instansi Pemberi:</span>
                            <span class="text-slate-300 text-[11px] sm:text-xs block leading-tight truncate" 
                                  :title="getSpecDetail(selectedDetail).hibah_alamat_pemberi || 'Alamat domisili instansi pemberi'" 
                                  x-text="getSpecDetail(selectedDetail).hibah_alamat_pemberi || '-'"></span>
                        </div>
                    </div>

                    <!-- KARTU ARSIP SCAN DOKUMEN BAST (PRATINJAU & UNDUH) - FULL WIDTH -->
                    <div class="w-full pt-2 border-t border-amber-500/20">
                        <div class="w-full p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border border-amber-500/30 shadow-md">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3.5">
                                <div class="flex items-start sm:items-center space-x-3.5 min-w-0 flex-1">
                                    <!-- Thumbnail / Ikon Dokumen -->
                                    <div class="relative shrink-0">
                                        <template x-if="isDokumenImage(getSpecDetail(selectedDetail).dokumen_path)">
                                            <div @click="window.open('/storage/' + getSpecDetail(selectedDetail).dokumen_path, '_blank')"
                                                 class="w-12 h-12 rounded-xl border border-amber-500/40 bg-slate-900 overflow-hidden cursor-pointer group shadow-sm flex items-center justify-center relative hover:ring-2 hover:ring-amber-400/50 transition-all">
                                                <img :src="'/storage/' + getSpecDetail(selectedDetail).dokumen_path" alt="Thumbnail BAST" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                <div class="absolute inset-0 bg-slate-950/40 group-hover:bg-slate-950/10 flex items-center justify-center transition-colors">
                                                    <span class="text-xs">🔍</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!isDokumenImage(getSpecDetail(selectedDetail).dokumen_path) && getSpecDetail(selectedDetail).dokumen_path">
                                            <div class="w-12 h-12 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 flex items-center justify-center text-xl shadow-inner">
                                                📄
                                            </div>
                                        </template>
                                        <template x-if="!getSpecDetail(selectedDetail).dokumen_path">
                                            <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center text-xl">
                                                📁
                                            </div>
                                        </template>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center flex-wrap gap-2">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-400">
                                                Arsip Scan Dokumen Sah (BAST / NPHD Bertanda Tangan)
                                            </span>
                                            <template x-if="getSpecDetail(selectedDetail).dokumen_path">
                                                <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1 shadow-sm">
                                                    <span>✓</span> Dokumen Sah Terlampir
                                                </span>
                                            </template>
                                            <template x-if="!getSpecDetail(selectedDetail).dokumen_path">
                                                <span class="px-2 py-0.5 rounded text-[9.5px] font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                    Belum Ada Arsip Scan (Menyusul)
                                                </span>
                                            </template>
                                        </div>

                                        <!-- Title or Filename -->
                                        <div class="text-xs font-bold text-white mt-1 break-words"
                                             x-text="getSpecDetail(selectedDetail).dokumen_path ? getSpecDetail(selectedDetail).dokumen_path.split('/').pop() : 'Berkas Scan NPHD / BAST Hibah Belum Dilampirkan'">
                                        </div>

                                        <!-- Subtext / Helper description -->
                                        <div class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                                            <template x-if="getSpecDetail(selectedDetail).dokumen_path">
                                                <span>Format: <strong class="font-mono text-amber-300 uppercase" x-text="getSpecDetail(selectedDetail).dokumen_path.split('.').pop()"></strong> · Berkas arsip legalitas serah terima hibah RSUD.</span>
                                            </template>
                                            <template x-if="!getSpecDetail(selectedDetail).dokumen_path">
                                                <span>Dapat diunggah sewaktu-waktu melalui tombol Ubah setelah naskah BAST selesai ditandatangani basah oleh Direktur/PPK RSUD dan Instansi Pemberi.</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="flex items-center flex-wrap gap-2 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-800/80">
                                    <template x-if="getSpecDetail(selectedDetail).dokumen_path">
                                        <a :href="'/storage/' + getSpecDetail(selectedDetail).dokumen_path" target="_blank"
                                           class="px-3.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Lihat Dokumen Sah</span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU INFORMASI PARA PIHAK: PEMBERI HIBAH & PPK RSUD (GAMBAR 2 ATAS) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5 text-[11px] pt-1">
                        <!-- Card Pihak Pemberi Hibah -->
                        <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-3">
                            <span class="text-[10.5px] font-extrabold text-amber-400 uppercase tracking-wider block flex items-center space-x-1.5">
                                <span>🏢 Informasi Instansi Pemberi Hibah</span>
                            </span>
                            <div class="space-y-2 divide-y divide-slate-800/60">
                                <div class="flex items-start justify-between gap-2 pt-1">
                                    <span class="text-slate-400 shrink-0">Nama Lembaga / Instansi:</span>
                                    <strong class="text-white font-bold text-right" :title="selectedDetail.pihak_hibah" x-text="selectedDetail.pihak_hibah || '-'"></strong>
                                </div>
                                <div class="flex items-start justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">Pimpinan / Direktur Instansi:</span>
                                    <strong class="text-amber-300 font-bold text-right" :title="getSpecDetail(selectedDetail).hibah_pimpinan" x-text="getSpecDetail(selectedDetail).hibah_pimpinan || '-'"></strong>
                                </div>
                                <div class="flex items-start justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">Alamat Kantor Pemberi:</span>
                                    <span class="text-teal-300 font-medium text-right leading-relaxed" :title="getSpecDetail(selectedDetail).hibah_alamat_pemberi" x-text="getSpecDetail(selectedDetail).hibah_alamat_pemberi || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Pejabat Pembuat Komitmen (Kolom 24 & Kolom 25) -->
                        <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-3">
                            <span class="text-[10.5px] font-extrabold text-amber-400 uppercase tracking-wider block flex items-center space-x-1.5">
                                <span>👔 Pejabat Pembuat Komitmen (PPK RSUD)</span>
                            </span>
                            <div class="space-y-2 divide-y divide-slate-800/60">
                                <div class="flex items-start justify-between gap-2 pt-1">
                                    <span class="text-slate-400 shrink-0">Nama PPK (Kolom 24):</span>
                                    <strong class="text-white font-bold text-right" :title="selectedDetail.astap?.ppk_nama || selectedDetail.astap?.spesifikasi_json?.ppk_nama" x-text="selectedDetail.astap?.ppk_nama || selectedDetail.astap?.spesifikasi_json?.ppk_nama || 'BUDI HARTONO, S.Sos'"></strong>
                                </div>
                                <div class="flex items-center justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">NIP PPK (Kolom 25):</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-cyan-300 font-mono font-bold select-all" x-text="selectedDetail.astap?.ppk_nip || selectedDetail.astap?.spesifikasi_json?.ppk_nip || '19760229 200801 1 010'"></span>
                                        <button type="button" @click.stop="navigator.clipboard.writeText(selectedDetail.astap?.ppk_nip || selectedDetail.astap?.spesifikasi_json?.ppk_nip || '19760229 200801 1 010'); showToast('NIP PPK disalin!', 'success')"
                                                class="p-0.5 text-slate-500 hover:text-amber-300 transition-colors cursor-pointer" title="Salin NIP">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-start justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">Keterangan / Catatan Hibah:</span>
                                    <span class="text-slate-300 italic text-right leading-relaxed" :title="selectedDetail.keterangan || selectedDetail.astap?.keterangan_tambahan" x-text="selectedDetail.keterangan || selectedDetail.astap?.keterangan_tambahan || '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===================================================================== -->
                <!-- 4. SPESIFIKASI TEKNIS FISIK BARANG (PMDN 108)                         -->
                <!-- ===================================================================== -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-4 shadow-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 gap-2 flex-wrap">
                        <h4 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider flex items-center space-x-2"
                            :class="{
                                'text-amber-400': getEffectiveKibCategory(selectedDetail) === 'KIB A',
                                'text-cyan-400':  getEffectiveKibCategory(selectedDetail) === 'KIB B',
                                'text-purple-400': getEffectiveKibCategory(selectedDetail) === 'KIB C',
                                'text-teal-400':  getEffectiveKibCategory(selectedDetail) === 'KIB D',
                                'text-orange-400': getEffectiveKibCategory(selectedDetail) === 'KIB E'
                            }">
                            <span>🔍 RINCIAN SPESIFIKASI TEKNIS ASET (<span x-text="getEffectiveKibCategory(selectedDetail)"></span>)</span>
                        </h4>
                        <div class="flex items-center gap-2">
                            <!-- Badge Ekstrakomtabel jika ada -->
                            <template x-if="selectedDetail.astap?.is_extracomtable || getSpecDetail(selectedDetail).is_extracom">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10.5px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm flex items-center gap-1">
                                    <span>📦</span>
                                    <span>EKSTRAKOMTABEL</span>
                                </span>
                            </template>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400" x-text="'Spesifikasi Khusus ' + getEffectiveKibCategory(selectedDetail)"></span>
                        </div>
                    </div>

                    <!-- 1. KIB A (TANAH) -->
                    <template x-if="getEffectiveKibCategory(selectedDetail) === 'KIB A'">
                        <div class="space-y-2.5">
                            <template x-if="getTanahItemsForDetail(selectedDetail).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(tItem, tIdx) in getTanahItemsForDetail(selectedDetail)" :key="tIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                        🌾 Bidang Tanah #<span x-text="tIdx + 1"></span>
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                          x-text="(tItem.tanah_jumlah_bidang || 1) + ' ' + (selectedDetail.satuan || selectedDetail.astap?.satuan || 'Bidang')"></span>
                                                    <template x-if="getRincianNibar(selectedDetail, tIdx, 'tanah_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedDetail, tIdx, 'tanah_items').tooltip"
                                                              x-text="getRincianNibar(selectedDetail, tIdx, 'tanah_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Total Taksiran: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(getTanahItemNilai(tItem, selectedDetail)).toLocaleString('id-ID')"></strong>
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
                                                    <span class="font-semibold text-[10px] inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md border" :class="getRincianKondisiStats(selectedDetail, tIdx, 'tanah_items').badge_class">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedDetail, tIdx, 'tanah_items').dot_class"></span>
                                                        <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedDetail, tIdx, 'tanah_items').text"></span>
                                                    </span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Taksiran Nilai Wajar</span>
                                                    <span class="text-emerald-400 font-mono font-bold block text-[10.5px]" x-text="'Rp ' + Number(getTanahItemNilai(tItem, selectedDetail)).toLocaleString('id-ID')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Lokasi</span>
                                                    <span class="text-teal-300 font-medium block truncate" :title="tItem.tanah_alamat" x-text="tItem.tanah_alamat || selectedDetail.alamat_barang || selectedDetail.astap?.alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback data tanah tunggal / legacy -->
                            <template x-if="getTanahItemsForDetail(selectedDetail).length === 0">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                🌾 Bidang Tanah #1
                                            </span>
                                            <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                  x-text="(selectedDetail.jumlah_volume || selectedDetail.astap?.jumlah_volume || 1) + ' ' + (selectedDetail.satuan || selectedDetail.astap?.satuan || 'Bidang')"></span>
                                            <template x-if="getRincianNibar(selectedDetail, 0)">
                                                <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                      :title="getRincianNibar(selectedDetail, 0).tooltip"
                                                      x-text="getRincianNibar(selectedDetail, 0).label"></span>
                                            </template>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Total Taksiran: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(selectedDetail.total_nilai || selectedDetail.astap?.nilai_aset || 0).toLocaleString('id-ID')"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📜 Hak &amp; Sertifikat</span>
                                            <span class="text-amber-300 font-bold block" x-text="getSpecDetail(selectedDetail).hak_tanah || getSpecDetail(selectedDetail).tanah_hak || 'Hak Pakai'"></span>
                                            <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="(getSpecDetail(selectedDetail).sertifikat_no || getSpecDetail(selectedDetail).tanah_sertifikat_no) ? ('No: ' + (getSpecDetail(selectedDetail).sertifikat_no || getSpecDetail(selectedDetail).tanah_sertifikat_no)) : 'Tanpa No Sertifikat'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(getSpecDetail(selectedDetail).luas_m2 || getSpecDetail(selectedDetail).tanah_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                            <span class="font-semibold text-[10px] inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md border" :class="getRincianKondisiStats(selectedDetail, 0).badge_class">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedDetail, 0).dot_class"></span>
                                                <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedDetail, 0).text"></span>
                                            </span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Taksiran Nilai Wajar</span>
                                            <span class="text-emerald-400 font-mono font-bold block text-[10.5px]" x-text="'Rp ' + Number(selectedDetail.total_nilai || selectedDetail.astap?.nilai_aset || 0).toLocaleString('id-ID')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Lokasi</span>
                                            <span class="text-teal-300 font-medium block truncate" :title="selectedDetail.alamat_barang || selectedDetail.astap?.alamat_barang" x-text="selectedDetail.alamat_barang || selectedDetail.astap?.alamat_barang || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 2. KIB B (PERALATAN & MESIN) -->
                    <template x-if="getEffectiveKibCategory(selectedDetail) === 'KIB B'">
                        <div class="space-y-3">
                            <template x-if="getMesinItemsForDetail(selectedDetail).length > 0">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                        <span class="flex items-center gap-1.5 text-cyan-400 font-bold">
                                            📦 Rincian Barang Terdaftar (<span x-text="getMesinItemsForDetail(selectedDetail).length"></span> Item)
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedDetail.jumlah_volume || selectedDetail.astap?.jumlah_volume || getMesinItemsForDetail(selectedDetail).reduce((s, m) => s + (parseFloat(m.mesin_jumlah_barang) || 1), 0)) + ' ' + (selectedDetail.satuan || selectedDetail.astap?.satuan || 'Unit')"></span>
                                    </div>
                                    <template x-for="(mItem, mIdx) in getMesinItemsForDetail(selectedDetail)" :key="mIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 space-y-2.5 shadow-sm hover:border-cyan-400/50 transition-all">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-mono font-bold text-[11px] border border-cyan-500/30"
                                                          x-text="'Item #' + (mIdx + 1)"></span>
                                                    <span class="text-white font-bold text-xs" x-text="(mItem.mesin_nama_barang ? (mItem.mesin_nama_barang + ' • ') : '') + ((mItem.mesin_merk || mItem.mesin_type) ? ((mItem.mesin_merk || '') + ' ' + (mItem.mesin_type || '')) : '-')"></span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                          x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || selectedDetail.satuan || 'Unit')"></span>
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                                          :class="(mItem.is_extracom || selectedDetail.astap?.is_extracomtable || getSpecDetail(selectedDetail).is_extracom) ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                          x-text="(mItem.is_extracom || selectedDetail.astap?.is_extracomtable || getSpecDetail(selectedDetail).is_extracom) ? '📦 Extracom' : '⚙️ Reguler'"></span>
                                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                          :class="getRincianKondisiStats(selectedDetail, mIdx, 'mesin_items').badge_class">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedDetail, mIdx, 'mesin_items').dot_class"></span>
                                                        <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedDetail, mIdx, 'mesin_items').text"></span>
                                                    </span>
                                                    <template x-if="getRincianNibar(selectedDetail, mIdx, 'mesin_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedDetail, mIdx, 'mesin_items').tooltip"
                                                              x-text="getRincianNibar(selectedDetail, mIdx, 'mesin_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Subtotal: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(mItem.mesin_jumlah_barang) || 1) * (parseFloat(mItem.mesin_nilai_satuan) || 0))).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Merk / Type / Bahan</span>
                                                    <span class="text-cyan-300 font-bold block truncate" x-text="(mItem.mesin_merk || '-') + ' / ' + (mItem.mesin_type || '-')"></span>
                                                    <span class="text-slate-300 text-[10px]" x-text="'Bhn: ' + (mItem.mesin_bahan || '-') + ' • Uk: ' + (mItem.mesin_ukuran || mItem.mesin_ukuran_cc || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔢 Identitas / No. Seri</span>
                                                    <span class="text-cyan-300 font-mono font-semibold block truncate" x-text="'Pabrik: ' + (mItem.mesin_no_pabrik || '-')"></span>
                                                    <span class="text-slate-400 font-mono text-[9px] block truncate" x-text="'Rgk: ' + (mItem.mesin_no_rangka || '-') + ' • Msn: ' + (mItem.mesin_no_mesin || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Taksiran Satuan</span>
                                                    <span class="text-emerald-300 font-medium block text-[10px]" x-text="'Rp ' + Number(mItem.mesin_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback jika single item / legacy KIB B -->
                            <template x-if="getMesinItemsForDetail(selectedDetail).length === 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Merk / Brand</span>
                                        <span class="text-white font-bold" x-text="getSpecDetail(selectedDetail).merk || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Type / Model</span>
                                        <span class="text-amber-300 font-bold font-mono" x-text="getSpecDetail(selectedDetail).type || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📏 Ukuran / Dimensi</span>
                                        <span class="text-cyan-300 font-bold" x-text="getSpecDetail(selectedDetail).ukuran || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🧪 Bahan / Material</span>
                                        <span class="text-white font-bold" x-text="getSpecDetail(selectedDetail).bahan || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🔢 No. Pabrik / Seri (SN)</span>
                                        <span class="text-cyan-300 font-mono font-bold" x-text="getSpecDetail(selectedDetail).no_pabrik || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🚗 No. Rangka / Mesin</span>
                                        <span class="text-slate-200 font-mono font-semibold" x-text="(getSpecDetail(selectedDetail).no_rangka || '-') + ' / ' + (getSpecDetail(selectedDetail).no_mesin || '-')"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 3. KIB C (GEDUNG & BANGUNAN) -->
                    <template x-if="getEffectiveKibCategory(selectedDetail) === 'KIB C'">
                        <div class="space-y-3">
                            <template x-if="getGedungItemsForDetail(selectedDetail).length > 0">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between px-1">
                                        <span class="text-[11px] font-bold text-purple-300 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>🏢 RINCIAN GEDUNG &amp; BANGUNAN:</span>
                                        </span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30" 
                                              x-text="getGedungItemsForDetail(selectedDetail).length + ' Gedung Terdaftar'"></span>
                                    </div>
                                    <template x-for="(gItem, gIdx) in getGedungItemsForDetail(selectedDetail)" :key="gIdx">
                                        <div class="p-3 rounded-2xl bg-slate-900/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-2.5">
                                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40" x-text="'Gedung #' + (gIdx + 1)"></span>
                                                    <span class="text-xs font-bold text-white" x-text="gItem.gedung_nama_barang || gItem.gedung_nama_bangunan || selectedDetail.nama_barang || selectedDetail.astap?.nama_barang"></span>
                                                    <template x-if="getRincianNibar(selectedDetail, gIdx, 'gedung_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedDetail, gIdx, 'gedung_items').tooltip"
                                                              x-text="getRincianNibar(selectedDetail, gIdx, 'gedung_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="flex items-center space-x-3 text-[10.5px] font-mono">
                                                    <span class="text-slate-400">Luas: <strong class="text-cyan-300" x-text="(gItem.gedung_luas_lantai_m2 || gItem.gedung_luas_lantai || gItem.gedung_luas_m2 || getSpecDetail(selectedDetail).gedung_luas_m2) ? ((gItem.gedung_luas_lantai_m2 || gItem.gedung_luas_lantai || gItem.gedung_luas_m2 || getSpecDetail(selectedDetail).gedung_luas_m2) + ' M²') : '-'"></strong></span>
                                                    <span class="text-emerald-400 font-bold" x-text="'Rp ' + Number(gItem.gedung_nilai_satuan || gItem.gedung_nilai_fisik || (Number(gItem.gedung_nilai_perencanaan || 0) + Number(gItem.gedung_nilai_fisik || 0) + Number(gItem.gedung_nilai_pengawasan || 0)) || gItem.nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏢 KONSTRUKSI</span>
                                                    <span class="text-purple-300 font-bold block" x-text="(gItem.gedung_konstruksi_bertingkat || gItem.gedung_bertingkat || 'Bertingkat') + ' • ' + (gItem.gedung_konstruksi_beton || gItem.gedung_beton || 'Beton')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 STATUS TANAH</span>
                                                    <span class="text-teal-300 font-semibold block truncate" x-text="gItem.gedung_status_tanah || 'Tanah Milik RSUD'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 KOMPONEN NILAI</span>
                                                    <span class="text-slate-300 block text-[9.5px]" x-text="gItem.gedung_nilai_fisik ? ('Fisik: Rp ' + Number(gItem.gedung_nilai_fisik).toLocaleString('id-ID')) : ('Taksiran: Rp ' + Number(gItem.gedung_nilai_satuan || gItem.nilai_satuan || 0).toLocaleString('id-ID'))"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 LOKASI FISIK</span>
                                                    <span class="text-emerald-300 font-medium block truncate" :title="gItem.gedung_alamat" x-text="gItem.gedung_alamat || selectedDetail.alamat_barang || selectedDetail.astap?.alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback data single gedung -->
                            <template x-if="getGedungItemsForDetail(selectedDetail).length === 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏢 Tipe Konstruksi</span>
                                        <span class="text-purple-300 font-bold" x-text="(getSpecDetail(selectedDetail).bertingkat || getSpecDetail(selectedDetail).gedung_bertingkat || 'Bertingkat') + ' • ' + (getSpecDetail(selectedDetail).beton || getSpecDetail(selectedDetail).gedung_beton || 'Beton')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Luas Lantai Gedung</span>
                                        <span class="text-white font-bold font-mono" x-text="(getSpecDetail(selectedDetail).luas_lantai_m2 || getSpecDetail(selectedDetail).gedung_luas_m2 || '-') + ' m²'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🌱 Status Penguasaan Tanah</span>
                                        <span class="text-teal-300 font-bold" x-text="getSpecDetail(selectedDetail).status_tanah || getSpecDetail(selectedDetail).gedung_status_tanah || 'Tanah Milik RSUD'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 4. KIB D (JALAN, IRIGASI DAN JARINGAN) -->
                    <template x-if="getEffectiveKibCategory(selectedDetail) === 'KIB D'">
                        <div class="space-y-3">
                            <template x-if="getJaringanItemsForDetail(selectedDetail).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(jItem, jIdx) in getJaringanItemsForDetail(selectedDetail)" :key="jIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-teal-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-teal-500/20 text-teal-300 font-mono font-bold text-[11px] border border-teal-500/30"
                                                          x-text="'Ruas #' + (jIdx + 1)"></span>
                                                    <span class="text-white font-bold text-xs" x-text="jItem.jaringan_nama_barang || selectedDetail.nama_barang || selectedDetail.astap?.nama_barang"></span>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Total Taksiran: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(jItem.jaringan_nilai_satuan || jItem.jaringan_nilai_fisik || selectedDetail.total_nilai || 0).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🛣️ Konstruksi</span>
                                                    <span class="text-teal-300 font-bold block" x-text="jItem.jaringan_konstruksi || getSpecDetail(selectedDetail).konstruksi || 'Aspal / Beton'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Dimensi / Luas</span>
                                                    <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(jItem.jaringan_luas || jItem.jaringan_luas_m2 || getSpecDetail(selectedDetail).luas_m2 || 0)).toLocaleString('id-ID') + ' m²' + (jItem.jaringan_panjang ? (' • P: ' + jItem.jaringan_panjang + (jItem.jaringan_lebar ? (' L: ' + jItem.jaringan_lebar) : '')) : '')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status Tanah</span>
                                                    <span class="text-amber-300 font-semibold block truncate" x-text="jItem.jaringan_status_tanah || getSpecDetail(selectedDetail).status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Lokasi</span>
                                                    <span class="text-teal-300 font-medium block truncate" x-text="jItem.jaringan_alamat || selectedDetail.alamat_barang || selectedDetail.astap?.alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback data single jaringan -->
                            <template x-if="getJaringanItemsForDetail(selectedDetail).length === 0">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-teal-500/30 space-y-2.5 shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-teal-500/20 text-teal-300 font-mono font-bold text-[11px] border border-teal-500/30">
                                                Ruas / Titik #1
                                            </span>
                                            <span class="text-white font-bold text-xs" x-text="selectedDetail.nama_barang || selectedDetail.astap?.nama_barang"></span>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Total Taksiran: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(selectedDetail.total_nilai || selectedDetail.astap?.nilai_aset || 0).toLocaleString('id-ID')"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🛣️ Konstruksi</span>
                                            <span class="text-teal-300 font-bold block" x-text="getSpecDetail(selectedDetail).konstruksi || 'Aspal / Beton'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Dimensi / Luas</span>
                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(getSpecDetail(selectedDetail).luas_m2 || getSpecDetail(selectedDetail).jaringan_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status Tanah</span>
                                            <span class="text-amber-300 font-semibold block truncate" x-text="getSpecDetail(selectedDetail).status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Lokasi</span>
                                            <span class="text-teal-300 font-medium block truncate" :title="selectedDetail.alamat_barang" x-text="selectedDetail.alamat_barang || selectedDetail.astap?.alamat_barang || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 5. KIB E (ASET TETAP LAINNYA) -->
                    <template x-if="getEffectiveKibCategory(selectedDetail) === 'KIB E'">
                        <div class="space-y-3">
                            <template x-if="getLainnyaItemsForDetail(selectedDetail).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(lItem, lIdx) in getLainnyaItemsForDetail(selectedDetail)" :key="lIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-purple-500/30 space-y-2.5 shadow-sm hover:border-purple-400/50 transition-all">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[11px] border border-purple-500/30"
                                                          x-text="'Item #' + (lIdx + 1)"></span>
                                                    <span class="text-white font-bold text-xs" x-text="lItem.lainnya_nama_barang || lItem.lainnya_judul || selectedDetail.nama_barang || selectedDetail.astap?.nama_barang"></span>
                                                    
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                                          :class="(lItem.is_extracom || selectedDetail.astap?.is_extracomtable || getSpecDetail(selectedDetail).is_extracom) ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                          x-text="(lItem.is_extracom || selectedDetail.astap?.is_extracomtable || getSpecDetail(selectedDetail).is_extracom) ? '📦 Extracom' : '⚙️ Reguler'"></span>

                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border"
                                                          :class="{
                                                              'bg-amber-500/15 text-amber-300 border-amber-500/30': (lItem.kib_e_type || 'buku') === 'buku',
                                                              'bg-purple-500/15 text-purple-300 border-purple-500/30': lItem.kib_e_type === 'kesenian',
                                                              'bg-teal-500/15 text-teal-300 border-teal-500/30': lItem.kib_e_type === 'hewan',
                                                              'bg-blue-500/15 text-blue-300 border-blue-500/30': lItem.kib_e_type === 'tanaman'
                                                          }"
                                                          x-text="lItem.kib_e_type === 'kesenian' ? '🎭 Kesenian/Kebudayaan' : (lItem.kib_e_type === 'hewan' ? '🐾 Hewan Ternak' : (lItem.kib_e_type === 'tanaman' ? '🌱 Tanaman/Tumbuhan' : '📚 Buku/Perpustakaan'))"></span>

                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                          x-text="(lItem.lainnya_jumlah || 1) + ' ' + (lItem.lainnya_satuan || selectedDetail.satuan || 'Buah')"></span>

                                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                          :class="getRincianKondisiStats(selectedDetail, lIdx, 'lainnya_items').badge_class">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedDetail, lIdx, 'lainnya_items').dot_class"></span>
                                                        <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedDetail, lIdx, 'lainnya_items').text"></span>
                                                    </span>
                                                    <template x-if="getRincianNibar(selectedDetail, lIdx, 'lainnya_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedDetail, lIdx, 'lainnya_items').tooltip"
                                                              x-text="getRincianNibar(selectedDetail, lIdx, 'lainnya_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Subtotal: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(lItem.lainnya_jumlah) || 1) * (parseFloat(lItem.lainnya_nilai_satuan) || 0))).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Spesifikasi / Judul</span>
                                                    <span class="text-purple-300 font-bold block truncate" x-text="lItem.lainnya_judul || lItem.lainnya_nama_barang || '-'"></span>
                                                    <span class="text-slate-300 text-[10px]" x-text="'Pencipta: ' + (lItem.lainnya_pencipta || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🧪 Bahan / Asal Spesimen</span>
                                                    <span class="text-amber-300 font-mono font-semibold block truncate" x-text="lItem.lainnya_bahan || lItem.lainnya_asal || '-'"></span>
                                                    <span class="text-slate-400 text-[9.5px] block truncate" x-text="'Uk: ' + (lItem.lainnya_ukuran || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Taksiran Satuan</span>
                                                    <span class="text-emerald-300 font-mono font-bold block text-[11px]" x-text="'Rp ' + Number(lItem.lainnya_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback data single KIB E -->
                            <template x-if="getLainnyaItemsForDetail(selectedDetail).length === 0">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Judul / Pencipta</span>
                                        <span class="text-purple-300 font-bold block truncate" x-text="getSpecDetail(selectedDetail).lainnya_judul || '-'"></span>
                                        <span class="text-slate-300 text-[10px]" x-text="'Pencipta: ' + (getSpecDetail(selectedDetail).lainnya_pencipta || '-')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🧪 Bahan &amp; Ukuran</span>
                                        <span class="text-amber-300 font-semibold block truncate" x-text="getSpecDetail(selectedDetail).lainnya_bahan || '-'"></span>
                                        <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Uk: ' + (getSpecDetail(selectedDetail).ukuran || '-')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">💰 Nilai Wajar Satuan</span>
                                        <span class="text-emerald-300 font-mono font-bold block text-[11px]" x-text="'Rp ' + Number(selectedDetail.total_nilai || selectedDetail.astap?.nilai_aset || 0).toLocaleString('id-ID')"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- ===================================================================== -->
                <!-- 5. DAFTAR UNIT REGISTER NIBAR (45-DIGIT SIMAT-RK)                      -->
                <!-- ===================================================================== -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-800 pb-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider">🏷️ RINCIAN NIBAR &amp; PENEMPATAN RUANGAN (REGISTER):</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Daftar unik kode NIBAR per-aset hibah beserta lokasi penempatannya.</p>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                            <button type="button" @click="resequenceSingleAstap(selectedDetail)"
                                class="px-2.5 py-1 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-[10.5px] font-bold transition-all flex items-center space-x-1 cursor-pointer shadow-sm active:scale-95"
                                title="Urutkan ulang register NIBAR barang ini agar berurutan tanpa celah">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Rapikan NIBAR Barang Ini</span>
                            </button>
                            @endif
                            <span class="text-[10px] font-extrabold px-3 py-1 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono shadow-sm"
                                  x-text="filteredDetailRegisters.length + ' / ' + getRegistersList(selectedDetail).length + ' Aset'"></span>
                        </div>
                    </div>

                    <!-- Filter Bar Interaktif Rincian Modal -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 bg-slate-900/70 p-3 rounded-xl border border-slate-800">
                        <div>
                            <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Penempatan</label>
                            <select x-model="detailPenempatanFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-amber-400">
                                <option value="all">Semua Penempatan</option>
                                <option value="sudah">📍 Sudah Ditempatkan</option>
                                <option value="belum">⚠️ Belum Ditempatkan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Aset</label>
                            <select x-model="detailKondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-amber-400">
                                <option value="all">Semua Kondisi</option>
                                <option value="Baik">Baik (B)</option>
                                <option value="Kurang Baik">Kurang Baik (KB)</option>
                                <option value="Rusak Berat">Rusak Berat (RB)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari NIBAR / Ruangan</label>
                            <input type="text" x-model="detailSearchQuery" placeholder="Cari NIBAR / No Reg / Ruang..."
                                   class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-400">
                        </div>
                    </div>

                    <!-- Scrollable Table -->
                    <div class="rounded-xl border border-slate-800/80 custom-scrollbar" style="max-height: 285px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full text-left text-[11px] text-slate-300 min-w-[640px]">
                            <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                                <tr>
                                    <th class="px-3.5 py-2.5 text-center">NIBAR RESMI</th>
                                    <th class="px-3.5 py-2.5 text-center" x-text="selectedDetail?.tipe_hibah === 'keluar' ? 'PENERIMA HIBAH' : 'LOKASI PENEMPATAN'">LOKASI PENEMPATAN</th>
                                    <th class="px-3.5 py-2.5 text-center">KONDISI</th>
                                    <th class="px-3.5 py-2.5 text-center whitespace-nowrap">QR CODE</th>
                                    <th class="px-3.5 py-2.5 text-center whitespace-nowrap">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                <template x-for="reg in filteredDetailRegisters" :key="reg.id">
                                    <tr class="hover:bg-slate-800/60 transition-colors">
                                        <!-- 1. NIBAR & No Register Resmi -->
                                        <td class="px-4 py-3 text-center whitespace-nowrap align-middle">
                                            <div class="inline-flex items-center justify-center gap-1.5 group/copy">
                                                <span class="font-mono text-[11px] font-bold text-slate-200 tracking-tight group-hover/copy:text-amber-300 transition-colors select-all"
                                                      :title="'NIBAR Resmi: ' + (reg.nibar || reg.no_register)"
                                                      x-text="reg.nibar || reg.no_register"></span>
                                                <button type="button" @click.stop="navigator.clipboard.writeText(reg.nibar || reg.no_register); showToast('NIBAR berhasil disalin ke clipboard!', 'success')"
                                                        title="Salin NIBAR ke Clipboard"
                                                        class="p-1 rounded-md bg-slate-800/60 hover:bg-amber-500/20 text-slate-400 hover:text-amber-300 border border-slate-700/60 hover:border-amber-500/30 transition-all cursor-pointer">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- 2. Lokasi Penempatan / Penerima Hibah -->
                                        <td class="px-4 py-3 text-center align-middle">
                                            <!-- Jika Hibah Keluar: Tampilkan Instansi Penerima -->
                                            <template x-if="selectedDetail?.tipe_hibah === 'keluar'">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs font-semibold shadow-sm max-w-[220px]">
                                                    <span class="text-xs shrink-0">📤</span>
                                                    <span class="truncate" :title="selectedDetail.pihak_hibah" x-text="selectedDetail.pihak_hibah || 'Penerima Hibah'"></span>
                                                </div>
                                            </template>
                                            <!-- Jika Hibah Masuk: Lokasi Penempatan (Jika belum ditempatkan, tampilkan badge Belum Ditempatkan) -->
                                            <template x-if="selectedDetail?.tipe_hibah !== 'keluar'">
                                                <div>
                                                    <template x-if="hasRealRuang(reg)">
                                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs font-semibold shadow-sm max-w-[220px]">
                                                            <span class="text-teal-400 text-xs shrink-0">📍</span>
                                                            <span class="truncate" :title="reg.ruang_pemegang" x-text="reg.ruang_pemegang"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!hasRealRuang(reg)">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 font-bold text-[10px]">
                                                            <span>⚠️</span>
                                                            <span>Belum Ditempatkan</span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>
                                        </td>

                                        <!-- 3. Kondisi Fisik -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold border inline-flex items-center gap-1.5 shadow-sm"
                                                  :class="{
                                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': (reg.kondisi || 'Baik') === 'Baik' || reg.kondisi === 'B',
                                                      'bg-amber-500/20 text-amber-300 border-amber-500/40': reg.kondisi === 'Kurang Baik' || reg.kondisi === 'KB' || reg.kondisi === 'Rusak Ringan' || reg.kondisi === 'RR',
                                                      'bg-rose-500/20 text-rose-300 border-rose-500/40': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'RB' || reg.kondisi === 'Rusak'
                                                  }">
                                                <span class="w-1.5 h-1.5 rounded-full"
                                                      :class="{
                                                          'bg-emerald-400': (reg.kondisi || 'Baik') === 'Baik' || reg.kondisi === 'B',
                                                          'bg-amber-400': reg.kondisi === 'Kurang Baik' || reg.kondisi === 'KB' || reg.kondisi === 'Rusak Ringan' || reg.kondisi === 'RR',
                                                          'bg-rose-400': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'RB' || reg.kondisi === 'Rusak'
                                                      }"></span>
                                                <span x-text="reg.kondisi === 'B' ? 'Baik' : ((reg.kondisi === 'KB' || reg.kondisi === 'RR' || reg.kondisi === 'Rusak Ringan') ? 'Kurang Baik' : ((reg.kondisi === 'RB' || reg.kondisi === 'Rusak') ? 'Rusak Berat' : (reg.kondisi || 'Baik')))"></span>
                                            </span>
                                        </td>

                                        <!-- 4. QR Code Download -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <button type="button" @click.stop="downloadQrCodeNibar(reg, selectedDetail)"
                                                title="Pratinjau &amp; Download QR NIBAR Aset Ini"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/25 border border-amber-500/30 hover:border-amber-400 text-amber-400 hover:text-amber-300 font-bold text-[10.5px] transition-all shadow-sm active:scale-95 group cursor-pointer leading-none">
                                                <svg class="w-3.5 h-3.5 text-amber-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-3.5-3.5M12 16l3.5-3.5M4 20h16"/>
                                                </svg>
                                                <span>Download QR</span>
                                            </button>
                                        </td>

                                        <!-- 5. AKSI -->
                                        <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center space-x-1.5">
                                                <!-- Riwayat Mutasi Aset -->
                                                <button type="button" @click.stop="openRiwayatModal(reg)" title="Cek Riwayat Mutasi Aset Ini"
                                                        class="p-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:text-purple-300 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>

                                                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                                <!-- Ubah Kondisi Barang -->
                                                <button type="button" @click.stop="openEditKondisiModal(reg)" title="Ubah Kondisi Aset Ini"
                                                        class="p-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                </button>

                                                <!-- Hapus Register -->
                                                <button type="button" @click.stop="deleteRegister(reg)" title="Hapus Aset Register Ini"
                                                        class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredDetailRegisters.length === 0">
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-slate-500 italic text-xs">
                                            Tidak ditemukan rincian unit register NIBAR yang sesuai dengan filter pencarian.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ===================================================================== -->
                <!-- 6. AUDIT TRAIL FOOTER                                                 -->
                <!-- ===================================================================== -->
                <div class="text-[10px] text-slate-500 flex items-center justify-between pt-2 border-t border-slate-800/80">
                    <span>Operator Input: <strong class="text-slate-400" x-text="selectedDetail.user ? selectedDetail.user.name : 'Administrator'"></strong></span>
                    <span>Dicatat Sistem: <strong class="text-slate-400" x-text="formatTanggalIndo(selectedDetail.created_at)"></strong></span>
                </div>
            </div>
        </template>

        <!-- ===================================================================== -->
        <!-- 7. MODAL FOOTER ACTIONS                                               -->
        <!-- ===================================================================== -->
        <div class="pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <template x-if="selectedDetail">
                <div class="flex items-center space-x-2">
                    <!-- Cetak BAST -->
                    <button type="button" @click="showModalDetail = false; openPrintBast(selectedDetail);"
                        class="px-4 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer shadow-md shadow-amber-500/10">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>🖨️ Cetak Lembar BAST Resmi</span>
                    </button>
                </div>
            </template>

            <button type="button" @click="showModalDetail = false"
                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-colors ml-auto cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</template>
