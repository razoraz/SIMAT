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

                    <!-- PPK & Alamat Penempatan RSUD (2 Kolom Seimbang) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs pt-1">
                        <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1.5">
                            <span class="text-emerald-400 text-[10px] font-bold uppercase tracking-wider block">✍️ Pejabat Pembuat Komitmen (PPK) RSUD</span>
                            <div>
                                <div class="font-extrabold text-white text-xs sm:text-sm" x-text="selectedDetail.astap?.ppk_nama || selectedDetail.astap?.spesifikasi_json?.ppk_nama || 'BUDI HARTONO, S.Sos'"></div>
                                <div class="font-mono text-[11px] text-slate-400" x-text="'NIP. ' + (selectedDetail.astap?.ppk_nip || selectedDetail.astap?.spesifikasi_json?.ppk_nip || '19760229 200801 1 010')"></div>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800/80 space-y-1.5">
                            <span class="text-purple-400 text-[10px] font-bold uppercase tracking-wider block">📍 Lokasi Penempatan Barang RSUD</span>
                            <div>
                                <div class="font-bold text-white text-xs sm:text-sm truncate" x-text="selectedDetail.astap?.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1'"></div>
                                <div class="text-[11px] text-teal-300 font-semibold truncate" x-text="'Ruangan / Unit: ' + (selectedDetail.astap?.unit?.nama || getRuangLabel(selectedDetail))"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan / Keterangan -->
                    <template x-if="selectedDetail.keterangan || selectedDetail.astap?.keterangan_tambahan">
                        <div class="p-3 rounded-xl bg-slate-950/80 border border-slate-800 space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan / Keterangan:</span>
                            <p class="text-slate-200 text-xs italic" x-text="selectedDetail.keterangan || selectedDetail.astap?.keterangan_tambahan"></p>
                        </div>
                    </template>
                </div>

                <!-- ===================================================================== -->
                <!-- 4. SPESIFIKASI TEKNIS FISIK BARANG (PMDN 108)                         -->
                <!-- ===================================================================== -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-4 shadow-xl">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 gap-2 flex-wrap">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center justify-center text-base shadow-inner">
                                ⚙️
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-white">
                                    Spesifikasi Fisik &amp; Identitas Teknis Barang
                                </h4>
                                <p class="text-[11px] text-slate-400">
                                    Rincian identitas fisik, merek, tipe, nomor seri, atau dimensi sesuai kelompok KIB.
                                </p>
                            </div>
                        </div>

                        <!-- Badge Ekstrakomtabel jika ada -->
                        <template x-if="selectedDetail.astap?.is_extracomtable || getSpecDetail(selectedDetail).is_extracom">
                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm flex items-center gap-1">
                                <span>📦</span>
                                <span>ASET EKSTRAKOMTABEL (≤ Rp 300.000)</span>
                            </span>
                        </template>
                    </div>

                    <!-- Multi-item mesin repeater jika ada -->
                    <template x-if="getSpecDetail(selectedDetail).mesin_items && getSpecDetail(selectedDetail).mesin_items.length > 0">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                <span class="flex items-center gap-1.5 text-amber-400 font-bold">
                                    📦 Rincian Multi-Barang Terdaftar (<span x-text="getSpecDetail(selectedDetail).mesin_items.length"></span> Item)
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Volume: ' + (selectedDetail.jumlah_volume || getSpecDetail(selectedDetail).mesin_items.length) + ' ' + (selectedDetail.satuan || 'Unit')"></span>
                            </div>
                            <template x-for="(mItem, mIdx) in getSpecDetail(selectedDetail).mesin_items" :key="mIdx">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-amber-500/30 space-y-2.5 shadow-sm hover:border-amber-400/50 transition-all">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-amber-500/20 text-amber-300 font-mono font-bold text-[11px] border border-amber-500/30"
                                                  x-text="'Item #' + (mIdx + 1)"></span>
                                            <span class="text-white font-bold text-xs" x-text="(mItem.mesin_nama_barang ? (mItem.mesin_nama_barang + ' • ') : '') + ((mItem.mesin_merk || mItem.mesin_type) ? ((mItem.mesin_merk || '') + ' ' + (mItem.mesin_type || '')) : '-')"></span>
                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                  x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || 'Unit')"></span>
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5 bg-emerald-500/15 text-emerald-300 border-emerald-500/30">
                                                <span class="w-1.5 h-1.5 rounded-full shrink-0 bg-emerald-400"></span>
                                                <span x-text="'Kondisi: ' + (mItem.mesin_kondisi || 'Baik')"></span>
                                            </span>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Subtotal: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(mItem.mesin_jumlah_barang) || 1) * (parseFloat(mItem.mesin_nilai_satuan) || 0))).toLocaleString('id-ID')"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Merk / Type / Bahan</span>
                                            <span class="text-amber-300 font-bold block truncate" x-text="(mItem.mesin_merk || '-') + ' / ' + (mItem.mesin_type || '-')"></span>
                                            <span class="text-slate-300 text-[10px]" x-text="'Bhn: ' + (mItem.mesin_bahan || '-') + ' • Uk: ' + (mItem.mesin_ukuran || '-')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔢 Identitas / No. Seri</span>
                                            <span class="text-cyan-300 font-mono font-semibold block truncate" x-text="'Pabrik: ' + (mItem.mesin_no_pabrik || '-')"></span>
                                            <span class="text-slate-400 font-mono text-[9px] block truncate" x-text="'Rgk: ' + (mItem.mesin_no_rangka || '-') + ' • Msn: ' + (mItem.mesin_no_mesin || '-')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Taksiran Nilai Satuan</span>
                                            <span class="text-emerald-300 font-medium block text-[10px]" x-text="'Rp ' + Number(mItem.mesin_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Fallback Grid Spesifikasi Fisik Reguler -->
                    <template x-if="!getSpecDetail(selectedDetail).mesin_items || getSpecDetail(selectedDetail).mesin_items.length === 0">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 text-xs">
                            <!-- KIB B: Peralatan & Mesin -->
                            <template x-if="getSpecDetail(selectedDetail).merk">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🏷️ Merk / Brand:</span>
                                    <strong class="text-white block truncate" x-text="getSpecDetail(selectedDetail).merk"></strong>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).type">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🔧 Tipe / Model:</span>
                                    <span class="text-amber-300 font-mono font-bold block truncate" x-text="getSpecDetail(selectedDetail).type"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).ukuran">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">📏 Ukuran / Dimensi:</span>
                                    <span class="text-slate-200 block truncate" x-text="getSpecDetail(selectedDetail).ukuran"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).bahan">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🧱 Bahan Material:</span>
                                    <span class="text-slate-200 block truncate" x-text="getSpecDetail(selectedDetail).bahan"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_pabrik">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🔢 Nomor Pabrik:</span>
                                    <span class="text-cyan-300 font-mono font-bold block truncate" x-text="getSpecDetail(selectedDetail).no_pabrik"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_rangka">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🚗 No. Rangka:</span>
                                    <span class="text-cyan-300 font-mono font-bold block truncate" x-text="getSpecDetail(selectedDetail).no_rangka"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_mesin">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">⚙️ No. Mesin:</span>
                                    <span class="text-cyan-300 font-mono font-bold block truncate" x-text="getSpecDetail(selectedDetail).no_mesin"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_polisi">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🚘 No. Polisi:</span>
                                    <span class="text-amber-300 font-mono font-black block truncate" x-text="getSpecDetail(selectedDetail).no_polisi"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).no_bpkb">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">📑 No. BPKB:</span>
                                    <span class="text-slate-200 font-mono block truncate" x-text="getSpecDetail(selectedDetail).no_bpkb"></span>
                                </div>
                            </template>

                            <!-- KIB A: Tanah -->
                            <template x-if="getSpecDetail(selectedDetail).tanah_luas_m2">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🌾 Luas Tanah:</span>
                                    <strong class="text-emerald-400 font-mono text-sm block" x-text="getSpecDetail(selectedDetail).tanah_luas_m2 + ' m²'"></strong>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).tanah_sertifikat_no">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">📜 Sertifikat Hak:</span>
                                    <span class="text-slate-200 font-mono block truncate" x-text="getSpecDetail(selectedDetail).tanah_sertifikat_no"></span>
                                </div>
                            </template>

                            <!-- KIB C: Gedung -->
                            <template x-if="getSpecDetail(selectedDetail).gedung_luas_m2">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🏢 Luas Lantai Gedung:</span>
                                    <strong class="text-purple-400 font-mono text-sm block" x-text="getSpecDetail(selectedDetail).gedung_luas_m2 + ' m²'"></strong>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).gedung_bertingkat">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🏛️ Konstruksi Bertingkat:</span>
                                    <span class="text-slate-200 block truncate" x-text="getSpecDetail(selectedDetail).gedung_bertingkat + ' • ' + (getSpecDetail(selectedDetail).gedung_beton || 'Beton')"></span>
                                </div>
                            </template>

                            <!-- KIB D: Jaringan -->
                            <template x-if="getSpecDetail(selectedDetail).jaringan_konstruksi">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🌐 Konstruksi Jaringan:</span>
                                    <span class="text-teal-300 block truncate" x-text="getSpecDetail(selectedDetail).jaringan_konstruksi"></span>
                                </div>
                            </template>
                            <template x-if="getSpecDetail(selectedDetail).jaringan_luas_m2">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">📐 Luas Bentang:</span>
                                    <span class="text-emerald-400 font-mono font-bold block" x-text="getSpecDetail(selectedDetail).jaringan_luas_m2 + ' m²'"></span>
                                </div>
                            </template>

                            <!-- KIB E: Aset Lainnya -->
                            <template x-if="getSpecDetail(selectedDetail).lainnya_judul">
                                <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">🎨 Judul / Pencipta:</span>
                                    <span class="text-amber-300 block truncate" x-text="getSpecDetail(selectedDetail).lainnya_judul"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- ===================================================================== -->
                <!-- 5. DAFTAR UNIT REGISTER NIBAR (45-DIGIT SIMAT-RK)                      -->
                <!-- ===================================================================== -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-3.5 shadow-xl">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2.5 border-b border-slate-800/80 gap-3">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-cyan-500/15 text-cyan-400 border border-cyan-500/30 flex items-center justify-center text-base shadow-inner">
                                🏷️
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-white">
                                    Daftar Unit Register Aset (NIBAR Resmi 45-Digit SIMAT-RK)
                                </h4>
                                <p class="text-[11px] text-slate-400">
                                    Tercatat unik untuk penatausahaan inventarisasi ruangan (KIR RSUD).
                                </p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-bold bg-slate-900 border border-slate-800 text-cyan-300 shrink-0"
                              x-text="getRegistersList(selectedDetail).length + ' Unit Terdaftar'"></span>
                    </div>

                    <!-- Filter Bar Mini Khusus Register -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
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

                    <!-- Scrollable Table Identik Kemitraan -->
                    <div class="rounded-xl border border-slate-800/80 custom-scrollbar" style="max-height: 285px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full text-left text-[11px] text-slate-300 min-w-[640px]">
                            <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                                <tr>
                                    <th class="px-3.5 py-2.5 text-center">NIBAR RESMI</th>
                                    <th class="px-3.5 py-2.5 text-center">Lokasi / Pengelolaan (KIR)</th>
                                    <th class="px-3.5 py-2.5 text-center">Kondisi Fisik</th>
                                    <th class="px-3.5 py-2.5 text-center whitespace-nowrap">Status</th>
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

                                        <!-- 2. Lokasi / Pengelolaan (KIR) -->
                                        <td class="px-4 py-3 text-center align-middle">
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs font-semibold shadow-sm max-w-[240px]">
                                                <span class="text-teal-400 text-xs shrink-0">📍</span>
                                                <span class="truncate" :title="reg.ruang_pemegang || reg.unit?.nama || (selectedDetail.astap?.unit?.nama || 'Gudang Aset')" 
                                                      x-text="reg.ruang_pemegang || reg.unit?.nama || (selectedDetail.astap?.unit?.nama || 'Gudang Aset')"></span>
                                            </div>
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

                                        <!-- 4. Status Operasional -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold border inline-flex items-center gap-1.5 shadow-sm"
                                                  :class="selectedDetail.tipe_hibah === 'keluar' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'">
                                                <span class="w-1.5 h-1.5 rounded-full"
                                                      :class="selectedDetail.tipe_hibah === 'keluar' ? 'bg-rose-400' : 'bg-emerald-400 animate-pulse'"></span>
                                                <span x-text="selectedDetail.tipe_hibah === 'keluar' ? 'Dihibahkan' : (reg.status || 'Tersedia')"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredDetailRegisters.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-3 py-6 text-center text-slate-500 italic text-xs">
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

                    <!-- Ubah Data (Khusus Hibah Masuk yang memiliki astap_id) -->
                    <template x-if="selectedDetail.tipe_hibah === 'masuk' && selectedDetail.astap_id">
                        <a :href="'/astap/' + selectedDetail.astap_id + '/edit-hibah'"
                            class="px-4 py-2.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer shadow-md shadow-cyan-500/10">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
