<!-- Modal Riwayat Reklasifikasi Aset (Bespoke Dark Dashboard) -->
<div x-show="showRiwayatReklasModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="closeRiwayatReklasModal()"
     style="display: none;">

    <!-- Backdrop with subtle glassmorphism -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
         @click="closeRiwayatReklasModal()"></div>

    <!-- Modal Dialog Container -->
    <div class="relative w-full max-w-3xl bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl shadow-indigo-500/10 overflow-hidden transform transition-all flex flex-col max-h-[90vh] z-10"
         @click.stop>
        
        <!-- Top Gradient Glow Line -->
        <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-cyan-500 to-purple-600"></div>

        <!-- Modal Header -->
        <div class="px-6 py-5 border-b border-slate-800/80 flex items-start justify-between gap-4 bg-slate-900/90">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0 shadow-sm shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base font-black text-white tracking-tight">Riwayat Transaksi Reklasifikasi</h3>
                        <template x-if="selectedRiwayatItem">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/40"
                                  x-text="(selectedRiwayatItem.reklas_history?.length || selectedRiwayatItem.reklas_count || 1) + 'x Reklas Terdaftar'"></span>
                        </template>
                    </div>
                    <div class="mt-1 flex items-center gap-2 text-xs text-slate-400 flex-wrap" x-if="selectedRiwayatItem">
                        <span class="font-bold text-slate-200" x-text="cleanNamaBarang(selectedRiwayatItem?.nama_barang)"></span>
                        <span class="text-slate-600">•</span>
                        <span class="font-mono text-cyan-400 text-[11px]" x-text="selectedRiwayatItem?.kode_barang"></span>
                        <span class="text-slate-600">•</span>
                        <span class="text-slate-400" x-text="'T.A. ' + (selectedRiwayatItem?.tahun_perolehan || '-')"></span>
                    </div>
                </div>
            </div>

            <!-- Close Button -->
            <button type="button" 
                    @click="closeRiwayatReklasModal()"
                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body: Timeline List of Reklasifikasi -->
        <div class="p-6 overflow-y-auto space-y-4 custom-scrollbar flex-1 bg-slate-900/50">
            <template x-if="selectedRiwayatItem && selectedRiwayatItem.reklas_history && selectedRiwayatItem.reklas_history.length > 0">
                <div class="space-y-4 relative before:absolute before:inset-0 before:left-5 before:w-0.5 before:bg-slate-800/80 before:z-0">
                    <template x-for="(rek, idx) in selectedRiwayatItem.reklas_history" :key="rek.id || idx">
                        <div class="relative z-10 pl-11 group">
                            <!-- Bullet Indicator on timeline -->
                            <div class="absolute left-3 top-4 -translate-x-1/2 w-4 h-4 rounded-full bg-slate-900 border-2 border-indigo-500 flex items-center justify-center shadow-md shadow-indigo-500/20 group-hover:scale-110 group-hover:border-cyan-400 transition-transform">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                            </div>

                            <!-- Card Container -->
                            <div class="bg-slate-950/80 border border-slate-800/80 hover:border-slate-700 rounded-2xl p-4 transition-all shadow-sm shadow-slate-950/50">
                                <!-- Card Header: Urutan, Jenis & Tanggal -->
                                <div class="flex items-start justify-between gap-3 flex-wrap border-b border-slate-800/60 pb-3 mb-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 text-[10px] font-black"
                                              x-text="'Reklas #' + (rek.urutan || (idx + 1))"></span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-slate-800/80 text-slate-200 border border-slate-700/60 text-[10.5px] font-bold"
                                              x-text="rek.jenis_reklas"></span>
                                        <template x-if="rek.sub_koreksi_label">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-amber-500/15 text-amber-300 border border-amber-500/30 text-[10px] font-bold"
                                                  x-text="rek.sub_koreksi_label"></span>
                                        </template>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-mono text-xs font-bold text-cyan-400" x-text="rek.nilai_formatted"></div>
                                        <div class="text-[10px] text-slate-500 font-medium" x-text="rek.tanggal_reklas"></div>
                                    </div>
                                </div>

                                <!-- Dokumen Berita Acara Reklasifikasi -->
                                <div class="mb-3 flex items-center gap-2 text-xs text-slate-400">
                                    <span class="text-slate-500 text-[11px] font-medium">No. BA Reklas:</span>
                                    <span class="font-mono text-slate-200 font-bold bg-slate-900 px-2 py-0.5 rounded-md border border-slate-800" 
                                          x-text="rek.nomor_ba_reklas || '-'"></span>
                                </div>

                                <!-- Alur Pemindahan (Asal ➔ Tujuan) -->
                                <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3 mb-3">
                                    <div class="text-[10px] font-black tracking-wider uppercase text-slate-400 mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Perubahan Klasifikasi Akun / KIB</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-center">
                                        <!-- Klasifikasi Asal -->
                                        <div class="p-2.5 rounded-lg bg-slate-950/60 border border-slate-800/80">
                                            <div class="text-[9.5px] font-extrabold uppercase text-rose-400/90 mb-1">Klasifikasi Asal</div>
                                            <div class="flex items-center gap-1.5 text-xs text-slate-300">
                                                <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-bold text-[10px]" x-text="'KIB ' + (rek.asal_kib || '-')"></span>
                                                <span class="font-mono text-[11px] text-slate-400" x-text="rek.asal_kode || '-'"></span>
                                            </div>
                                        </div>

                                        <!-- Klasifikasi Tujuan -->
                                        <div class="p-2.5 rounded-lg bg-indigo-950/20 border border-indigo-500/20">
                                            <div class="text-[9.5px] font-extrabold uppercase text-emerald-400/90 mb-1">Klasifikasi Tujuan Baru</div>
                                            <div class="flex items-center gap-1.5 text-xs text-indigo-200">
                                                <span class="px-1.5 py-0.5 rounded bg-indigo-500/30 text-indigo-200 font-bold text-[10px]" x-text="'KIB ' + (rek.tujuan_kib || '-')"></span>
                                                <span class="font-mono text-[11px] text-indigo-300 font-bold" x-text="rek.tujuan_kode || '-'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alasan / Keterangan Reklasifikasi -->
                                <div class="text-xs text-slate-300 bg-slate-900/60 rounded-xl p-2.5 border border-slate-800/60">
                                    <span class="text-slate-400 font-bold">Alasan / Catatan Penyesuaian:</span>
                                    <p class="mt-0.5 text-slate-300 leading-relaxed" x-text="rek.alasan_reklas || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Keadaan Kosong (Jika belum ada transaksi reklasifikasi) -->
            <template x-if="!selectedRiwayatItem || !selectedRiwayatItem.reklas_history || selectedRiwayatItem.reklas_history.length === 0">
                <div class="py-12 text-center text-slate-500">
                    <div class="w-12 h-12 rounded-2xl bg-slate-800/60 border border-slate-700/50 flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-sm font-bold text-slate-400">Belum Ada Riwayat Reklasifikasi</div>
                    <div class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Aset ini belum pernah mengalami transaksi reklasifikasi akun atau perpindahan KIB.</div>
                </div>
            </template>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-800/80 bg-slate-900/90 flex items-center justify-between gap-3">
            <div class="text-xs text-slate-500">
                <span class="hidden sm:inline">RSUD Dr. H. Koesnandi • Modul Tutup Buku & Reklasifikasi BMD</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="closeRiwayatReklasModal()"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-colors cursor-pointer">
                    Tutup Riwayat
                </button>
                <template x-if="selectedRiwayatItem">
                    <button type="button" 
                            @click="const itm = selectedRiwayatItem; closeRiwayatReklasModal(); openReklas(itm);"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-colors shadow-md shadow-indigo-600/20 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Proses Reklas Baru</span>
                    </button>
                </template>
            </div>
        </div>

    </div>
</div>
