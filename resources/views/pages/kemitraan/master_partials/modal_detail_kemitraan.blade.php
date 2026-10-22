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
    
    <div class="border border-slate-800 rounded-3xl max-w-5xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto bg-slate-900">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="space-y-2 min-w-0 flex-1">
                <!-- Badges & Chips Row -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Kategori KIB Badge -->
                    <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0"
                        :class="{
                            'bg-amber-500/20 text-amber-300 border-amber-500/30': getEffectiveKibCategory(selectedAstapDetail) === 'KIB A',
                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     getEffectiveKibCategory(selectedAstapDetail) === 'KIB B',
                            'bg-purple-500/20 text-purple-300 border-purple-500/30': getEffectiveKibCategory(selectedAstapDetail) === 'KIB C',
                            'bg-teal-500/20 text-teal-300 border-teal-500/30':     getEffectiveKibCategory(selectedAstapDetail) === 'KIB D',
                            'bg-orange-500/20 text-orange-300 border-orange-500/30': getEffectiveKibCategory(selectedAstapDetail) === 'KIB E'
                        }"
                        x-text="getEffectiveKibCategory(selectedAstapDetail)"></span>

                    <!-- Badge Kemitraan Akun 1.5.2 vs Milik RSUD Pasca Reklas -->
                    <template x-if="!(selectedAstapDetail?.has_reklas || selectedAstapDetail?.is_reklas || selectedAstapDetail?.status_konsesi === 'Selesai / Reklasifikasi' || selectedAstapDetail?.status_konsesi === 'Selesai')">
                        <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0 bg-cyan-500/20 text-cyan-300 border-cyan-500/30 shadow-sm">
                            🤝 KEMITRAAN AKUN 1.5.2
                        </span>
                    </template>
                    <template x-if="selectedAstapDetail?.has_reklas || selectedAstapDetail?.is_reklas || selectedAstapDetail?.status_konsesi === 'Selesai / Reklasifikasi' || selectedAstapDetail?.status_konsesi === 'Selesai'">
                        <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0 bg-emerald-500/20 text-emerald-300 border-emerald-500/30 shadow-sm">
                            🏛️ MILIK RSUD
                        </span>
                    </template>

                    <!-- Badge Objek Pemanfaatan BMD RSUD (Hanya jika belum direklas balik) -->
                    <template x-if="selectedAstapDetail?.is_dimanfaatkan && !(selectedAstapDetail?.has_reklas || selectedAstapDetail?.is_reklas || selectedAstapDetail?.status_konsesi === 'Selesai / Reklasifikasi' || selectedAstapDetail?.status_konsesi === 'Selesai')">
                        <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0 bg-sky-500/20 text-sky-300 border-sky-500/40 shadow-sm flex items-center gap-1.5">
                            <span>🏛️</span>
                            <span>OBJEK PEMANFAATAN BMD</span>
                        </span>
                    </template>

                    <!-- Badge Aset Ditambahkan Mitra jika objek pemanfaatan memiliki barang mitra -->
                    <template x-if="selectedAstapDetail?.is_dimanfaatkan && getAsetDitambahkanMitraForDetail(selectedAstapDetail).length > 0">
                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0 bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-sm shadow-emerald-500/10"
                            :title="'Objek kerjasama ini memuat ' + getAsetDitambahkanMitraForDetail(selectedAstapDetail).length + ' aset yang ditambahkan/didatangkan oleh mitra rekanan'">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span x-text="'📦 +' + getAsetDitambahkanMitraForDetail(selectedAstapDetail).length + ' ASET MITRA'"></span>
                        </span>
                    </template>

                    <!-- Badge Reklasifikasi jika ada riwayat reklas -->
                    <template x-if="selectedAstapDetail?.has_reklas || selectedAstapDetail?.is_reklas || (selectedAstapDetail?.reklas_count && selectedAstapDetail?.reklas_count > 0)">
                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg text-[10.5px] font-black border uppercase tracking-wider shrink-0 bg-indigo-500/20 text-indigo-300 border-indigo-500/30 shadow-sm"
                            :title="'Aset ini memiliki riwayat Reklasifikasi' + (selectedAstapDetail?.jenis_reklas ? ' (' + selectedAstapDetail.jenis_reklas.replace(/_/g, ' ') + ')' : '')">
                            <svg class="w-3 h-3 shrink-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>REKLASIFIKASI</span>
                        </span>
                    </template>

                    <!-- Kode 108 / NIBAR -->
                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full shadow-inner"
                        x-text="'Kode: ' + (selectedAstapDetail?.kode_barang || '-')"></span>

                    <!-- Tanggal PKS -->
                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0 shadow-inner">
                        <span class="text-slate-400">📅 PKS:</span>
                        <span class="text-cyan-300 font-bold" x-text="formatTanggalIndo(selectedAstapDetail?.tanggal_pks || selectedAstapDetail?.created_at)"></span>
                    </span>

                    <!-- Status Kondisi Keseluruhan -->
                    <template x-if="selectedAstapDetail">
                        <span class="px-2.5 py-1 rounded-lg border text-[11px] font-bold flex items-center space-x-1.5 shrink-0 shadow-sm"
                              :class="getKondisiStats(selectedAstapDetail).badge_class">
                            <span class="w-1.5 h-1.5 rounded-full" :class="getKondisiStats(selectedAstapDetail).dot_class"></span>
                            <span x-text="'Kondisi: ' + getKondisiStats(selectedAstapDetail).text"></span>
                        </span>
                    </template>
                </div>

                <!-- Title & Meta Subtitle -->
                <div class="pt-1 space-y-1">
                    <h3 class="text-lg sm:text-xl md:text-2xl font-black text-white leading-tight tracking-tight break-words" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></h3>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                        <span class="text-cyan-400 font-semibold" x-text="selectedAstapDetail?.jenis_aset_nama || 'Aset Kemitraan (Akun 1.5.2)'"></span>
                        <template x-if="selectedAstapDetail?.alamat_barang">
                            <div class="flex items-center gap-1 text-slate-300">
                                <span class="text-slate-600">•</span>
                                <span class="text-teal-400">📍</span>
                                <span class="truncate max-w-md" x-text="selectedAstapDetail.alamat_barang"></span>
                            </div>
                        </template>
                        <template x-if="selectedAstapDetail?.penyedia_nama && selectedAstapDetail.penyedia_nama !== '-'">
                            <div class="flex items-center gap-1 text-slate-300">
                                <span class="text-slate-600">•</span>
                                <span class="text-slate-400">Mitra:</span>
                                <span class="text-amber-300 font-bold" x-text="selectedAstapDetail.penyedia_nama"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tombol Close -->
            <button type="button" @click="showDetailModal = false" class="w-9 h-9 rounded-2xl bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-slate-700/60 hover:border-rose-500/30 flex items-center justify-center transition-all shrink-0 cursor-pointer shadow-sm active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <template x-if="selectedAstapDetail">
            <div class="space-y-4 text-xs text-slate-300">

                <!-- Top 4 Metric KPI Cards (Fully Responsive Grid) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1">🏷️ Klasifikasi PMDN 108</span>
                        <span class="text-white font-bold text-xs sm:text-[13px] leading-snug break-words line-clamp-2 block" :title="selectedAstapDetail.jenis_aset_nama" x-text="selectedAstapDetail.jenis_aset_nama"></span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1"
                              x-text="selectedAstapDetail.label_tahun_kpi || (selectedAstapDetail.is_dimanfaatkan ? '🏛️ Tahun Perolehan BMD' : '🏢 Tahun Penempatan di RSUD')"></span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-cyan-300 font-black font-mono text-sm sm:text-base tracking-tight" x-text="selectedAstapDetail.tahun_perolehan"></span>
                            <span class="text-slate-500 font-mono text-xs">•</span>
                            <span class="px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 font-mono font-bold text-[11px]" x-text="'TW ' + (selectedAstapDetail.triwulan ? String(selectedAstapDetail.triwulan).replace(/[^0-9]/g, '') || selectedAstapDetail.triwulan : '1')"></span>
                        </div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1">📏 Volume Fisik</span>
                        <span class="text-teal-300 font-black font-mono text-sm sm:text-base block truncate tracking-tight" x-text="selectedAstapDetail.volume_satuan"></span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-950/90 to-slate-900/90 border border-slate-800/80 hover:border-slate-700/80 transition-all shadow-md min-w-0 flex flex-col justify-between">
                        <span class="text-slate-400 text-[10px] uppercase font-extrabold tracking-wider block mb-1">💰 Taksiran Nilai Wajar</span>
                        <span class="text-emerald-400 font-black font-mono text-sm sm:text-base block truncate tracking-tight drop-shadow-sm" x-text="selectedAstapDetail.jumlah_realisasi"></span>
                    </div>
                </div>

                <!-- DOKUMEN LEGALITAS PKS & KONSESI KERJASAMA -->
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950/25 border border-cyan-500/30 space-y-4 shadow-xl">
                    <!-- Header Section PKS -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-cyan-500/20 gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 flex items-center justify-center text-lg shrink-0 shadow-inner">
                                📜
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-black uppercase tracking-wider text-cyan-300">
                                    Dokumen Perjanjian Kerja Sama (PKS) &amp; Masa Konsesi
                                </h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Dasar hukum legalitas perikatan dan batas waktu pemanfaatan Barang Milik Daerah.
                                </p>
                            </div>
                        </div>

                        <!-- Status Konsesi & Tombol Aksi -->
                        <div class="flex items-center gap-2 flex-wrap shrink-0">
                            <!-- Status Konsesi Badge -->
                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold border inline-flex items-center gap-1.5 shadow-sm"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': selectedAstapDetail.status_konsesi === 'Aktif',
                                      'bg-amber-500/20 text-amber-300 border-amber-500/40': selectedAstapDetail.status_konsesi === 'Konsesi Berakhir',
                                      'bg-purple-500/20 text-purple-300 border-purple-500/40': selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan',
                                      'bg-blue-500/20 text-blue-300 border-blue-500/40': selectedAstapDetail.status_konsesi === 'Selesai' || selectedAstapDetail.status_konsesi === 'Selesai / Reklasifikasi',
                                      'bg-rose-500/20 text-rose-300 border-rose-500/40': selectedAstapDetail.status_konsesi === 'Dihentikan' || selectedAstapDetail.status_konsesi === 'Dibatalkan'
                                  }">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                          'bg-emerald-400 animate-pulse': selectedAstapDetail.status_konsesi === 'Aktif',
                                          'bg-amber-400': selectedAstapDetail.status_konsesi === 'Konsesi Berakhir',
                                          'bg-purple-400': selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan',
                                          'bg-blue-400': selectedAstapDetail.status_konsesi === 'Selesai' || selectedAstapDetail.status_konsesi === 'Selesai / Reklasifikasi',
                                          'bg-rose-400': selectedAstapDetail.status_konsesi === 'Dihentikan' || selectedAstapDetail.status_konsesi === 'Dibatalkan'
                                      }"></span>
                                <span x-text="'Status: ' + (selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan' ? 'Dikembalikan ke Rekanan' : ((selectedAstapDetail.status_konsesi === 'Selesai' || selectedAstapDetail.status_konsesi === 'Selesai / Reklasifikasi') ? 'Sudah Jadi Milik RSUD' : (selectedAstapDetail.status_konsesi === 'Dibatalkan' ? 'Dibatalkan' : selectedAstapDetail.status_konsesi)))"></span>
                            </span>

                            <!-- Countdown Sisa Hari -->
                            <template x-if="selectedAstapDetail.sisa_hari_konsesi !== null && selectedAstapDetail.sisa_hari_konsesi !== undefined">
                                <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-bold shadow-sm"
                                      :class="{
                                          'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30': selectedAstapDetail.sisa_hari_konsesi > 60,
                                          'bg-amber-500/15 text-amber-300 border border-amber-500/30': selectedAstapDetail.sisa_hari_konsesi > 0 && selectedAstapDetail.sisa_hari_konsesi <= 60,
                                          'bg-rose-500/15 text-rose-300 border border-rose-500/30': selectedAstapDetail.sisa_hari_konsesi <= 0
                                      }"
                                      x-text="selectedAstapDetail.sisa_hari_konsesi > 0 ? ('⏱️ Sisa ' + selectedAstapDetail.sisa_hari_konsesi + ' Hari') : '🛑 Konsesi Berakhir'"></span>
                            </template>

                            <!-- Adendum Badge -->
                            <template x-if="selectedAstapDetail.is_extended || (selectedAstapDetail.riwayat_adendum && selectedAstapDetail.riwayat_adendum.length > 0)">
                                <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-black uppercase tracking-wider bg-emerald-500/25 text-emerald-300 border border-emerald-400/50 shadow-sm flex items-center gap-1"
                                      title="Kontrak PKS telah diperpanjang (Adendum)">
                                    <span>🔄</span>
                                    <span x-text="'ADENDUM #' + (selectedAstapDetail.jumlah_extend || (selectedAstapDetail.riwayat_adendum ? selectedAstapDetail.riwayat_adendum.length : 1))"></span>
                                </span>
                            </template>

                            <!-- Tombol Perpanjang PKS (Extend) -->
                            <template x-if="selectedAstapDetail && (selectedAstapDetail.kemitraan_id || selectedAstapDetail.id)">
                                <template x-if="selectedAstapDetail.status_konsesi !== 'Dikembalikan ke Rekanan' && selectedAstapDetail.status_konsesi !== 'Selesai' && selectedAstapDetail.status_konsesi !== 'Selesai / Reklasifikasi' && selectedAstapDetail.status_konsesi !== 'Dihentikan' && selectedAstapDetail.status_konsesi !== 'Dibatalkan'">
                                    <button type="button" @click="openExtendModalFromDetail()"
                                        class="px-3 py-1.5 rounded-xl text-[10.5px] font-extrabold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white border border-emerald-400/40 flex items-center gap-1.5 transition-all active:scale-95 shadow-md shadow-emerald-500/20 cursor-pointer"
                                        title="Perpanjang Masa Konsesi (Buat Adendum PKS)">
                                        <span>🔄</span>
                                        <span>Perpanjang PKS (Extend)</span>
                                    </button>
                                </template>
                            </template>
                        </div>
                    </div>

                    <!-- 4 Kolom Rincian PKS (Grid Cards) -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1">Nomor PKS:</span>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-cyan-300 font-bold text-xs sm:text-sm block truncate select-all" :title="selectedAstapDetail.nomor_pks" x-text="selectedAstapDetail.nomor_pks || '-'"></span>
                                <template x-if="selectedAstapDetail.nomor_pks && selectedAstapDetail.nomor_pks !== '-'">
                                    <button type="button" @click.stop="navigator.clipboard.writeText(selectedAstapDetail.nomor_pks); showToast('Nomor PKS berhasil disalin!', 'success')"
                                            class="p-1 rounded text-slate-500 hover:text-cyan-300 hover:bg-slate-800 transition-colors cursor-pointer" title="Salin Nomor PKS">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1">Tanggal PKS:</span>
                            <span class="text-white font-bold text-xs sm:text-sm block" x-text="formatTanggalIndo(selectedAstapDetail.tanggal_pks) || '-'"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1">Skema Kemitraan:</span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs font-black"
                                  :class="{
                                      'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40': selectedAstapDetail.skema_kemitraan === 'Sewa' || !selectedAstapDetail.skema_kemitraan,
                                      'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40': selectedAstapDetail.skema_kemitraan === 'KSP',
                                      'bg-purple-500/20 text-purple-300 border border-purple-500/40': selectedAstapDetail.skema_kemitraan === 'BGS/BSG',
                                      'bg-amber-500/20 text-amber-300 border border-amber-500/40': selectedAstapDetail.skema_kemitraan === 'KSPI'
                                  }"
                                  x-text="selectedAstapDetail.skema_kemitraan || 'Sewa'"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800/80">
                            <span class="text-slate-400 block text-[10px] uppercase font-extrabold tracking-wider mb-1">Masa Konsesi / Kerjasama:</span>
                            <span class="text-slate-200 font-mono font-bold text-[11px] sm:text-xs block leading-tight" x-text="(formatTanggalIndo(selectedAstapDetail.tanggal_mulai) || '?') + ' s.d. ' + (formatTanggalIndo(selectedAstapDetail.tanggal_selesai) || '?')"></span>
                        </div>
                    </div>

                    <!-- Highlight Finansial / Nilai Sewa atau Kontribusi Pemanfaatan BMD -->
                    <template x-if="selectedAstapDetail.harga_sewa || selectedAstapDetail.nilai_kontribusi || selectedAstapDetail.spesifikasi_json?.harga_sewa || selectedAstapDetail.spesifikasi_json?.kemitraan_harga_sewa">
                        <div class="p-3.5 rounded-xl bg-gradient-to-r from-cyan-950/50 via-slate-950 to-slate-950/90 border border-cyan-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-inner">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 flex items-center justify-center text-lg shrink-0">
                                    💰
                                </div>
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-cyan-400 block"
                                          x-text="(selectedAstapDetail.skema_kemitraan === 'Sewa' || !selectedAstapDetail.skema_kemitraan) ? 'Tarif / Nilai Harga Sewa Pemanfaatan BMD' : 'Nilai Kontribusi / Bagi Hasil Pemanfaatan'"></span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-900 border border-slate-800 text-slate-300"
                                              x-text="'Siklus Pembayaran: ' + (selectedAstapDetail.periode_sewa || selectedAstapDetail.spesifikasi_json?.periode_sewa || 'Per Tahun')"></span>
                                        <template x-if="selectedAstapDetail.keterangan_kontribusi || selectedAstapDetail.spesifikasi_json?.keterangan_kontribusi">
                                            <span class="text-[10.5px] text-slate-400 italic" x-text="selectedAstapDetail.keterangan_kontribusi || selectedAstapDetail.spesifikasi_json?.keterangan_kontribusi"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <span class="text-base sm:text-lg font-mono font-black text-cyan-300 block tracking-tight"
                                      x-text="'Rp ' + Number(selectedAstapDetail.harga_sewa || selectedAstapDetail.nilai_kontribusi || selectedAstapDetail.spesifikasi_json?.harga_sewa || selectedAstapDetail.spesifikasi_json?.kemitraan_harga_sewa || 0).toLocaleString('id-ID')"></span>
                                <span class="text-[10px] text-cyan-400/80 font-mono font-semibold"
                                      x-text="(selectedAstapDetail.skema_kemitraan === 'Sewa' || !selectedAstapDetail.skema_kemitraan) ? ('/ ' + (selectedAstapDetail.periode_sewa || selectedAstapDetail.spesifikasi_json?.periode_sewa || 'Tahun')) : 'Pemanfaatan BMD'"></span>
                            </div>
                        </div>
                    </template>

                    <!-- KARTU DOKUMEN BAST & KONTRAK KERJASAMA (PRATINJAU, GANTI & HAPUS) - FULL WIDTH -->
                    <div class="w-full pt-3 border-t border-cyan-500/20">
                        <!-- Input File Tersembunyi untuk Ubah / Upload Dokumen BAST -->
                        <input type="file" id="fileInputDokumenDetail" @change="handleUploadDokumenDetail" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" class="hidden">

                        <div class="w-full p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border border-cyan-500/30 shadow-md">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3.5">
                                <div class="flex items-start sm:items-center space-x-3.5 min-w-0 flex-1">
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
                                                <span class="px-2 py-0.5 rounded text-[9.5px] font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                    Belum Ada Arsip Scan (Menyusul)
                                                </span>
                                            </template>
                                        </div>

                                        <!-- Title or Filename -->
                                        <div class="text-xs font-bold text-white mt-1 break-words"
                                             x-text="selectedAstapDetail?.dokumen_path ? selectedAstapDetail.dokumen_path.split('/').pop() : 'Berkas Scan PKS / BAST Belum Dilampirkan'">
                                        </div>

                                        <!-- Subtext / Helper description -->
                                        <div class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                                            <template x-if="selectedAstapDetail?.dokumen_path">
                                                <span>Format: <strong class="font-mono text-cyan-300 uppercase" x-text="selectedAstapDetail.dokumen_path.split('.').pop()"></strong> · Berkas arsip legalitas resmi kemitraan RSUD.</span>
                                            </template>
                                            <template x-if="!selectedAstapDetail?.dokumen_path">
                                                <span>Unggah berkas setelah selesai ditandatangani basah & distempel resmi oleh Direktur RSUD dan Pihak Mitra. Format: PDF, JPG, PNG (Maks 10 MB).</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="flex items-center flex-wrap gap-2 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-800/80">
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
                                            <button type="button" @click="openDokumenPreview(selectedAstapDetail.dokumen_path, selectedAstapDetail.nama_barang)"
                                                title="Tampilkan / Pratinjau Berkas BAST"
                                                class="px-3.5 py-1.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs font-extrabold transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Tampilkan Berkas</span>
                                            </button>

                                            <button type="button" @click="triggerUploadDokumenDetail()"
                                                title="Ganti Berkas BAST dengan Dokumen Baru"
                                                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                <span>Ganti Berkas</span>
                                            </button>

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
                <!-- SECTION: RIWAYAT SUMBER ASAL PEROLEHAN PRA-KEMITRAAN (BMD RSUD)           -->
                <!-- ========================================================================= -->
                <template x-if="selectedAstapDetail && (selectedAstapDetail.is_dimanfaatkan || selectedAstapDetail.has_reklas_origin || selectedAstapDetail.asal_sumber_dana !== 'kemitraan_murni' || (selectedAstapDetail.penyedia_pengadaan_nama && selectedAstapDetail.penyedia_pengadaan_nama !== '-'))">
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-indigo-500/30 space-y-4 shadow-xl">
                        <!-- Header Seksi -->
                        <div class="flex items-center justify-between pb-3 border-b border-indigo-500/20 gap-2 flex-wrap">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center text-base shadow-inner">
                                    🏛️
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-indigo-400 flex items-center gap-2">
                                        <span>Riwayat Asal Perolehan Pra-Kemitraan (BMD RSUD)</span>
                                    </h4>
                                    <p class="text-[11px] text-slate-400">
                                        Histori asal-usul aset sebelum masuk skema pemanfaatan mitra (Akun Kemitraan 1.5.2).
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-bold border"
                                      :class="selectedAstapDetail.asal_sumber_dana_badge || 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40'"
                                      x-text="selectedAstapDetail.asal_sumber_dana_label || 'BMD RSUD'">
                                </span>
                                <template x-if="selectedAstapDetail.has_reklas_origin">
                                    <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1">
                                        <span>🔄 Transisi Reklasifikasi</span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Grid Konten Asal Perolehan -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5 text-[11px]">
                            <!-- 1. Transisi Reklasifikasi (Jika Direklasifikasi ke Kemitraan) -->
                            <template x-if="selectedAstapDetail.has_reklas_origin || (selectedAstapDetail.reklas_nomor_ba && selectedAstapDetail.reklas_nomor_ba !== '-')">
                                <div class="p-4 rounded-xl bg-slate-900/80 border border-amber-500/30 space-y-3 lg:col-span-2">
                                    <div class="flex items-center justify-between pb-2 border-b border-amber-500/20">
                                        <span class="text-[10.5px] font-extrabold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>🔄 Berita Acara Reklasifikasi ke Akun Kemitraan</span>
                                        </span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20"
                                              x-text="selectedAstapDetail.reklas_tanggal ? formatDate(selectedAstapDetail.reklas_tanggal) : '-'"></span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-slate-300">
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Tahun Perolehan BMD:</span>
                                            <strong class="text-emerald-400 font-mono text-xs select-all" x-text="selectedAstapDetail.tahun_perolehan_bmd || selectedAstapDetail.tahun_pengadaan_asal || '-'"></strong>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Nomor BA Reklas:</span>
                                            <strong class="text-amber-300 font-mono text-xs select-all" x-text="selectedAstapDetail.reklas_nomor_ba || '-'"></strong>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">KIB & Buku Aset Asal:</span>
                                            <strong class="text-white text-xs" x-text="(selectedAstapDetail.reklas_asal_kib || 'Aset Tetap') + ' (RSUD)'"></strong>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Kode 108 Rekening Asal:</span>
                                            <strong class="text-cyan-300 font-mono text-xs select-all" x-text="selectedAstapDetail.reklas_asal_kode || '-'"></strong>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Uraian / Alasan Reklas:</span>
                                            <span class="text-slate-300 text-[10.5px] italic line-clamp-2" :title="selectedAstapDetail.reklas_alasan" x-text="selectedAstapDetail.reklas_alasan || 'Pemanfaatan BMD Bersama Mitra'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- 2. Belanja Modal: Data Vendor Penyedia Pengadaan Asal BMD -->
                            <template x-if="selectedAstapDetail.penyedia_pengadaan_nama && selectedAstapDetail.penyedia_pengadaan_nama !== '-'">
                                <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-3">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                                        <span class="text-[10.5px] font-extrabold text-emerald-400 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>🏢 Pihak Penyedia Pengadaan Asal (Vendor)</span>
                                        </span>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-bold">Sumber Belanja RSUD</span>
                                    </div>
                                    <div class="space-y-2 divide-y divide-slate-800/60">
                                        <div class="flex items-start justify-between gap-2 pt-1">
                                            <span class="text-slate-400 shrink-0">Nama Perusahaan / Rekanan:</span>
                                            <strong class="text-white font-bold text-right" :title="selectedAstapDetail.penyedia_pengadaan_nama" x-text="selectedAstapDetail.penyedia_pengadaan_nama"></strong>
                                        </div>
                                        <div class="flex items-start justify-between gap-2 pt-2">
                                            <span class="text-slate-400 shrink-0">Pimpinan / Direktur Vendor:</span>
                                            <strong class="text-emerald-300 font-bold text-right" :title="selectedAstapDetail.penyedia_pengadaan_pemilik" x-text="selectedAstapDetail.penyedia_pengadaan_pemilik || '-'"></strong>
                                        </div>
                                        <div class="flex items-start justify-between gap-2 pt-2">
                                            <span class="text-slate-400 shrink-0">Rekening Bank Penyedia:</span>
                                            <span class="text-amber-400 font-mono font-bold text-right select-all" :title="selectedAstapDetail.penyedia_pengadaan_rekening" x-text="selectedAstapDetail.penyedia_pengadaan_rekening || '-'"></span>
                                        </div>
                                        <div class="flex items-start justify-between gap-2 pt-2">
                                            <span class="text-slate-400 shrink-0">Alamat Kantor Penyedia:</span>
                                            <span class="text-slate-300 font-medium text-right leading-relaxed" :title="selectedAstapDetail.penyedia_pengadaan_alamat" x-text="selectedAstapDetail.penyedia_pengadaan_alamat || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- 3. Belanja Modal: Dokumen Kontrak & SP2D Asal BMD -->
                            <template x-if="selectedAstapDetail.penyedia_pengadaan_nama && selectedAstapDetail.penyedia_pengadaan_nama !== '-'">
                                <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-3">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                                        <span class="text-[10.5px] font-extrabold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>📄 Dokumen Pengadaan &amp; SP2D Asal</span>
                                        </span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20"
                                              x-text="selectedAstapDetail.metode_pengadaan || 'DPA Belanja'"></span>
                                    </div>
                                    <div class="space-y-2 divide-y divide-slate-800/60">
                                        <div class="flex items-start justify-between gap-2 pt-1">
                                            <span class="text-slate-400 shrink-0">Nomor SPK / Kontrak:</span>
                                            <div class="text-right">
                                                <strong class="text-white font-mono font-bold select-all block" x-text="selectedAstapDetail.spk_nomor_pengadaan || '-'"></strong>
                                                <span class="text-[10px] text-slate-400" x-show="selectedAstapDetail.spk_tanggal_pengadaan" x-text="selectedAstapDetail.spk_tanggal_pengadaan ? formatDate(selectedAstapDetail.spk_tanggal_pengadaan) : ''"></span>
                                            </div>
                                        </div>
                                        <div class="flex items-start justify-between gap-2 pt-2">
                                            <span class="text-slate-400 shrink-0">Nomor Faktur / BAST Asal:</span>
                                            <div class="text-right">
                                                <strong class="text-emerald-300 font-mono font-bold select-all block" x-text="(selectedAstapDetail.faktur_nomor_pengadaan !== '-' ? selectedAstapDetail.faktur_nomor_pengadaan : selectedAstapDetail.bast_nomor_pengadaan) || '-'"></strong>
                                                <span class="text-[10px] text-slate-400" x-show="selectedAstapDetail.bast_tanggal_pengadaan || selectedAstapDetail.faktur_tanggal_pengadaan" x-text="formatDate(selectedAstapDetail.bast_tanggal_pengadaan || selectedAstapDetail.faktur_tanggal_pengadaan)"></span>
                                            </div>
                                        </div>
                                        <div class="flex items-start justify-between gap-2 pt-2">
                                            <span class="text-slate-400 shrink-0">Nomor SP2D Pengadaan:</span>
                                            <div class="text-right">
                                                <strong class="text-amber-400 font-mono font-bold select-all block" x-text="selectedAstapDetail.sp2d_nomor_pengadaan || '-'"></strong>
                                                <span class="text-[10px] text-slate-400" x-show="selectedAstapDetail.sp2d_tanggal_pengadaan" x-text="selectedAstapDetail.sp2d_tanggal_pengadaan ? formatDate(selectedAstapDetail.sp2d_tanggal_pengadaan) : ''"></span>
                                            </div>
                                        </div>
                                        <div class="flex items-start justify-between gap-2 pt-2">
                                            <span class="text-slate-400 shrink-0">Tahun Anggaran Perolehan:</span>
                                            <strong class="text-amber-300 font-mono font-bold text-right" x-text="selectedAstapDetail.tahun_perolehan_bmd || selectedAstapDetail.tahun_pengadaan_asal || '-'"></strong>
                                        </div>
                                        <div class="flex items-start justify-between gap-2 pt-2">
                                            <span class="text-slate-400 shrink-0">DPA / Rekening Belanja:</span>
                                            <span class="text-cyan-300 text-right leading-relaxed font-mono font-semibold" :title="selectedAstapDetail.rekening_belanja_pengadaan" x-text="selectedAstapDetail.rekening_belanja_pengadaan || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- 4. Hibah Masuk (Jika Asal Hibah) -->
                            <template x-if="selectedAstapDetail.asal_sumber_dana === 'hibah'">
                                <div class="p-4 rounded-xl bg-slate-900/70 border border-purple-500/30 space-y-3 lg:col-span-2">
                                    <div class="flex items-center justify-between pb-2 border-b border-purple-500/20">
                                        <span class="text-[10.5px] font-extrabold text-purple-400 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>🎁 Dokumen BAST Hibah Masuk</span>
                                        </span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20"
                                              x-text="selectedAstapDetail.hibah_tanggal_bast ? formatDate(selectedAstapDetail.hibah_tanggal_bast) : '-'"></span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-300">
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Pihak Pemberi Hibah:</span>
                                            <strong class="text-white text-xs" x-text="selectedAstapDetail.hibah_pemberi || '-'"></strong>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Nomor BAST Hibah:</span>
                                            <strong class="text-purple-300 font-mono text-xs select-all" x-text="selectedAstapDetail.hibah_nomor_bast || '-'"></strong>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- 5. Mutasi Eksternal / Pelimpahan SKPD (Jika Asal Mutasi Masuk) -->
                            <template x-if="selectedAstapDetail.asal_sumber_dana === 'mutasi_eksternal'">
                                <div class="p-4 rounded-xl bg-slate-900/70 border border-indigo-500/30 space-y-3 lg:col-span-2">
                                    <div class="flex items-center justify-between pb-2 border-b border-indigo-500/20">
                                        <span class="text-[10.5px] font-extrabold text-indigo-400 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>🚚 SK &amp; Berita Acara Mutasi Eksternal (Pelimpahan SKPD)</span>
                                        </span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20"
                                              x-text="selectedAstapDetail.mutasi_tanggal ? formatDate(selectedAstapDetail.mutasi_tanggal) : '-'"></span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-slate-300">
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Instansi / OPD Asal:</span>
                                            <strong class="text-white text-xs" x-text="selectedAstapDetail.mutasi_skpd_asal || '-'"></strong>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Nomor SK Dasar Mutasi:</span>
                                            <strong class="text-indigo-300 font-mono text-xs select-all" x-text="selectedAstapDetail.mutasi_nomor_sk || '-'"></strong>
                                        </div>
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800">
                                            <span class="text-slate-400 text-[10px] block uppercase font-semibold">Nomor Berita Acara (BAMB):</span>
                                            <strong class="text-indigo-300 font-mono text-xs select-all" x-text="selectedAstapDetail.mutasi_nomor_bamb || '-'"></strong>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- ========================================================================= -->
                <!-- SECTION: RIWAYAT ADENDUM / PERPANJANGAN KONSESI PKS (AUDIT TRAIL)         -->
                <!-- ========================================================================= -->
                <template x-if="selectedAstapDetail && selectedAstapDetail.riwayat_adendum && selectedAstapDetail.riwayat_adendum.length > 0">
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-emerald-500/30 space-y-3.5 shadow-xl">
                        <div class="flex items-center justify-between pb-2.5 border-b border-emerald-500/20 gap-2 flex-wrap">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center text-base shadow-inner">
                                    🔄
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-extrabold uppercase tracking-wider text-emerald-400">
                                        Riwayat Perpanjangan / Adendum PKS (Audit Trail)
                                    </h4>
                                    <p class="text-[11px] text-slate-400">
                                        Histori legalitas perpanjangan masa pemanfaatan dan penyesuaian nilai wajar.
                                    </p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-mono font-extrabold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30"
                                  x-text="selectedAstapDetail.riwayat_adendum.length + ' Kali Adendum'"></span>
                        </div>

                        <div class="space-y-2.5">
                            <template x-for="(adendum, idx) in selectedAstapDetail.riwayat_adendum" :key="idx">
                                <div class="p-3.5 rounded-xl bg-slate-900/90 border border-slate-800 hover:border-emerald-500/40 transition-colors space-y-2">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30"
                                                  x-text="'Adendum #' + (adendum.extend_ke || (idx + 1))"></span>
                                            <span class="font-bold text-white text-xs font-mono" x-text="adendum.nomor_adendum"></span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'📅 ' + formatTanggalIndo(adendum.tanggal_adendum)"></span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] bg-slate-950/60 p-2.5 rounded-lg border border-slate-800/80">
                                        <div>
                                            <span class="text-slate-400 block text-[9.5px] uppercase font-bold">Masa Selesai Baru:</span>
                                            <span class="font-mono text-emerald-400 font-bold" x-text="formatTanggalIndo(adendum.tanggal_selesai_baru)"></span>
                                            <template x-if="adendum.tanggal_selesai_lama">
                                                <span class="text-[9.5px] text-slate-400 block" x-text="'(Sebelumnya: ' + formatTanggalIndo(adendum.tanggal_selesai_lama) + ')'"></span>
                                            </template>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[9.5px] uppercase font-bold">Nilai Wajar:</span>
                                            <span class="font-mono text-cyan-300 font-bold" x-text="adendum.nilai_aset_baru ? ('Rp ' + Number(adendum.nilai_aset_baru).toLocaleString('id-ID')) : '-'"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[9.5px] uppercase font-bold">Dicatat Oleh:</span>
                                            <span class="text-slate-300 font-medium truncate block" x-text="adendum.created_by || 'Admin'"></span>
                                        </div>
                                    </div>

                                    <template x-if="adendum.keterangan && adendum.keterangan !== '-'">
                                        <div class="text-[11px] text-slate-300 flex items-start space-x-1.5 italic">
                                            <span class="text-slate-400">📝</span>
                                            <span x-text="adendum.keterangan"></span>
                                        </div>
                                    </template>

                                    <template x-if="adendum.berkas_path">
                                        <div class="pt-1 flex items-center justify-end">
                                            <a :href="'/storage/' + adendum.berkas_path" target="_blank"
                                               class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10.5px] font-bold flex items-center gap-1 transition-all">
                                                <span>📎 Berkas Adendum</span>
                                                <span>↗</span>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

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
                                <div class="rounded-xl border border-slate-800/90 custom-scrollbar" style="max-height: 320px; overflow-y: auto; overflow-x: auto;">
                                    <table class="w-full text-left text-[11px] text-slate-300 min-w-[800px]">
                                        <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px] border-b border-slate-800 sticky top-0 z-10 shadow-sm" style="background-color: #020617;">
                                            <tr>
                                                <th class="px-3 py-2.5 text-center w-12 whitespace-nowrap">#</th>
                                                <th class="px-3 py-2.5 min-w-[200px]">Nama Barang &amp; Spesifikasi</th>
                                                <th class="px-3 py-2.5 text-center whitespace-nowrap min-w-[150px]">Kode 108 &amp; NIBAR</th>
                                                <th class="px-3 py-2.5 text-center whitespace-nowrap">Volume</th>
                                                <th class="px-3 py-2.5 text-center whitespace-nowrap">Kondisi</th>
                                                <th class="px-3 py-2.5 text-right whitespace-nowrap min-w-[130px]">Taksiran Nilai (Rp)</th>
                                                <th class="px-3 py-2.5 text-center w-24 whitespace-nowrap">Aksi</th>
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
                                                <td colspan="3" class="px-3 py-2 text-right uppercase tracking-wider text-slate-400 whitespace-nowrap">Total Investasi Aset Mitra:</td>
                                                <td class="px-3 py-2 text-center text-teal-300 whitespace-nowrap" x-text="getTotalVolumeAsetDitambahkanMitra(selectedAstapDetail) + ' Unit'"></td>
                                                <td></td>
                                                <td class="px-3 py-2 text-right text-emerald-400 font-extrabold whitespace-nowrap" x-text="'Rp ' + formatRupiah(getTotalNilaiAsetDitambahkanMitra(selectedAstapDetail))"></td>
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
                                            <span class="text-amber-300 font-bold block" x-text="selectedAstapDetail.spesifikasi_json?.hak_tanah || selectedAstapDetail.spesifikasi_json?.tanah_hak || 'Hak Pakai'"></span>
                                            <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="(selectedAstapDetail.spesifikasi_json?.sertifikat_no || selectedAstapDetail.spesifikasi_json?.tanah_sertifikat_no) ? ('No: ' + (selectedAstapDetail.spesifikasi_json?.sertifikat_no || selectedAstapDetail.spesifikasi_json?.tanah_sertifikat_no)) : 'Tanpa No Sertifikat'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.spesifikasi_json?.tanah_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
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
                                                          :class="(mItem.is_extracom || selectedAstapDetail?.is_extracomtable || selectedAstapDetail?.spesifikasi?.is_extracomtable) ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                          x-text="(mItem.is_extracom || selectedAstapDetail?.is_extracomtable || selectedAstapDetail?.spesifikasi?.is_extracomtable) ? '📦 Extracom' : '⚙️ Reguler'"></span>
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
                                                    <span class="text-slate-300 text-[10px]" x-text="'Bhn: ' + (mItem.mesin_bahan || '-') + ' • Uk: ' + (mItem.mesin_ukuran || mItem.mesin_ukuran_cc || mItem.ukuran || mItem.ukuran_cc || selectedAstapDetail.spesifikasi_json?.ukuran || selectedAstapDetail.spesifikasi_json?.mesin_ukuran_cc || '-')"></span>
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
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📏 Ukuran / Kapasitas</span>
                                        <span class="text-cyan-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.ukuran || selectedAstapDetail.spesifikasi_json?.ukuran_cc || selectedAstapDetail.spesifikasi_json?.mesin_ukuran || selectedAstapDetail.spesifikasi_json?.mesin_ukuran_cc || '-'"></span>
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
                                                    <span class="text-xs font-bold text-white" x-text="gItem.gedung_nama_barang || gItem.gedung_nama_bangunan || selectedAstapDetail.nama_barang"></span>
                                                    <template x-if="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items').tooltip"
                                                              x-text="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="flex items-center space-x-3 text-[10.5px] font-mono">
                                                    <span class="text-slate-400">Luas: <strong class="text-cyan-300" x-text="(gItem.gedung_luas_lantai_m2 || gItem.gedung_luas_lantai || gItem.gedung_luas_m2 || selectedAstapDetail.spesifikasi_json?.luas_lantai_m2) ? ((gItem.gedung_luas_lantai_m2 || gItem.gedung_luas_lantai || gItem.gedung_luas_m2 || selectedAstapDetail.spesifikasi_json?.luas_lantai_m2) + ' M²') : '-'"></strong></span>
                                                    <span class="text-emerald-400 font-bold" x-text="'Rp ' + Number(gItem.gedung_nilai_satuan || gItem.gedung_nilai_fisik || (Number(gItem.gedung_nilai_perencanaan || 0) + Number(gItem.gedung_nilai_fisik || 0) + Number(gItem.gedung_nilai_pengawasan || 0)) || gItem.nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏢 Konstruksi</span>
                                                    <span class="text-purple-300 font-bold block" x-text="(gItem.gedung_konstruksi_bertingkat || gItem.gedung_bertingkat || 'Bertingkat') + ' • ' + (gItem.gedung_konstruksi_beton || gItem.gedung_beton || 'Beton')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status Tanah</span>
                                                    <span class="text-teal-300 font-semibold block truncate" x-text="gItem.gedung_status_tanah || 'Tanah Milik RSUD'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Komponen Nilai</span>
                                                    <span class="text-slate-300 block text-[9.5px]" x-text="gItem.gedung_nilai_fisik ? ('Fisik: Rp ' + Number(gItem.gedung_nilai_fisik).toLocaleString('id-ID')) : ('Taksiran: Rp ' + Number(gItem.gedung_nilai_satuan || gItem.nilai_satuan || 0).toLocaleString('id-ID'))"></span>
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
                                        <span class="text-purple-300 font-bold" x-text="(selectedAstapDetail.spesifikasi_json?.bertingkat || selectedAstapDetail.spesifikasi_json?.gedung_bertingkat || selectedAstapDetail.spesifikasi_json?.konstruksi_bertingkat || 'Bertingkat') + ' • ' + (selectedAstapDetail.spesifikasi_json?.beton || selectedAstapDetail.spesifikasi_json?.gedung_beton || selectedAstapDetail.spesifikasi_json?.konstruksi_beton || 'Beton')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Luas Lantai Gedung</span>
                                        <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.luas_lantai_m2 || selectedAstapDetail.spesifikasi_json?.gedung_luas_lantai || selectedAstapDetail.spesifikasi_json?.gedung_luas_lantai_m2 || selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.volume_satuan || '-') + ' m²'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🌱 Status Penguasaan Tanah</span>
                                        <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.status_tanah || selectedAstapDetail.spesifikasi_json?.gedung_status_tanah || 'Tanah Milik RSUD'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 4. KIB D (JALAN, IRIGASI DAN JARINGAN) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail) === 'KIB D'">
                        <div class="space-y-3">
                            <template x-if="getJaringanItemsForDetail(selectedAstapDetail).length > 0">
                                <div class="space-y-2.5">
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
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(jItem.jaringan_nilai_satuan || jItem.jaringan_nilai_fisik || selectedAstapDetail.nilai_aset || 0).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🛣️ Konstruksi</span>
                                                    <span class="text-teal-300 font-bold block" x-text="jItem.jaringan_konstruksi || selectedAstapDetail.spesifikasi_json?.konstruksi || 'Aspal / Beton'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Dimensi / Luas</span>
                                                    <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(jItem.jaringan_luas || jItem.jaringan_luas_m2 || selectedAstapDetail.spesifikasi_json?.luas_m2 || 0)).toLocaleString('id-ID') + ' m²' + (jItem.jaringan_panjang ? (' • P: ' + jItem.jaringan_panjang + (jItem.jaringan_lebar ? (' L: ' + jItem.jaringan_lebar) : '')) : '')"></span>
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

                            <!-- Fallback data single jaringan -->
                            <template x-if="getJaringanItemsForDetail(selectedAstapDetail).length === 0">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-teal-500/30 space-y-2.5 shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-teal-500/20 text-teal-300 font-mono font-bold text-[11px] border border-teal-500/30">
                                                Ruas / Titik #1
                                            </span>
                                            <span class="text-white font-bold text-xs" x-text="selectedAstapDetail.nama_barang"></span>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Total Taksiran: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="selectedAstapDetail.jumlah_realisasi || ('Rp ' + Number(selectedAstapDetail.nilai_aset || selectedAstapDetail.total_realisasi || 0).toLocaleString('id-ID'))"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🛣️ Konstruksi</span>
                                            <span class="text-teal-300 font-bold block" x-text="selectedAstapDetail.spesifikasi_json?.konstruksi || 'Aspal / Beton'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Dimensi / Luas</span>
                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.spesifikasi_json?.jaringan_luas || 0)).toLocaleString('id-ID') + ' m²' + (selectedAstapDetail.spesifikasi_json?.panjang_km ? (' • P: ' + selectedAstapDetail.spesifikasi_json.panjang_km + (selectedAstapDetail.spesifikasi_json?.lebar_m ? (' L: ' + selectedAstapDetail.spesifikasi_json.lebar_m) : '')) : '')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status Tanah</span>
                                            <span class="text-amber-300 font-semibold block truncate" x-text="selectedAstapDetail.spesifikasi_json?.status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Lokasi</span>
                                            <span class="text-teal-300 font-medium block truncate" :title="selectedAstapDetail.alamat_barang" x-text="selectedAstapDetail.alamat_barang || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 5. KIB E (ASET TETAP LAINNYA) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail) === 'KIB E'">
                        <div class="space-y-3">
                            <template x-if="getLainnyaItemsForDetail(selectedAstapDetail).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(lItem, lIdx) in getLainnyaItemsForDetail(selectedAstapDetail)" :key="lIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-purple-500/30 space-y-2.5 shadow-sm hover:border-purple-400/50 transition-all">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[11px] border border-purple-500/30"
                                                          x-text="'Item #' + (lIdx + 1)"></span>
                                                    <span class="text-white font-bold text-xs" x-text="lItem.lainnya_nama_barang || lItem.lainnya_judul || selectedAstapDetail.nama_barang"></span>
                                                    
                                                    <!-- Badge Status Akuntansi: Extracom vs Reguler -->
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                                          :class="(lItem.is_extracom || selectedAstapDetail?.is_extracomtable || selectedAstapDetail?.spesifikasi?.is_extracomtable) ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                          x-text="(lItem.is_extracom || selectedAstapDetail?.is_extracomtable || selectedAstapDetail?.spesifikasi?.is_extracomtable) ? '📦 Extracom' : '⚙️ Reguler'"></span>

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
                                                    <span class="text-[9.5px]" :class="(lItem.is_extracom || selectedAstapDetail?.is_extracomtable || selectedAstapDetail?.spesifikasi?.is_extracomtable) ? 'text-amber-400 font-medium' : 'text-slate-400'" x-text="(lItem.is_extracom || selectedAstapDetail?.is_extracomtable || selectedAstapDetail?.spesifikasi?.is_extracomtable) ? 'Maksimal Rp 300.000' : 'Aset Tetap Intrakomptabel'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Fallback data single KIB E -->
                            <template x-if="getLainnyaItemsForDetail(selectedAstapDetail).length === 0">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-purple-500/30 space-y-2.5 shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[11px] border border-purple-500/30">Item #1</span>
                                            <span class="text-white font-bold text-xs" x-text="selectedAstapDetail.spesifikasi_json?.judul || selectedAstapDetail.nama_barang"></span>
                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30" x-text="selectedAstapDetail.volume_satuan"></span>
                                        </div>
                                        <div class="text-[11px] font-mono">
                                            <span class="text-slate-400">Total Taksiran: </span>
                                            <strong class="text-emerald-400 font-bold" x-text="selectedAstapDetail.jumlah_realisasi || ('Rp ' + Number(selectedAstapDetail.nilai_aset || selectedAstapDetail.total_realisasi || 0).toLocaleString('id-ID'))"></strong>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Judul / Uraian Rincian</span>
                                            <span class="text-purple-300 font-bold block truncate" x-text="selectedAstapDetail.spesifikasi_json?.judul || selectedAstapDetail.nama_barang || '-'"></span>
                                            <span class="text-slate-400 text-[10px]" x-text="'Pencipta/Brand: ' + (selectedAstapDetail.spesifikasi_json?.pencipta || '-')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🧪 Bahan &amp; Ukuran</span>
                                            <span class="text-cyan-300 font-semibold block truncate" x-text="'Bhn: ' + (selectedAstapDetail.spesifikasi_json?.bahan || '-')"></span>
                                            <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Uk: ' + (selectedAstapDetail.spesifikasi_json?.ukuran || '-')"></span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Wajar Satuan</span>
                                            <span class="text-emerald-300 font-mono font-bold block text-[11px]" x-text="selectedAstapDetail.jumlah_realisasi || ('Rp ' + Number(selectedAstapDetail.nilai_aset || selectedAstapDetail.total_realisasi || 0).toLocaleString('id-ID'))"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- PIHAK REKANAN MITRA & PEJABAT PEMBUAT KOMITMEN (PPK RSUD) -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3.5">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                        <h4 class="text-xs font-extrabold text-teal-400 uppercase tracking-wider flex items-center space-x-2">
                            <span>🏢 Pihak Rekanan (Mitra) &amp; Pejabat Pembuat Komitmen (PPK RSUD)</span>
                        </h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400">Legalitas Para Pihak</span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5 text-[11px]">
                        <!-- Card Pihak Mitra Rekanan (Penyewa PKS) -->
                        <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-3">
                            <span class="text-[10.5px] font-extrabold text-cyan-400 uppercase tracking-wider block flex items-center space-x-1.5">
                                <span>🏢 Informasi Mitra Pihak Ketiga (Pemanfaat / Penyewa)</span>
                            </span>
                            <div class="space-y-2 divide-y divide-slate-800/60">
                                <div class="flex items-start justify-between gap-2 pt-1">
                                    <span class="text-slate-400 shrink-0">Nama Lembaga / Mitra PKS:</span>
                                    <strong class="text-white font-bold text-right" :title="selectedAstapDetail.mitra_nama || selectedAstapDetail.penyedia_nama" x-text="(selectedAstapDetail.mitra_nama !== '-' ? selectedAstapDetail.mitra_nama : selectedAstapDetail.penyedia_nama) || '-'"></strong>
                                </div>
                                <div class="flex items-start justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">Pimpinan / Direktur Mitra:</span>
                                    <strong class="text-cyan-300 font-bold text-right" :title="selectedAstapDetail.mitra_pimpinan || selectedAstapDetail.penyedia_pemilik" x-text="(selectedAstapDetail.mitra_pimpinan !== '-' ? selectedAstapDetail.mitra_pimpinan : selectedAstapDetail.penyedia_pemilik) || '-'"></strong>
                                </div>
                                <div class="flex items-start justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">Alamat Kantor Mitra:</span>
                                    <span class="text-teal-300 font-medium text-right leading-relaxed" :title="selectedAstapDetail.mitra_alamat || selectedAstapDetail.penyedia_alamat" x-text="(selectedAstapDetail.mitra_alamat !== '-' ? selectedAstapDetail.mitra_alamat : selectedAstapDetail.penyedia_alamat) || '-'"></span>
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
                                    <strong class="text-white font-bold text-right" :title="selectedAstapDetail.ppk_nama" x-text="selectedAstapDetail.ppk_nama || '-'"></strong>
                                </div>
                                <div class="flex items-center justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">NIP PPK (Kolom 25):</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-cyan-300 font-mono font-bold select-all" x-text="selectedAstapDetail.ppk_nip || '-'"></span>
                                        <template x-if="selectedAstapDetail.ppk_nip && selectedAstapDetail.ppk_nip !== '-'">
                                            <button type="button" @click.stop="navigator.clipboard.writeText(selectedAstapDetail.ppk_nip); showToast('NIP PPK disalin!', 'success')"
                                                    class="p-0.5 text-slate-500 hover:text-cyan-300 transition-colors cursor-pointer" title="Salin NIP">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-start justify-between gap-2 pt-2">
                                    <span class="text-slate-400 shrink-0">Keterangan / Catatan PKS:</span>
                                    <span class="text-slate-300 italic text-right leading-relaxed" :title="selectedAstapDetail.keterangan" x-text="selectedAstapDetail.keterangan || '-'"></span>
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
                                    <th class="px-3.5 py-2.5 text-center">NIBAR RESMI</th>
                                    <th class="px-3.5 py-2.5 text-center">Lokasi / Pengelolaan</th>
                                    <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                    <th class="px-3.5 py-2.5 text-center whitespace-nowrap">QR Code</th>
                                    <th class="px-3.5 py-2.5 text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                <template x-for="reg in filteredRegisters" :key="reg.id">
                                    <tr class="hover:bg-slate-800/60 transition-colors">
                                        <!-- 1. NIBAR & No Register Resmi -->
                                        <td class="px-4 py-3 text-center whitespace-nowrap align-middle">
                                            <div class="inline-flex items-center justify-center gap-1.5 group/copy">
                                                <span class="font-mono text-[11px] font-bold text-slate-200 tracking-tight group-hover/copy:text-cyan-300 transition-colors select-all"
                                                      :title="'NIBAR Resmi: ' + (reg.nibar || reg.no_register)"
                                                      x-text="reg.nibar || reg.no_register"></span>
                                                <button type="button" @click.stop="navigator.clipboard.writeText(reg.nibar || reg.no_register); showToast('NIBAR berhasil disalin ke clipboard!', 'success')"
                                                        title="Salin NIBAR ke Clipboard"
                                                        class="p-1 rounded-md bg-slate-800/60 hover:bg-cyan-500/20 text-slate-400 hover:text-cyan-300 border border-slate-700/60 hover:border-cyan-500/30 transition-all cursor-pointer">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <!-- 2. Lokasi / Pengelolaan -->
                                        <td class="px-4 py-3 text-center align-middle">
                                            <template x-if="reg.ruang_pemegang">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-xs font-semibold shadow-sm max-w-[220px]">
                                                    <span class="text-teal-400 text-xs shrink-0">📍</span>
                                                    <span class="truncate" :title="reg.ruang_pemegang" x-text="reg.ruang_pemegang"></span>
                                                </div>
                                            </template>
                                            <template x-if="!reg.ruang_pemegang">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-cyan-950/80 border border-cyan-500/40 text-cyan-200 font-bold text-[11px] shadow-sm max-w-[220px]">
                                                    <span class="text-xs shrink-0">🤝</span>
                                                    <span class="truncate" x-text="selectedAstapDetail?.kemitraan?.mitra_nama ? ('Mitra: ' + selectedAstapDetail.kemitraan.mitra_nama) : 'Konsesi Kemitraan (Mitra)'"></span>
                                                </div>
                                            </template>
                                        </td>
                                        <!-- 3. Kondisi Fisik -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold border inline-flex items-center gap-1.5 shadow-sm"
                                                  :class="{
                                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': reg.kondisi === 'Baik' || reg.kondisi === 'B',
                                                      'bg-amber-500/20 text-amber-300 border-amber-500/40': reg.kondisi === 'Kurang Baik' || reg.kondisi === 'KB' || reg.kondisi === 'Rusak Ringan' || reg.kondisi === 'RR',
                                                      'bg-rose-500/20 text-rose-300 border-rose-500/40': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'RB' || reg.kondisi === 'Rusak'
                                                  }">
                                                <span class="w-1.5 h-1.5 rounded-full"
                                                      :class="{
                                                          'bg-emerald-400': reg.kondisi === 'Baik' || reg.kondisi === 'B',
                                                          'bg-amber-400': reg.kondisi === 'Kurang Baik' || reg.kondisi === 'KB' || reg.kondisi === 'Rusak Ringan' || reg.kondisi === 'RR',
                                                          'bg-rose-400': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'RB' || reg.kondisi === 'Rusak'
                                                      }"></span>
                                                <span x-text="reg.kondisi === 'B' ? 'Baik' : ((reg.kondisi === 'KB' || reg.kondisi === 'RR' || reg.kondisi === 'Rusak Ringan') ? 'Kurang Baik' : ((reg.kondisi === 'RB' || reg.kondisi === 'Rusak') ? 'Rusak Berat' : (reg.kondisi || 'Baik')))"></span>
                                            </span>
                                        </td>
                                        <!-- 4. QR Code Download -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <button type="button" @click.stop="downloadQrCodeNibar(reg, selectedAstapDetail)"
                                                title="Pratinjau & Download QR NIBAR Aset Ini"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/25 border border-cyan-500/30 hover:border-cyan-400 text-cyan-400 hover:text-cyan-300 font-bold text-[10.5px] transition-all shadow-sm active:scale-95 group cursor-pointer leading-none">
                                                <svg class="w-3.5 h-3.5 text-cyan-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-3.5-3.5M12 16l3.5-3.5M4 20h16"/>
                                                </svg>
                                                <span>Download QR</span>
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

                <!-- SECTION TERMINASI / AKHIR MASA KONSESI & PERUBAHAN STATUS (KHUSUS ASET YANG DITAMBAHKAN MITRA) -->
                <template x-if="selectedAstapDetail && !selectedAstapDetail.is_dimanfaatkan">
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-[11px] font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                                    <span>⚖️</span>
                                    <span>Penyelesaian Akhir Masa Konsesi (Terminasi Kemitraan)</span>
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    Eksekusi penyelesaian hukum & fisik aset: Pengembalian ke mitra atau penetapan hak milik RSUD.
                                </p>
                            </div>
                            <template x-if="selectedAstapDetail.is_transferred_to_rsud || selectedAstapDetail.status_konsesi === 'Selesai / Reklasifikasi'">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/10 text-emerald-300 border border-emerald-500/30">
                                    🏛️ Resmi Milik RSUD
                                </span>
                            </template>
                            <template x-if="selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan'">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-purple-500/10 text-purple-300 border border-purple-500/30">
                                    📦 Telah Dikembalikan
                                </span>
                            </template>
                        </div>

                        <!-- KONDISI A: JIKA SUDAH PERNAH DITERMINASI (TAMPILKAN RINGKASAN BA TERMINASI) -->
                        <template x-if="selectedAstapDetail.nomor_ba_terminasi || selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan' || selectedAstapDetail.status_konsesi === 'Selesai / Reklasifikasi'">
                            <div class="p-3.5 rounded-xl border space-y-2.5"
                                 :class="selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan' ? 'bg-purple-950/20 border-purple-500/30' : 'bg-emerald-950/20 border-emerald-500/30'">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold"
                                          :class="selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan' ? 'text-purple-300' : 'text-emerald-300'"
                                          x-text="selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan' ? '📦 Ringkasan Berita Acara Pengembalian ke Mitra' : '🏛️ Ringkasan Berita Acara Serah Terima Aset Milik RSUD'"></span>
                                    <span class="text-[10px] font-mono text-slate-400" x-text="selectedAstapDetail.tanggal_terminasi ? formatTanggalIndo(selectedAstapDetail.tanggal_terminasi) : '-'"></span>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px]">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Nomor Berita Acara:</span>
                                        <span class="font-mono font-bold text-white" x-text="selectedAstapDetail.nomor_ba_terminasi || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Kondisi Fisik Akhir:</span>
                                        <span class="font-semibold text-amber-300" x-text="selectedAstapDetail.kondisi_akhir || selectedAstapDetail.kondisi || 'Baik'"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Penyerah / Penerima:</span>
                                        <span class="text-slate-200 truncate block" x-text="(selectedAstapDetail.pihak_penyerah_nama || '-') + ' → ' + (selectedAstapDetail.pihak_penerima_nama || '-')"></span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-800">
                                    <button type="button" @click="printTerminasiFromDetail()"
                                            class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 flex items-center gap-1.5 cursor-pointer">
                                        <span>🖨️ Cetak Lembar BAST Terminasi</span>
                                    </button>
                                    <template x-if="selectedAstapDetail.berkas_ba_terminasi">
                                        <a :href="'/storage/' + selectedAstapDetail.berkas_ba_terminasi" target="_blank"
                                           class="px-3 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
                                            <span>📎 Unduh Berkas Scan BAST</span>
                                        </a>
                                    </template>

                                    <!-- Tombol Batal Pengembalian ke Rekanan -->
                                    <template x-if="selectedAstapDetail.status_konsesi === 'Dikembalikan ke Rekanan'">
                                        <button type="button" @click="batalDikembalikanKeMitra(selectedAstapDetail)"
                                                class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:border-rose-400 text-xs font-bold flex items-center gap-1.5 cursor-pointer transition-all ml-auto">
                                            <span>↩️ Batalkan Pengembalian ke Rekanan</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- TOMBOL AKSI PENGEMBALIAN KE MITRA (SEMBUNYIKAN JIKA SUDAH DIKEMBALIKAN / RESMI RSUD / DIBATALKAN) -->
                        <div x-show="selectedAstapDetail.status_konsesi !== 'Dikembalikan ke Rekanan' && selectedAstapDetail.status_konsesi !== 'Selesai / Reklasifikasi' && selectedAstapDetail.status_konsesi !== 'Dibatalkan'">
                            <button type="button" @click="openTerminasiModal(selectedAstapDetail, 'kembalikan')"
                                    class="w-full p-4 rounded-2xl bg-gradient-to-br from-purple-950/40 via-slate-900 to-slate-950 border border-purple-500/30 hover:border-purple-400 text-left transition-all hover:scale-[1.005] hover:shadow-lg hover:shadow-purple-500/20 group cursor-pointer">
                                <div class="flex items-center gap-2.5 mb-1.5">
                                    <span class="text-xl group-hover:scale-110 transition-transform">📦</span>
                                    <span class="text-xs font-extrabold text-purple-300 group-hover:text-purple-200">
                                        Kembalikan Aset ke Mitra Rekanan
                                    </span>
                                    <span class="ml-auto text-[9.5px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 font-mono">
                                        Masa Pakai Selesai
                                    </span>
                                </div>
                                <p class="text-[10.5px] text-slate-400 leading-relaxed">
                                    Untuk aset titipan mitra (sewa/KSO) yang masa pakainya telah selesai dan dikembalikan secara resmi ke pihak rekanan swasta melalui Berita Acara (BAST).
                                </p>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- SECTION STATUS & PEMBATALAN KERJA SAMA KEMITRAAN (BESPOKE HIGH-END DARK UI) -->
                <div class="space-y-3">
                    <!-- KONDISI 1: JIKA STATUS KEMITRAAN TELAH DIBATALKAN -->
                    <template x-if="selectedAstapDetail && selectedAstapDetail.status_konsesi === 'Dibatalkan'">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-rose-950/40 via-slate-900 to-slate-950 border border-rose-500/40 shadow-xl shadow-rose-950/30 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-rose-500/20 pb-3">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-lg shrink-0 shadow-inner">
                                        🚫
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-extrabold uppercase font-mono tracking-widest text-rose-400 block">
                                            Status Penghentian Konsesi
                                        </span>
                                        <h4 class="text-xs font-black text-white">
                                            Kemitraan Dibatalkan Secara Resmi
                                        </h4>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase font-mono bg-rose-500/20 text-rose-300 border border-rose-500/40 shrink-0 self-start sm:self-auto">
                                    🚫 Status: Dibatalkan
                                </span>
                            </div>

                            <!-- Detail Alasan & Log Pembatalan -->
                            <div class="p-3 rounded-xl bg-slate-950/90 border border-slate-800 space-y-2">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">
                                    Alasan / Keterangan Pembatalan:
                                </span>
                                <p class="text-xs text-rose-200 font-medium leading-relaxed italic bg-rose-950/20 p-2.5 rounded-lg border border-rose-500/20"
                                   x-text="selectedAstapDetail.alasan_batal || 'Tidak ada keterangan alasan yang tercatat.'"></p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 text-[11px] text-slate-400">
                                    <div>
                                        <span class="text-[10px] block">Dibatalkan Pada:</span>
                                        <span class="font-mono text-slate-200 font-semibold" x-text="selectedAstapDetail.dibatalkan_at ? formatTanggalIndo(selectedAstapDetail.dibatalkan_at) : '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] block">Dibatalkan Oleh:</span>
                                        <span class="text-slate-200 font-semibold" x-text="selectedAstapDetail.dibatalkan_by || 'Administrator'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Penjelasan Kontekstual Berdasarkan Tipe Aset -->
                            <template x-if="selectedAstapDetail.is_dimanfaatkan">
                                <div class="p-3 rounded-xl bg-cyan-950/30 border border-cyan-500/30 text-[11px] text-cyan-200/90 leading-relaxed flex items-start gap-2.5">
                                    <span class="text-base shrink-0">🏛️</span>
                                    <p>
                                        <strong>Status Aset BMD RSUD:</strong> Aset fisik daerah ini telah otomatis dikembalikan ke status operasional semula (<strong>Tersedia</strong>) pada KIB sebelumnya. Tato dan catatan riwayat kemitraan tetap tersimpan dan tidak dihapus untuk kepentingan riwayat audit akuntabilitas.
                                    </p>
                                </div>
                            </template>
                            <template x-if="!selectedAstapDetail.is_dimanfaatkan">
                                <div class="p-3 rounded-xl bg-amber-950/30 border border-amber-500/30 text-[11px] text-amber-200/90 leading-relaxed flex items-start gap-2.5">
                                    <span class="text-base shrink-0">📦</span>
                                    <p>
                                        <strong>Status Aset Ditambahkan Mitra:</strong> Aset rekanan ini dinonaktifkan permanen dan dikunci dari reklasifikasi atau pengubahan data. Tindakan yang diizinkan hanya melihat detail riwayat atau menghapus aset ke tempat sampah.
                                    </p>
                                </div>
                            </template>

                            <!-- Tombol Pulihkan Status (Batal Pembatalan) -->
                            <div class="pt-2 border-t border-slate-800 flex justify-end">
                                <button type="button" @click="batalPembatalanKemitraan(selectedAstapDetail)"
                                        class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                                    <span>↩️ Pulihkan Status Kemitraan (Batal Pembatalan)</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- KONDISI 2: JIKA STATUS KEMITRAAN BELUM DIBATALKAN / BELUM DITERMINASI -->
                    <template x-if="selectedAstapDetail && selectedAstapDetail.status_konsesi !== 'Dibatalkan' && selectedAstapDetail.status_konsesi !== 'Dikembalikan ke Rekanan' && selectedAstapDetail.status_konsesi !== 'Selesai / Reklasifikasi'">
                        <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center text-lg shrink-0">
                                    🚫
                                </div>
                                <div>
                                    <span class="text-xs font-extrabold text-white block">
                                        Batalkan Status Kemitraan Ini
                                    </span>
                                    <p class="text-[10px] text-slate-400 leading-relaxed">
                                        Hentikan atau batalkan status konsesi kerja sama aset ini dengan mencatat alasan pembatalan secara resmi.
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="openModalBatalKemitraan(selectedAstapDetail)"
                                    class="px-4 py-2 rounded-xl bg-rose-500/15 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/40 hover:border-rose-500 font-extrabold text-xs transition-all duration-200 shrink-0 flex items-center justify-center gap-1.5 active:scale-95 cursor-pointer shadow-sm">
                                <span>🚫 Batalkan Kemitraan</span>
                            </button>
                        </div>
                    </template>
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

                <!-- Tombol Extend / Perpanjang Masa Konsesi PKS -->
                <template x-if="selectedAstapDetail && selectedAstapDetail.status_konsesi !== 'Dikembalikan ke Rekanan' && selectedAstapDetail.status_konsesi !== 'Selesai' && selectedAstapDetail.status_konsesi !== 'Selesai / Reklasifikasi' && selectedAstapDetail.status_konsesi !== 'Dihentikan' && selectedAstapDetail.status_konsesi !== 'Dibatalkan'">
                    <button type="button" @click="openExtendModalFromDetail()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs transition-all shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:scale-95 flex items-center gap-2 cursor-pointer"
                        title="Perpanjang Masa Konsesi (Buat Adendum PKS)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Perpanjang PKS (Extend)</span>
                    </button>
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
