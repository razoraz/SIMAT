<!-- FRONTEND MODAL: DETAIL MUTASI EKSTERNAL (PELIMPAHAN SKPD) & RINCIAN REGISTER NIBAR -->
<!-- Dibuat seragam dan komprehensif mengikuti standar Data ASTAP -->
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
        
        <!-- 1. MODAL HEADER -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="space-y-1.5 min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- KIB Category Badge -->
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                        :class="{
                            'bg-amber-400/20 text-amber-300 border-amber-400/30': (selectedAstapDetail || selectedMutasi)?.category === 'EXTRACOM',
                            'bg-amber-500/20 text-amber-300 border-amber-500/30': getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB A',
                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB B',
                            'bg-purple-500/20 text-purple-300 border-purple-500/30': getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB C',
                            'bg-teal-500/20 text-teal-300 border-teal-500/30':     getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB D',
                            'bg-orange-500/20 text-orange-300 border-orange-500/30': getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB E',
                            'bg-rose-500/20 text-rose-300 border-rose-500/30':     getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB F',
                            'bg-indigo-500/20 text-indigo-300 border-indigo-500/30': getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'ATB'
                        }"
                        x-text="(selectedAstapDetail || selectedMutasi)?.category === 'EXTRACOM' ? '📦 EXTRACOM' : getEffectiveKibCategory(selectedAstapDetail || selectedMutasi)"></span>

                    <!-- Source Badge -->
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                        :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30' : 'bg-purple-500/20 text-purple-300 border-purple-500/30'"
                        x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? '📤 TRANSFER KE OPD LUAR (MUTASI KELUAR)' : '🔄 DARI OPD LUAR (MUTASI MASUK)'">
                    </span>

                    <!-- Status BAST Badge -->
                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                        :class="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')
                            ? 'bg-rose-500/20 text-rose-300 border-rose-500/40'
                            : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'"
                        x-text="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal') ? '✕ Dibatalkan' : '✓ Disahkan (Selesai)'"></span>

                    <!-- Kode Barang 108 -->
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full"
                        x-text="'Kode: ' + ((selectedAstapDetail || selectedMutasi)?.kode_barang || (selectedAstapDetail || selectedMutasi)?.kode_108 || '-')"></span>

                    <!-- Tanggal BAST / Dokumen -->
                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                        <span class="text-slate-400">📅 Tanggal BAST:</span>
                        <span class="text-cyan-300 font-bold" x-text="formatTanggalIndo((selectedAstapDetail || selectedMutasi)?.mutasi_tanggal || (selectedAstapDetail || selectedMutasi)?.tgl_raw || (selectedAstapDetail || selectedMutasi)?.tgl)"></span>
                    </span>

                    <!-- Kondisi Badge -->
                    <template x-if="selectedAstapDetail || selectedMutasi">
                        <span class="px-2.5 py-0.5 rounded-lg border text-[11px] font-semibold flex items-center space-x-1.5 shrink-0"
                              :class="getKondisiStats(selectedAstapDetail || selectedMutasi).badge_class">
                            <span class="w-1.5 h-1.5 rounded-full" :class="getKondisiStats(selectedAstapDetail || selectedMutasi).dot_class"></span>
                            <span x-text="'Kondisi: ' + getKondisiStats(selectedAstapDetail || selectedMutasi).text"></span>
                        </span>
                    </template>
                </div>

                <!-- Nama Barang Title -->
                <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words"
                    x-text="(selectedAstapDetail || selectedMutasi)?.nama_murni || (selectedAstapDetail || selectedMutasi)?.nama_barang || (selectedAstapDetail || selectedMutasi)?.nama || ''"></h3>
            </div>

            <!-- Tombol Action Header: Cetak & Close -->
            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" @click="showDetailModal = false"
                    class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <template x-if="selectedAstapDetail || selectedMutasi">
            <div class="space-y-4 text-xs text-slate-300">

                <!-- 1. BANNER STATUS DIBATALKAN (JIKA DIBATALKAN) -->
                <template x-if="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                    <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/40 text-rose-200 shadow-lg shadow-rose-950/30 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-400 animate-pulse"></span>
                                <span class="font-extrabold text-xs uppercase tracking-wider text-rose-300">🚫 Transaksi Mutasi Eksternal Ini Telah Dibatalkan</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md bg-rose-500/20 text-rose-300 font-mono text-[10px] font-bold border border-rose-500/30 uppercase">Status: Dibatalkan</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] pt-1 border-t border-rose-500/20">
                            <div>
                                <span class="text-rose-400 block text-[10px] uppercase font-bold">Dibatalkan Oleh:</span>
                                <span class="font-bold text-white" x-text="(selectedAstapDetail || selectedMutasi)?.dibatalkan_by || 'Petugas SIMAT'"></span>
                            </div>
                            <div>
                                <span class="text-rose-400 block text-[10px] uppercase font-bold">Waktu Pembatalan:</span>
                                <span class="font-mono text-white" x-text="(selectedAstapDetail || selectedMutasi)?.dibatalkan_at || '-'"></span>
                            </div>
                        </div>
                        <div class="pt-1 border-t border-rose-500/20">
                            <span class="text-rose-400 block text-[10px] uppercase font-bold">Alasan Pembatalan:</span>
                            <p class="text-slate-200 italic text-xs leading-relaxed" x-text="(selectedAstapDetail || selectedMutasi)?.alasan_batal || '-'"></p>
                        </div>
                    </div>
                </template>

                <!-- 2. TOP 4 METRIC KPI CARDS (RESPONSIVE GRID PERSIS ASTAP) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏷️ Jenis PMDN 108</span>
                        <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate"
                              :title="(selectedAstapDetail || selectedMutasi).jenis_aset_nama || (selectedAstapDetail || selectedMutasi).kode_108"
                              x-text="(selectedAstapDetail || selectedMutasi).jenis_aset_nama || (selectedAstapDetail || selectedMutasi).kode_108 || '-'"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Tahun Perolehan</span>
                        <span class="text-cyan-300 font-extrabold font-mono text-xs sm:text-sm block"
                              x-text="(selectedAstapDetail || selectedMutasi).tahun_perolehan || '-'"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📏 Volume / Satuan</span>
                        <span class="text-teal-300 font-extrabold font-mono text-xs sm:text-sm block truncate"
                              x-text="((selectedAstapDetail || selectedMutasi).jumlah_volume || 1) + ' ' + ((selectedAstapDetail || selectedMutasi).satuan || 'Aset')"></span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Realisasi Perolehan</span>
                        <span class="text-emerald-400 font-extrabold font-mono text-xs sm:text-sm block truncate"
                              x-text="(selectedAstapDetail || selectedMutasi).jumlah_realisasi || (selectedAstapDetail || selectedMutasi).nilai_perolehan_formatted || formatRupiah((selectedAstapDetail || selectedMutasi).nilai_perolehan)"></span>
                    </div>
                </div>

                <!-- 3. DOKUMEN BAMB / BAST -->
                <div class="p-4 rounded-2xl border space-y-2.5 shadow-sm"
                    :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'bg-cyan-500/10 border-cyan-500/30' : 'bg-purple-500/10 border-purple-500/30'">
                    <div class="flex items-center justify-between font-extrabold text-xs uppercase tracking-wider border-b pb-2"
                        :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'text-cyan-300 border-cyan-500/20' : 'text-purple-300 border-purple-500/20'">
                        <span class="flex items-center space-x-1.5">
                            <span x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? '📜 Dokumen Berita Acara Serah Terima (BAST) / Transfer Keluar' : '📜 Dokumen Berita Acara Mutasi Barang (BAMB) / Mutasi Eksternal Masuk'"></span>
                        </span>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded border"
                            :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'bg-cyan-950/80 border-cyan-500/40 text-cyan-300' : 'bg-purple-950/80 border-purple-500/40 text-purple-300'"
                            x-text="'Nomor BAST: ' + ((selectedAstapDetail || selectedMutasi).kode || (selectedAstapDetail || selectedMutasi).mutasi_nomor_bamb || '-')"></span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold"
                                x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Instansi / SKPD Penerima:' : 'Instansi / OPD Pengirim:'"></span>
                            <span class="font-bold text-white text-sm"
                                x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? ((selectedAstapDetail || selectedMutasi).opd_tujuan || '-') : ((selectedAstapDetail || selectedMutasi).opd_asal || (selectedAstapDetail || selectedMutasi).mutasi_asal || '-')"></span>
                            <template x-if="(selectedAstapDetail || selectedMutasi).alamat_instansi">
                                <p class="text-[10.5px] text-slate-300 mt-0.5" x-text="'📍 ' + (selectedAstapDetail || selectedMutasi).alamat_instansi"></p>
                            </template>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold"
                                x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Nomor BAST Serah Terima:' : 'Nomor BAMB / SK Mutasi Masuk:'"></span>
                            <span class="font-mono font-bold"
                                :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'text-cyan-300' : 'text-purple-300'"
                                x-text="(selectedAstapDetail || selectedMutasi).mutasi_nomor_bamb || (selectedAstapDetail || selectedMutasi).kode || '-'"></span>
                            <template x-if="(selectedAstapDetail || selectedMutasi).nomor_sk_dasar">
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'SK Dasar: ' + (selectedAstapDetail || selectedMutasi).nomor_sk_dasar"></p>
                            </template>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Dokumen:</span>
                            <span class="text-slate-200 font-medium font-mono" x-text="formatTanggalIndo((selectedAstapDetail || selectedMutasi).mutasi_tanggal || (selectedAstapDetail || selectedMutasi).tgl_raw)"></span>
                        </div>
                    </div>
                    <template x-if="(selectedAstapDetail || selectedMutasi).alasan_mutasi || (selectedAstapDetail || selectedMutasi).mutasi_keterangan">
                        <div class="pt-2 border-t text-[11px] text-slate-300"
                            :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'border-cyan-500/20' : 'border-purple-500/20'">
                            <span class="text-slate-400 font-semibold">Maksud / Keterangan: </span>
                            <span x-text="(selectedAstapDetail || selectedMutasi).alasan_mutasi || (selectedAstapDetail || selectedMutasi).mutasi_keterangan"></span>
                        </div>
                    </template>
                </div>

                <!-- 4. PIHAK YANG TERLIBAT -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Pihak Pertama -->
                    <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2">
                        <div class="flex items-center space-x-1.5 border-b border-slate-800 pb-1.5 text-slate-300 font-bold uppercase tracking-wider text-[11px]">
                            <span>📤</span>
                            <span x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'PIHAK PERTAMA (PENYERAH RSUD)' : 'PIHAK PERTAMA (SKPD PENGIRIM)'"></span>
                        </div>
                        <div class="space-y-1">
                            <div>
                                <span class="text-slate-400 text-[10px] block">Pejabat / Pihak yang Menyerahkan:</span>
                                <p class="text-emerald-400 font-bold text-xs" x-text="(selectedAstapDetail || selectedMutasi).pj_asal_nama || ((selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Pengurus Barang RSUD Dr. H. Koesnadi' : 'Pejabat Penyerah OPD Asal')"></p>
                                <p class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + ((selectedAstapDetail || selectedMutasi).pj_asal_nip || '-')"></p>
                                <p class="text-[10px] text-slate-400" x-text="(selectedAstapDetail || selectedMutasi).pj_asal_jabatan || ((selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Pengurus Barang Pengguna RSUD' : 'Pengurus Barang / PPK Asal')"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Pihak Kedua -->
                    <div class="p-3.5 rounded-2xl border space-y-2"
                        :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'bg-cyan-950/20 border-cyan-500/30' : 'bg-indigo-950/20 border-indigo-500/30'">
                        <div class="flex items-center space-x-1.5 border-b pb-1.5 font-bold uppercase tracking-wider text-[11px]"
                            :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'border-cyan-500/30 text-cyan-300' : 'border-indigo-500/30 text-indigo-300'">
                            <span>📥</span>
                            <span x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'PIHAK KEDUA (PENERIMA OPD LUAR)' : 'PIHAK KEDUA (PENERIMA RSUD KOESNANDI)'"></span>
                        </div>
                        <div class="space-y-1">
                            <div>
                                <span class="text-slate-400 text-[10px] block" x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Instansi / SKPD Penerima:' : 'Unit / Ruangan Penempatan Baru:'"></span>
                                <p class="font-bold text-xs"
                                    :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'text-cyan-200' : 'text-indigo-200'"
                                    x-text="(selectedAstapDetail || selectedMutasi).ruangan_tujuan || (selectedAstapDetail || selectedMutasi).opd_tujuan || 'RSUD Dr. H. Koesnadi'"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10px] block">Pejabat Penerima:</span>
                                <p class="text-white font-bold text-xs" x-text="(selectedAstapDetail || selectedMutasi).pejabat_opd_tujuan || (selectedAstapDetail || selectedMutasi).pj_tujuan_nama || (selectedAstapDetail || selectedMutasi).ppk_nama || 'Pejabat Penerima'"></p>
                                <p class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + ((selectedAstapDetail || selectedMutasi).nip_pejabat_opd_tujuan || (selectedAstapDetail || selectedMutasi).pj_tujuan_nip || (selectedAstapDetail || selectedMutasi).ppk_nip || '-')"></p>
                                <p class="text-[10px] font-semibold"
                                    :class="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'text-cyan-400/90' : 'text-indigo-400/90'"
                                    x-text="(selectedAstapDetail || selectedMutasi).jabatan_opd_tujuan || ((selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Pejabat Penerima Instansi Luar' : 'Pengurus Barang Pengguna RSUD Dr. H. Koesnadi')"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. DYNAMIC SPESIFIKASI BERDASARKAN JENIS ASET (KIB A - E) PERSIS ASTAP -->
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider flex items-center space-x-1.5"
                            :class="{
                                'text-amber-400': getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB A',
                                'text-cyan-400':  getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB B',
                                'text-purple-400': getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB C',
                                'text-teal-400':  getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB D',
                                'text-orange-400': getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB E',
                                'text-amber-300': (selectedAstapDetail || selectedMutasi)?.category === 'EXTRACOM'
                            }">
                            <span>🔍 <span x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Rincian Spesifikasi Aset Mutasi Keluar' : 'Rincian Spesifikasi Mutasi Masuk'"></span> (<span x-text="getEffectiveKibCategory(selectedAstapDetail || selectedMutasi)"></span>)</span>
                        </h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400"
                              x-text="'Spesifikasi Fisik ' + getEffectiveKibCategory(selectedAstapDetail || selectedMutasi)"></span>
                    </div>

                    <!-- KIB A (TANAH) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB A'">
                        <div class="space-y-2.5">
                            <template x-if="getTanahItemsForDetail(selectedAstapDetail || selectedMutasi).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(tItem, tIdx) in getTanahItemsForDetail(selectedAstapDetail || selectedMutasi)" :key="tIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                        🌾 Bidang Tanah #<span x-text="tIdx + 1"></span>
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                          x-text="(tItem.tanah_jumlah_bidang || tItem.tanah_jumlah_barang || 1) + ' ' + ((selectedAstapDetail || selectedMutasi).satuan || 'Bidang')"></span>
                                                    <template x-if="getRincianNibar(selectedAstapDetail || selectedMutasi, tIdx, 'tanah_items')">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedAstapDetail || selectedMutasi, tIdx, 'tanah_items').tooltip"
                                                              x-text="getRincianNibar(selectedAstapDetail || selectedMutasi, tIdx, 'tanah_items').label"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Nilai Perolehan: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="formatRupiah(tItem.tanah_nilai_fisik || tItem.tanah_nilai_satuan || (selectedAstapDetail || selectedMutasi).nilai_perolehan)"></strong>
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
                                                    <span class="text-slate-300 text-[10px]" x-text="'Kondisi: ' + (tItem.tanah_kondisi || 'Baik')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📝 Penggunaan Khusus</span>
                                                    <span class="text-slate-200 font-medium block truncate" x-text="tItem.tanah_penggunaan || 'Fasilitas Kesehatan'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Lokasi</span>
                                                    <span class="text-teal-300 font-medium block truncate" :title="tItem.tanah_alamat" x-text="tItem.tanah_alamat || (selectedAstapDetail || selectedMutasi).alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="getTanahItemsForDetail(selectedAstapDetail || selectedMutasi).length === 0">
                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-[11px]">
                                        <div>
                                            <span class="text-slate-400 block text-[10px]">Status Hak:</span>
                                            <span class="text-amber-300 font-bold" x-text="(selectedAstapDetail || selectedMutasi).spesifikasi_json?.hak_tanah || 'Hak Pakai'"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px]">Luas Tanah:</span>
                                            <span class="text-cyan-300 font-mono font-bold" x-text="(Number((selectedAstapDetail || selectedMutasi).spesifikasi_json?.luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px]">Letak / Alamat:</span>
                                            <span class="text-emerald-300 font-semibold" x-text="(selectedAstapDetail || selectedMutasi).alamat_barang || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- KIB B (PERALATAN & MESIN) & EXTRACOM -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB B' || (selectedAstapDetail || selectedMutasi)?.category === 'EXTRACOM'">
                        <div class="space-y-3">
                            <template x-if="getMesinItemsForDetail(selectedAstapDetail || selectedMutasi).length > 0">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                        <span class="flex items-center gap-1.5 text-cyan-400">
                                            📦 Rincian Barang Terdaftar (<span x-text="getMesinItemsForDetail(selectedAstapDetail || selectedMutasi).length"></span> Item)
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + ((selectedAstapDetail || selectedMutasi).jumlah_volume || 1) + ' ' + ((selectedAstapDetail || selectedMutasi).satuan || 'Unit')"></span>
                                    </div>
                                    <template x-for="(mItem, mIdx) in getMesinItemsForDetail(selectedAstapDetail || selectedMutasi)" :key="mIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 space-y-2.5 shadow-sm hover:border-cyan-400/50 transition-all">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-mono font-bold text-[11px] border border-cyan-500/30"
                                                          x-text="'Item #' + (mIdx + 1)"></span>
                                                    <template x-if="mItem.is_extracom || (parseFloat(mItem.mesin_nilai_satuan) || 0) <= 300000">
                                                        <span class="px-2 py-0.5 rounded-lg bg-amber-500/20 text-amber-300 font-bold text-[10px] border border-amber-500/40">
                                                            📦 EXTRACOM (≤ Rp 300rb)
                                                        </span>
                                                    </template>
                                                    <span class="text-white font-bold text-xs" x-text="(mItem.mesin_nama_barang ? (mItem.mesin_nama_barang + ' • ') : '') + ((mItem.mesin_merk || mItem.mesin_type) ? ((mItem.mesin_merk || '') + ' ' + (mItem.mesin_type || '')) : '-')"></span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                          x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || 'Unit')"></span>
                                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold border"
                                                          :class="mItem.mesin_kondisi === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'"
                                                          x-text="'Kondisi: ' + (mItem.mesin_kondisi || 'Baik')"></span>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Subtotal: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="formatRupiah((parseFloat(mItem.mesin_jumlah_barang) || 1) * (parseFloat(mItem.mesin_nilai_satuan) || 0))"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Merk / Type / Bahan</span>
                                                    <span class="text-cyan-300 font-bold block truncate" x-text="(mItem.mesin_merk || '-') + ' / ' + (mItem.mesin_type || '-')"></span>
                                                    <span class="text-slate-300 text-[9.5px]" x-text="'Bahan: ' + (mItem.mesin_bahan || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔢 No. Pabrik / Rangka / Mesin</span>
                                                    <span class="text-slate-200 font-mono block truncate" x-text="'Pabrik: ' + (mItem.mesin_no_pabrik || '-')"></span>
                                                    <span class="text-slate-400 font-mono text-[9px] block truncate" x-text="'Rangka: ' + (mItem.mesin_no_rangka || '-')"></span>
                                                    <span class="text-slate-400 font-mono text-[9px] block truncate" x-text="'Mesin: ' + (mItem.mesin_no_mesin || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🚗 No. Polisi &amp; BPKB</span>
                                                    <span class="text-amber-300 font-mono font-bold block" x-text="mItem.mesin_no_polisi || '-'"></span>
                                                    <span class="text-slate-400 font-mono text-[9px] block" x-text="'BPKB: ' + (mItem.mesin_no_bpkb || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏥 Ruangan / Unit Pemegang</span>
                                                    <span class="text-emerald-300 font-semibold block truncate" x-text="mItem.ruang_pemegang || (selectedAstapDetail || selectedMutasi).ruangan_tujuan || '-'"></span>
                                                    <span class="text-slate-400 text-[9px] block" x-text="'Thn Pembuatan: ' + (mItem.mesin_tahun_pembuatan || '-')"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- KIB C (GEDUNG & BANGUNAN) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB C'">
                        <div class="space-y-3">
                            <template x-if="getGedungItemsForDetail(selectedAstapDetail || selectedMutasi).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(gItem, gIdx) in getGedungItemsForDetail(selectedAstapDetail || selectedMutasi)" :key="gIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-purple-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[11px] border border-purple-500/30"
                                                          x-text="'Gedung #' + (gIdx + 1)"></span>
                                                    <span class="text-white font-bold text-xs" x-text="gItem.gedung_nama_barang || (selectedAstapDetail || selectedMutasi).nama_barang"></span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                          x-text="(gItem.gedung_jumlah_bangunan || 1) + ' ' + (gItem.gedung_satuan || 'Gedung')"></span>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Subtotal: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="formatRupiah((parseFloat(gItem.gedung_jumlah_bangunan) || 1) * (parseFloat(gItem.gedung_nilai_satuan) || 0))"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏢 Konstruksi &amp; Tingkat</span>
                                                    <span class="text-purple-300 font-bold block" x-text="(gItem.gedung_beton || 'Beton') + ' • ' + (gItem.gedung_bertingkat === 'Ya' ? 'Bertingkat' : 'Tidak Bertingkat')"></span>
                                                    <span class="text-slate-300 text-[9.5px]" x-text="'Status Tanah: ' + (gItem.gedung_status_tanah || 'Tanah Pemkab')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas Total Lantai</span>
                                                    <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(gItem.gedung_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                    <span class="text-slate-400 text-[9.5px]" x-text="'Kondisi: ' + (gItem.gedung_kondisi || 'Baik')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📋 Dokumen / IMB</span>
                                                    <span class="text-amber-300 font-mono text-[10px] block truncate" x-text="gItem.gedung_dokumen_no || 'Tanpa Dokumen IMB'"></span>
                                                    <span class="text-slate-400 text-[9.5px]" x-text="'Fungsi: ' + (gItem.gedung_fungsi || 'Pelayanan Medis')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Gedung</span>
                                                    <span class="text-emerald-300 font-medium block truncate" :title="gItem.gedung_alamat" x-text="gItem.gedung_alamat || (selectedAstapDetail || selectedMutasi).alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- KIB D (JARINGAN & IRIGASI) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB D'">
                        <div class="space-y-3">
                            <template x-if="getJaringanItemsForDetail(selectedAstapDetail || selectedMutasi).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(jItem, jIdx) in getJaringanItemsForDetail(selectedAstapDetail || selectedMutasi)" :key="jIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-teal-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-teal-500/20 text-teal-300 font-mono font-bold text-[11px] border border-teal-500/30"
                                                          x-text="'Ruas #' + (jIdx + 1)"></span>
                                                    <span class="text-white font-bold text-xs" x-text="jItem.jaringan_nama_barang || (selectedAstapDetail || selectedMutasi).nama_barang"></span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                          x-text="(jItem.jaringan_jumlah || 1) + ' ' + (jItem.jaringan_satuan || 'Ruas')"></span>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Subtotal: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="formatRupiah((parseFloat(jItem.jaringan_jumlah) || 1) * (parseFloat(jItem.jaringan_nilai_satuan) || 0))"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🛣️ Konstruksi &amp; Tipe</span>
                                                    <span class="text-teal-300 font-bold block" x-text="(jItem.jaringan_konstruksi || 'Aspal/Pipa') + ' • ' + (jItem.jaringan_beton || 'Beton')"></span>
                                                    <span class="text-slate-300 text-[9.5px]" x-text="'Tanah: ' + (jItem.jaringan_status_tanah || 'Tanah Pemkab')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Dimensi &amp; Luas</span>
                                                    <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(jItem.jaringan_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                    <span class="text-slate-400 text-[9.5px]" x-text="'P: ' + (jItem.jaringan_panjang_m || 0) + ' m • L: ' + (jItem.jaringan_lebar_m || 0) + ' m'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📋 Dokumen / Legalitas</span>
                                                    <span class="text-amber-300 font-mono text-[10px] block truncate" x-text="jItem.jaringan_dokumen_no || 'Tanpa Nomor Dokumen'"></span>
                                                    <span class="text-slate-400 text-[9.5px]" x-text="'Keterangan: ' + (jItem.jaringan_keterangan || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Jaringan</span>
                                                    <span class="text-emerald-300 font-medium block truncate" :title="jItem.jaringan_alamat" x-text="jItem.jaringan_alamat || (selectedAstapDetail || selectedMutasi).alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- KIB E (ASET TETAP LAINNYA) -->
                    <template x-if="getEffectiveKibCategory(selectedAstapDetail || selectedMutasi) === 'KIB E'">
                        <div class="space-y-3">
                            <template x-if="getLainnyaItemsForDetail(selectedAstapDetail || selectedMutasi).length > 0">
                                <div class="space-y-2.5">
                                    <template x-for="(lItem, lIdx) in getLainnyaItemsForDetail(selectedAstapDetail || selectedMutasi)" :key="lIdx">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-orange-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-orange-500/20 text-orange-300 font-mono font-bold text-[11px] border border-orange-500/30"
                                                          x-text="'Item #' + (lIdx + 1)"></span>
                                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border"
                                                          :class="(lItem.kib_e_type === 'kesenian') ? 'bg-purple-500/20 text-purple-300 border-purple-500/40' : ((lItem.kib_e_type === 'hewan_tumbuhan') ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-amber-500/20 text-amber-300 border-amber-500/40')"
                                                          x-text="(lItem.kib_e_type === 'kesenian' ? '🎨 Kesenian' : (lItem.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan/Tumbuhan' : '📚 Buku/Kepustakaan'))"></span>
                                                    <span class="text-white font-bold text-xs" x-text="lItem.lainnya_nama_barang || lItem.lainnya_judul || (selectedAstapDetail || selectedMutasi).nama_barang"></span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                          x-text="(lItem.lainnya_jumlah || lItem.lainnya_jumlah_barang || 1) + ' ' + (lItem.lainnya_satuan || 'Eksemplar')"></span>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Subtotal: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="formatRupiah((parseFloat(lItem.lainnya_jumlah || lItem.lainnya_jumlah_barang) || 1) * (parseFloat(lItem.lainnya_nilai_satuan) || 0))"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📚 Judul &amp; Pencipta</span>
                                                    <span class="text-amber-300 font-bold block truncate" x-text="lItem.lainnya_judul || lItem.lainnya_nama_barang || '-'"></span>
                                                    <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Pencipta: ' + (lItem.lainnya_pencipta || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔍 Spesifikasi &amp; Asal</span>
                                                    <span class="text-slate-200 font-medium block truncate" x-text="lItem.lainnya_spesifikasi || '-'"></span>
                                                    <span class="text-slate-400 text-[9px] block truncate" x-text="'Bahan: ' + (lItem.lainnya_bahan || '-') + ' • Thn: ' + (lItem.lainnya_tahun || '-')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Satuan &amp; Kondisi</span>
                                                    <span class="text-emerald-300 font-medium block text-[10px]" x-text="formatRupiah(lItem.lainnya_nilai_satuan || 0)"></span>
                                                    <span class="text-slate-300 text-[9.5px]" x-text="'Kondisi: ' + (lItem.lainnya_kondisi || 'Baik')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏥 Ruang / Unit Pemegang</span>
                                                    <span class="text-amber-300 font-medium block truncate" x-text="lItem.ruang_pemegang || (selectedAstapDetail || selectedMutasi).ruangan_tujuan || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- 6. BERKAS LEGALITAS & DOKUMEN MUTASI DIGITAL -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3.5 shadow-lg">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                        <span class="text-xs font-black text-white uppercase tracking-wider flex items-center space-x-2">
                            <span class="text-indigo-400">📜</span>
                            <span>BERKAS LEGALITAS &amp; DOKUMEN MUTASI DIGITAL</span>
                        </span>
                        <span class="text-[10px] font-mono font-bold px-2.5 py-0.5 rounded-full border bg-slate-900 border-slate-800 text-slate-300"
                            x-text="((selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'No. BAST: ' : 'No. BAMB: ') + ((selectedAstapDetail || selectedMutasi)?.mutasi_nomor_bamb || (selectedAstapDetail || selectedMutasi)?.kode || '-')"></span>
                    </div>

                    <!-- KARTU BERKAS: BERITA ACARA (BAMB / BAST) -->
                    <div class="p-3.5 rounded-2xl border transition-all duration-200"
                        :class="(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url
                            ? 'bg-slate-900/90 border-indigo-500/40 shadow-md shadow-indigo-500/5'
                            : 'bg-slate-950/50 border-slate-800/80 opacity-75'">
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border"
                                    :class="(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url
                                        ? ((selectedAstapDetail || selectedMutasi).dokumen_lampiran_url.toLowerCase().endsWith('.pdf') ? 'bg-rose-500/15 border-rose-500/30 text-rose-400' : 'bg-indigo-500/15 border-indigo-500/30 text-indigo-400')
                                        : 'bg-slate-900 border-slate-800 text-slate-600'">
                                    <template x-if="(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url && (selectedAstapDetail || selectedMutasi).dokumen_lampiran_url.toLowerCase().endsWith('.pdf')">
                                        <span class="text-lg">📄</span>
                                    </template>
                                    <template x-if="(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url && !(selectedAstapDetail || selectedMutasi).dokumen_lampiran_url.toLowerCase().endsWith('.pdf')">
                                        <span class="text-lg">🖼️</span>
                                    </template>
                                    <template x-if="!(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url">
                                        <span class="text-lg">📑</span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider"
                                            x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'Berkas Berita Acara (BAST Transfer Keluar)' : 'Berkas Berita Acara (BAMB / SK Pelimpahan)'"></span>
                                        <template x-if="(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url">
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-extrabold border"
                                                :class="(selectedAstapDetail || selectedMutasi).dokumen_lampiran_url.toLowerCase().endsWith('.pdf') ? 'bg-rose-500/20 text-rose-300 border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'"
                                                x-text="(selectedAstapDetail || selectedMutasi).dokumen_lampiran_url.toLowerCase().endsWith('.pdf') ? 'PDF' : 'GAMBAR'"></span>
                                        </template>
                                    </div>
                                    <p class="text-xs font-bold text-white truncate max-w-[260px] sm:max-w-md mt-0.5"
                                       :title="((selectedAstapDetail || selectedMutasi)?.dokumen_lampiran || '').split('/').pop() || 'Belum Ada Berkas'"
                                       x-text="((selectedAstapDetail || selectedMutasi)?.dokumen_lampiran || '').split('/').pop() || 'Belum diunggah'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Aksi Kartu Berkas -->
                        <div class="mt-3 pt-2.5 border-t border-slate-800/80">
                            <template x-if="(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url">
                                <button type="button" @click="openDocPreview((selectedAstapDetail || selectedMutasi).dokumen_lampiran_url, ((selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? 'BAST Transfer Keluar: ' : 'BAMB Pelimpahan Masuk: ') + ((selectedAstapDetail || selectedMutasi)?.mutasi_nomor_bamb || (selectedAstapDetail || selectedMutasi)?.kode || ''))"
                                    class="w-full py-1.5 px-2.5 rounded-xl bg-indigo-500/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/40 text-[11px] font-bold transition-all flex items-center justify-center space-x-1.5 shadow-sm active:scale-95 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Lihat Berkas</span>
                                </button>
                            </template>
                            <template x-if="!(selectedAstapDetail || selectedMutasi)?.dokumen_lampiran_url">
                                <span class="text-[10.5px] text-slate-500 italic flex items-center gap-1.5 py-1">
                                    <span>📎</span> Belum ada berkas scan berita acara yang diunggah (opsional).
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 7. TABEL RINCIAN REGISTER NIBAR PER-UNIT PERSIS DATA ASTAP -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-800 pb-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider">🏷️ RINCIAN NIBAR &amp; PENEMPATAN RUANGAN (REGISTER):</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="(selectedMutasi && selectedMutasi.tipe === 'keluar') ? 'Daftar kode NIBAR unit aset yang dimutasi keluar ke instansi / OPD tujuan.' : 'Daftar unik kode NIBAR per-aset hasil mutasi masuk beserta ruangan penempatannya di RSUD Koesnandi.'"></p>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            <span class="text-[10px] font-extrabold px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono shadow-sm"
                                  x-text="filteredRegisters.length + ' / ' + ((selectedAstapDetail || selectedMutasi).registers ? (selectedAstapDetail || selectedMutasi).registers.length : 0) + ' Aset'"></span>
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

                    <!-- Responsive Scroll Container (Max 5 Rows Scrollable) -->
                    <div class="rounded-xl border border-slate-800/80 custom-scrollbar shadow-inner" style="max-height: 295px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full text-left text-[11px] text-slate-300 min-w-[780px]">
                            <thead class="bg-slate-950 text-slate-400 font-extrabold uppercase text-[9.5px] border-b border-slate-800 tracking-wider shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                                <tr>
                                    <th class="px-4 py-3 text-center whitespace-nowrap">NIBAR RESMI</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">Status</th>
                                    <th class="px-4 py-3 text-center whitespace-nowrap min-w-[200px]">Lokasi Penempatan</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">Kondisi</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">QR Code</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                <template x-for="reg in filteredRegisters" :key="reg.id">
                                    <tr class="hover:bg-slate-800/60 transition-colors">
                                        <!-- 1. NIBAR & No Register Resmi -->
                                        <td class="px-4 py-3 text-center whitespace-nowrap align-middle">
                                            <div class="inline-flex items-center justify-center gap-1.5 group/copy">
                                                <span class="font-mono text-[11px] font-bold text-slate-200 tracking-tight group-hover/copy:text-emerald-300 transition-colors select-all"
                                                      :title="'NIBAR Resmi: ' + (reg.nibar || reg.no_register)"
                                                      x-text="reg.nibar || reg.no_register"></span>
                                                <button type="button" @click.stop="navigator.clipboard.writeText(reg.nibar || reg.no_register); showToast('NIBAR berhasil disalin ke clipboard!', 'success')"
                                                        title="Salin NIBAR ke Clipboard"
                                                        class="p-1 rounded-md bg-slate-800/60 hover:bg-emerald-500/20 text-slate-400 hover:text-emerald-300 border border-slate-700/60 hover:border-emerald-500/30 transition-all cursor-pointer">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- 2. Status Aset -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <template x-if="reg.status === 'Mutasi Keluar OPD' || (selectedAstapDetail || selectedMutasi)?.tipe === 'keluar'">
                                                <span class="px-2.5 py-1 rounded-xl text-[9.5px] font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-sm">
                                                    🏛️ MUTASI KELUAR
                                                </span>
                                            </template>
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && (selectedAstapDetail || selectedMutasi)?.tipe !== 'keluar'">
                                                <span class="px-2.5 py-1 rounded-xl text-[9.5px] font-bold uppercase tracking-wider bg-slate-950 text-slate-400 border border-slate-800"
                                                      x-text="(isRegisterPlacedInUnit(reg) || reg.status === 'Tidak Tersedia') ? 'Tidak Tersedia' : (reg.status || 'Tersedia')">
                                                </span>
                                            </template>
                                        </td>

                                        <!-- 3. Lokasi Penempatan (MURNI LOKASI FISIK RUANGAN RSUD / OPD TUJUAN) -->
                                        <td class="px-4 py-3 text-center align-middle">
                                            <!-- A. Mutasi Keluar Antar-OPD -->
                                            <template x-if="reg.status === 'Mutasi Keluar OPD' || (selectedAstapDetail || selectedMutasi)?.tipe === 'keluar'">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-cyan-950/80 border border-cyan-500/40 text-cyan-300 text-xs font-semibold shadow-sm max-w-[220px]"
                                                     :title="(selectedAstapDetail || selectedMutasi)?.opd_tujuan || 'OPD Penerima'">
                                                    <span class="text-cyan-400 text-xs shrink-0">🏛️</span>
                                                    <span class="truncate" x-text="(selectedAstapDetail || selectedMutasi)?.opd_tujuan || 'OPD Penerima'"></span>
                                                </div>
                                            </template>

                                            <!-- B. Mutasi Masuk / Internal yang Telah Ditempatkan di Ruangan / Unit RSUD -->
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && (selectedAstapDetail || selectedMutasi)?.tipe !== 'keluar' && isRegisterPlacedInUnit(reg)">
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-emerald-500/40 text-emerald-300 text-xs font-semibold shadow-sm max-w-[220px]"
                                                     :title="reg.ruang_pemegang">
                                                    <span class="text-emerald-400 text-xs shrink-0">📍</span>
                                                    <span class="truncate" x-text="reg.ruang_pemegang"></span>
                                                </div>
                                            </template>

                                            <!-- C. Belum Ditempatkan (Gudang Aset / Standby) -->
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && (selectedAstapDetail || selectedMutasi)?.tipe !== 'keluar' && !isRegisterPlacedInUnit(reg)">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 font-bold text-[10px]">
                                                    <span>⚠️</span>
                                                    <span>Belum Ditempatkan</span>
                                                </span>
                                            </template>
                                        </td>

                                        <!-- 4. Kondisi Fisik Murni -->
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

                                        <!-- 5. QR Code Download -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <button type="button" @click.stop="downloadQrCodeNibar(reg, selectedAstapDetail || selectedMutasi)"
                                                title="Pratinjau & Download QR NIBAR Aset Ini"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/25 border border-emerald-500/30 hover:border-emerald-400 text-emerald-400 hover:text-emerald-300 font-bold text-[10.5px] transition-all shadow-sm active:scale-95 group cursor-pointer leading-none">
                                                <svg class="w-3.5 h-3.5 text-emerald-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-3.5-3.5M12 16l3.5-3.5M4 20h16"/>
                                                </svg>
                                                <span>Download QR</span>
                                            </button>
                                        </td>

                                        <!-- 6. Aksi (Riwayat Mutasi, Ubah Kondisi, Hapus) -->
                                        <td class="px-3.5 py-3 text-center whitespace-nowrap align-middle">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- 1. Tombol Cek Riwayat Aset (Mutasi) -->
                                                <button type="button" @click.stop="openRiwayatModal(reg)" title="Cek Riwayat Mutasi Aset Ini"
                                                        class="p-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:text-purple-300 transition-all cursor-pointer shadow-sm active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>

                                                @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'edit') || Auth::user()?->canDo('mutasi', 'edit'))
                                                <!-- 2. Tombol Ubah Kondisi Barang (Modal Khusus) -->
                                                <button type="button" @click.stop="openEditKondisiModal(reg)" title="Ubah Kondisi Aset Ini"
                                                        class="p-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer shadow-sm active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                </button>
                                                @endif

                                                @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'delete') || Auth::user()?->canDo('mutasi', 'delete'))
                                                <!-- 3. Tombol Hapus Register -->
                                                <button type="button" @click.stop="deleteRegister(reg)" title="Hapus Aset Register Ini"
                                                        class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all cursor-pointer shadow-sm active:scale-95">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredRegisters.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-3 py-6 text-center text-slate-500 italic text-xs">
                                            Tidak ditemukan rincian register NIBAR yang sesuai dengan filter pencarian.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 8. CATATAN / MAKSUD & ALASAN MUTASI -->
                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                    <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider" x-text="(selectedAstapDetail || selectedMutasi)?.tipe === 'keluar' ? '💡 Maksud &amp; Alasan Mutasi Keluar Antar-OPD:' : '💡 Maksud &amp; Alasan Mutasi Masuk Antar-OPD:'"></span>
                    <p class="text-slate-200 text-xs leading-relaxed" x-text="(selectedAstapDetail || selectedMutasi).alasan_mutasi || (selectedAstapDetail || selectedMutasi).mutasi_keterangan || '-'"></p>
                </div>

                <!-- ALASAN PEMBATALAN JIKA TRANSAKSI DIBATALKAN -->
                <template x-if="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                    <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 space-y-1.5">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                            <span class="text-rose-400 text-[10px] font-extrabold uppercase tracking-wider">Informasi Pembatalan Transaksi</span>
                            <span class="text-rose-300/80 text-[10px] font-semibold" x-show="(selectedAstapDetail || selectedMutasi)?.dibatalkan_by" x-text="'• Oleh: ' + (selectedAstapDetail || selectedMutasi)?.dibatalkan_by"></span>
                            <span class="text-slate-400 text-[10px]" x-show="(selectedAstapDetail || selectedMutasi)?.dibatalkan_at" x-text="'(' + (selectedAstapDetail || selectedMutasi)?.dibatalkan_at + ')'"></span>
                        </div>
                        <p class="text-rose-200 text-xs leading-relaxed" x-text="(selectedAstapDetail || selectedMutasi)?.alasan_batal ? ('Alasan: ' + (selectedAstapDetail || selectedMutasi)?.alasan_batal) : 'Alasan pembatalan tidak dicantumkan.'"></p>
                    </div>
                </template>

                <!-- RIWAYAT REAKTIVASI JIKA PERNAH DIAKTIFKAN KEMBALI -->
                <template x-if="(selectedAstapDetail || selectedMutasi)?.alasan_reaktivasi && !((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                    <div class="p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 space-y-1.5">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span class="text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider">Riwayat Pengaktifan Kembali (Reaktivasi)</span>
                            <span class="text-emerald-300/80 text-[10px] font-semibold" x-show="(selectedAstapDetail || selectedMutasi)?.diaktifkan_by" x-text="'• Oleh: ' + (selectedAstapDetail || selectedMutasi)?.diaktifkan_by"></span>
                            <span class="text-slate-400 text-[10px]" x-show="(selectedAstapDetail || selectedMutasi)?.diaktifkan_at" x-text="'(' + (selectedAstapDetail || selectedMutasi)?.diaktifkan_at + ')'"></span>
                        </div>
                        <p class="text-emerald-200 text-xs leading-relaxed" x-text="'Alasan Reaktivasi: ' + (selectedAstapDetail || selectedMutasi)?.alasan_reaktivasi"></p>
                    </div>
                </template>
            </div>
        </template>

        <!-- 9. FOOTER MODAL ACTIONS -->
        <div class="pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-2">
            <button type="button" @click="showDetailModal = false"
                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-xs transition-all shadow-md active:scale-95 cursor-pointer">
                Tutup Detail
            </button>
            <div class="flex items-center space-x-2">
                {{-- Indikator Status Dibatalkan --}}
                <template x-if="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                    <span class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-extrabold text-xs select-none">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <span>Status: Dibatalkan</span>
                    </span>
                </template>

                @if(in_array(auth()->user()?->role, ['master_admin', 'admin']))
                    {{-- Jika Belum Dibatalkan: Tombol Batalkan Mutasi --}}
                    <template x-if="!((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                        <button type="button" @click="openBatalModal(selectedAstapDetail || selectedMutasi)"
                            class="px-4 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 hover:border-rose-400 font-extrabold text-xs transition-all shadow-md flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Batalkan Mutasi</span>
                        </button>
                    </template>

                    {{-- Jika Sudah Dibatalkan: Tombol Aktifkan Kembali Mutasi --}}
                    <template x-if="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                        <button type="button" @click="openReaktivasiModal(selectedAstapDetail || selectedMutasi)"
                            title="Batalkan status pembatalan dan aktifkan kembali dokumen transaksi mutasi ini"
                            class="px-4 py-2.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 hover:border-emerald-400 font-extrabold text-xs transition-all shadow-md flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Aktifkan Kembali Mutasi</span>
                        </button>
                    </template>
                @endif

                {{-- Tombol Cetak / Preview BAST (Dinamis: Aktif jika Disahkan, Disabled jika Dibatalkan) --}}
                <button type="button" 
                    :disabled="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')"
                    @click="openPrintModal(selectedAstapDetail || selectedMutasi)"
                    :title="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal') ? 'Pencetakan BAST dinonaktifkan karena transaksi telah dibatalkan.' : 'Cetak Dokumen Resmi Berita Acara Serah Terima (BAST)'"
                    class="px-5 py-2.5 rounded-xl font-extrabold text-xs transition-all flex items-center space-x-2 select-none leading-none"
                    :class="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')
                        ? 'bg-slate-900 border border-slate-800 text-slate-500 cursor-not-allowed opacity-60 shadow-none'
                        : 'bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white shadow-lg shadow-purple-500/25 active:scale-95 cursor-pointer'">
                    
                    <template x-if="!((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </template>
                    <template x-if="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal')">
                        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </template>

                    <span x-text="((selectedAstapDetail || selectedMutasi)?.status || '').toLowerCase().includes('batal') ? '🔒 Cetak / Preview BAST' : '🖨️ Cetak / Preview BAST'"></span>
                </button>
            </div>
        </div>

    </div>
