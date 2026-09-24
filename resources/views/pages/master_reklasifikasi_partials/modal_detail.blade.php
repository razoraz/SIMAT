<!-- MODAL DETAIL AUDIT & KOMPARASI SPESIFIKASI REKLASIFIKASI -->
<div x-show="showModalDetail" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md" @click="showModalDetail = false"></div>

    <!-- Modal Container -->
    <div class="relative w-full max-w-4xl max-h-[92vh] flex flex-col bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden z-10"
         x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
        
        <!-- Header Strip Accent -->
        <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-cyan-500 to-emerald-500 shrink-0"></div>

        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-800 bg-slate-950/70 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 rounded-2xl bg-indigo-500/15 text-indigo-400 border border-indigo-500/30 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base sm:text-lg font-black text-white">Rincian Audit Reklasifikasi</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-wider">
                            Audit Trail BPK
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Komparasi snapshot spesifikasi aset sebelum vs sesudah pemindahan bukuan</p>
                </div>
            </div>

            <button type="button" @click="showModalDetail = false"
                class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <div class="overflow-y-auto p-6 space-y-5 flex-1 scrollbar-thin scrollbar-thumb-slate-700">
            <template x-if="detailItem">
                <div class="space-y-5">
                    
                    <!-- 1. Ringkasan Dokumen Berita Acara & Aset Terpilih -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-inner">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama Barang Milik Daerah (BMD)</span>
                                <h4 class="text-base font-extrabold text-white mt-0.5" x-text="detailItem.astap?.nama_barang || 'Aset #' + detailItem.astap_id"></h4>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="text-xs font-mono text-cyan-400 bg-cyan-950/40 px-2 py-0.5 rounded border border-cyan-500/20"
                                          x-text="'Kode 108: ' + (detailItem.astap?.kode_108 || detailItem.astap?.jenis_astap?.sub_sub_rincian_objek || '-')"></span>
                                    <span class="text-xs text-slate-400" x-text="'Tahun Pengadaan: ' + (detailItem.astap?.tahun_perolehan || detailItem.tahun)"></span>
                                </div>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nilai Mutasi Reklasifikasi</span>
                                <div class="text-lg font-black font-mono text-emerald-400 mt-0.5"
                                     x-text="'Rp ' + Number(detailItem.nilai_reklas || 0).toLocaleString('id-ID')"></div>
                                <div class="text-[11px] font-medium text-slate-400" x-text="'Triwulan ' + detailItem.triwulan + ' · Periode ' + detailItem.tahun"></div>
                            </div>
                        </div>

                        <!-- Grid Info Administratif -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1">
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jenis Transaksi:</span>
                                <span class="font-bold text-indigo-300 mt-1 inline-block" x-text="getJenisReklasLabel(detailItem.jenis_reklas)"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor Berita Acara (BA):</span>
                                <span class="font-bold font-mono text-white mt-1 inline-block" x-text="detailItem.nomor_ba_reklas || 'Tidak ada nomor BA'"></span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Transaksi:</span>
                                <span class="font-bold text-white mt-1 inline-block" x-text="formatDateIndo(detailItem.tanggal_reklas)"></span>
                            </div>
                        </div>

                        <!-- Keterangan / Alasan Reklasifikasi -->
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 flex items-start gap-2.5">
                            <span class="text-base text-indigo-400 shrink-0">📝</span>
                            <div class="text-xs text-slate-300 leading-relaxed">
                                <span class="font-bold text-white">Alasan / Catatan Rekonsiliasi:</span>
                                <p class="mt-0.5 italic text-slate-300" x-text="detailItem.keterangan || 'Tidak ada catatan tambahan.'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Perpindahan Rekening / Kelompok KIB -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-950/90 to-slate-900/90 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                                <span>🔄</span> Perpindahan Kelompok &amp; Rekening Akuntansi PMDN 108
                            </h5>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 items-stretch">
                            <!-- Rekening Asal (Mutasi Kurang) -->
                            <div class="p-3.5 rounded-xl bg-rose-500/5 border border-rose-500/25 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        ASAL (MUTASI KURANG -)
                                    </span>
                                    <span class="text-[11px] font-mono font-bold text-rose-300" x-text="detailItem.asal_kib || detailItem.jenis_reklas_asal?.kelompok_kib || '-'"></span>
                                </div>
                                <div class="text-xs font-extrabold text-white mt-1"
                                     x-text="detailItem.jenis_reklas_asal?.nama_sub_rincian || detailItem.astap?.jenis_astap?.uraian_sub_rincian || '-'"></div>
                                <div class="text-[11px] font-mono text-rose-200/70"
                                     x-text="'Kode: ' + (detailItem.jenis_reklas_asal?.kode_prefix || detailItem.astap?.jenis_astap?.sub_rincian_objek || '-')"></div>
                            </div>

                            <!-- Rekening Tujuan (Mutasi Tambah) -->
                            <div class="p-3.5 rounded-xl bg-emerald-500/5 border border-emerald-500/25 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        TUJUAN (MUTASI TAMBAH +)
                                    </span>
                                    <span class="text-[11px] font-mono font-bold text-emerald-300" x-text="detailItem.tujuan_kib || detailItem.jenis_reklas_tujuan?.kelompok_kib || '-'"></span>
                                </div>
                                <div class="text-xs font-extrabold text-white mt-1"
                                     x-text="detailItem.jenis_reklas_tujuan?.nama_sub_rincian || detailItem.tujuan_nama || '-'"></div>
                                <div class="text-[11px] font-mono text-emerald-200/70"
                                     x-text="'Kode: ' + (detailItem.jenis_reklas_tujuan?.kode_prefix || detailItem.tujuan_kode || detailItem.astap?.kode_108 || '-')"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Perbandingan Spesifikasi Fisik (Audit Trail Snapshot) -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3.5 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="p-1 rounded-lg bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 text-sm">⚖️</span>
                                <h5 class="text-xs font-bold text-white uppercase tracking-wider">
                                    Perbandingan Spesifikasi Fisik (Sebelum vs Sesudah)
                                </h5>
                            </div>
                            <span class="text-[10px] text-cyan-300 font-semibold px-2 py-0.5 rounded-md bg-cyan-500/10 border border-cyan-500/20">
                                Snapshot Otentik
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Kolom Spesifikasi Lama -->
                            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                                    <span class="text-xs font-extrabold text-rose-400 flex items-center gap-1.5">
                                        <span>⏪</span> Spesifikasi Lama (Sebelum Reklas)
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700"
                                          x-text="detailItem.asal_kib || detailItem.astap?.category || 'KIB Asal'"></span>
                                </div>

                                <div class="space-y-2 text-xs">
                                    <template x-for="(val, key) in getFormattedSpec(detailItem.spesifikasi_lama || detailItem.astap?.spesifikasi_json)" :key="key">
                                        <div class="flex items-start justify-between gap-2 py-1 border-b border-slate-800/40">
                                            <span class="text-slate-400 text-[11px]" x-text="key"></span>
                                            <span class="font-bold text-slate-200 text-[11px] text-right break-words max-w-[60%]" x-text="val"></span>
                                        </div>
                                    </template>
                                    <template x-if="!detailItem.spesifikasi_lama && !detailItem.astap?.spesifikasi_json">
                                        <div class="p-3 text-center text-slate-500 text-xs italic">
                                            Tidak ada rincian spesifikasi fisik lama tersimpan.
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Kolom Spesifikasi Baru -->
                            <div class="p-4 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-3 shadow-lg shadow-cyan-500/5">
                                <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                                    <span class="text-xs font-extrabold text-cyan-300 flex items-center gap-1.5">
                                        <span>⏩</span> Spesifikasi Baru (Pasca Reklas)
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30"
                                          x-text="detailItem.tujuan_kib || detailItem.jenis_reklas_tujuan?.kelompok_kib || 'KIB Tujuan'"></span>
                                </div>

                                <div class="space-y-2 text-xs">
                                    <template x-for="(val, key) in getFormattedSpec(detailItem.spesifikasi_baru)" :key="key">
                                        <div class="flex items-start justify-between gap-2 py-1 border-b border-slate-800/40">
                                            <span class="text-cyan-200/80 text-[11px]" x-text="key"></span>
                                            <span class="font-bold text-white text-[11px] text-right break-words max-w-[60%]" x-text="val"></span>
                                        </div>
                                    </template>
                                    <template x-if="!detailItem.spesifikasi_baru || Object.keys(detailItem.spesifikasi_baru).length === 0">
                                        <div class="p-4 text-center text-slate-400 text-xs italic bg-slate-950/60 rounded-xl border border-dashed border-slate-800">
                                            Tidak ada spesifikasi khusus yang diubah (Hanya penyesuaian rekening pembukuan).
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Petugas & Validitas Audit -->
                    <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <span>👤 Petugas Operator:</span>
                            <span class="font-bold text-slate-200" x-text="detailItem.user?.name || 'Administrator System'"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>ID Log:</span>
                            <span class="font-mono text-indigo-400 font-bold" x-text="'#REK-' + String(detailItem.id).padStart(5, '0')"></span>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-slate-800 bg-slate-950/70 flex items-center justify-between shrink-0">
            <span class="text-[11px] text-slate-400 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Standar Audit BPK RI · PMDN No. 108/2016</span>
            </span>

            <button type="button" @click="showModalDetail = false"
                class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-all cursor-pointer">
                Tutup Rincian
            </button>
        </div>
    </div>
</div>