</div>
</template>

<!-- ========================================================================= -->
<!-- MODAL VIEWER PRATINJAU DOKUMEN DIGITAL (PDF & GAMBAR BERKAS BAMB / BAST)  -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="previewDocModalOpen" x-cloak
        @click.self="closeDocPreview()"
        @keydown.escape.window="closeDocPreview()"
        class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-5 md:p-8 overflow-y-auto"
        style="background-color: rgba(2, 6, 23, 0.90); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <!-- Click outside backdrop to close -->
        <div class="fixed inset-0" @click="closeDocPreview()"></div>

        <!-- Modal Dialog Box -->
        <div class="relative w-full max-w-5xl rounded-3xl border border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[92vh] z-10"
             style="background-color: #0f172a;"
             @click.stop>

            <!-- Viewer Header -->
            <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between gap-3 bg-slate-950/90">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 border"
                        :class="previewDocType === 'pdf' ? 'bg-rose-500/20 border-rose-500/30 text-rose-400' : 'bg-cyan-500/20 border-cyan-500/30 text-cyan-400'">
                        <span x-text="previewDocType === 'pdf' ? '📄' : '🖼️'" class="text-base"></span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-black text-white truncate" x-text="previewDocTitle || 'Pratinjau Berkas Dokumen'"></h3>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-extrabold border uppercase"
                                :class="previewDocType === 'pdf' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40'"
                                x-text="previewDocType === 'pdf' ? 'PDF DOKUMEN' : 'GAMBAR SCAN'"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-mono truncate mt-0.5" x-text="previewDocUrl"></p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <!-- Buka Tab Baru -->
                    <a :href="previewDocUrl" target="_blank" rel="noopener noreferrer"
                        title="Buka Penuh di Tab Baru"
                        class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700 font-bold text-xs flex items-center space-x-1.5 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span class="hidden sm:inline">Tab Baru</span>
                    </a>

                    <!-- Unduh -->
                    <a :href="previewDocUrl" :download="(previewDocTitle || 'dokumen').replace(/[^a-zA-Z0-9_-]/g, '_')"
                        title="Unduh Berkas Ini"
                        class="px-3 py-1.5 rounded-xl bg-indigo-500/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/40 font-bold text-xs flex items-center space-x-1.5 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="hidden sm:inline">Unduh</span>
                    </a>

                    <!-- Tutup -->
                    <button type="button" @click="closeDocPreview()"
                        class="w-8 h-8 rounded-xl bg-slate-900 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-slate-800 flex items-center justify-center transition-all cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Viewer Body Container -->
            <div class="flex-1 overflow-auto bg-slate-950 p-2 sm:p-4 flex items-center justify-center min-h-[60vh] max-h-[78vh] custom-scrollbar">
                <!-- Jika PDF: Tampilkan Iframe Viewer -->
                <template x-if="previewDocType === 'pdf'">
                    <iframe :src="previewDocUrl"
                            class="w-full h-full min-h-[70vh] rounded-2xl border border-slate-800 shadow-inner bg-slate-900"
                            frameborder="0"></iframe>
                </template>

                <!-- Jika Gambar: Tampilkan Image Viewer Responsif -->
                <template x-if="previewDocType === 'image'">
                    <div class="w-full h-full flex items-center justify-center p-2">
                        <img :src="previewDocUrl"
                             :alt="previewDocTitle"
                             class="max-h-[72vh] max-w-full w-auto h-auto rounded-2xl object-contain border border-slate-800 shadow-2xl">
                    </div>
                </template>
            </div>

            <!-- Viewer Footer Bar -->
            <div class="px-5 py-2.5 bg-slate-950 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span>💡</span> Gunakan kontrol zoom dan rotasi bawaan browser untuk memeriksa berkas lebih teliti.
                </span>
                <button type="button" @click="closeDocPreview()"
                    class="px-4 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition-all cursor-pointer">
                    Tutup Pratinjau
                </button>
            </div>

        </div>
    </div>
</template>
